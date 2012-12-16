<?php
/**
 * @package    EasyCreator
 * @subpackage Parts
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 20-Apr-2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class PartTablesAdmin_map
{
    public $group = 'tables';

    private $logging = false;

    private $logger = null;

    /**
     * Info about the thing.
     *
     * @return EcrProjectTemplateInfo
     */
    public function info()
    {
        $info = new EcrProjectTemplateInfo;

        $info->group = $this->group;
        $info->title = 'Map and Admin';
        $info->description = jgettext('This will map an existing table and create an admin interface and menu link');

        return $info;
    }//function

    /**
     * Get insert options.
     *
     * @return void
     */
    public function getOptions()
    {
        $db = JFactory::getDBO();
        $tables = $db->getTableList();

        $ajaxLink = 'index.php?option=com_easycreator&tmpl=component&controller=ajax&task=part_task';
        $ajaxLink .= '&group=tables&part=admin_map';
        $ajaxLink .= '&part_task=show_tablefields';

        $ajax = "new Request({ url: '".$ajaxLink
        ."'+'&table_name='+this.value, update:'addPartTableFields', onComplete: function() '
        .'{ div_new_element.show(); } }).send();";

        echo '<strong id="table_name_label">'.jgettext('Table').' :</strong>';
        echo '<select name="table_name" id="table_name" onchange="'.$ajax.'">';
        echo '<option value="">'.jgettext('Choose').'...</option>';

        $prefix = $db->getPrefix();

        foreach($tables as $table)
        {
            $v = str_replace($prefix, '', $table);
            echo '<option value="'.$v.'">'.$v.'</option>';
        }//foreach
        echo '</select>';

        echo '<div id="addPartTableFields"></div>';

        EcrHtmlSelect::name();

        echo '<strong>'.jgettext('Menu link').' :</strong><br />';
        echo '<input type="checkbox" name="create_menu_link" checked="checked"'
        .' id="create_menu_link" value="create_menu_link">';

        echo '<label for="create_menu_link">'.jgettext('Create a menu link').'</label><br />';
        echo '<hr />';

        EcrHtmlOptions::logging();

        $requireds = array('element_name', 'table_name');
        EcrHtmlButton::submitParts($requireds);
    }//function

    /**
     * Shows the fields of a given table.
     *
     * AJAX called
     *
     * @return void
     */
    public function show_tablefields()
    {
        $table_name = JFactory::getApplication()->input->get('table_name');

        if( ! $table_name)
        {
            return;
        }

        $db = JFactory::getDBO();

        $table_name = $db->getPrefix().$table_name;
        $fields = $db->getTableColumns($table_name);

        if( ! count($fields) || ! count($fields[$table_name]))
        {
            JFactory::getApplication()->enqueueMessage(jgettext('No table fields found'), 'error');
        }

        ?>
<table>
    <tr>
        <th colspan="2"><?php echo jgettext('Include')?></th>
        <th><?php echo jgettext('Editable'); ?></th>
        <th><?php echo jgettext('Type'); ?></th>
    </tr>
    <?php
    foreach($fields[$table_name] as $key => $value)
    {
        switch($value)
        {
            case 'int':
            case 'tinyint':
                $def = '0';
                break;

            default:
                $def = 'NULL';
                break;
        }//switch

        $javascript1 = "$('tblfield_edit_".$key."').disabled=(this.checked)?false:true;";
        $javascript2 = "$('tblfield_type_".$key."').disabled=(this.checked && $('tblfield_edit_"
        .$key."').checked)?false:true;";

        $javascript = $javascript1.$javascript2;
        ?>
    <tr>
        <td><input type="checkbox" onchange="<?php echo $javascript; ?>"
            name="table_fields[]" checked="checked"
            id="tblfield_<?php echo $key; ?>"
            value="<?php echo $key; ?>">
            </td>
        <td><label for="tblfield_<?php echo $key; ?>">
         <?php echo $key.'<br />('.$value.')'; ?>
        </label></td>
        <td><input type="checkbox" onchange="<?php echo $javascript2; ?>"
            name="table_fields_edits[]" checked="checked"
            id="tblfield_edit_<?php echo $key; ?>"
            value="<?php echo $key; ?>"></td>
        <td><select name="table_fields_types[<?php echo $key; ?>]"
            id="tblfield_type_<?php echo $key; ?>">
            <option>text</option>
            <option>text area</option>
            <option>radio bool</option>
            <option>html</option>
        </select></td>
    </tr>
    <?php
    }//foreach
    ?>
</table>
    <?php
    }//function

    /**
     * Inserts the part into the project.
     *
     * @param EcrProjectBase $project The project.
     * @param array $options Insert options.
     * @param EcrLogger $logger The EcrLogger.
     *
     * @return boolean
     */
    public function insert(EcrProjectBase $project, $options, EcrLogger $logger)
    {
        $input = JFactory::getApplication()->input;

        $element_name = $input->get('element_name');
        $table_name = $input->get('table_name');
        $req_table_fields = $input->get('table_fields', array(), 'array');
        $req_table_fields_edits = $input->get('table_fields_edits', array(), 'array');
        $req_table_fields_types = $input->get('table_fields_types', array(), 'array');

        if( ! $table_name)
        {
            JFactory::getApplication()->enqueueMessage(jgettext('No table given'), 'error');

            return false;
        }

        $db = JFactory::getDBO();
        $prefix = $db->getPrefix();
        $fields = $db->getTableFields($prefix.$table_name);

        $tableFields = array();
        $table_vars = '';

        if(count($fields))
        {
            $tableFields = $fields[$prefix.$table_name];

            foreach($tableFields as $key => $value)
            {
                if( ! in_array($key, $req_table_fields))
                continue;

                $table_vars .= EcrTableHelper::formatTableVar($key, $value);
            }//foreach
        }

        /*
         * Add substitutes
         */
        $project->addSubstitute('ECR_ELEMENT_NAME', $element_name);
        $project->addSubstitute('_ECR_LOWER_ELEMENT_NAME_', strtolower($element_name));
        $project->addSubstitute('_ECR_TABLE_NAME_', $table_name);
        $project->addSubstitute('##ECR_TABLE_VARS##', $table_vars);

        /*
         * Read part options files
         */
        $files = JFolder::files($options->pathSource.DS.'options', '.', true, true);

        foreach($files as $file)
        {
            $fileContents = JFile::read($file);

            if(strpos($fileContents, '<?php') === 0)
            {
                $fileContents = substr($fileContents, 6);
            }

            $project->substitute($fileContents);
            $project->addSubstitute('##'.strtoupper(JFile::stripExt(JFile::getName($file))).'##', $fileContents);
        }//foreach

        /*
         * Add manual substitutes
         */
        $substitutes['##ECR_VIEW1_TMPL1_THS##'] = '?>';
        $substitutes['##ECR_VIEW1_TMPL1_TDS##'] = '?>';
        $substitutes['##ECR_VIEW2_TMPL1_OPTION2##'] = '?>';

        $i = 0;

        foreach($tableFields as $key => $value)
        {
            if( ! in_array($key, $req_table_fields))
            continue;

            $substitutes['##ECR_VIEW1_TMPL1_THS##'] .= '    <th>'.NL;
            $substitutes['##ECR_VIEW1_TMPL1_THS##'] .= "        <?php echo JHTML::_('grid.sort', '"
            .$key."', '".$key."', \$this->lists['order_Dir'], \$this->lists['order']);?>".NL;

            $substitutes['##ECR_VIEW1_TMPL1_THS##'] .= '    </th>'.NL;

            $substitutes['##ECR_VIEW1_TMPL1_TDS##'] .= '    <td>'.NL;
            $substitutes['##ECR_VIEW1_TMPL1_TDS##'] .= '        <?php echo $row->'.$key.'; ?>'.NL;
            $substitutes['##ECR_VIEW1_TMPL1_TDS##'] .= '    </td>'.NL;

            if(in_array($key, $req_table_fields_edits))
            {
                $s = $project->getSubstitute('##ECR_VIEW2_TMPL1_OPTION2VAL##');
                $s = str_replace('_ECR_FIELDVALUE_1_', $key, $s);
                $s = str_replace('_ECR_FIELDVALUE_2_', $req_table_fields_types[$key], $s);
                $substitutes['##ECR_VIEW2_TMPL1_OPTION2##'] .= $s;
            }

            $i ++;
        }//foreach
        $substitutes['##ECR_VIEW1_TMPL1_THS##'] .= '    <?php $coloumnCount += '.$i.'; ?>'.NL;

        $substitutes['##ECR_VIEW1_TMPL1_THS##'] .= '    <?php '.NL;
        $substitutes['##ECR_VIEW1_TMPL1_TDS##'] .= '    <?php '.NL;
        $substitutes['##ECR_VIEW2_TMPL1_OPTION2##'] .= '    <?php '.NL;

        foreach($substitutes as $key => $value)
        {
            $project->addSubstitute($key, $value);
        }//foreach

        /*
         * Remove options
         */
        if( ! in_array('ordering', $req_table_fields))
        {
            $project->addSubstitute('##ECR_CONTROLLER1_OPTION1##', '');
        }

        if( ! in_array('published', $req_table_fields))
        {
        }

        $project->addSubstitute('ECR_SUBPACKAGE', 'Tables');

        $input->set('element_scope', 'admin');

        if( ! $project->insertPart($options, $logger))
        {
            return false;
        }

        /*
         * Create menu link
         */
        if($input->get('create_menu_link', false))
        {
            $link = 'option='.$options->ecr_project.'&view='.strtolower($element_name).$project->listPostfix
            .'&controller='.strtolower($element_name).$project->listPostfix;

            if( ! $project->addSubmenuEntry($element_name, $link))
            {
                JFactory::getApplication()->enqueueMessage(jgettext('Unable to create menu link'), 'error');

                return false;
            }
        }

        return true;
    }//function
}//class
