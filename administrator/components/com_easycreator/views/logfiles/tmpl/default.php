<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

if( ! count($this->logFiles)):
    ecrHTML::displayMessage(jgettext('No logfiles found'), 'notice');

    return;
endif;

ecrLoadHelper('php_file_tree');

//--Add css
ecrStylesheet('php_file_tree');

//--Add javascript
ecrScript('php_file_tree');
ecrScript('log');

$fileTree = new phpFileTree(ECRPATH_LOGS, '', " onclick=\"loadLog('[file]', '[id]');\"", '', array('log'), true);
?>

<h1 style="display: inline;"><span class="img32c icon-32-menus"></span><?php echo jgettext('Logfiles'); ?></h1>

<span class="img icon-16-server">
    <?php echo ECRPATH_LOGS; ?>
</span>

<div class="ecr_floatbox" style="width: 230px;">
    <div class="ecr_button img icon-16-delete" onclick="submitbutton('clear_log');">
    	<?php echo jgettext('Clear log'); ?>
    </div>
    <?php echo $fileTree->drawFullTree(); ?>
</div>

<div style="margin-left: 240px;">
    <div id="ecr_logView" style="background-color: #fff;"></div>
</div>

<div style="clear: both;"></div>
