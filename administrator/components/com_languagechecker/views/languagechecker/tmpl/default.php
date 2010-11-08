<?php
/**
 * @version SVN: $Id$
 * @package    LanguageChecker
 * @subpackage Views
 * @author     Nikolai Plath {@link http://easy-joomla.org}
 * @author     Created on 12-Sep-10
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');
defined('NL') || define('NL', "\n");

JHtml::_('behavior.tooltip');

$document = JFactory::getDocument();

$assetPath = JURI::root(true).'/administrator/components/com_languagechecker/assets';
$mifPath = $assetPath.'/javascript/miftree';

$document->addScript($mifPath.'/Core/Mif.Tree.js');
$document->addScript($mifPath.'/Core/Mif.Tree.Node.js');
$document->addScript($mifPath.'/Core/Mif.Tree.Selection.js');
$document->addScript($mifPath.'/Core/Mif.Tree.Hover.js');
$document->addScript($mifPath.'/Core/Mif.Tree.Load.js');
$document->addScript($mifPath.'/Core/Mif.Tree.Draw.js');
$document->addScript($mifPath.'/More/Mif.Tree.Checkbox.js');

$document->addStyleSheet($mifPath.'/assets/styles/mif-tree.css');

$document->addScript($assetPath.'/javascript/treedemo.js');
$document->addStyleSheet($assetPath.'/css/languagechecker.css');
?>
<style>
div.jlpOptions {
padding-top: 0.7em;
width: 100%;
}
div.jlpOptions div {
float: left;
margin-left: 0.5em;
}
</style>
<form name="adminForm">

<div class="jlpOptions">
	<div>
		Language<br />
        <?php echo $this->lists['language']; ?>
	</div>
	<div>
		Extension<br />
        <?php echo $this->lists['components']; ?>
	</div>
	<div>
		Scope<br />
        <?php echo $this->lists['scopes']; ?>
	</div>
	<div>
		sub scope<br />
        <?php echo $this->lists['subScope']; ?>
	</div>
	<div>
		<?php echo $this->checks->get('includeCoreLanguage'); ?>
    </div>
</div>
<div style="clear: both"></div><div class="jlpOptions" style="background-color: #ccff99;">
	<div>
		IN Format<br />
        <?php echo $this->lists['langFormatIn']; ?>
	</div>
</div>
<div style="clear: both"></div>
<div class="jlpOptions" style="background-color: #ffc;">
	<div>
		OUT Format<br />
        <?php echo $this->lists['langFormatOut']; ?>
	</div>
    <div>
		<?php echo $this->checks->get('includeLineNumbers'); ?>
	</div>
	<div>
        <?php echo $this->checks->get('markFuzzy'); ?>
        <br />
        <?php echo $this->checks->get('markKeyDiffers'); ?>
    </div>
</div>
	<div style="width: 200px; float: left; margin-left: 1em;">
        <b>Exclude dirs:</b>
        <br />
		<div id="tree_container"></div>
	</div>
<div style="clear: both"></div>
<div class="jlpOptions" style="background-color: #cce5ff;">

<input type="submit" name="task" value="test" value="TEST" />
<input type="submit" name="task" value="write" value="WRITE" />
</div>
<div style="clear: both"></div>

<input type="hidden" id="excludeDirs" name="excludeDirs" value="<?php echo $this->excludeDirs; ?>" />
<input type="hidden" name="option" value="com_languagechecker" />
<?php if($this->langFormatOut && $this->comPath) : ?>
<textarea style="font-size: 12px; height: 300px; width: 100%; overflow: auto;
background-color: #fff; border: 2px dashed gray; padding: 0.5em;">
<?php echo $this->parser->generate($this->checker, $this->buildOpts); ?>
</textarea>
<?php endif; ?>

</form>
<?php echo LanguageCheckerHelper::footer();
//#var_dump($_REQUEST);
//#var_dump($this->strings);
//#var_dump($this->translations);
