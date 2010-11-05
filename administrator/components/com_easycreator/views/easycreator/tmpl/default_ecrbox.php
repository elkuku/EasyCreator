<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 24-Mar-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

$link = new stdClass();

$link->versionCheck = 'http://projects.easy-joomla.org/index.php?option=com_versions'
    .'&amp;tmpl=component&amp;catid=1&amp;myVersion='.ECR_VERSION;
$link->forum = 'http://forum.easy-joomla.org/index.php?board=16.0';
$link->bugTracker = 'http://joomlacode.org/gf/project/easyjoomla/tracker/?action=TrackerItemBrowse&amp;tracker_id=8236';
$link->features = 'http://joomlacode.org/gf/project/easyjoomla/tracker/?action=TrackerItemBrowse&amp;tracker_id=9611';
$link->latestVersion = 'http://projects.easy-joomla.org/component/versions/?catid=1&amp;task=feed&amp;tmpl=component';
$link->incubatorFeed = 'http://projects.easy-joomla.org/incubator-newsfeeds/EasyCreator.feed';
$link->svn = 'http://anonymous@joomlacode.org/svn/easyjoomla/easy_creator/trunk/';

$ohlohImg = '/administrator/components/com_easycreator/assets/images/ohloh_static_logo.png';
$ecrLogo = 'administrator/components/com_easycreator/assets/images/easycreator-shadow.png';
?>

<a href="index.php?option=com_easycreator&amp;controller=help" style="float: right; padding-right: 0.3em;">
	<span class="img icon-16-forum">Credits</span>
</a>

<ul style="list-style: none; margin: 0; padding: 0;">
    <li class="img icon-16-sig">
        <a href="<?php echo ECR_DOCU_LINK; ?>" class="external">
            <?php echo jgettext('Documentation'); ?>
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
        <a href="<?php echo $link->versionCheck; ?>" class="external">
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
    <li>
    <div style="float: right">
        <!-- Ohloh button -->
        <a href="http://www.ohloh.net/stack_entries/new?project_id=EasyCreator&amp;ref=WidgetProjectUsersLogo"
         style="border-bottom:none; text-decoration:none; display:block; background:url(<?php
         echo JURI::root(true).$ohlohImg; ?>) 0px 0px no-repeat;width:73px;height:23px;"
         title="<?php echo jgettext('Support EasyCreator by adding it to your stack at Ohloh'); ?>"
         onmouseout="this.style.background = 'url(<?php
         echo JURI::root(true).$ohlohImg; ?>) 0px 0px no-repeat'"
         onmouseover="this.style.background = 'url(<?php
         echo JURI::root(true).$ohlohImg; ?>) 0 -23px no-repeat'"
          ></a>
    </div>
    </li>
</ul>

<?php echo JHTML::image($ecrLogo, 'EasyCreator Logo'); ?>

<div class="ecrFooter">
EasyCreator <span style="color: blue;"><?php echo ECR_VERSION; ?></span>
</div>

<script>
var ecrInfoBox = new Fx.Slide($('ecrInfoBox'));
ecrInfoBox.hide();
</script>
