<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 06-Apr-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');
//<div style="clear: both;"></div>
?>
<?php
$InstallFile = null;
$unInstallFile = null;
//#var_dump($this->project->tables);

ecrLoadHelper('dqml2tree');
ecrLoadHelper('sqlparser');
ecrLoadHelper('SQL.Parser');

ecrLoadHelper('dbupdater');

$paths = array(
'1.0' => JPATH_COMPONENT.'/test/install1.sql'
, '1.1' => JPATH_COMPONENT.'/test/install2.sql'
, '1.2' => JPATH_COMPONENT.'/test/install3.sql'
);

//$updater = new dbUpdater($paths);
//$files = $updater->parseFiles();

//var_dump($files);

/*
$totalCountSql = SqlParser::ParseString( $contents, false );// -> getCountQuery();
//$totalCountSql = SqlParser::Tokenize( $contents, false );// -> getCountQuery();

//$result = YourDbAbstractionLayer->ExecuteQuery($totalCountSQL);

//$query2tree = new dqml2tree($contents);
//$sql_tree = $query2tree->make();

echo '<pre>'.print_r($totalCountSql, 1).'</pre>';
*/
?>
<div class="ecr_floatbox">
<table>
	<tr>
		<th colspan="2" class="infoHeader imgbarleft icon-24-install"><?php echo jgettext('Install and Uninstall SQL') ?>
		</th>
	</tr>
	<?php if(count($this->installFiles['sql'])) :?>
    	<tr>
    		<th><?php echo jgettext('Folder'); ?></th>
    		<th><?php echo jgettext('Name'); ?></th>
    	</tr>
    	<?php foreach($this->installFiles['sql'] as $file) : ?>
        	<?php
            if(strpos($file->name, 'install') === 0)
            $InstallFile = $file;

            if(strpos($file->name, 'uninstall') === 0)
            $unInstallFile = $file;
            ?>
        	<tr>
        		<td style="background-color: #cce5ff;"><?php echo $file->folder; ?></td>
        		<td><?php echo $file->name; ?></td>
        	</tr>
    	<?php endforeach; ?>
	<?php else : ?>
    	<tr>
    		<td colspan="2" style="color: orange;"><?php echo jgettext('Not found'); ?></td>
    	</tr>
	<?php endif; ?>
</table>

	<?php if( ! $InstallFile) : ?>
<div class="ecr_button img icon-16-add"
	onclick="createFile('sql', 'install');"><?php echo jgettext('Create install file'); ?>
</div>
	<?php else : ?>
<div class="ecr_button img icon-16-update" onclick="createFile('sql', 'install');">
    <?php echo jgettext('Update install file'); ?>
</div>
	<?php endif; ?>

	<?php if( ! $unInstallFile) : ?>
<div class="ecr_button img icon-16-add" onclick="createFile('sql', 'uninstall')">
    <?php echo jgettext('Create uninstall file'); ?>
</div>
	<?php else : ?>
<div class="ecr_button img icon-16-update" onclick="createFile('sql', 'uninstall')">
    <?php echo jgettext('Update uninstall file'); ?>
</div>
	<?php endif; ?>

</div>
<input type="hidden" name="type1" id="type1" />
<input type="hidden" name="type2" id="type2" />

