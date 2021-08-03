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
$privacy = (strlen($params->get('url_privacy',''))) ? '<a target="_blank" href="'.$params->get('url_privacy').'">Datenschutzbestimmungen</a>': 'Datenschutzbestimmungen';
$rules = (strlen($params->get('url_rules',''))) ? '<a target="_blank" href="'.$params->get('url_rules').'">Wettkampfreglement</a>': 'Datenschutz';
?>

<div class="privacy_panel">
    <div class="uk-margin">
        <h2 class="uk-h3"><i class="fas fa-shield-alt"></i> Datenschutzbestimmungen &amp; Rechtliches</h2>
    </div>
    <div class="uk-margin">
        <label class="uk-grid uk-child-width-expand uk-flex uk-flex-middle" uk-grid>
            <div class="uk-width-auto">
                <input class="uk-checkbox" name="privacy" type="checkbox" required="true" value="1">
            </div>
            <div>
                <div>
                    <span>Hiermit bestätige ich, dass ich die <?php echo $privacy;?> und das <?php echo $rules;?> gelesen und akzeptiert habe.</span>
                </div>
            </div>
        </label>
    </div>
    <div class="uk-margin">
        <label class="uk-grid uk-child-width-expand uk-flex uk-flex-middle" uk-grid>
            <div class="uk-width-auto">
                <input class="uk-checkbox" name="disclaimer" type="checkbox" required="true" value="1">
            </div>
            <div>
                <div>
                    <span>Verein Mountain Marathon übernimmt keine Haftung. Die/der Teilnehmer ist selber für den Versicherungsschutz verantwortlich.</span>
                </div>
            </div>
        </label>
    </div>
</div>
