<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author		Nikolai Plath {@link http://www.nik-it.de}
 * @author		Created on 14-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

ecrScript('templates');
?>

<br />
<?php ecrHTML::floatBoxStart(); ?>
<h2><?php echo jgettext('Install templates'); ?></h2>
<div class="ecrBigInfo"><?php echo jgettext('Select a template package to import'); ?></div>

<br />
<form enctype="multipart/form-data" action="index.php" method="post" name="installForm">
	<div>
    <label for="install_package"><?php echo jgettext('Package File'); ?></label>

    <input class="input_box" id="install_package" name="install_package" type="file" size="57" />
    <input class="button" type="button" value="<?php echo jgettext('Upload and install package'); ?>" onclick="submitInstallForm();" />

    <input type="hidden" name="option" value="com_easycreator" />
    <input type="hidden" name="controller" value="templates" />
    <input type="hidden" name="task" value="do_install" />
	</div>
</form>

<?php ecrHTML::floatBoxEnd(); ?>

<div style="clear: both;"></div>

</div><!-- Div from outer -->

<input type="hidden" name="com_type" value="" />
<input type="hidden" name="template" value="" />
<?php
