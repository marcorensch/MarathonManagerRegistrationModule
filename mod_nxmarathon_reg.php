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

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Date\Date;
require_once 'helper.php';

$user = JFactory::getUser();
$isroot = $user->authorise('core.admin');
$nxdebug = $params->get('debug',0) && $isroot;
$document = JFactory::getDocument();

if($isroot){
    $userList = ModNxMarathonRegHelper::getUsers(true);
}

// UIKIT
if ($params->get('load_uikit', 1)) {
    $document->addScript('modules/mod_nxmarathon_reg/assets/uikit/3.5.9/js/uikit.min.js');
    $document->addScript('modules/mod_nxmarathon_reg/assets/uikit/3.5.9/js/uikit-icons.min.js');
    $document->addStyleSheet('modules/mod_nxmarathon_reg/assets/uikit/3.5.9/css/uikit.min.css');

    if ($nxdebug) {
        $document->addScriptDeclaration('console.log(`%c custom uikit loaded by Module-ID ' . $module->id . ' `,`color:blue;background:darkgrey;`)');
    }
}
// FontAwesome
if ($params->get('load_fa', 1)) {
    $document->addScript('media/com_nxmarathonmanager/font-awesome-514/js/all.min.js');
    $document->addStyleSheet('media/com_nxmarathonmanager/font-awesome-514/css/all.min.css');

    if ($nxdebug) {
        $document->addScriptDeclaration('console.log(`%c FontAwesome loaded by Module-ID ' . $module->id . ' `,`color:blue;background:darkgrey;`)');
    }
}

// incude jQuery
if($params->get('load_jquery',1)){
    JHtml::_('jquery.framework');
}

if($params->get('mode','dynamic') === 'dynamic'){
    if(array_key_exists('eventToRegister',$GLOBALS)){
        $eventdata = $GLOBALS['eventToRegister'];
    }else{
        return;
    }
}else{
    // static get eventData by ourself
    if($params->get('static_key')){
        $eventdata = ModNxMarathonRegHelper::getEvent($params->get('static_key'));
    }
}

if(!count((array)$eventdata)){
    echo JText::sprintf('NX_ERR_NO_EV_ERR',$params->get('static_key'));
    return;
};
// check if Registration has been made already
$registered = ModNxMarathonRegHelper::checkForRegistration($eventdata->id, $user->id);
if(!$params->get('multiple_registrations',0) && $registered && !$isroot){
    // is already registered and multiple registrations are forbidden
    $dateHtml = HtmlHelper::date($registered->created, 'D, d. M Y');
    echo JText::sprintf('NX_ERR_ALREADY_REGISTERED', $dateHtml, $registered->teamname, $eventdata->name);
    return;
}


// Build arrivalOptions Select
$eventdata->arrivaloptions = json_decode($eventdata->arrivaloptions);
$arrivalOptions = array();
foreach ($eventdata->arrivaloptions as $d){
    $date = new Date($d->arrival_option);
    $d->label = HtmlHelper::date($date, 'l, d.m.Y');
    $d->value = $date->toUnix();
    $arrivalOptions[] = $d;
}

// Build Year for reference
$eventDateObj = new Date($eventdata->eventdate);
$eventYear = HTMLHelper::date($eventDateObj, 'Y');

if($nxdebug){
    echo '<h3>Debug EventData:</h3>';
    echo '<pre>' . var_export($eventdata,1) . '</pre>';
}

// Get the component parameters
$componentParams = ModNxMarathonRegHelper::getComponentParams('com_nxmarathonmanager');
if(!$componentParams){
    return;
}
$nations = ModNxMarathonRegHelper::getNations();

if(strpos($eventdata->headerimg,'://')){
    // nothing to do is full url
}else{
    $eventdata->headerimg = JUri::base().$eventdata->headerimg;
}


// echo '<pre>' . var_export($componentParams->registration_min_age,1) . '</pre>';
$minAge = $componentParams->registration_min_age;
$yearNow = intval(HtmlHelper::date(new Date('now'), Text::_('Y')));
$maxYear = $yearNow - $minAge;

// Map Options /0 = no maps; 1=one included; 2=first included n by price; 3=all by price
switch($eventdata->map_option){
    case '0':
    case '1':
        $showMapSelection = false;
        break;
    case '2':
        $firstMapToPay = 1;
        $initialMapsToPay = 0;
        $showMapSelection = true;
        $mapPrice = intval($eventdata->map_price);
        $initialPriceForMaps = $mapPrice * $initialMapsToPay;
        break;
    case '3':
        $firstMapToPay = 0;
        $initialMapsToPay = 1;
        $showMapSelection = true;
        $mapPrice = intval($eventdata->map_price);
        $initialPriceForMaps = $mapPrice * $initialMapsToPay;
}


/* JAVASCRIPTS */
$document->addStyleSheet('modules/mod_nxmarathon_reg/tmpl/assets/css/main.css');
$document->addScript('modules/mod_nxmarathon_reg/tmpl/assets/js/main.js');
$document->addScript('modules/mod_nxmarathon_reg/tmpl/assets/js/ajax.js');
if($showMapSelection) {
    $calcMapPriceScripts = "
                jQuery('document').ready(function($){
                    $(document).on('change','#maps',function(){
                        let mapsPrice = ($('#maps').val()-$firstMapToPay) * $mapPrice;
                        $('#maps_price_txt').text(mapsPrice);
                        $('#maps_price_total').val(mapsPrice);
                    });
                    $(document).on('click','#resetForm', function(){
                        $('#maps_price_txt').text($initialPriceForMaps);
                    });
                });
";
    $document->addScriptDeclaration($calcMapPriceScripts);
}
$nxdebugPrint = ($nxdebug) ? 1 : 0;
$getFormData = "
        let marathonRegDebug = $nxdebugPrint;
        jQuery(document).ready(function($){

            $('form.nx-registration-form *:invalid').on('change',function(){
                $(this).css({'border-color':'inherit','border-width':'inherit'});
            });
            
            $('#form_$module->id').on('click','#submitForm',function(){

                if($('form#form_$module->id')[0].checkValidity()){
                    let formdata = $('form#form_$module->id').serializeArray();
                    saveFormData(jQuery, formdata, $module->id);
                }else{
                    UIkit.notification({
                        message: '<div class=\'uk-text-center\'><i class=\'fas fa-exclamation-triangle\'></i><br>Einige Felder sind nicht korrekt ausgefüllt</div>',
                        status: 'danger',
                        pos: 'bottom',
                        timeout: 5000
                    });
                    $('form.nx-registration-form *:invalid').css({'border-color':'#ff2727','border-width':'2px'})
                }
                
            });
        });
";
$document->addScriptDeclaration($getFormData);


// StarterOptions ( Params for the first runner elements that are loaded on pageload)
$remover = false;
$email = true;

// The below line is no longer used in Joomla 4
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require ModuleHelper::getLayoutPath('mod_nxmarathon_reg', $params->get('layout', 'default'));
