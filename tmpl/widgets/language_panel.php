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

<div class="language_panel">
    <div class="uk-margin">
        <h2 class="uk-h3"><i class="fas fa-globe-europe"></i> Sprache</h2>
        <p>In welcher Sprache sollen wir die Laufunterlagen für Dein Team bereitstellen?</p>
    </div>
    <div class="uk-margin">
        <div class="uk-child-width-1-1@s uk-child-width-1-2@m uk-grid-small" uk-grid>
            <div>
                <select class="uk-select" id="lastinfolang" name="lastinfolang" default="" required="true">
                    <option value="">Bitte wählen</option>
                    <option value="de">Deutsch</option>
                    <option value="fr">Französisch</option>
                    <option value="en">Englisch</option>
                </select>
            </div>
        </div>
    </div>
</div>
