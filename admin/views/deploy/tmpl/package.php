<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 25-Apr-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

ecrScript('php2js');

?>
<h3><?php echo jgettext('Source files'); ?></h3>

<div class="ecr_floatbox">
    <div class="infoHeader img icon24-ftp">
        <?php echo jgettext('Files to deploy') ?>
    </div>
    <?php echo $this->loadTemplate('archive'); ?>
</div>

<div class="clr"></div>

<h3><?php echo jgettext('Destination'); ?></h3>

<div class="ecr_floatbox">
    <?php echo $this->loadTemplate('ftp'); ?>
</div>
<div class="ecr_floatbox">
    <?php echo $this->loadTemplate('github'); ?>
</div>

<div class="clr"></div>

<h3><?php echo jgettext('Joomla! update'); ?></h3>

<div class="ecr_floatbox">
    <?php echo $this->loadTemplate('updatefiles'); ?>
</div>

<div class="clr"></div>

<?php echo EcrHtmlDebug::logConsole(); ?>
<div style="height: 75px;"></div>
