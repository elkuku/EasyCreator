<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 24-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

JHtml::_('behavior.modal');

$link = new stdClass;

//$link->versionCheck = 'http://inkubator.der-beta-server.de/releases/easycreator.html?tmpl=component'
//.'&myVersion='.ECR_VERSION;
$link->versionCheck = 'http://inkubator.der-beta-server.de/releases/easycreator.html?myVersion='.ECR_VERSION;
$link->forum = 'http://joomlacode.org/gf/project/elkuku/forum/?action=ForumBrowse&forum_id=15109';
$link->bugTracker = 'http://joomlacode.org/gf/project/elkuku/tracker/?action=TrackerItemBrowse&tracker_id=10284';
$link->features = 'http://joomlacode.org/gf/project/elkuku/tracker/?action=TrackerItemBrowse&tracker_id=10283';
$link->latestVersion = 'http://inkubator.der-beta-server.de/releases/easycreator.feed';
$link->incubatorFeed = 'http://inkubator.der-beta-server.de/snapshots/EasyCreator.feed';
$link->svn = 'http://anonymous@joomlacode.org/svn/elkuku/easy_creator/trunk/';
$link->translations = 'http://g11n.der-beta-server.de/translations/elkuku/easycreator';

$ohlohImg = '/administrator/components/com_easycreator/assets/images/ohloh_static_logo.png';
?>

<ul style="list-style: none; margin: 0; padding: 0;">
    <li class="img icon-16-sig">
        <a href="<?php echo ECR_DOCU_LINK; ?>" class="external">
            <?php echo jgettext('Documentation'); ?>
        </a>
    </li>
    <li class="img icon-16-forum">
        <a href="index.php?option=com_easycreator&amp;controller=help">
            <?php echo jgettext('Credits'); ?>
        </a>
    </li>
    <li class="img icon-16-forum">
        <a href="<?php echo $link->forum; ?>" class="external">
            <?php echo jgettext('Forum'); ?>
        </a>
    </li>
    <li class="img icon-16-bug">
        <a href="<?php echo $link->bugTracker; ?>"
        class="external">
            <?php echo jgettext('Bugtracker'); ?>
        </a>
    </li>
    <li class="img icon-16-add">
        <a href="<?php echo $link->features; ?>"
        class="external">
            <?php echo jgettext('Feature requests'); ?>
        </a>
    </li>

    <li class="img icon-16-rename">
    <!-- <a href="<?php echo $link->versionCheck; ?>" class="modal external"> -->
        <a href="<?php echo $link->versionCheck; ?>" target="_blank" class="external">
            <?php echo jgettext('Version check'); ?>
        </a>
    </li>
    <li class="img icon-16-rss">
        <a href="<?php echo $link->latestVersion; ?>" class="external">
            <?php echo jgettext('Latest version'); ?>
        </a>
    </li>
    <li class="img icon-16-rss">
        <a href="<?php echo $link->incubatorFeed; ?>" class="external">
            <?php echo jgettext('Incubator'); ?>
        </a>
    </li>
    <li class="img icon-16-article">
        <a href="<?php echo $link->svn; ?>" class="external">
            <?php echo jgettext('SVN'); ?>
        </a>
    </li>
    <li class="img icon-16-locale">
        <a href="<?php echo $link->translations; ?>" class="external">
            <?php echo jgettext('Help translating'); ?>
        </a>
    </li>
    <li>
        <!-- Ohloh button -->
        <a class="ohloh" href="http://www.ohloh.net/stack_entries/new?project_id=EasyCreator&amp;ref=WidgetProjectUsersLogo"
         style="border-bottom:none; text-decoration:none; display:block; background:url(<?php
         echo JURI::root(true).$ohlohImg; ?>) 0px 0px no-repeat;width:73px;height:23px;"
         title="<?php echo jgettext('Support EasyCreator by adding it to your stack at Ohloh'); ?>"
         onmouseout="this.style.background = 'url(<?php
         echo JURI::root(true).$ohlohImg; ?>) 0px 0px no-repeat'"
         onmouseover="this.style.background = 'url(<?php
         echo JURI::root(true).$ohlohImg; ?>) 0 -23px no-repeat'"
          ></a>
    </li>
</ul>

<div style="clear: both;"></div>

<div class="ecrFooter">
EasyCreator <span style="color: blue;"><?php echo ECR_VERSION; ?></span>
</div>

<script>
var ecrInfoBox = new Fx.Slide($('ecrInfoBox'));
ecrInfoBox.hide();
</script>
