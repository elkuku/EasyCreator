<?php defined('_JEXEC') || die('=;)');
/**
 * @package       EasyCreator
 * @subpackage    Views
 * @author        Nikolai Plath
 * @author        Created on 14-Oct-2009
 * @license       GNU/GPL, see JROOT/LICENSE.php
 */

ecrScript('install', 'tmpl');
echo $this->loadTemplate('tmpl-releases');

?>

<h1><?php echo jgettext('Install templates'); ?></h1>

<div class="ecr_floatbox">
    <h2>Install from WEB</h2>
    <div id="ecrTemplatesResult"></div>

    <script>EcrInstallWeb.fetchReleases('ecrTemplatesResult')</script>

</div>

<div class="ecr_floatbox">
    <h2>Upload Package</h2>
    <form enctype="multipart/form-data" action="index.php" method="post" name="installForm">
        <div>
            <label class="inline" for="install_package"><?php echo jgettext('Package File'); ?></label>

            <input class="input_box" id="install_package" name="install_package" type="file" size="57"/>

            <input class="btn btn-success btn-large" type="button"
                   value="<?php echo jgettext('Upload and install package'); ?>"
                   onclick="submitInstallForm();"/>

            <input type="hidden" name="option" value="com_easycreator"/>
            <input type="hidden" name="controller" value="templates"/>
            <input type="hidden" name="task" value="do_install"/>
        </div>
    </form>
</div>

<div style="clear: both;"></div>

</div><!-- Div from outer - do NOT remove !-->

<input type="hidden" name="com_type" value=""/>
<input type="hidden" name="template" value=""/>
