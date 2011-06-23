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

$InstallFile = null;
$unInstallFile = null;
//#var_dump($this->project->tables);

?>

<?php echo ecrHTML::floatBoxStart(); ?>
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
            if(strpos($file->name, 'install') === 0) $InstallFile = $file;
            if(strpos($file->name, 'uninstall') === 0) $unInstallFile = $file;
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
	<?php endif; ?>
	<?php if( ! $unInstallFile) : ?>
<div class="ecr_button img icon-16-add"
	onclick="createFile('sql', 'uninstall')"><?php echo jgettext('Create uninstall file'); ?></div>
	<?php endif; ?>
	<?php if($InstallFile) : ?>
<h3><?php echo jgettext('Install SQL analysis'); ?></h3>
<?php
$path = JPATH_ADMINISTRATOR.DS.'components'.DS.$this->project->comName;
$path .=($InstallFile->folder) ? DS.$InstallFile->folder : '';
$path .= DS.$InstallFile->name;
//#echo $path;
$sqlInstall = JFile::read($path);
echo '<pre>'.$sqlInstall.'</pre>';
echo '<h4>Generated :</h4>';
echo count($this->project->tables).' tables'.BR;
$db = JFactory::getDBO();
$columnQuery = "SHOW FULL COLUMNS FROM `%s`;";
var_dump($this->project->tables);

foreach($this->project->tables as $table)
{
    if('true' == $table->foreign)
    continue;

    echo '<h4>'.$table->name.'</h4>';

    if( ! count($table->getFields()))
    {
        $table->addFields(EasyTableHelper::getTableFieldsNew($table->name));
//#	        $fields = $db->getTableFields($db->getPrefix().$table->name);
//#	        var_dump($fields);
//    $db->setQuery(sprintf($columnQuery, $db->getPrefix().$table->name));//, $dbPrefix.$table->name));
//    $fields = $db->loadAssocList();
//#	        var_dump($fields);
//	        foreach($fields as $field)
//	        {
//	        	$table->addField(new EasyTableField($field));
//	        }//foreach
    }

//	    $ret = '';
//	    $indent = '';
//	    $started = false;
//	    foreach($table->getFields() as $field)
//	    {
//	        $ret .=($started) ? $indent.', ' : $indent.'  ';
//	        $started = true;
//	        $ret .= EasyTableHelper::formatSqlField($field);
//	        $ret .= NL;
//	    }//foreach
//	    var_dump($ret);

        $cc = EasyTableHelper::getTableCreate($table);
        var_dump($cc);

//        #	var_dump($table);
        $createString = $db->getTableCreate($db->getPrefix().$table->name);
//	    var_dump( $createString);
    }//foreach


?>
<?php endif; ?>

<?php echo ecrHTML::floatBoxEnd(); ?>
<input type="hidden" name="type1" id="type1" />
<input type="hidden" name="type2" id="type2" />