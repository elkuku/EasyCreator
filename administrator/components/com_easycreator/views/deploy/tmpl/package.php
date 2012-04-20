<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elkuku
 * Date: 17.04.12
 * Time: 13:33
 * To change this template use File | Settings | File Templates.
 */

ecrScript('php2js');

?>
<h3><?php echo jgettext('Source files'); ?></h3>

<div class="ecr_floatbox">
    <div class="infoHeader img icon-24-ftp">
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

<h3><?php echo jgettext('Log console'); ?></h3>
<div id="pollStatus"></div>
<pre id="ecrDebugBox"></pre>
