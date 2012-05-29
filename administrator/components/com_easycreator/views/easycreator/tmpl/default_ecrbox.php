<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 24-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

//JHtml::_('behavior.modal');

$link = new stdClass;

//$link->versionCheck = 'http://inkubator.der-beta-server.de/releases/easycreator.html?tmpl=component'
//.'&myVersion='.ECR_VERSION;
$link->versionCheck = 'http://inkubator.der-beta-server.de/releases/easycreator.html?myVersion='.ECR_VERSION;
$link->forum = 'http://joomlacode.org/gf/project/elkuku/forum/?action=ForumBrowse&amp;forum_id=15109';
$link->bugTracker = 'https://github.com/elkuku/EasyCreator/issues';
$link->features = 'http://joomlacode.org/gf/project/elkuku/tracker/?action=TrackerItemBrowse&amp;tracker_id=10283';
$link->latestVersion = 'https://github.com/elkuku/EasyCreator/commits/master.atom';
//$link->incubatorFeed = 'http://inkubator.der-beta-server.de/snapshots/EasyCreator.feed';
$link->repo = 'https://github.com/elkuku/EasyCreator/';
$link->translations = 'https://opentranslators.transifex.net/projects/p/easycreator/';

$ohlohImg = '/media/com_easycreator/admin/images/ohloh_static_logo.png';
?>

<ul style="list-style: none; margin: 0; padding: 0;">
    <li class="img icon16-forum">
        <a href="index.php?option=com_easycreator&amp;controller=help">
            <?php echo jgettext('Credits'); ?>
        </a>
    </li>
    <li class="img icon16-sig">
    <a href="<?php echo ECR_DOCU_LINK; ?>" class="external">
            <?php echo jgettext('Documentation'); ?>
        </a>
    </li>
    <li class="img icon16-forum">
        <a href="<?php echo $link->forum; ?>" class="external">
            <?php echo jgettext('Forum'); ?>
        </a>
    </li>
    <li class="img icon16-bug">
        <a href="<?php echo $link->bugTracker; ?>"
        class="external">
            <?php echo jgettext('Bugtracker'); ?>
        </a>
    </li>
    <li class="img icon16-add">
        <a href="<?php echo $link->features; ?>"
        class="external">
            <?php echo jgettext('Feature requests'); ?>
        </a>
    </li>

    <!--
    <li class="img icon16-rename">
     <a href="<?php echo $link->versionCheck; ?>" class="ecr_modal external">
        <a href="<?php echo $link->versionCheck; ?>" target="_blank" class="external">
    <?php echo jgettext('Version check'); ?>
    </a>
    </li>
     -->
    <li class="img icon16-git">
    <a href="<?php echo $link->repo; ?>" class="external">
        <?php echo jgettext('Git repository'); ?>
    </a>
    </li>
    <li class="img icon16-rss">
        <a href="<?php echo $link->latestVersion; ?>" class="external">
            <?php echo jgettext('Recent commits'); ?>
        </a>
    </li>
    <li class="img icon16-locale">
        <a href="<?php echo $link->translations; ?>" class="external">
            <?php echo jgettext('Help translating'); ?>
        </a>
    </li>
    <li>
        <!-- Ohloh button -->
        <a class="ohloh"
           href="http://www.ohloh.net/stack_entries/new?project_id=EasyCreator&amp;ref=WidgetProjectUsersLogo"
         style="border-bottom:none; text-decoration:none; display:block; background:url(<?php
         echo JURI::root(true).$ohlohImg; ?>) 0 0 no-repeat;width:73px;height:23px;"
         title="<?php echo jgettext('Support EasyCreator by adding it to your stack at Ohloh'); ?>"
         onmouseout="this.style.background = 'url(<?php
         echo JURI::root(true).$ohlohImg; ?>) 0 0 no-repeat'"
         onmouseover="this.style.background = 'url(<?php
         echo JURI::root(true).$ohlohImg; ?>) 0 -23px no-repeat'"
          ></a>
    </li>
</ul>

<div style="clear: both;"></div>

<script type="text/javascript">
var ecrInfoBox = new Fx.Slide(document.id('ecrInfoBox'));
ecrInfoBox.hide();
</script>
