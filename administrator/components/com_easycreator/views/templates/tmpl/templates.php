<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 09-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/*
 * Context menu
 */
//--Add css
ecrStylesheet('contextmenu');

//--Add javascript
ecrScript('contextmenu');
ecrScript('templates');

JHTML::_('behavior.modal');

//--Allowed extensions
//TODO set somewhere else...
$allowed_exts = array('php', 'css', 'xml', 'js', 'ini', 'txt', 'html', 'sql');
$allowed_pics = array('png', 'gif', 'jpg', 'ico');

ecrHTML::initFileTree();
$fileTree = new phpFileTree();

$js = '';
$js .= " onmousedown=\"setAction(event, '[folder]', '[file]', '[id]');\"";
$js .= " onclick=\"ecr_loadFile('templates', '[folder]', '[file]', '[id]');\"";

$fileTree->setJs('file', $js);
$fileTree->setJs('folder', " onmousedown=\"setAction(event, '[folder]', '[file]');\"");
?>

<table width="100%">
	<tr valign="top">
		<td width="20%">
		<?php ecrHTML::floatBoxStart(); ?>
			<span style="float: right;" class="img icon-16-info hasEasyTip" title="<?php echo jgettext('File tree').'::'.jgettext('Left click files to edit.').'<br />'.jgettext('Right click files and folders for options.'); ?>">&nbsp;</span>
		<?php echo $fileTree->startTree(); ?>
		<ul>
			<li class="pft-directoryX">
				<div style="font-size: 1.3em;">
					<?php echo jgettext('Extension templates'); ?>
				</div>
				<ul>
				    <?php
                    foreach($this->comTypes as $com_type => $comName):
                        if( ! isset($this->templates[$com_type])):
                            continue;
                        endif;

                        $path = str_replace(JPATH_ROOT.DS, '', $this->path);
                        $js = " onmousedown=\"setAction('', '".$path."', '".$com_type."');\"";

                        ?>
						<li class="pft-directory">
							<div<?php echo $js; ?>>
							    <?php echo $comName; ?>
							</div>
							<ul>
<?php
                            foreach($this->templates[$com_type] as $template):
                                $fileTree->setDir($this->path.DS.$com_type.DS.$template->folder);
                                $js = " onmousedown=\"setAction('', '".$path.DS.$com_type."', '".$template->folder."');\"";
                                echo '<li class="pft-directory">';
                                echo '<div'.$js.' class="hasEasyTip" title="'.$template->info.'">';
                                echo '<span class="img icon-16-info" />'.$template->name;
                                echo '</div>';
                                echo $fileTree->drawTree();
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
                foreach(EasyProjectHelper::getPartsGroups() as $group):
                    $js = " onmousedown=\"setAction('', '".$path.DS."parts', '".$group."');\"";
                    ?>
					<li class="pft-directory"><?php echo '<div'.$js.'>'.jgettext($group).'</div>'; ?>
						<ul>
<?php
                        foreach(EasyProjectHelper::getParts($group) as $part):
                            if($easyPart = EasyProjectHelper::getPart($group, $part, '', '')):
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
                                $fileTree->setDir(ECRPATH_PARTS.DS.$group.DS.$part);
                                echo '<li class="pft-directory">';
                                echo '<div'.$js.' class="hasEasyTip" title="'.$toolTip.'">';
                                echo '<span class="img icon-16-info"/>'.$title;
                                echo '</div>';
                                echo $fileTree->drawTree();
                                echo '</li>';
                            endif;
                        endforeach;
?>
						</ul>
					</li>
<?php			endforeach; ?>
				</ul>
			</li>
		</ul>
		<ul>
			<li class="pft-directoryX">
				<div style="font-size: 1.3em;">
					<?php echo jgettext('Standard files'); ?>
				</div>
				<ul>
				<?php
                $fileTree->setDir(ECRPATH_EXTENSIONTEMPLATES.DS.'std');
                echo $fileTree->drawTree();
                ?>
				</ul>
			</li>
		</ul>
        <?php echo $fileTree->endTree(); ?>
        <?php ecrHTML::floatBoxEnd(); ?>
		</td>
		<td>
			<?php ecrHTML::prepareFileEdit(); ?>
		</td>
	</tr>
</table>

<input type="hidden" name="com_type" value="<?php echo $this->com_type; ?>" />
<input type="hidden" name="template" value="<?php echo $this->template; ?>" />
<input type="hidden" name="old_task" value="templates" />
<input type="hidden" name="old_controller" value="templates" />

<?php
echo ecrHTML::contextMenu();
