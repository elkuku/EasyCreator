<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 25-Apr-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

?>
<div class="infoHeader img icon24-github">
    <?php echo jgettext('Update files') ?>
</div>

<div id="updateFiles"></div>

<div class="buttons">
    <a class="btn" href="javascript:;"
       onclick="EcrDeploy.addUpdateFile();"><?php echo jgettext('Add file'); ?></a>
    <a class="btn" href="javascript:;" onclick="EcrDeploy.saveUpdateFiles();"><?php echo jgettext('Save'); ?></a>
</div>
