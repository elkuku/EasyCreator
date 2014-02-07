<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 10-Aug-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

ecrScript('dbtables', 'parts', 'autocode', 'util');

ecrStylesheet('stuffer');

echo '<h2 style="color: red; float: right;">W I P !';
echo '</h2>';

$tableHelper = new EcrTableHelper;

$db = JFactory::getDBO();

$dbTables = $db->getTableList();
$dbPrefix = $db->getPrefix();
$dbName = JFactory::getConfig()->get('db');

$scopes = array('admin' => jgettext('Admin'), 'site' => jgettext('Site'));

$allTables = array();

$allTables = $this->project->tables;

$discoveredTables = $tableHelper->discoverTables($this->project);

foreach($discoveredTables as $table)
{
    if( ! array_key_exists($table->name, $allTables))
    {
        $allTables[$table->name] = $table;
    }
}//foreach

$tables = array();
$infoQuery = "SHOW TABLE STATUS FROM `%s` LIKE '%s';";
$columnQuery = "SHOW FULL COLUMNS FROM `%s`;";

foreach($allTables as $k => $table)
{
    $db->setQuery(sprintf($infoQuery, $dbName, $dbPrefix.$table->name));
    $status = $db->loadAssocList();

    $db->setQuery(sprintf($columnQuery, $dbPrefix.$table->name));
    $fields = $db->loadAssocList();

    $table->status =(isset($status[0])) ? $status[0] : array();

    $table->hasInstall =(array_key_exists($table->name, $discoveredTables))
    ? $discoveredTables[$table->name]->install
    : false;

    $table->isRegistered =(array_key_exists($table->name, $this->project->tables)) ? true : false;
    $table->inDB =(in_array($dbPrefix.$table->name, $dbTables)) ? true : false;

    if(count($fields))
    {
        $table->addFields($fields);
    }

    $tables[] = $table;
}//foreach

$autocodeList = EcrProjectHelper::getAutoCodeList();
?>

<input type="hidden" name="table_name" id="table_name" />

<div id="addElementMessage"></div>

<div id="addBox">
        <div class="closeButton" onclick="document.id('addBox').style.display='none';">X</div>
        <div id="addPartShow" >
            <strong style="color: red;"><?php echo jgettext('Select an element'); ?></strong>
        </div>
</div>

<div id="er_display">
<?php
$plumbs = '';
$tableCount = 0;
foreach($tables as $table) :

    if(isset($this->project->tables[$table->name]))
    {
        foreach($this->project->tables[$table->name]->getRelations() as $relation)
        {
            if($relation->onTable)
            {
                //-- C $plumbs .= "$('#table_".$table->name."').plumb({target: 'table_".$relation->onTable."'});".NL;
            }
        }//foreach
    }

    if($table->isRegistered && $table->inDB) :
        $tableCount ++;
        $fields = $db->getTableFields($dbPrefix.$table->name, false);
        ?>
	    <div class="plumbBox" id="table_<?php echo $table->name; ?>"style="float: left;
	    border: 1px solid gray; background-color: #fff; margin-left: 3em;">

	    <table cellspacing="0" cellpadding="0" width="100%">
	    <thead>

	    <tr>
    	    <th colspan="2">
    	       <div class="table_name">
        	       	<span style="color: gray;"><?php echo $dbPrefix; ?></span>
        	       	<?php echo $table->name; ?>
    	       	</div>
    	    </th>
    	    <th class="hasTip" title="<?php echo nl2br(print_r($table->status, true)); ?>">
    	    &bull;I&bull;
    	    </th>
	    </tr>

	    </thead>
	    <tbody>

	    <?php
        foreach($table->getFields() as $field) :
            echo '<tr>';
            printf('<td>%s</td><td style="color: gray;">%s</td>', $field->name, $field->type);
            echo '<th class="hasTip" style="background-color: #b2cce5;" title="'
            .nl2br(print_r($field, true)).'">&bull;I&bull;</th>';
            echo '</tr>';
        endforeach;
        ?>

	    </tbody>
	    </table>

        <?php if($table->hasInstall) : ?>
	    <table>
	    <?php
        foreach(array_keys($autocodeList) as $scope)
        {
            echo '<div class="color_'.$scope.'" style="text-align: center; font-weight: bold;">'.$scope.'</div>';

            foreach(array_keys($autocodeList[$scope]) as $name)
            {
                foreach(array_keys($autocodeList[$scope][$name]) as $element)
                {
                    $k = "$scope.$name.$element.".$table->name;

                    if(array_key_exists($k, $this->project->autoCodes))
                    {
                       echo "<div class=\"ecr_button img icon16-edit\" onclick=\"loadAutoCode('"
                       .$this->ecr_project."', 'edit', '$name', '$table->name', '$scope', '$element');\">";
                       echo $name;
                       echo '</div>'.NL;
                    }
                    else
                    {
                       echo "<div class=\"ecr_button img icon16-add\" onclick=\"loadAutoCode('"
                       .$this->ecr_project."', 'new', '$name', '".$table->name."', '$scope', '$element');\">";
                       echo $name;
                       echo '</div>'.NL;
                    }
                }//foreach
            }//foreach
        }//foreach
        ?>
	    </table>


       	<div class="color_relations" style="text-align: center; font-weight: bold;">
       		<?php echo jgettext('Relations'); ?>
       	</div>
        <?php
        if(isset($this->project->tables[$table->name])
        && $this->project->tables[$table->name]->getRelations()) :

        foreach($this->project->tables[$table->name]->getRelations() as $relation)
        {
            echo $relation->type.' '.$relation->onTable.BR
                .' ON '.$relation->field.' = '.$relation->onTable.'.'.$relation->onField.BR;

            if(count($relation->aliases))
            {
                foreach($relation->aliases as $alias)
                {
                    echo 'Alias '.$alias->alias.' = '.$relation->onTable.'.'.$alias->aliasField.BR;
                }//foreach
            }
        ?>
        <p style="text-align: right;">
        	<span class="ecr_button img icon16-edit" onclick="editRelation('<?php echo $table->name; ?>');">Edit</span>
        </p>
        <?php
        }//foreach
        endif; ?>

        <hr />
        <!--
        <div class="ecr_button img icon16-add" style="float: right;" onclick="new_relation_container.show();">
            <?php echo jgettext('Add relation') ?>
        </div>
         -->

        <div id="new_relation_container">
        <?php echo jgettext('New relation'); ?>
        <br />
        <select name="relations[<?php echo $table->name; ?>][join_type]">
        <option value=""><?php echo jgettext('Select...'); ?></option>
        <option>LEFT JOIN</option>
        <option>RIGHT JOIN</option>
        <option>INNER JOIN</option>
        </select>
        <br />

        <?php echo jgettext('Foreign table'); ?>
        <br />
        <select name="relations[<?php echo $table->name; ?>][foreign_table]"
         onchange="
         getTableFieldSelector(this.value, 'relations[<?php echo $table->name; ?>][foreign_table_field]');
         $('alias_table_<?php echo $table->name; ?>').innerHTML = this.value;
         getTableFieldSelector(this.value, 'relations[<?php echo $table->name; ?>][alias_field]');">
        <option value=""><?php echo jgettext('Select...'); ?></option>
        <?php foreach($this->project->tables as $foreignTable) :
        if($foreignTable->name != $table->name) :
            echo '<option>'.$foreignTable->name.'</option>';
            endif;
        endforeach; ?>
        </select>
        <strong>.</strong>
        <span id="relations[<?php echo $table->name; ?>][foreign_table_field]_container"></span>
        <br />
        ON
        <br />
        <?php
        echo $table->name; ?>
        <strong>.</strong>
        <select name="relations[<?php echo $table->name; ?>][own_field]">
        <option value=""><?php echo jgettext('Select...'); ?></option>
        <?php foreach($table->getFields() as $field) :
                echo '<option>'.$field->name.'</option>';
            endforeach; ?>
        </select>
        <br />

        <?php
        echo jgettext('Aliases');
        echo BR;
        echo jgettext('Name'); ?>
        <input type="text" name="relations[<?php echo $table->name; ?>][alias]" />
        <br />

        <span id="alias_table_<?php echo $table->name; ?>"></span>
        <strong>.</strong>
        <span id="relations[<?php echo $table->name; ?>][alias_field]_container"></span>
        <br />

        <p style="text-align: right;">
        	<span class="ecr_button img icon16-ecr_save" onclick="addRelation('<?php echo $table->name; ?>');">Save</span>
        </p>
        </div><!-- New relation container -->
        <script type="text/javascript">
        //	new_relation_container = new Fx.Slide('new_relation_container');
        //	new_relation_container.hide();
        </script>

        <?php endif; ?>
	    </div>
	    <?php
    endif;
endforeach;
?>
<div style="clear: both"></div>
</div>
<?php if( ! $tableCount) : ?>
<h2><?php echo jgettext('No tables to design'); ?></h2>
<?php endif; ?>
<table class="adminlist">
<thead>
    <tr>
        <th><?php echo jgettext('Name'); ?></th>
        <th width="5%"><?php echo jgettext('Is registered'); ?></th>
        <th width="5%"><?php echo jgettext('In DB'); ?></th>
        <th width="5%"><?php echo jgettext('Has install'); ?></th>
        <th width="5%"><?php echo jgettext('Info'); ?></th>
    </tr>
</thead>
<tbody>
<?php

    foreach($tables as $table)
    {
        $k = 1 - $k;
?>
    <tr class="row<?php echo $k; ?>">
        <td><?php echo $table->name; ?></td>
        <td>
            <?php
            echo $table->isRegistered;

            if( ! $table->isRegistered)
            {
                echo '<div id="res-'.$table->name.'"></div>';
                echo '<div class="ecr_button img icon16-add" id="addbtn-'.$table->name.'"';
                echo " onclick=\"document.adminForm.table_name.value='$table->name';"
                 ."submitbutton('register_table');\">";
                //registerTable('$this->ecr_project', '$table->name', 'res-$table->name');\">";
                echo jgettext('Register').'</div>';
            }
            ?>
        </td>
        <td>
            <?php
            echo $table->inDB;

            if( ! $table->inDB)
            {
                echo '</div><div class="ecr_button img icon16-add" onclick="createTable(\''
                .$table->name.'\', \'res-'.$table->name.'\');">CREATE</div>';
            }
            ?>
         </td>
             <?php
             if($table->inDB && $table->isRegistered)
             {
/*
                 foreach($tableHelper->types as $typeName=>$s)
                 {
                     echo '<td>';
                     foreach($scopes as $sType => $sName)
                     {
                        if( in_array($sType, $table->$typeName) )
                        {
                            echo "<div class=\"ecr_button img icon16-edit\"
 onclick=\"loadPart('".$this->ecr_project."', 'edit', '$typeName', '$table->name', '$sType');\">$sName</div>".NL;
                        }
                        else
                        {
                            echo "<div class=\"ecr_button img icon16-add\"
 onclick=\"loadPart('".$this->ecr_project."', 'new', '$typeName', '$table->name', '$sType');\">$sName</div>".NL;
                        }
                     }//foreach
                     echo '</td>';
                 }//foreach
*/
             }
             else
             {
//-- C                 #echo str_repeat('<td>&nbsp;</td>', count($tableHelper->types));
             }
             ?>
        <td><?php echo $table->hasInstall; ?></td>
        <td><div class="hasTip" title="<?php echo $table->name; ?> Info::<pre><?php
        print_r($table->status); ?></pre>">INFO</div></td>
    </tr>
<?php
    }//foreach
?>
</tbody>
</table>

<table width="100%">
    <tr valign="top">
        <td width="10%" nowrap="nowrap">

                    <div class="t">
                <div class="t">
                    <div class="t"></div>
                </div>
            </div>

            <div class="m">
<h2><?php echo jgettext('Register Table'); ?></h2>
<select name="register_tbl">
<option><?php echo jgettext('Select'); ?></option>
<?php
foreach($dbTables as $table)
{
    $s = str_replace($db->getPrefix(), '', $table);
    if(array_key_exists($s, $this->project->tables)) continue;

    echo '<option>'.$s.'</option>';
}//foreach
?>
</select>
<a class="ecr_button" href="javascript:;" onclick="submitform('register_table');">
	<?php echo jgettext('Register'); ?>
</a>
<h2><?php echo jgettext('New Table'); ?></h2>
@todo muss per js eingefügt werden, da sich table_name überschneidet..
<!--
<table>

    <tr>
        <th><?php echo jgettext('Name'); ?></th>
        <td><?php echo $dbPrefix; ?> <input type="text" name="table_name" /></td>
    </tr>
    <tr valign="top">
        <th><?php echo jgettext('Fields'); ?></th>
        <td>
        <div id="addField"></div>
        <br />
<div onclick="addField();" class="ecr_button"><?php echo jgettext('Add Field'); ?></div>
        </td>
    </tr>

    <tr>
    <td colspan="2"><hr /></td>
    </tr>

    <tr>
        <td colspan="2" class="ecr_button" onclick="submitform('createTable');"><?php echo jgettext('CREATE Table'); ?></td>
    </tr>
</table>
 -->
           </div>
            <div class="b">
                <div class="b">
                    <div class="b"></div>
                </div>
            </div>

</td>
<td>&nbsp;</td>
<td width="10%" nowrap="nowrap">
</td>
</tr>
</table>

<input type="hidden" name="old_task" value="tables" />
<?php

ECR_DEBUG ? EcrDebugger::varDump($this->project) : null;
