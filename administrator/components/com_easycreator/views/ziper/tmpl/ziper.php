<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage	Views
 * @author		Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author		Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$params = JComponentHelper::getParams('com_easycreator');

ecrScript('util');

ecrHTML::floatBoxStart();
echo $this->loadTemplate('format');
ecrHTML::floatBoxEnd();

ecrHTML::floatBoxStart();
echo $this->loadTemplate('options');
ecrHTML::floatBoxEnd();
?>

<?php ecrHTML::floatBoxStart(); ?>
    <div class="ecr_button" onclick="$('ecr_ajax_loader').className='ecr_ajax_loader_big'; submitbutton('ziperzip');"
    style="margin: 1em; padding: 1em; text-align: center;">
        <div id="ecr_ajax_loader" class="img icon-32-ecr_archive"
        style="padding-bottom: 32px; margin-top: 1em; margin-bottom: 1em; margin-left: 3em;"></div>
        <h1>
        <?php echo sprintf(jgettext('Create %s'), $this->project->name); ?>
        </h1>
        <div id="xecr_ajax_loader"></div>
    </div>
<?php ecrHTML::floatBoxEnd(); ?>

<div style="clear: both;"></div>

<?php $this->drawArchive(); ?>

<script type="text/javascript">
updateName('<?php echo $this->ecr_project; ?>');
</script>

<input type="hidden" name="old_task" value="<?php echo JRequest::getCmd('task'); ?>" />
