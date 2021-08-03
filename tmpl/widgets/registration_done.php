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

<div class="nx-marathon-registration-done uk-hidden">
    <div class="uk-alert-success" uk-alert>
        <a class="uk-alert-close" uk-close></a>
        <p><?php echo JText::sprintf('REGISTRATION_SAVED', $user->email);?></p>
    </div>

    <p class="uk-text-large">Vielen Dank für Eure Registration, die folgenden Daten wurden gespeichert:</p>
    <div class="registrationInfo">
        <div class="data-saved uk-flex uk-flex-center">
            <div class="data-saved-container uk-width-1-1@s uk-width-5-6@m uk-width-3-4@l">
                <table class="info uk-table uk-table-divider">
                </table>
            </div>
        </div>

        <div class="uk-margin uk-flex uk-flex-center printed-page">
            <div class="uk-width-1-1@s uk-width-5-6@m uk-width-3-4@l">
                <div class="uk-card uk-tile-muted uk-card-small">
                    <div class="uk-card-header">
                        <h2 class="uk-h3">Einzahlungsinformationen</h2>
                    </div>
                    <div class="uk-card-body">
                        <table class="uk-table uk-table-divider">
                            <tbody>
                            <tr><td class="uk-width-1-2 uk-width-1-4@m">Empfänger</td><td>OK Mountain Marathon<br>8003 Zurich</td></tr>
                            <tr><td>Postkonto</td><td>85-334272-4</td></tr>
                            <tr><td>Deine Referenz</td><td><span class="referencenum"></span></td></tr>
                            <tr><td>IBAN</td><td>CH53 0900 0000 8533 4272 4</td></tr>
                            <tr><td>BIC</td><td>POFICH-BEXXX</td></tr>
                            </tbody>
                        </table>
                        <table class="uk-table uk-table-divider">
                            <tbody>
                            <tr><td class="uk-width-1-2 uk-width-1-4@m">Betrag</td><td>CHF <span class="price"></span>.-</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="actions uk-child-width-1-1 uk-child-width-expand@m uk-margin-bottom" uk-grid>
        <!--
        <div class="uk-hidden">
            <a data-module-id="<?php echo $module->id;?>" href="<?php echo Juri::base();?>" target="_self" title="Zurück zur Startseite" class="uk-button uk-button-secondary"><i class="fas fa-home"></i> Zurück zur Startseite</a>
        </div>
        -->
        <div>
            <button data-module-id="<?php echo $module->id;?>" title="Anmeldung per Mail senden" class="uk-button uk-button-secondary uk-width-1-1 sendmail"><i class="fas fa-envelope"></i> Bestätigung per E-Mail an Läufer 1&amp;2 senden</button>
        </div>
        <div>
            <button data-module-id="<?php echo $module->id;?>" title="Anmeldung drucken" class="uk-button uk-button-secondary uk-width-1-1" onclick="window.print()"><i class="fas fa-print"></i> Anmeldung Drucken</button>
        </div>
    </div>


    <?php if($isroot && $params->get('adminoptions',1)):?>
    <button data-module-id="<?php echo $module->id;?>" class="uk-button uk-button-secondary reg-back">Zurück</button>
    <?php endif;?>
</div>
