<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 03-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

if( ! JComponentHelper::getParams('com_easycreator')->get('cred_author')) :
    //-- Parameters have not been set
    $link = '<a href="index.php?option=com_easycreator&controller=config">'.jgettext('Configuration settings').'</a>';
    JError::raiseNotice(100, sprintf(jgettext('Please set your personal information in %s'), $link));
endif;
//<a class="modal" href="http://joomla.org">@TEST</a>
//<div onclick="checkVersion();">Check</div>
?>

<div style="background-color: #fff; width: 222px; position: absolute; right: 3em;">
    <?php echo ecrHTML::boxStart(); ?>
        <div class="ecr_button img icon-16-easycreator" onclick="ecrInfoBox.toggle();">
        	<?php echo jgettext('EasyCreator Information'); ?>
        </div>
        <div id="ecrInfoBox" style="background-color: #ccc;">
            <?php echo $this->loadTemplate('ecrbox'); ?>
        </div>
    <?php echo ecrHTML::boxEnd(); ?>
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
<span class="img128 icon-128-easycreator"></span>
<h1 style="margin-top: 120px;">What do you want to Create today ?</h1>
</div>

<div class="projectListHeader registered"><?php echo jgettext('Registered Projects'); ?></div>

<?php echo $this->loadTemplate('projectlist'); ?>

<?php #echo ecrHTML::boxStart(); ?>
    <?php $this->addTemplatePath(JPATH_COMPONENT.DS.'views'.DS.'register'.DS.'tmpl'); ?>
    <?php echo $this->loadTemplate('unregistered'); ?>
<?php #echo ecrHTML::boxEnd(); ?>

<div style="clear: both;"></div>
