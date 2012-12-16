<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 03-Jun-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

?>
<a href="javascript:;" class="btn btn-success btn-large"
   onclick="EcrZiper.createPackage();"
   style="margin: 1em; padding: 1em;">
    <i class="img32 icon32-ecr_package"></i>
    <br />
    <br />
    <?php echo sprintf(jgettext('Create %s'), $this->project->name); ?>
</a>

<div class="progress progress-success progress-striped active">
    <div id="ecrProgressBar" class="bar" style="width: 0%;"></div>
</div>

<div id="ajaxMessage"></div>

<div id="zipResultLinks"></div>
