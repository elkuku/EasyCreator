<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- Add css
ecrStylesheet('php_file_tree');

$comTypes = EcrProjectHelper::getProjectTypes();
$jVersions = array('15', '25');

?>
<div class="white_box">
    <div class="wizard-header">
        <span id="wizard-loader" class="img32 icon-32-wizard"></span>
        <span class="wiz_step">1 / 3</span><?php echo jgettext('Extension type'); ?>
    </div>
    <div class="ecr_wiz_desc">
        <?php echo jgettext('Choose a component type from a predefined template'); ?>
    </div>
    <?php

    echo jgettext('Hide templates for Joomla! versions');

    foreach($jVersions as $v) : ?>
        <input type="checkbox" id="hideVersion<?php echo $v; ?>"
               onchange="changeJVersion('<?php echo $v; ?>');">
        <label for="hideVersion<?php echo $v; ?>"
               class="img4 icon-joomla-compat-<?php echo $v; ?>">
        </label>
    <?php endforeach; ?>

</div>

<div style="clear: both; height: 1em;"></div>

<?php foreach(EcrProjectHelper::getProjectTypes() as $extType => $description): ?>
<div class="ecr_floatbox" style="width: 250px;">
    <div class="ecr_floatbox_title img icon-12-<?php echo $extType; ?>">
        <?php echo $description; ?>
    </div>
    <?php if(isset($this->templateList[$extType])): ?>
    <?php if('' != $this->notes[$extType]) : ?>
        <span class="ecr_button"
              onclick="<?php echo $extType.'_notes'; ?>.toggle();"><?php echo jgettext('Notes'); ?>
        </span>
    <?php endif; ?>
    <?php if(count($this->infoLinks[$extType])) : ?>
        <span class="ecr_button"
              onclick="<?php echo $extType.'_links'; ?>.toggle();"><?php echo jgettext('See also...'); ?>
        </span>
        <div class="ecr_wiz_desc" id="<?php echo $extType.'_links'; ?>">
            <strong><?php echo jgettext('External infos'); ?></strong>
            <ul>
                <?php foreach($this->infoLinks[$extType] as $text => $href) : ?>
                <li>
                    <a class="external" href="<?php echo $href; ?>">
                        <?php echo $text; ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <script type="text/javascript">
                <?php echo $extType.'_links = new Fx.Slide(\''.$extType.'_links\');'; ?>
                <?php echo $extType.'_links'; ?>.hide();
        </script>
        <?php endif; ?>
    <?php if('' != $this->notes[$extType]) : ?>
        <div class="ecr_wiz_desc" id="<?php echo $extType.'_notes'; ?>">
            <strong><?php echo jgettext('Notes'); ?></strong><br />
            <?php echo $this->notes[$extType]; ?>
        </div>
        <script type="text/javascript">
                <?php echo $extType.'_notes = new Fx.Slide(\''.$extType.'_notes\');'; ?>
                <?php echo $extType.'_notes'; ?>.hide();
        </script>
        <?php endif; ?>
    <div style="clear: both; height: 1em;"></div>

    <?php
    foreach($this->templateList[$extType] as $template):
        $link = ($template->authorUrl)
            ? '<a href="'.$template->authorUrl.'" class="external">'.jgettext('Description').'</a>'
            : '';

        $htmlId = $extType.'_'.$template->folder;
        $action = "onclick=\"setTemplate('$extType', '$template->folder'); goWizard(2);\"";

        if($template->jVersion != '1.5' && ECR_JVERSION == '1.5') :
            $action = '';
            $s = '<span class="img icon-16-logout"></span>';
            $m = '<strong style=\'color: red;\'>'
                .jgettext('Joomla 1.6 extension templates can not be build on Joomla 1.5')
                .'</strong>';

            $template->description = $m.BR.$template->description;
            $template->info = $template->info.BR.$m;
            $template->name = $s.$template->name;
        endif;
        ?>

        <div class="wizard-row jcompat_<?php echo str_replace('.', '', $template->jVersion);?>">
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
                    <?php echo $template->description; ?><br/>
                    <?php if($link) echo '<strong>'.jgettext('Link').'</strong> '.$link.'<br />'; ?>
                    <?php if($template->author) echo '<strong>'.jgettext('Author').'</strong> '.$template->author.'<br />'; ?>
                    <strong><?php echo jgettext('Version'); ?></strong>
                    <?php echo $template->version; ?><br/>
                    <strong><?php echo jgettext('PHP version'); ?></strong>
                    <?php echo $template->phpVersion; ?><br/>
                    <br/>
                    <strong><?php echo jgettext('Files'); ?></strong>

                    <div id="<?php echo $htmlId; ?>_files"></div>
                </div>
                <script type="text/javascript">
                    <?php echo $htmlId.' = new Fx.Slide(\''.$htmlId.'\');'; ?>
                    <?php echo $htmlId; ?>.hide();
                </script>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php endforeach; ?>

<div style="clear: both;"></div>

<input type="hidden" id="tpl_type" name="tpl_type"/>
<input type="hidden" id="tpl_name" name="tpl_name"/>
