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


?>
<div class="nx-registration_<?php echo $module->id;?>" uk-scrollspy="target: .nx-animate; cls:uk-animation-slide-bottom-medium;delay:80;repeat:false;">
    <div class="registration-container">

        <?php if(strlen($eventdata->headerimg)):?>
            <div class="uk-height-medium uk-background-cover uk-background-top-center uk-flex uk-flex-middle uk-flex-center nx-animate" data-src="<?php echo $eventdata->headerimg;?>" uk-img>
            </div>
        <?php else: ?>
        <?php endif;?>
        <h1 class="uk-margin nx-animate">Anmeldeformular <?php echo $eventdata->name;?></h1>
        <div class="nx-marathon-manager-registration-form-container">
            <h2 class="nx-animate">Team Registration</h2>
            <form id="form_<?php echo $module->id;?>" class="nx-registration-form" action="" formaction="">
            <fieldset class="uk-fieldset nx-animate">
                <h2 class="uk-h3 uk-flex uk-flex-middle"><span style="font-size: .7em"> <i class="fas fa-users"></i></span><span class="uk-margin-small-left">Team</span></h2>
                <?php if($isroot && $params->get('adminoptions',1)):?>
                <div class="uk-margin-top uk-card uk-card-secondary uk-card-body">
                    <h3>Administratoroptionen</h3>
                    <p>Die Felder in diesem Bereich können durch den Administrator manuell gesetzt werden.</p>
                    <label for="user_id">Benutzerverknüpfung</label>
                    <select class="uk-select" id="user_id" name="user_id" required="true">
                        <option selected disabled value="">Bitte wähle den Useraccount</option>
                        <option value="0">Auf Meinen Account registrieren</option>
                        <?php foreach ($userList as $u)
                            {
                                echo '<option value="'.$u->id.'">'.$u->name.' ('.$u->id.')</option>';
                            }
                        ?>
                    </select>
                    <div class="uk-margin">
                        <label for="eventid">Event ID <span class="uk-text-small">(wird automatisch ausgefüllt)</span></label>
                        <div class="uk-width-medium">
                            <input name="eventid" class="uk-input" type="text" placeholder="Event ID" value="<?php echo $eventdata->id;?>">
                        </div>
                    </div>
                    <div class="uk-margin">
                        <div class="uk-button uk-button-primary demofill">Formular mit Demodaten füllen</div>
                    </div>
                </div>
                <?php else:?>
                    <div class="uk-hidden">
                        <input readonly name="eventid" class="uk-input" type="hidden" placeholder="Event ID" value="<?php echo $eventdata->id;?>">
                        <input readonly name="user_id" class="uk-input" type="hidden" placeholder="User ID" value="<?php echo $user->get('id');?>">
                    </div>
                <?php endif;?>

                <div class="uk-margin">
                    <input name="teamname" class="uk-input" type="text" placeholder="Team Name" style="font-size: 1.3em;" required>
                </div>

                <div class="uk-margin <?php if(!$nxdebug)echo 'uk-hidden';?>">
                    <label for="eventyear">Event Year</label>
                    <input name="eventyear" class="uk-input" type="text" placeholder="" value="<?php echo $eventYear;?>" style="" required>
                </div>

                <div class="uk-margin <?php if(!$nxdebug)echo 'uk-hidden';?>">
                    <label for="lastinfomailing">Last Info Mail List</label>
                    <input name="lastinfomailing" class="uk-input" type="text" value="<?php echo $eventdata->lastinfoslist;?>">
                </div>

                <div class="uk-margin">
                    <input uk-tooltip="title:Bitte trage die Telefonnummer inkl. Landesvorwahl ein:<br>+4179 000 00 00" name="emergency_number" class="uk-input" type="tel" placeholder="Notfall-Telefonnummer" pattern="((\+|00)?[1-9]{2}|0)[1-9]( ?[0-9]){6,}" required>
                    <span class="uk-text-meta uk-text-small">Eine mobile Rufnummer, die während des Laufs mitgeführt wird und als Notfallkontakt dient.</span>
                </div>
            </fieldset>
            <fieldset class="uk-fieldset nx-animate">
                <div class="uk-margin">
                    <h3 class="uk-h4 uk-flex uk-flex-middle">
                        <span style="font-size: .7em"><i class="fas fa-route"></i></span>
                        <span class="uk-margin-small-left">Parcours &amp; Kategorie</span>
                    </h3>
                </div>

                <div class="uk-margin uk-grid-small uk-child-width-1-1@s uk-child-width-expand@m uk-grid" uk-margin>
                    <div class="">
                        <select class="uk-select" id="parcours" name="parcours" required="true">
                            <option selected disabled value="">Bitte wähle den Parcours</option>
                            <option value="1">1 Orienteering-Marathon Strong</option>
                            <option value="3">3 Orienteering-Marathon Light</option>
                            <option value="5">5 Score-Marathon</option>
                            <option value="7">7 Trail-Marathon Strong</option>
                            <option value="9">9 Trail-Marathon Light</option>
                        </select>
                    </div>

                    <div class="">
                        <select class="uk-select" id="category" name="category" required="true">
                            <option selected disabled value="">Bitte wähle die Kategorie</option>
                            <option value="1">Men (M)</option>
                            <option value="2">Women (W)</option>
                            <option value="3">Couples (C)</option>
                            <option value="4">Family (F)</option>
                        </select>
                    </div>
                </div>
            </fieldset>
            <fieldset class="uk-fieldset nx-animate uk-hidden">
                <div class="uk-margin-top">
                    <div class="uk-margin">
                        <h3 class="uk-h4 uk-flex uk-flex-middle"><span style="font-size: .7em"><i class="fas fa-map"></i></span><span class="uk-margin-small-left">Karten</span></h3>
                    </div>
                    <div class="uk-margin uk-grid-small uk-child-width-expand uk-grid uk-flex uk-flex-middle">
                        <div>
                            <select class="uk-select" id="maps" name="maps" required="true" default="2">
                                <!--
                                <option value="1">Eine Karte (Im Startgeld inbegriffen)</option>
                                -->
                                <option value="2">Zwei Karten</option>
                                <!--
                                <option value="3">Drei Karten</option>
                                <option value="4">Vier Karten</option>
                                <option value="5">Fünf Karten</option>
                                -->
                            </select>
                        </div>
                        <div class="">
                            <p class="uk-margin-small-left">Aktueller Preis für <b>Karten</b> gemäss Deiner Auswahl: <b>CHF <span id="maps_price_txt"><?php echo $initialPriceForMaps;?></span>.-</b></p>
                        </div>
                    </div>
                </div>
                <input class="uk-hidden" type="text" id="maps_price_total" name="maps_price_total" value="<?php echo $initialPriceForMaps;?>" />
            </fieldset>
            <fieldset class="uk-fieldset uk-margin-large-top nx-animate">
                <div>
                    <h2 class="uk-h3 uk-flex uk-flex-middle">
                        <span style="font-size: .7em"> <i class="fas fa-user"></i></span>
                        <span class="uk-margin-small-left">Läufer</span>
                        <div class="uk-inline-block uk-margin-left uk-hidden">
                            <div class="uk-button uk-button-link addRunner"><span uk-icon="icon:plus; ratio:.6"></span> Läufer hinzufügen</div>
                        </div>
                    </h2>
                </div>
                <div id="runners_container" class="repeatable_container uk-child-width-1-1@s uk-child-width-expand@m" uk-grid>
                    <!-- Hier werden die Teilnehmer geladen -->
                    <?php
                    for($i = 0; $i < $params->get('startcardscount',2); $i++){
                        include 'widgets/runner_panel.php';
                    }
                    ?>
                </div>
                <div class="addParticipentBtnContainer uk-width-1-1 uk-margin">
                    <div class="uk-button uk-button-secondary addRunner"><span uk-icon="icon:plus; ratio:.6"></span> Läufer hinzufügen</div>
                </div>
            </fieldset>
            <fieldset class="uk-fieldset uk-margin-large nx-animate">
                <?php include "widgets/language_panel.php"; ?>
            </fieldset>
            <fieldset class="uk-fieldset uk-margin-large nx-animate">
                <?php include "widgets/arrival_panel.php"; ?>
            </fieldset>
            <fieldset class="uk-fieldset uk-margin-large nx-animate">
                <?php include "widgets/privacy_panel.php"; ?>
            </fieldset>
            <fieldset class="uk-fieldset nx-animate">
                <div class="uk-child-width-1-1 uk-child-width-1-3@m uk-grid-small" uk-grid>
                    <div>
                        <input class="uk-button uk-button-danger uk-width-1-1" id="resetForm" name="resetForm" type="reset" value="Formular leeren" />
                    </div>
                    <div class="uk-visible@m"></div>
                    <div>
                        <input class="uk-button uk-button-secondary uk-width-1-1" id="submitForm" name="submitForm" type="button" value="Jetzt Teilnehmen!" />
                    </div>
                </div>
            </fieldset>
        </form>
        </div>
    </div>

    <div id="waiter_<?php echo $module->id;?>" class="uk-height-viewport uk-position-top uk-position-fixed uk-flex uk-flex-middle uk-flex-center" style="background-color:#fff; display: none; z-index: 99999999;">
        <div class="uk-text-center spinner-container">
            <div class="spinner" uk-spinner="ratio: 3"></div>
            <div class="uk-margin spinner_text">
                <span class="uk-text-large">Verarbeitet Daten...</span>
            </div>
        </div>
    </div>

    <?php include 'widgets/registration_done.php';?>

</div>

<div class="uk-hidden clonesrc">
    <?php
        // AdderOptions Overrides Starter ( Params for the n added runner elements that are loaded on click)
        $remover = true;
        $email = false;
        include 'widgets/runner_panel.php';
    ?>
</div>


