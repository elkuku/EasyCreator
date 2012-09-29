<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/* @var EcrProjectBase $pType */
//-- Add css
ecrStylesheet('php_file_tree');

$comTypes = EcrProjectHelper::getProjectTypes();
$jVersions = array('25', '30');

?>

<div class="white_box">
    <div class="wizard-header">
        <span id="wizard-loader" class="img32 icon32-wizard"></span>
        <span class="wiz_step">1 / 3</span><?php echo jgettext('Extension type'); ?>
    </div>
    <div class="ecr_wiz_desc">
        <?php echo jgettext('Choose a component type from a predefined template'); ?>
    </div>
    <div>
        <?php

        echo jgettext('Hide templates for Joomla! versions');

        foreach($jVersions as $v) : ?>
            <input type="checkbox" id="hideVersion<?php echo $v; ?>"
                   onchange="changeJVersion('<?php echo $v; ?>');">
            <label for="hideVersion<?php echo $v; ?>"
                   class="inline">
                <i class="img4 iconjoomla-compat-<?php echo $v; ?>"></i>
            </label>
            <?php endforeach;
        ?>
    </div>
</div>

<div style="clear: both; height: 1em;"></div>
<?php /*
<ul class="nav nav-tabs">
	<?php foreach(EcrProjectHelper::getProjectTypes() as $extType => $pType): ?>

    <li>
	    <a href="#<?php echo 'tab-'.$extType; ?>" data-toggle="tab"><?php echo $pType->translateType(); ?></a>
    </li>
	<?php endforeach; ?>
</ul>

<div class="tab-content">
	<?php foreach(EcrProjectHelper::getProjectTypes() as $extType => $pType): ?>

        <div class="tab-pane" id="<?php echo 'tab-'.$extType; ?>">
	        <?php if('' != $this->notes[$extType]) : ?>
            <span class="btn-inverse btn<?php echo ECR_TBAR_SIZE; ?>"
                  onclick="<?php echo $extType.'_notes'; ?>.toggle();">
                <?php echo jgettext('Notes'); ?>
            </span>
	        <?php endif; ?>
	        <?php if(count($this->infoLinks[$extType])) : ?>
            <span class="btn-inverse btn<?php echo ECR_TBAR_SIZE; ?>"
                  onclick="<?php echo $extType.'_links'; ?>.toggle();">
            <?php echo jgettext('See also...'); ?>
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
                <strong><?php echo jgettext('Notes'); ?></strong><br/>
		        <?php echo $this->notes[$extType]; ?>
            </div>
            <script type="text/javascript">
			        <?php echo $extType.'_notes = new Fx.Slide(\''.$extType.'_notes\');'; ?>
			        <?php echo $extType.'_notes'; ?>.hide();
            </script>
	        <?php endif; ?>
            <div style="clear: both; height: 1em;"></div>

	        <?php/*
	        foreach($this->templateList[$extType] as $template):
		        $link = ($template->authorUrl)
			        ? '<a href="'.$template->authorUrl.'" class="external">'.jgettext('Description').'</a>'
			        : '';

		        $htmlId = $extType.'_'.$template->folder;
		        $action = "onclick=\"setTemplate('$extType', '$template->folder'); goWizard(2);\"";
		        ?>

                <div class="btn-group jcompat_<?php echo str_replace('.', '', $template->jVersion);?>">
                    <a title="<?php echo $template->info; ?>"
                       href="javascript:;"
                       class="btn hasTip"
                       style="display: block; min-width: 160px;"
				        <?php echo $action; ?>
                            >
                        <!--
                <i style="float: left" class="img iconjoomla-compat-<?php
					        echo str_replace('.', '', $template->jVersion); ?>"></i>
                -->
				        <?php echo $template->name; ?>
                    </a>

                    <a title="<?php echo jgettext('Info').'::'.jgettext('Click to view files'); ?>"
                       class="btn-info btn hasTip" href="javascript:;" id="btn_<?php echo $htmlId; ?>"
                       onclick="getExtensionTemplateInfo(<?php echo "'$extType', '$template->folder', $htmlId"; ?>)">
				        <?php echo jgettext('Info') ?>
                    </a>
                </div>

                <div id="<?php echo $htmlId; ?>">
			        <?php echo $template->description; ?><br/>
			        <?php if($link) echo '<strong>'.jgettext('Link').'</strong> '.$link.'<br />'; ?>
			        <?php if($template->author) echo '<strong>'.jgettext('Author').'</strong> '.$template->author.'<br />'; ?>
                    <strong><?php echo jgettext('Joomla! version'); ?></strong>
			        <?php echo $template->jVersion; ?><br/>
                    <strong><?php echo jgettext('Version'); ?></strong>
			        <?php echo $template->version; ?><br/>
                    <strong><?php echo jgettext('PHP version'); ?></strong>
			        <?php echo $template->phpVersion; ?><br/>
                    <br/>
			        <?php if(count($template->complements)) : ?>
                    <strong><?php echo jgettext('Complements'); ?></strong>
                    <ul>

				        <?php foreach($template->complements as $complement) : ?>
                        <li>
					        <?php echo $complement->folder.' ('.$complement->version.')'; ?>
                            <br />
                            &rArr; <tt><?php echo $complement->targetDir; ?></tt>
                        </li>
				        <?php endforeach; ?>
                    </ul>
			        <?php endif; ?>
                    <strong><?php echo jgettext('Files'); ?></strong>

                    <div id="<?php echo $htmlId; ?>_files"></div>
                </div>
                <script type="text/javascript">
				        <?php echo $htmlId.' = new Fx.Slide(\''.$htmlId.'\');'; ?>
				        <?php echo $htmlId; ?>.hide();
                </script>
		        <?php endforeach; ?>

        </div>
	<?php endforeach; ?>
</div>
*/ ?>


<?php foreach(EcrProjectHelper::getProjectTypes() as $extType => $pType): ?>
<div class="ecr_floatbox" style="width: 245px;">
    <div class="ecr_floatbox_title img icon12-<?php echo $extType; ?>">
        <?php echo $pType->translateType(); ?>
    </div>
    <?php if(isset($this->templateList[$extType])): ?>
    <?php if('' != $this->notes[$extType]) : ?>
        <span class="btn-inverse btn<?php echo ECR_TBAR_SIZE; ?>"
              onclick="<?php echo $extType.'_notes'; ?>.toggle();">
            <?php echo jgettext('Notes'); ?>
        </span>
        <?php endif; ?>
    <?php if(count($this->infoLinks[$extType])) : ?>
        <span class="btn-inverse btn<?php echo ECR_TBAR_SIZE; ?>"
              onclick="<?php echo $extType.'_links'; ?>.toggle();">
            <?php echo jgettext('See also...'); ?>
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
            <strong><?php echo jgettext('Notes'); ?></strong><br/>
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
        ?>

        <div class="btn-group jcompat_<?php echo str_replace('.', '', $template->jVersion);?>">
            <a title="<?php echo $template->info; ?>"
               href="javascript:;"
               class="btn hasTip"
               style="display: block; min-width: 160px;"
                <?php echo $action; ?>
                >
                <!--
                <i style="float: left" class="img iconjoomla-compat-<?php
                    echo str_replace('.', '', $template->jVersion); ?>"></i>
                -->
                <?php echo $template->name; ?>
            </a>

            <a title="<?php echo jgettext('Info').'::'.jgettext('Click to view files'); ?>"
               class="btn-info btn hasTip" href="javascript:;" id="btn_<?php echo $htmlId; ?>"
               onclick="getExtensionTemplateInfo(<?php echo "'$extType', '$template->folder', $htmlId"; ?>)">
                <?php echo jgettext('Info') ?>
            </a>
        </div>

        <div id="<?php echo $htmlId; ?>">
            <?php echo $template->description; ?><br/>
            <?php if($link) echo '<strong>'.jgettext('Link').'</strong> '.$link.'<br />'; ?>
            <?php if($template->author) echo '<strong>'.jgettext('Author').'</strong> '.$template->author.'<br />'; ?>
            <strong><?php echo jgettext('Joomla! version'); ?></strong>
            <?php echo $template->jVersion; ?><br/>
            <strong><?php echo jgettext('Version'); ?></strong>
            <?php echo $template->version; ?><br/>
            <strong><?php echo jgettext('PHP version'); ?></strong>
            <?php echo $template->phpVersion; ?><br/>
            <br/>
            <?php if(count($template->complements)) : ?>
            <strong><?php echo jgettext('Complements'); ?></strong>
            <ul>

            <?php foreach($template->complements as $complement) : ?>
                <li>
                    <?php echo $complement->folder.' ('.$complement->version.')'; ?>
                    <br />
                    &rArr; <tt><?php echo $complement->targetDir; ?></tt>
                </li>
            <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            <strong><?php echo jgettext('Files'); ?></strong>

            <div id="<?php echo $htmlId; ?>_files"></div>
        </div>
        <script type="text/javascript">
                <?php echo $htmlId.' = new Fx.Slide(\''.$htmlId.'\');'; ?>
                <?php echo $htmlId; ?>.hide();
        </script>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php endforeach; ?>

<div style="clear: both;"></div>

<input type="hidden" id="tpl_type" name="tpl_type"/>
<input type="hidden" id="tpl_name" name="tpl_name"/>
