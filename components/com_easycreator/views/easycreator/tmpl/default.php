<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Frontent
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 24-Sep-2008
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

?>
<table width="100%">
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
