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

<div class="arrival_panel">
    <div class="uk-margin">
        <h2 class="uk-h3"><i class="fas fa-bus"></i> Anreise</h2>
    </div>
    <div class="uk-margin">
        <div class="uk-child-width-1-1@s uk-child-width-expand@m uk-grid-small" uk-grid>
            <div>
                <select class="uk-select" id="arrival_type" name="arrival_type" required="true">
                    <option value="">Anreise Art</option>
                    <option value="car">Automobil</option>
                    <option value="public">Öffentlicher Transport (ÖV - Bus / Bahn)</option>
                    <option value="both">beides (Läufer reisen getrennt an)</option>
                </select>
            </div>
            <div>
                <select class="uk-select" id="arrival_date" name="arrival_date" required="true">
                    <option value="">Registration vor Ort am</option>
                    <?php foreach ($arrivalOptions as $option){
                        echo '<option value="'.$option->value.'">'.$option->label.'</option>';
                    };?>
                </select>
            </div>
        </div>
    </div>
</div>
