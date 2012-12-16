<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Frontent
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 24-Sep-2008
 */

?>
<table style="width: 100%">
    <tr>
        <td>
            <?php easyHTML::projectSelector(); ?>
        </td>
        <td align="center">
            <strong style="color: red;">
                <?php echo jgettext('Please select a project'); ?>
            </strong>
        </td>
    </tr>
</table>
