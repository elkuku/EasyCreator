<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 09-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/*
 * Context menu
 */
//-- Add css
ecrStylesheet('contextmenu');

//-- Add javascript
ecrScript('contextmenu', 'templates');

JHTML::_('behavior.modal', 'a.ecr_modal');

//-- Allowed extensions
//-- TODO set somewhere else...
$allowed_exts = array('php', 'css', 'xml', 'js', 'ini', 'txt', 'html', 'sql');
$allowed_pics = array('png', 'gif', 'jpg', 'ico');

ecrLoadMedia('php_file_tree');
$fileTree = new EcrFileTree;

$js = '';
$js .= " onmousedown=\"setAction(event, '[folder]', '[file]', '[id]');\"";
$js .= " onclick=\"ecr_loadFile('templates', '[folder]', '[file]', '[id]');\"";

$fileTree->setJs('file', $js);
$fileTree->setJs('folder', " onmousedown=\"setAction(event, '[folder]', '[file]');\"");
?>

<table width="100%">
    <tr valign="top">
        <td width="20%">
            <div class="ecr_floatbox">
                <span style="float: right;" class="img icon16-info hasTip"
                      title="<?php echo jgettext('File tree').'::'.jgettext('Left click files to edit.').'<br />'.jgettext('Right click files and folders for options.'); ?>">&nbsp;</span>
                <?php echo $fileTree->startTree(); ?>
                <ul>
                    <li class="pft-directoryX">
                        <div style="font-size: 1.3em;">
                            <?php echo jgettext('Extension templates'); ?>
                        </div>
                        <ul>
                            <?php
                            /* @var EcrProjectBase $pType */
                            foreach($this->comTypes as $pTag => $pType):
                                if(false == isset($this->templates[$pTag])):
                                    continue;
                                endif;

                                $path = str_replace(JPATH_ROOT.DS, '', $this->path);
                                $js = " onmousedown=\"setAction('', '".$path."', '".$pTag."');\"";

                                ?>
                                <li class="pft-directory">
                                    <div<?php echo $js; ?>>
                                        <?php echo $pType->translateTypePlural(); ?>
                                    </div>
                                    <ul>
                                        <?php
                                        foreach($this->templates[$pTag] as $template):
                                            $js = " onmousedown=\"setAction('', '".$path.DS.$pTag."', '".$template->folder."');\"";
                                            echo '<li class="pft-directory">';
                                            echo '<div'.$js.' class="hasTip" title="'.$template->info.'">';
                                            echo '<span class="img icon16-info" />'.$template->name;
                                            echo '</div>';
                                            echo $fileTree->setDir($this->path.DS.$pTag.DS.$template->folder)->drawTree();
                                            echo '</li>';
                                        endforeach;
                                        ?>
                                    </ul>
                                </li>
                                <?php
                            endforeach;
                            ?>
                        </ul>
                    </li>
                </ul>
                <ul>
                    <li class="pft-directoryX">
                        <div style="font-size: 1.3em;">
                            <?php echo jgettext('Extension templates parts'); ?>
                        </div>
                        <ul>
                            <?php
                            foreach(EcrProjectHelper::getPartsGroups() as $group):
                                $js = " onmousedown=\"setAction('', '".$path.DS."parts', '".$group."');\"";
                                ?>
                                <li class="pft-directory"><?php echo '<div'.$js.'>'.jgettext($group).'</div>'; ?>
                                    <ul>
                                        <?php
                                        foreach(EcrProjectHelper::getParts($group) as $part):
                                            $easyPart = EcrProjectHelper::getPart($group, $part, '', '');

                                            if($easyPart):
                                                $toolTip = $group.'::'.$part;
                                                $title = $part;
                                                if(method_exists($easyPart, 'info')):
                                                    $info = $easyPart->info();
                                                    $title = $info->title;
                                                    $toolTip = $info->title;
                                                    if($info->description):
                                                        $toolTip .= '::'.$info->description;
                                                    endif;
                                                endif;

                                                $js = " onmousedown=\"setAction('', '".$path.DS.'parts'.DS.$group."', '".$part."');\"";
                                                echo '<li class="pft-directory">';
                                                echo '<div'.$js.' class="hasTip" title="'.$toolTip.'">';
                                                echo '<span class="img icon16-info"/>'.$title;
                                                echo '</div>';
                                                echo $fileTree->setDir(ECRPATH_PARTS.DS.$group.DS.$part)->drawTree();
                                                echo '</li>';
                                            endif;
                                        endforeach;
                                        ?>
                                    </ul>
                                </li>
                                <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>
                <ul>
                    <li class="pft-directory">
                        <div style="font-size: 1.3em;">
                            <?php echo jgettext('Standard files'); ?>
                        </div>
                            <?php
                            echo $fileTree->setDir(ECRPATH_EXTENSIONTEMPLATES.DS.'std')->drawTree();
                            ?>
                    </li>
                </ul>
                <?php echo $fileTree->endTree(); ?>
            </div>

            <div class="ecr_floatbox">
                <h3><?php echo jgettext('Template variables'); ?></h3>

                <h4><?php echo jgettext('Common variables')?></h4>
                <ul class="unstyled">
                    <?php foreach(EcrProjectTemplateHelper::getReplacementInfo() as $infoKey => $infoText) : ?>
                    <li>
                        <?php
                            echo ('' == $infoText)
                            ? '<strong>'
                            : '<strong class="hasTip img icon16-info" title="'.$infoKey.'::'.$infoText.'">';
                        ?>
                        <?php echo $infoKey.'</strong>'; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <hr />
                <h4><?php echo jgettext('Custom variables')?></h4>
                <?php echo jgettext('Custom variables may be defined in every template using the file options.php'); ?>
            </div>
        </td>
        <td>
            <?php EcrHtml::prepareFileEdit(); ?>
        </td>
    </tr>
</table>

<input type="hidden" name="com_type" value="<?php echo $this->com_type; ?>"/>
<input type="hidden" name="template" value="<?php echo $this->template; ?>"/>
<input type="hidden" name="old_task" value="templates"/>
<input type="hidden" name="old_controller" value="templates"/>

<?php
EcrHtmlContextmenu::display();
