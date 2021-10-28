<?php
/**
 * @package    mod_nxmarathon_reg
 *
 * @author     proximate <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;



class ModNxMarathonRegHelper{

    public static function getComponentParams($extension){
        if($extension){
            try{
                $db = JFactory::getDbo();
                $query = $db->getQuery(true);
                $query
                    ->select($db->quoteName('params'))
                    ->from($db->quoteName('#__extensions'))
                    ->where($db->quoteName('element') . " = " . $db->quote(''.strtolower($extension).''));
                $db->setQuery($query);
                $obj = $db->loadObject();

                return json_decode($obj->params);

            }catch(Exception $e){
                JFactory::getApplication()->enqueueMessage(JText::_('NX_ERR_NO_DBERR_APPPARAMS'), 'error');
                JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
                return false;
            }

        }else{
            JFactory::getApplication()->enqueueMessage(JText::_('NX_ERR_NO_EXTENSION'), 'error');
            return false;
        }
    }

    public static function buildErr($type,$text, $detail, $auth_err = true){
        /**
         *
         *  @param $auth_err  this message an authentification error - when yes it adds the translated NOT_AUTHORISED_TO_DO_X in front
         *
         */
        $err = new stdClass();
        if($auth_err) {
            $err->txt = JText::sprintf('NOT_AUTHORISED_TO_DO_X', $text);
        }else{
            $err->txt = $text;
        }
        $err->type = $type;
        $err->detail = $detail;
        $err->html = '<div class="uk-animation-scale-up uk-text-center"><div><span uk-icon="icon: warning;ratio:5"></span></div><div>'.$err->txt.'</div><pre class="uk-height-medium uk-overflow-scroll uk-text-left">'.var_export($detail,1).'</pre></div>';
        $err->success = false;
        return $err;
    }

    public static function getEvent($eventId){
        try{
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query
                ->select('*')
                ->from($db->quoteName('#__nxmarathonmanager_event'))
                ->where($db->quoteName('id') . " = ".$db->quote($eventId));
            $db->setQuery($query);
            return $db->loadObject();

        }catch(Exception $e){
            JFactory::getApplication()->enqueueMessage(JText::sprintf('NX_ERR_NO_DB_ERR_GET_EVENT',$e->getMessage()), 'error');
            return false;
        }
    }

    public static function getNations(){
        try{
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query
                ->select($db->quoteName(array('id','countrycode','name')))
                ->from($db->quoteName('#__nxmarathonmanager_country'))
                ->where($db->quoteName('published') . " = 1")
            ->orderBy('ordering ASC');
            $db->setQuery($query);
            return $db->loadObjectList('id');

        }catch(Exception $e){
            JFactory::getApplication()->enqueueMessage(JText::sprintf('NX_ERR_NO_DB_ERR',$e->getMessage()), 'error');
            return false;
        }
    }

    public static function getCategoryId($parcours, $admingrp){
        try{
            if(!$parcours || strlen($parcours) == 0){
                $result = new stdClass();
                $result->msg = [JText::_('PARCOURS_NOT_DEF')];
                $result->status = 'failed';
                $result->id = '0';
            }
            if(!$admingrp || strlen($admingrp) == 0){
                if(!isset($result)){
                    $result = new stdClass();
                    $result->status = 'failed';
                    $result->id = '0';
                }
                $result->msg[] = JText::_('GRP_NOT_DEF');

            }
            if(isset($result)){
                // break the flow
                return $result;
            }
            // Get a db connection.
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            $query->select($db->quoteName('id'));
            $query->from($db->quoteName('#__nxmarathonmanager_teamcategory'));
            $query->where($db->quoteName('event_main_catid') . ' = ' . $db->quote($parcours))
                ->where($db->quoteName('event_group_catid') . ' = ' . $db->quote($admingrp));
            $query->order('ordering ASC');

            $db->setQuery($query);
            $result = $db->loadObject();

            if($result){
                $result->msg = 'ok';
                $result->status = 'success';
            }else{
                $result = new stdClass();
                $result->msg = JText::sprintf('DB_CATEGORY_NOT_FOUND',$parcours,$admingrp);
                $result->status = 'failed';
                $result->id = '0';
            }
        }catch(Exception $e){
            if(!isset($result)){
                $result = new stdClass();
                $result->status = 'failed';
                $result->id = '0';
            }
            $result->msg[] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Checks if a registration was already done
     *
     * @return mixed  object on success, false on failure.
     *
     */
    public function checkForRegistration($eventId, $userId)
    {
        // Get a db connection.
        $db = JFactory::getDbo();

        // Create a new query object.
        $query = $db->getQuery(true);

        // Get from #__nxmarathonmanager_registration as a
        $query->select(array('a.id','a.teamname','a.created'));
        $query->from($db->quoteName('#__nxmarathonmanager_registration', 'a'));

        $query->where('a.eventid = ' . (int) $eventId);
        // Get where a.created_by is $userId
        $query->where('a.created_by = ' . (int) $userId);

        // Reset the query using our newly populated query object.
        $db->setQuery($query);
        // Load the results as a stdClass object.
        $data = $db->loadObject();

        if (empty($data))
        {
            return false;
        }

        // return data object.
        return $data;
    }

    public static function getRegistration($registrationId, $reg_price = 0){
        // Get a db connection.
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select(array('a.*','b.name'));
        $query->from($db->quoteName('#__nxmarathonmanager_registration','a'));
        $query->join('LEFT', $db->quoteName('#__nxmarathonmanager_teamcategory', 'b') . ' ON ' . $db->quoteName('a.teamcategory') . ' = ' . $db->quoteName('b.id'));
        $query->where($db->quoteName('a.id') . ' = ' . $db->quote($registrationId));


        $db->setQuery($query);
        $data = $db->loadObject();

        $result = new stdClass();
        if($data && count((array)$data)){
            $user = JFactory::getUser();
            $data->usermail = $user->get('email');//user email
            // Add Registration Price for display:
            $data->reg_price = $reg_price;
            $result->msg = 'ok';
            $result->status = 'success';
            $result->html = self::buildHTML($data);
            $result->data = $data;
        }else{
            $result->msg = JText::sprintf('DB_REGISTRATION_NOT_FOUND',$registrationId);
            $result->status = 'failed';
            $result->id = '0';
            $result->html = false;
        }

        return $result;
    }

    public static function buildHtml($data){

        $rowsToBuild = self::buildRowsToBuild();
        $lastinfoLanguages = self::buildLastInfoLanguages();

        $html = '';
        foreach($rowsToBuild as $key => $title){
            if(property_exists($data, $key)){
                switch($key){
                    case 'created':
                    case 'arrival_date':
                    $value = HtmlHelper::date($data->$key, 'd.m.Y');
                        break;
                    case 'maps_price_total':
                    case 'total_price':
                    case 'reg_price':
                        $value = 'CHF ' . $data->$key . '.-';
                        if($key === 'total_price'){
                            $value = '<b style="font-size: 1.1em">' . $value . '</b>';
                        }
                        break;
                    case 'participants':
                        $value = self::buildParticipantsView($data->$key);
                        break;
                    case 'arrival_type':
                        $labels = array('both' => 'Beides, Teilnehmer reisen getrennt an', 'car' => 'PKW', 'public' => 'Öffentliche Transportmittel');
                        $value = $labels[$data->$key];
                        break;
                    case 'reference_num':
                        $value = '<b style="font-size: 1.1em">' . $data->$key . '</b>';
                        break;
                    case 'lastinfolang':
                        $value = '<span>' . $lastinfoLanguages[$data->$key] . '</span>';
                        break;
                    default:
                        $value = $data->$key;
                }


                $html .= "<tr><th class='reg_lbl'>$title</th><td class='reg_val'>$value</td></tr>\n";
            }
        }
        return $html;
    }

    private static function buildParticipantsView($participants, $marginTop = '0px'){
        $countries = self::getNations();
        $genders = array('m' => 'Herr<br>','w' => 'Frau<br>','d' => '');
        $ptReduction = array('0' => 'keine','ga' => 'General Abonnement (GA)','ht' => 'Halbtax');
        $pObj = json_decode($participants);
        $html = '';
        foreach ($pObj as $runnerdata){
            $country = $runnerdata->runner_country;
            $countryName = ($countries[$country]->countrycode && $countries[$country]->countrycode !== 'xx') ? ', '.$countries[$country]->name : '';
            $rows = '';
            $rows .='<div>' . $genders[$runnerdata->runner_gender] .$runnerdata->runner_firstname . ' ' . $runnerdata->runner_lastname . '</div>';
            $rows .= '<div>' . htmlspecialchars($runnerdata->runner_location) . $countryName.'</div>';
            $rows .='<div class="runner_details uk-margin-top">';
                $rows .= '<div >Jahrgang: '. $runnerdata->runner_year . '</div>';
                if(property_exists($runnerdata,'runner_email') && strlen($runnerdata->runner_email)) $rows .= '<div uk-tooltip="'.JText::_('RUNNER_EMAIL_INFO').'"><span class="runner-address">' . $runnerdata->runner_email .'</span><span>*</span></div>';
                $rows .= '<div class="">ÖV Vergünstigung: '. $ptReduction[$runnerdata->runner_pt_reduction] . '</div>';
            $rows .= '</div>'; // Details end
            $html .= "<div class='uk-margin-small' style='margin-top:$marginTop'><div class='uk-card uk-tile-muted uk-card-small uk-card-body'><div>$rows</div></div></div>\n";
        }
        return $html;
    }

    public static function getUsers($onlyActive = true){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('id','name')));
        $query->from($db->quoteName('#__users'));
        if($onlyActive){
            $query->where($db->quoteName('block') . ' = 0');
        }
        $query->order('name ASC');
        $db->setQuery($query);

        return $db->loadObjectList();
    }

    private static function buildRowsToBuild(){
        return array(
            'created'=>'Registrationsdatum',
            'teamname'=>'Teamname',
            'name' => 'Kategorie',
            'contactemail' => 'Kontakt E-Mail',
            'emergency_number' => 'Notfall Telefonnummer',
            'arrival_date' => 'Anmeldung vor Ort am',
            'arrival_type' => 'Ankunft mit',
            'participants' => 'Läufer',
            //'maps_count' => 'Anzahl Karten',
            'lastinfolang' => 'Sprache Dokumente',
            'maps_price_total' => 'Preis für Karten',
            'reg_price' => 'Anmeldegebühr',
            'total_price' => 'Startgebühr total',
            'reference_num' => 'Referenz Nummer',
        );
    }

    private static  function buildLastInfoLanguages(){
        return array(
            'de' => 'Deutsch',
            'fr' => 'Französisch',
            'en' => 'Englisch',
        );
    }

    private static function buildMail($contents){

        $rowsToBuild = self::buildRowsToBuild();
        $lastinfoLanguages = self::buildLastInfoLanguages();

        $body = '';
        $rows = '';
        foreach($rowsToBuild as $key => $title){
            if(property_exists($contents, $key)){
                switch($key){
                    case 'created':
                    case 'arrival_date':
                        $value = HtmlHelper::date($contents->$key, 'd.m.Y');
                        break;
                    case 'maps_price_total':
                    case 'total_price':
                    case 'reg_price':
                        $value = 'CHF ' . $contents->$key . '.-';
                        if($key === 'total_price'){
                            $value = '<b style="font-size: 1.1em">' . $value . '</b>';
                        }
                        break;
                    case 'participants':
                        $value = self::buildParticipantsView($contents->$key,'20px');
                        break;
                    case 'arrival_type':
                        $labels = array('both' => 'Beides, Teilnehmer reisen getrennt an', 'car' => 'PKW', 'public' => 'Öffentliche Transportmittel');
                        $value = $labels[$contents->$key];
                        break;
                    case 'reference_num':
                        $value = '<b style="font-size: 1.1em">' . $contents->$key . '</b>';
                        break;
                    case 'lastinfolang':
                        $value = '<span>' . $lastinfoLanguages[$contents->$key] . '</span>';
                        break;
                    default:
                        $value = $contents->$key;
                }

                $rows .= "<tr><td style='width:50%' width='50%'>$title</td><td style='width:50%' width='50%'>$value</td></tr>";
            }
            $table = '<table cellpadding="3px"><tbody>' . $rows . '</tbody></table>';
            if($contents->toRunners){
                $intro = '';
            }else{
                $intro = '<div id="intro" style="font-size:1.3em;line-height: 1.4em;">Du hast euch erfolgreich für den <strong>'.$contents->eventname.'</strong> angemeldet.</div>';
            }
            $intro .= '<div id="thankyou">Vielen Dank für Eure Registration, die folgenden Daten wurden gespeichert:</div>';
            $payment = '<div style="margin-bottom:10px;margin-top:20px;"><h3>Zahlungsinformationen</h3><p>Diese Anmeldung ist nach Eingang Eurer Startgebühr von <strong>CHF '.$contents->total_price.'.-</strong> gültig.<br>Bitte verwende die untenstehenden Informationen für die Zahlung.</p></div>';
            $payment_data = '<table cellpadding="5px" style="background-color:#f4f4f6;color:#4f5260;width: 90%" width="90%">
                            <tbody>
                            <tr><td width="40%">Empfänger</td><td>OK Mountain Marathon<br>8003 Zurich</td></tr>
                            <tr><td>Postkonto</td><td>85-334272-4</td></tr>
                            <tr><td>Deine Referenz<br><span style="font-size:0.8em;">Bitte unter Mitteilung an Empfänger eintragen.</span></td><td><span>'.$contents->reference_num.'</span></td></tr>
                            <tr><td>IBAN</td><td>CH53 0900 0000 8533 4272 4</td></tr>
                            <tr><td>BIC</td><td>POFICH-BEXXX</td></tr>
                            </tbody>
                        </table>';
            $body = '<body>'.$intro.$table.$payment.$payment_data.'</body>';
            $head = '<head>
                            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                            <title></title>
                            <style type="text/css">
                                html, body { width: 100% !important; }
                                p { font-family: Arial; }
                                a { font-family: Arial; }
                                td { font-family: Arial; }
                                th { font-family: Arial; text-align: left; }
                                div#intro {font-size:1.3em; line-height: 1.4em;}
                                div#thankyou {margin-bottom:20px;}
                            </style>
                        </head>';
            $html = '<html>'.$head.$body.'</html>';
        }
        return $html;
    }

    private static function sendMail($recipients, $subject, $body){
        $mailer = JFactory::getMailer();
        $config = JFactory::getConfig();
        $sender = array(
            $config->get( 'mailfrom' ),
            $config->get( 'fromname' )
        );
        $mailer->setSender($sender);
        $mailer->addRecipient($recipients);
        $mailer->setSubject($subject);
        $mailer->isHtml(true);
        $mailer->Encoding = 'base64';
        $mailer->setBody($body);

        return $mailer->Send();
    }

    /**
     * Checks for a recipient in the database will call create if needed
     *
     * @param $mailaddress      object      information about the recipient, firstname, lastname, emailaddress
     * @param $user             object      joomla user that created the registration
     * @return                  string      id of the item found or created
     * @since 1.0
     */
    private static function checkForRecipient($recipient, $user){
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from($db->quoteName('#__acym_user'));
            $query->where($db->quoteName('email') . ' = '.$db->quote($recipient->email));
            $db->setQuery($query);

            $AMuser = $db->loadObject();

            if($AMuser){
                if(!$AMuser->active || !$AMuser->confirmed){
                    $AMuser->activeState = self::setRecipientActive($AMuser->id, $user);
                }else{
                    $AMuser->activeState = 'not changed';
                }
            }

            return $AMuser;

        }catch (Exception $e){
            return false;
        }

    }

    /**
     * Set a recipient to back to active if disabled
     *
     * @param $recipient    object      information about the recipient, firstname, lastname, emailaddress
     * @param $user         object      joomla user that created the registration
     * @return              string      id of the created item
     * @since 1.0
     */
    private static function setRecipientActive($AMUserId, $user){
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            // Fields to update.
            $fields = array(
                $db->quoteName('source') . ' = ' . $db->quote('Edited to active subscriber by Registration for event by '.$user->id),
                $db->quoteName('active') . ' = 1',
                $db->quoteName('confirmed') . ' = 1'
            );

            // Conditions for which records should be updated.
            $conditions = array(
                $db->quoteName('id') . ' = '.$AMUserId,
            );

            $query->update($db->quoteName('#__acym_user'))->set($fields)->where($conditions);

            $db->setQuery($query);

            $result = $db->execute();

            if($result){
                return 'changed';
            }else{
                return 'unknown error changing active state for Mailing User ID '.$AMUserId;
            }

        }catch (Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * Adds a new recipient to AcyMailing
     *
     * @param $recipient    object              information about the recipient, firstname, lastname, emailaddress
     * @param $user         object              joomla user that created the registration
     * @return              string | false      the created item id or false on failure
     * @since 1.0
     */
    private static function createRecipient($recipient, $user){
        try{
            $date = new Date('now');

            // Get a db connection.
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            $columns = array('name', 'email', 'creation_date', 'active', 'confirmed', 'cms_id', 'source');
            $values = array(
                $db->quote($recipient->name),
                $db->quote($recipient->email),
                $db->quote($date->toSQL()),
                1,
                1,
                $user->id,
                $db->quote('Added while Event Registration by User ID '.$user->id),
            );

            $query
                ->insert($db->quoteName('#__acym_user'))
                ->columns($db->quoteName($columns))
                ->values(implode(',', $values));

            $db->setQuery($query);
            $db->execute();
            return $db->insertid();

        }catch (Exception $e){
            return false;
        }
    }


    /**
     * Checks if the recipient is already registered for a given list, if not it links the user to the list
     * @param $listId           string      id of the AcyMailing list where this recipient should be enlisted to
     * @param $recipientId      string      id of the user item that should be linked
     * @param $activated        bool        defines if the abonnement for the list should be active
     * @since 1.0
     */
    private static function registerForList($listId, $recipientId){
        if($listId === '0' || !strlen($listId)){
            return;
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__acym_user_has_list'));
        $query->where($db->quoteName('user_id') . ' = '.$db->quote($recipientId));
        $query->where($db->quoteName('list_id') . ' = '.$db->quote($listId));
        $db->setQuery($query);

        $result = $db->loadObject();

        if(count((array)$result)){
            // is already registered
            if(!$result->status){
                // is registered but status is disabled
                $result->status = 1;
                $db = JFactory::getDbo();

                $query = $db->getQuery(true);

                // Fields to update.
                $fields = array(
                    $db->quoteName('status') . ' = 1'
                );
                $conditions = array(
                    $db->quoteName('user_id') . ' = ' . $db->quote($recipientId),
                    $db->quoteName('list_id') . ' = ' . $db->quote($listId)
                );
                $query->update($db->quoteName('#__acym_user_has_list'))->set($fields)->where($conditions);
                $db->setQuery($query);
                $db->execute();
            }
        }else{
            // is not yet registered for this list add it now
            $date = new Date('now');
            // Get a db connection.
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $columns = array('user_id', 'list_id', 'status', 'subscription_date');
            $values = array($db->quote($recipientId), $db->quote($listId), 1, $db->quote($date->toSql()));

            // Prepare the insert query.
            $query
                ->insert($db->quoteName('#__acym_user_has_list'))
                ->columns($db->quoteName($columns))
                ->values(implode(',', $values));

            // Set the query using our newly populated query object and execute it.
            $db->setQuery($query);
            $db->execute();

        }
    }

    /**
     * Main Entrypoint to add a AcyMailing abonnement link to a given Marathon Runner object (runner)
     * @param $recipient    object      {name;email; ...}
     * @param $user         object      Joomla! User Object which created the registration
     *
     * @return              bool         registered or not
     * @since 1.0
     */
    private static function registerForMailing($recipient, $user, $listId){

        $AMUser = self::checkForRecipient($recipient, $user);
        if(!$AMUser){
            // User does not yet exist in AcyMailing:
            $AMUserId = self::createRecipient($recipient, $user);
        }else{
            $AMUserId = $AMUser->id;
        }

        if($AMUserId){
            // Link AcyMailing User to List & enable or simply enable if already entered
            self::registerForList($listId, $AMUserId);
            return $AMUser;
        }else{
            return false;
        }
    }

    /* AJAX */
    public static function saveRegistrationAjax(){
        // sets the game finished toggle
        $jinput = JFactory::getApplication()->input;
        $datainput = $jinput->get('data', null, null);
        $dataset = json_decode($datainput);

        // Set the additional params
        $date = new Date();
        $modified = $date->toSQL(); // 2013-12-01 15:20:00
        $timestamp = $date->toUnix();


        $user = Factory::getUser();
        $authorised = $user->authorise('registration.create', 'com_nxmarathonmanager');
        if(!$authorised) {
            $details = new stdClass();
            $details->timestamp = $timestamp;
            $details->userId = $user->id;
            $details->username = $user->username;
            $details->block = $user->block;
            $details->activation = $user->activation;
            $details->groups = $user->get('groups');
            return self::buildErr('danger', 'Anmeldungen einreichen', $details);
        }

        $formData = new stdClass();
        $formData->errors = array();

        foreach($dataset as $data){
            $key = $data->name;
            $formData->$key = $data->value;
        }
        /*
         * {
         *      "participants0":
         *          {
         *              "runner_gender":"m",
         *              "runner_firstname":"vorname",
         *              "runner_lastname":"nachname",
         *              "runner_location":"ort",
         *              "runner_country":"0",
         *              "runner_year":"jg",
         *              "runner_email":"0",
         *              "runner_newsletter":"0",
         *              "pt_reduction":"ht"
         *          },
         *      "participants1": {...}
         *  }
         */

        // Group Runners Data
        $innerRunnerData = array('gender','firstname','lastname','country','email','newsletter','location','pt_reduction','year');
        $participants = new stdClass();
        for($i = 1; $i < 5; $i++){
            $participant = new stdClass();
            foreach ($innerRunnerData as $innerKey){
                $key = 'runner_'.$i.'_'.$innerKey;
                if(property_exists($formData, $key)){
                    $pkey = 'runner_'.$innerKey;
                    $participant->$pkey = $formData->$key;
                }elseif($innerKey == 'newsletter' && property_exists($formData, 'runner_'.$i.'_email') && !property_exists($formData, $key)){
                    // Checkbox not ticked to receive newsletter we save it here as info (we have to check with the field email if the runner even exist in formdata
                    $pkey = 'runner_'.$innerKey;
                    $participant->$pkey = '0';
                }
            }
            // add participant to participants obj if it has data
            if(count((array)$participant)){
                $pindex = $i-1;
                $memberKey = 'participants'.$pindex;
                $participants->$memberKey = $participant;
            }
        }

        $formData->participants = $participants;
        $formData->participantsJson = json_encode($participants);

        $category = self::getCategoryId($formData->parcours,$formData->category);
        $event = self::getEvent($formData->eventid);

        // the registration fee calculation
        $registration_fee = ($date < $event->event_earlybird_end && $event->earlybird_fee) ? $event->earlybird_fee : $event->participation_fee;

        if($category->status == 'success'){
            $formData->categoryId = $category->id;
        }else{
            $formData->errors[] = $category->msg;
        }
        $year = HtmlHelper::date($date, 'Y');

        try{
            // Get a db connection.
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            $createOnUser = ($formData->user_id !== '0' && strlen($formData->user_id)) ? $formData->user_id : $user->get('id');

            $created = $date->toSQL(); // 2013-12-01 15:20:00
            $referenceNum = 'SIMM-'.$formData->eventyear.'-'.$formData->eventid.'-'.$createOnUser;

            // Preparing Data
            $values = array(
                $db->quote('' . JFilterOutput::stringURLSafe($formData->teamname)),
                $db->quote('' . $formData->teamname),
                $db->quote('' . $formData->arrival_date),
                $db->quote('' . $formData->arrival_type),
                $db->quote('' . $user->email),
                $db->quote('' . $formData->emergency_number),
                $db->quote('' . $formData->eventid),
                $db->quote('' . $formData->maps),
                $db->quote('' . $formData->maps_price_total),
                $db->quote('' . $formData->lastinfolang),
                $db->quote(0),
                $db->quote('' . $formData->participantsJson),
                $db->quote('' . $formData->privacy),
                $db->quote($category->id),

                $db->quote(intval($registration_fee) + intval($formData->maps_price_total)),
                $db->quote($user->get('id')),       // modified : can be the admin or the user
                $db->quote($createOnUser),                   // created: has to be the user for proper linking
                $db->quote($created),
                $db->quote($referenceNum)
            );

            $columns = array(
                'alias',
                'teamname',
                'arrival_date',
                'arrival_type',
                'contactemail',
                'emergency_number',
                'eventid',
                'maps_count',
                'maps_price_total',
                'lastinfolang',
                'paid',
                'participants',
                'privacy',
                'teamcategory',
                'total_price',
                'created_by',
                'modified_by',
                'created',
                'reference_num'
            );

            $query->insert($db->quoteName('#__nxmarathonmanager_registration'));

            $query->columns($db->quoteName($columns))
                ->values(implode(',', $values));

            // Set the query using our newly populated query object and execute it.
            $db->setQuery($query);
            $result = $db->execute();
            $row = $db->insertid();

            // Do the newsletter registration if checked
            $newsLetterStatus = array();
            $lastInfoStatus = array();
            $componentParams = self::getComponentParams('com_nxmarathonmanager');
            if(property_exists($componentParams,'newsletterlist') && $componentParams->newsletterlist){
                $newsletterId = $componentParams->newsletterlist;
            }
            if($row && isset ($participants)){
                foreach ($participants as $p){
                    $p->name = $p->runner_firstname . ' ' . $p->runner_lastname;
                    $p->email = $p->runner_email;
                    // Newsletter abo if enabled to get abo & abo defined in params
                    if(intval($p->runner_newsletter) && isset($newsletterId) && intval($newsletterId)){
                        $newsLetterStatus[] = self::registerForMailing($p, $user, $newsletterId);
                    }
                    // LastInfo abo
                    if(property_exists($formData, "lastinfomailing") && $formData->lastinfomailing){
                        $lastInfoStatus[] = self::registerForMailing($p, $user, $formData->lastinfomailing);
                    }
                }
            }
            // Add Creator of registration to lastinfolist too:
            $lastInfoStatus[] = self::registerForMailing($user, $user, $formData->lastinfomailing);

            if($row){
                $registration = self::getRegistration($row, $registration_fee);
                $registration->success = true;
                $registration->category = $category;
                $registration->newsletterList = $newsLetterStatus;
                $registration->lastInfoList = $lastInfoStatus;
                $registration->data->eventname = $event->name;
                return $registration;
            }else{
                return false;
            }

        }catch (Exception $e){
            $Error = new stdClass();
            $Error->message = $e->getMessage();
            $Error->dump = $query->dump();

            return self::buildErr('danger', $e->getMessage(), false);
        }
    }

    public static function sendMailAjax(){

        $jinput = JFactory::getApplication()->input;
        $datainput = $jinput->get('data', null, null);
        $dataset = json_decode($datainput);

        $body = self::buildMail($dataset->contents);
        $subject = 'Deine Registration für den ' . $dataset->contents->eventname;

        return self::sendMail($dataset->recipient, $subject, $body);

    }


}
