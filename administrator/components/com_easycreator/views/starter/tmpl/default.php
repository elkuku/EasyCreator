<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

ecrLoadHelper('easytemplatehelper');

//-- Add css
ecrStylesheet('php_file_tree');

$comTypes = EasyProjectHelper::getProjectTypes();
$templateList = EasyTemplateHelper::getTemplateList();

$docBase = 'http://docs.joomla.org/';

$infoLinks = array(
      'component' => array(
            'Category:Components'
            => $docBase.'Category:Components'
    )
    , 'module' => array(
              'Category:Modules' => $docBase.'Category:Modules'
            , 'Creating a Hello World Module'
            => $docBase.'Tutorial:Creating_a_Hello_World_Module_for_Joomla_1.5'
    )
    , 'plugin' => array(
              'Category:Plugins' => $docBase.'Category:Plugins'
            , 'Creating a Plugin for Joomla 1.5' => $docBase.'Tutorial:Creating_a_Plugin_for_Joomla_1.5'
            , 'How to create a content plugin' => $docBase.'How_to_create_a_content_plugin'
            , 'How to create a search plugin' => $docBase.'How_to_create_a_search_plugin'
            , 'How to create a system plugin' => $docBase.'How_to_create_a_system_plugin'
            , 'Joomla System Plugin Specification' => $docBase.'Reference:Joomla_System_Plugin_Specification'
    )
    , 'template' => array(
              'Category:Templates' => $docBase.'Category:Templates'
            , 'Category:Template_FAQ' => $docBase.'Category:Template_FAQ'
            , 'How to override the output from the Joomla! core'
            => $docBase.'How_to_override_the_output_from_the_Joomla!_core'
            , 'The Joomla! CSS explained' => 'http://www.joomla-css.nl'
    )
    , 'library' => array()
    , 'package' => array()
);

?>
<div class="white_box">
<?php echo ecrHTML::BoxStart(); ?>
	<div class="wizard-header">
    	<span id="wizard-loader" class="img32 icon-32-wizard"></span>
        <span class="wiz_step">1 / 3</span><?php echo jgettext('Extension type'); ?>
	</div>
    <div class="ecr_wiz_desc">
        <?php echo jgettext('Choose a component type from a predefined template'); ?>
    </div>
<?php
echo jgettext('Hide templates for Joomla! versions');

$hideVersions = JRequest::getVar('show_templates_jversion', array());
$jVersions = array('1.5', '1.6');

foreach ($jVersions as $v)
{
    $vX = str_replace('.', '', $v);
    $checked =(in_array($v, $hideVersions)) ? ' checked="checked"' : '';
    ?>
    <input type="checkbox" name="show_templates_jversion[]"
    id="hideVersion<?php echo $vX; ?>"
    <?php echo $checked; ?>
    value="<?php echo $v; ?>" onchange="submitform()">
    <label for="hideVersion<?php echo $vX; ?>"
	class="img4 icon-joomla-compat-<?php echo $vX; ?>">
    </label>
    <?php
}//foreach
?>

<?php echo ecrHTML::boxEnd(); ?>
</div>
<div style="clear: both; height: 1em;"></div>
<?php
//<input type="checkbox" name="show_templates_jversion[]" value="1.6"> 1.6
?>
<div style="clear: both; height: 1em;"></div>
<?php foreach(EasyProjectHelper::getProjectTypes() as $extType => $description): ?>
    <div class="ecr_floatbox" style="width: 250px;">
        <?php echo ecrHTML::boxStart(); ?>
        <div class="ecr_floatbox_title img icon-12-<?php echo $extType; ?>">
            <?php echo $description; ?>
        </div>
        <?php if(isset($templateList[$extType])): ?>
            <?php
            foreach($templateList[$extType] as $template):
                if(in_array($template->jVersion, $hideVersions))
                continue;

                $link =($template->authorUrl)
                ? '<a href="'.$template->authorUrl.'" class="external">'.jgettext('Description').'</a>'
                : '';

                $htmlId = $extType.'_'.$template->folder;
                $action = "onclick=\"setTemplate('$extType', '$template->folder'); goWizard(2);\"";

                if($template->jVersion == '1.6' && ECR_JVERSION == '1.5')
                {
                    $action = '';
                    $s = '<span class="img icon-16-logout"></span>';
                    $m = '<strong style=\'color: red;\'>'
                    .jgettext('Joomla 1.6 extension templates can not be build on Joomla 1.5')
                    .'</strong>';

                    $template->description = $m.BR.$template->description;
                    $template->info = $template->info.BR.$m;
                    $template->name = $s.$template->name;
                }
                else
                {
                }
                ?>
        		<div class="wizard-row">
    				<div title="<?php echo jgettext('Info').'::'.jgettext('Click to view files'); ?>"
    				class="ecr_button img icon-16-add hasEasyTip" style="float: right;"
    				id="btn_<?php echo $htmlId; ?>"
    				onclick="getExtensionTemplateInfo(<?php echo "'$extType', '$template->folder', $htmlId"; ?>)">
    				    <?php echo jgettext('Info') ?>
    				</div>
            		<div class="hasEasyTip" title="<?php echo $template->info; ?>">
                        <a href="javascript:;" style="display: block;"
                        class="ecr_button img icon-joomla-compat-<?php
                        echo str_replace('.', '', $template->jVersion); ?>" <?php echo $action; ?>>
                            <?php echo $template->name; ?>
                        </a>
                        <div id="<?php echo $htmlId; ?>">
                            <?php echo $template->description; ?>
                            <br />
                            <?php echo $link; ?>
                            <br />
                            <?php
                            if($template->author) :
                                echo '<strong>'.jgettext('Author').'</strong> '.$template->author.'<br />';
                            endif;
                            ?>
                            <strong>
                                <?php echo jgettext('Version'); ?>
                            </strong>
                            <?php echo $template->version; ?>
                            <br />
                            <strong> <?php echo jgettext('PHP version'); ?>
                            </strong> <?php echo $template->phpVersion; ?>
                            <br />
                            <br />
                            <strong><?php echo jgettext('Files'); ?></strong>
                            <div id="<?php echo $htmlId; ?>_files"></div>
                        </div>
        				<script type="text/javascript">
        					<?php echo $htmlId; ?> = new Fx.Slide('<?php echo $htmlId; ?>');
        					<?php echo $htmlId; ?>.hide();
        				</script>
                    </div>
        		</div>
            <?php endforeach; ?>
        <?php if(count($infoLinks[$extType])) : ?>
        <div class="ecr_wiz_desc">
            <strong><?php echo jgettext('External infos'); ?></strong>
            <ul>
            <?php foreach($infoLinks[$extType] as $text => $href) : ?>
                <li>
                	<a class="external" href="<?php echo $href; ?>">
                	    <?php echo $text; ?>
                	</a>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
       <?php endif; ?>
       <?php echo ecrHTML::boxEnd(); ?>
   </div>
<?php endforeach; ?>

<div style="clear: both;"></div>

<input type="hidden" id="tpl_type" name="tpl_type"  />
<input type="hidden" id="tpl_name" name="tpl_name" />
