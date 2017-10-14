<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 09-Jul-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

?>

<div class="infoHeader img icon16-installfolder"><?php echo jgettext('Build folder') ?></div>

<div class="customPath">
    <span id="buildFolder"><?php echo JPath::clean($this->project->getZipPath()); ?></span>
    <div id="versionSubDir" style="display: <?php echo ($this->preset->createVersionSubdir) ? 'inline' : 'none;"'; ?>">
    <?php echo DIRECTORY_SEPARATOR . $this->project->version; ?>
    </div>
</div>

<?php
echo EcrHelp::info(
    jgettext('The folder where your final package ends up. The folders extension_name and version will be added automatically.'
        .'<br />'
        .'If left blank the default folder will be used.')
        , jgettext('Build folder'));
?>
