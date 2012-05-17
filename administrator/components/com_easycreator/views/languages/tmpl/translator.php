<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 11-Sep-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die ('=;)');

//-- Add CSS
ecrStylesheet('icon', 'translator');

ecrScript('php2js', 'translator');

$useGoogle = JComponentHelper::getParams('com_easycreator')->get('use_google_trans_api');

$fieldID = JRequest::getInt('field_id');
$adIds = JRequest::getVar('ad_ids');

$baseLink = 'index.php?option=com_easycreator&tmpl=component&controller=ajax&format=raw';
$baseLink .= '&ecr_project='.JRequest::getCmd('ecr_project');
$baseLink .= '&scope='.$this->scope;
$baseLink .= '&trans_lang='.$this->trans_lang;
$baseLink .= '&trans_key='.$this->trans_key;

$ret_type = JRequest::getCmd('ret_type', 'ini');

$langTag = substr($this->trans_lang, 0, 2);

if($this->trans_lang != 'en-GB' && ! $this->trans_default)
{
    //-- No default translation available
    EcrHtml::displayMessage(array(jgettext('Unable to translate without default translation')
    , sprintf(jgettext('Please translate %s first'), 'en-GB')), 'error');

    return;
}

$translation = '';
$extensionPrefix = strtolower($this->project->extensionPrefix);

if($this->translation)
{
    $translation =($this->translation == '**TRANSLATE**') ? '' : $this->translation;
}
else if($this->trans_lang == 'en-GB')
{
    $translation = strtolower($this->trans_key);

    if($extensionPrefix
    && strpos($translation, $extensionPrefix) === 0)
    {
        $translation = substr($translation, strlen($extensionPrefix));
    }

    $translation = ucfirst($translation);
    $translation = str_replace('_', ' ', $translation);
}

if(strpos($translation, '"') === 0)
{
    $translation = substr($translation, 1);
}

if(strrpos($translation, '"') == strlen($translation) - 1)
{
    $translation = substr($translation, 0, strlen($translation) - 1);
}

/*
 * Google translation API is now a paid service :(
 *
if($useGoogle && $this->trans_lang != 'en-GB') :
    JFactory::getDocument()->addScript('http://www.google.com/jsapi');
?>
	<div id="google_loader">
		<span class="ajax_loading">
		    <?php echo jgettext('Loading Google Translation API'); ?>
		</span>
	</div>

	<script type="text/javascript">
		google.load('language', '1');
		var gbranding_displayed = false;
		$('google_loader').innerHTML = '';
	</script>
<?php endif;
*/
?>

<table width="100%">
	<tr>
		<th style="background-color: #CCE5FF;"><?php echo jgettext('Key'); ?></th>
		<td style="background-color: #CCE5FF;"><?php echo htmlentities($this->trans_key); ?></td>
	</tr>
	<?php if($this->trans_lang != 'en-GB') : ?>
	<tr>
		<th style="background-color: #FFFF99;">en-GB</th>
		<td>
		<div style="background-color: #FFFF99;" id="default"><?php echo htmlentities($this->trans_default); ?>
		</div>
		</td>
	</tr>
	<?php endif; ?>
		<tr>
		<th style="background-color: #E5FF99;"><?php echo $this->trans_lang; ?></th>
		<td style="background-color: #E5FF99;">
		<textarea name="translation" id="translation" rows="4"
			style="width: 100%; font-size: 1.3em;"><?php echo htmlspecialchars($translation); ?></textarea>
		</td>
	</tr>
</table>

<div id="ajResult"></div>
<div id="ajaxDebug"></div>

<div class="ecr_easy_toolbar" style="float: right;">
    <ul>
        <li>
            <a href="javascript:;" onclick="ecrTranslator.translate(<?php
            echo "'$baseLink', '$fieldID', '$this->trans_lang', '$ret_type', '$adIds'"; ?>
            );" accesskey="s" title="<?php echo jgettext('Translate [s]'); ?>">
                <span class="icon32-ecr_save"></span>
                <?php echo jgettext('Translate'); ?>
            </a>
        </li>
        <?php if($this->translation) : ?>
        <li>
            <a href="javascript:;" onclick="ecrTranslator.deleteTranslation(<?php echo "'$baseLink', '$fieldID'"; ?>);">
            	<span class="icon32-ecr_delete"></span>
            	<?php echo jgettext('Delete'); ?>
            </a>
        </li>
        <?php endif; ?>
    </ul>
</div>

<?php if($this->trans_lang != 'en-GB') : ?>
<div class="ecr_easy_toolbar" style="float: left;">
    <ul>
        <li>
            <a href="javascript:;" accesskey="c" onclick="ecrTranslator.copyTrans();">
            	<span class="icon16-copytrans"></span>
            	<?php echo jgettext('Copy'); ?>
            </a>
        </li>
    	<?php if(0) : //$useGoogle) : ?>
            <li>
            	<span id="gtranslate_branding" style="float: right; padding-left: 0.5em;"></span>
                <a href="javascript:;" accesskey="g" onclick="ecrTranslator.google_translate('<?php echo $langTag; ?>');">
                	<span class="icon16-copytrans"></span>
                	<?php echo jgettext('Google translate'); ?>
                </a>
        	</li>
    	<?php endif; ?>
    </ul>
</div>
<?php endif; ?>

<script type="text/javascript">
	var ecrTranslator = new ecrTranslator();
	$('translation').focus();
</script>

