<?php
/**
 * @version $Id$
 * @package    EasyCreator
 * @subpackage Help
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

?>
<br />
<div class="white_box">
<?php ecrHTML::boxStart(); ?>

<h1><?php echo jgettext('Help'); ?></h1>
<p>
    <span class="img32a icon-32-help">
    	EasyCreator documentation can be found on the unofficial german Joomla! wiki Joomla-Nafu.de. Don't worry - it has been written in english language (I hope that you will understand my poor english)
    	<br />
    	As this is a wiki (just like wikipedia) - your are hereby invited to contribute or correct my errors - big thanks !
    	<br />
    	<br />
    	So... here it is: <a class="external" href="<?php echo ECR_DOCU_LINK; ?>">EasyCreator Documentation</a> (WIP)
    </span>
</p>

<h1>Credits</h1>
<ul>
    <li>
        <strong>The icon</strong> belongs to the very funny constructor game <a href="http://games.kde.org/game.php?game=ktuberling" class="external"><span class="img icon-16-easycreator">KTuberling</span></a>. I would call EasyCreator also a "potato editor" <tt>=;)</tt>
    </li>
    <li>
        <strong>EditArea</strong> The great code editor by Christophe Dolivet. <a href="http://www.cdolivet.com/index.php?page=editArea" class="external">www.cdolivet.net/editarea</a>
    </li>
    <li>
        <strong>PHP File Tree</strong> The beautiful File tree is build on the idea by Cory S.N. LaViska - <a href="http://abeautifulsite.net/2007/06/php-file-tree/" class="external">abeautifulsite.net</a> - <em>heavily modified</em>
    </li>
    <li>
        <strong>Manifest maker class</strong> by Ian MacLennan <a href="http://joomlacode.org/gf/project/ianstools/" class="external">joomlacode.org/gf/project/ianstools</a> - <em>slightly modified</em>
    </li>
    <li>
        <strong>Package creation</strong> is done with <a href="http://pear.php.net" class="external">PEAR</a> packages <a href="http://pear.php.net/package/Archive_Tar" class="external">Archive_Tar</a> and <a href="http://pear.php.net/package/Archive_Zip" class="external">Archive_Zip</a>.
    </li>
    <li>
        <strong>Package install routine</strong> for J! 1.5 by Andrew Eddie - New Life in IT Pty Ltd <a href="http://jxtended.com/" class="external">jxtended.com</a>
    </li>
    <li>
        <strong>Diff engine</strong> comes from <a href="http://www.dokuwiki.org" class="external">Dokuwiki</a> - see &copy; + License in helpers/DifferenceEngine.php
    </li>
    <li>
        <strong>QTabs</strong> comes from <a href="http://www.latenight-coding.com/mootools/classes/qtabs.html" class="external">latenight-coding</a>
    </li>
    <li>
        <strong>Extension templates</strong> see individual templates and most of all <a href="http://docs.joomla.org/" class="external">docs.joomla.org</a>
    </li>
    <li>
        <strong>Fellow translators ::</strong>
        <ul>
            <li><strong>French</strong> crony</li>
            <li><strong>Polish</strong> keran</li>
            <li><strong>Chinese</strong> baijianpeng <a href="http://www.joomlagate.com" class="external">www.joomlagate.com</a></li>
        </ul>
    </li>
    <li>Who's missing?... ah, me - Nikolai Plath (aka elkuku) - <a href="http://www.nik-it.de" class="external">NiK-IT.de</a> - responsible for the <strong>rest</strong> <tt style="color: green;">=;)</tt></li>
</ul>

<?php ecrHTML::boxEnd(); ?>
</div>
<br />
<?php
