<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 03-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

defined('_JEXEC') || die('=;)');

$link = new stdClass;

$link->repo = 'https://github.com/elkuku/EasyCreator/';
$link->bugTracker = 'https://github.com/elkuku/EasyCreator/issues';
$link->latestVersion = 'https://github.com/elkuku/EasyCreator/commits/master.atom';

if( ! JComponentHelper::getParams('com_easycreator')->get('cred_author')) :
    //-- Parameters have not been set
    $href = '<a href="index.php?option=com_easycreator&controller=config">'.jgettext('Configuration settings').'</a>';
    JFactory::getApplication()->enqueueMessage(
        sprintf(jgettext('Please set your personal information in %s'), $href), 'warning');
endif;
?>
<div class="ecrInfoBoxContainer">
    <div class="btn block">
        <i class="img icon16-easycreator"></i>
        <?php echo jgettext('EasyCreator Information'); ?>
    </div>
    <div>
        <ul style="list-style: none; margin: 0; padding: 0;">
            <li class="img icon16-git">
                <a href="<?php echo $link->repo; ?>" class="external">
                    <?php echo jgettext('Git repository'); ?>
                </a>
            </li>
            <li class="img icon16-bug">
                <a href="<?php echo $link->bugTracker; ?>"
                   class="external">
                    <?php echo jgettext('Bugtracker'); ?>
                </a>
            </li>
            <li class="img icon16-rss">
                <a href="<?php echo $link->latestVersion; ?>" class="external">
                    <?php echo jgettext('Recent commits'); ?>
                </a>
            </li>
        </ul>
    </div>
</div>

<div style="text-align: center">
    <span class="img128 icon128-easycreator"></span>
    <h1 class="ecrTitel">What do you want to Create today ?</h1>
</div>

<?php echo $this->loadTemplate('projectlist'); ?>

<?php $this->addTemplatePath(JPATH_COMPONENT.DS.'views'.DS.'register'.DS.'tmpl'); ?>
<?php echo $this->loadTemplate('unregistered'); ?>

<div style="clear: both;"></div>
