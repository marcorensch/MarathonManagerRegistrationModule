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

<div class="repeatable_element uk-animation-slide-right-medium">
    <div class="uk-card uk-card-body uk-tile-muted uk-card-small uk-box-shadow-small uk-position-relative" style="z-index: 0">
        <div style="z-index: 3">
            <?php if($remover):?>
            <div class="runner_remover uk-position-top-right">
                <div class="uk-padding-small">
                    <a class="remove_runner" uk-icon="close" uk-tooltip="Läufer entfernen"></a>
                </div>
            </div>
            <?php endif;?>
            <div class="uk-margin">
                <h4>Läufer <span class="runner_num">n</span></h4>
            </div>
            <div class="uk-grid-small uk-child-width-1-1@s uk-child-width-1-2@m uk-grid">
                <div>
                    <div class="uk-form-controls">
                        <select class="uk-select runner_gender" name="runner__gender" required="true">
                            <option value="">Anrede</option>
                            <option value="m">Herr</option>
                            <option value="w">Frau</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="uk-margin uk-grid-small uk-child-width-1-1@s uk-child-width-expand@m uk-grid" uk-margin>
                <div class="">
                    <input name="runner__firstname" class="uk-input runner_firstname" type="text" placeholder="Vorname" required="true">
                </div>
                <div class="">
                    <input name="runner__lastname" class="uk-input runner_lastname" type="text" placeholder="Nachname" required="true">
                </div>
            </div>

            <?php if($email):?>
            <div class="uk-margin runner_email_container">
                <div class="uk-grid uk-grid-small uk-child-width-1-1@s uk-child-width-auto@m uk-flex uk-flex-top" uk-margin>
                    <div class="uk-width-expand@m">
                        <input
                                name="runner__email"
                                class="uk-input runner_email"
                                type="email"
                                placeholder="Läufer Kontakt E-Mail (name@tld.xx)"
                                required
                        >
                        <div class="uk-text-meta uk-text-small">
                            <div>Diese E-Mail Adresse wird genutzt, um Dir wichtige Informationen zum Lauf mitzuteilen.</div>
                        </div>
                    </div>
                    <div class="">
                        <div class="uk-margin-small-top">
                            <label>
                                <input name="runner__newsletter" class="uk-checkbox runner_newsletter" type="checkbox" value="1" style="border: 1px solid rgba(0,0,0,.1); border-radius:4px;">
                                Für Newsletter anmelden
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif;?>
            <div>
                <div class="uk-grid-small uk-child-width-1-2@m uk-child-width-1-1@s" uk-grid>
                    <div>
                        <div>
                            <input
                                    name="runner__year"
                                    class="uk-input runner_year"
                                    type="number"
                                    min="1920"
                                    max="<?php echo $maxYear;?>"
                                    title="Das Mindestalter ist <?php echo $minAge;?>, Jahrgang: <?php echo $maxYear;?>"
                                    placeholder="Jahrgang"
                                    required="true"
                            >
                        </div>
                    </div>
                    <div>
                        <div>
                            <input name="runner__location" class="uk-input" type="text" placeholder="Wohnort" required="true">
                        </div>
                        <div class="uk-margin-small-top">
                            <div class="uk-form-controls">
                                <select class="uk-select runner_country" name="runner__country" required="true">
                                    <option selected disabled value="">Nationalität</option>
                                    <?php foreach($nations as $nation){
                                        echo '<option value="'.$nation->id.'">'.$nation->name.'</option>';
                                    }?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label" for="runner__pt_reduction">Vergünstigung Öffentlicher Transport</label>
                <div class="uk-form-controls">
                    <select name="runner__pt_reduction" class="uk-select runner_pt_reduction">
                        <option value="0">keine</option>
                        <option value="ga">GA</option>
                        <option value="ht">Halbtax</option>
                    </select>
                </div>
                <div class="uk-text-meta uk-text-small">
                    <span>Solltest Du im Besitz eines Schweizer Generalabonnements (GA) oder HALBTAX sein, welches zum Event-Zeitpunkt gültig ist, so teile uns dies bitte mit, damit wir diese bei der Bestellung allfälliger Bergbahn Tickets oder anderen beachten können.<b>Wenn Du die Option für GA oder HALBTAX aktivierst, so führe diese bitte unbedingt am Event mit dir.</b></span>
                </div>
            </div>
        </div>
        <div class="uk-position-top-right" style="z-index: -1">
            <div class="uk-padding">
                <span style="font-size: 4em; opacity: .05"> <i class="fas fa-user"></i></span>
            </div>
        </div>
    </div>
</div>
