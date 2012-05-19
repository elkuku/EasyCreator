<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 03-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

if( ! JComponentHelper::getParams('com_easycreator')->get('cred_author')) :
    //-- Parameters have not been set
    $link = '<a href="index.php?option=com_easycreator&controller=config">'.jgettext('Configuration settings').'</a>';
    JFactory::getApplication()->enqueueMessage(
        sprintf(jgettext('Please set your personal information in %s'), $link), 'warning');
endif;
//<a class="ecr_modal" href="http://joomla.org">@TEST</a>
//<div onclick="checkVersion();">Check</div>
?>
<div class="ecrInfoBoxContainer">
    <div class="btn block" onclick="ecrInfoBox.toggle();">
        <i class="img icon16-easycreator"></i>
        <?php echo jgettext('EasyCreator Information'); ?>
    </div>
    <div id="ecrInfoBox" style="background-color: #ccc;">
        <?php echo $this->loadTemplate('ecrbox'); ?>
    </div>
</div>

<?php
/*
if(JComponentHelper::getParams('com_easycreator')->get('versionCheck')) :
    if(JFactory::getSession()->get('ecr_versionCheck')) :
        //-- Do smthng ?
    //else :
        echo '<div id="ecr_versionCheck">';
        JFactory::getDocument()->addScriptDeclaration("window.addEvent('domready', function() { checkVersion(); });");
        echo '</div>';
        JFactory::getSession()->set('ecr_versionCheck', 'checked');
    endif;
else:
    echo jgettext('Version check is disabled');
endif;
*/
?>
<div style="text-align: center">
    <span class="img128 icon128-easycreator"></span>
    <h1 class="ecrTitel">What do you want to Create today ?</h1>
</div>

<?php echo $this->loadTemplate('projectlist'); ?>

<?php $this->addTemplatePath(JPATH_COMPONENT.DS.'views'.DS.'register'.DS.'tmpl'); ?>
<?php echo $this->loadTemplate('unregistered'); ?>

<div style="clear: both;"></div>
