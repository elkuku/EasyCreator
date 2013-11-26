<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 09-Jul-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

$buildPath = $this->project->getZipPath();
?>

<div class="infoHeader img icon16-installfolder"><?php echo jgettext('Build folder') ?></div>

<div class="customPath">
    <span id="buildFolder"><?php echo JPath::clean($buildPath); ?></span>
    <?php echo DS.'&nbsp;'.$this->project->version; ?>
</div>

<?php echo EcrHelp::info(
    jgettext('The folder where your final package ends up. The folders extension_name and version will be added automatically.'
        .'<br />'
        .'If left blank the default folder will be used.')
        , jgettext('Build folder')); ?>

<?php if(false == JFolder::exists($buildPath.DS.$this->project->version)) :
    //-- The build folder does not exist - let's create it

    if(JFolder::create($buildPath.DS.$this->project->version)) :
        EcrHtml::message(jgettext('The folder has been created'));
    else :
        EcrHtml::message(
            array(jgettext('Unable to create the build folder - please check !'),
            $buildPath.DS.$this->project->version)
        ,'error');
    endif;
endif;
