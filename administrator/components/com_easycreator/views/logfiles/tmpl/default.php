<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

if( ! count($this->logFiles)):
    EcrHtml::message(jgettext('No logfiles found'), 'notice');

    return;
endif;

//-- Add css
ecrLoadMedia('php_file_tree');

//-- Add javascript
ecrScript('log');

$fileTree = new EcrFileTree(ECRPATH_LOGS, '', " onclick=\"EcrLog.loadLog('[file]', '[id]');\"", '', array('log'), true);
?>

<h1 style="display: inline;"><span class="img32c icon32-menus"></span><?php echo jgettext('Logfiles'); ?></h1>

<span class="img icon16-server">
    <?php echo ECRPATH_LOGS; ?>
</span>

<div class="ecr_floatbox" style="width: 230px;">
    <div class="btn block" onclick="submitbutton('clearLogfiles');">
        <i class="img icon16-delete"></i>
        <?php echo jgettext('Clear log'); ?>
    </div>
    <?php echo $fileTree->drawFullTree(); ?>
</div>

<div style="margin-left: 240px;">
    <div id="ecr_logView" style="background-color: #fff;"></div>
</div>

<div style="clear: both;"></div>
