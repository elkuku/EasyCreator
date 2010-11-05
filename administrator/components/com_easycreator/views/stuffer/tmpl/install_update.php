<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 06-Apr-.2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');
?>

<?php echo ecrHTML::floatBoxStart(); ?>
<table>
    <tr>
        <th colspan="2" class="infoHeader imgbarleft icon-24-update"><?php echo jgettext('Update') ?></th>
    </tr>
    <tr>
        <th><?php echo jgettext('Method'); ?></th>
        <td>
            <?php
            $checked =($this->project->method == 'upgrade') ? ' checked="checked"' : '';
            ?>
            <input type="checkbox" <?php echo $checked; ?> name="buildvars[method]" id="buildvars_method" value="upgrade" />
            <label for="buildvars_method" class="hasEasyTip" title="method=upgrade::<?php echo jgettext('This will perform an upgrade on installing your extension'); ?>">
                <?php echo jgettext('Upgrade'); ?>
            </label>
        </td>
    </tr>
</table>
<?php echo ecrHTML::floatBoxEnd();
