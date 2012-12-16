<?php
/**
 * @package    EasyCreator
 * @subpackage Parts
 * @author      Nikolai Plath (elkuku)
 * @author      Created on 01-Sep-2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class PartViewsData_form extends EcrProjectPart
{
    public $group = 'views';

    public $name = 'data_form';

    private $patterns = array();

    private $keys = array();

    private $fieldsOptions = array();

    /**
     * Constructor.
     *
     * @param string $element Element name
     * @param string $scope Scope name
     * @param string $basePath The base path
     */
    public function __construct($element, $scope, $basePath = '')
    {
        $this->_element = $element;
        $this->_scope = $scope;
        $this->basePath = $basePath;

        parent::__construct($this->group, $this->name, $element, $scope);

        //-- Read the files in /options folder
        $options = JFolder::files($this->basePath.DS.'tmpl'.DS.'options');

        foreach($options as $fName)
        {
            $fContents = JFile::read($this->basePath.DS.'tmpl'.DS.'options'.DS.$fName);
            $key = JFile::stripExt($fName);
            $this->fieldsOptions[$key] = $fContents;
        }//foreach

        $this->keys['##ECR_OPTIONS##'] = '__ECR_KEY__';
        /*
//#        $this->keys['##ECR_VIEW1_TMPL1_TDS##'] = '##ECR_KEY##';
//        $this->patterns['##ECR_VIEW1_TMPL1_THS##'] = '    <th>'.NL
//           ."        <?php echo JHTML::_('grid.sort', '##ECR_KEY##',
// *  '##ECR_KEY##', \$this->lists['order_Dir'], \$this->lists['order']);?>".NL
//           .'    </th>'.NL;
//        $this->patterns['##ECR_VIEW1_TMPL1_TDS##'] = '    <td>'.NL
//            .'        <php echo $row->##ECR_KEY##; ?>'.NL
//            .'    </td>'.NL;
 * */
    }//function
    /**
     * Info about the thing.
     *
     * @return EcrProjectTemplateInfo
     */
    public function info()
    {
        $info = new EcrProjectTemplateInfo;

        $info->group = $this->group;
        $info->title = 'Data Form';
        $info->description = jgettext('A simple, empty, model');

        return $info;
    }//function

    /**
     * Get insert options.
     *
     * @return void
     */
    public function getOptions()
    {
	    $input = JFactory::getApplication()->input;

        /* Array with required fields */
        $requireds = array();

        $tableName = $input->get('element');

        $db = JFactory::getDBO();
        $tables = $db->getTableList();

        $requireds[] = EcrHtmlSelect::name($tableName);
        $requireds[] = EcrHtmlSelect::scope($this->_scope);

        if( ! $tableName)
        {
            $ajaxLink = 'index.php?option=com_easycreator&tmpl=component&controller=ajax&task=part_task';
            $ajaxLink .= '&group=views&part=data_list';
            $ajaxLink .= '&part_task=show_tablefields';

            $ajax = "new Request({ url: '".$ajaxLink."'+'&element='+this.value, '
            .' update:'addPartTableFields', onComplete: function() { div_new_element.show(); } }).send();";

            echo '<strong id="table_name_label">'.jgettext('Table').' :</strong>';
            echo '<select name="table_name" id="table_name" onchange="'.$ajax.'">';
            echo '<option value="">'.jgettext('Choose...').'</option>';

            $prefix = $db->getPrefix();

            foreach($tables as $table)
            {
                $v = str_replace($prefix, '', $table);
                echo '<option value="'.$v.'">'.$v.'</option>';
            }//foreach
            echo '</select>';

            echo '<div id="addPartTableFields"></div>';
        }
        else
        {
            $this->show_tablefields($tableName);
            echo '<input type="hidden" name="table_name" value="'.$tableName.'" />';
        }

//        echo '<strong>'.jgettext('Menu link').' :</strong><br />';
//        echo '<input type="checkbox" name="create_menu_link"
//checked="checked" id="create_menu_link" value="create_menu_link">';
//        echo '<label for="create_menu_link">'.jgettext('Create a menu link').'</label><br />';
        echo '<hr />';

        EcrHtmlOptions::logging();

        EcrHtmlButton::submitParts($requireds);
    }//function

    /**
     * Inserts the part into the project.
     *
     * @param EcrProjectBase $EasyProject The project.
     * @param array $options Insert options.
     * @param EcrLogger $logger The EcrLogger.
     *
     * @return boolean
     */
    public function insert(EcrProjectBase $EasyProject, $options, EcrLogger $logger)
    {
	    $input = JFactory::getApplication()->input;

        $this->_options = $options;

        $EasyProject->addSubstitute('ECR_SUBPACKAGE', 'Views');

        $element_name = $input->get('element_name');
        $table_name = $input->get('table_name');
        $req_table_fields = $input->get('table_fields', array(), 'array');
        $req_table_fields_types = $input->get('table_fields_types', array(), 'array');

        if( ! $table_name)
        {
            $table_name = $element_name;
        }

        if( ! $table_name)
        {
            $table_name = $element_name;
            JFactory::getApplication()->enqueueMessage(jgettext('No table given'), 'error');

            return false;
        }

        $table_name = strtolower($table_name);

        $db = JFactory::getDBO();
        $prefix = $db->getPrefix();
        $fields = $db->getTableFields($prefix.$table_name);

        $tableFields = array();
        $table_vars = '';

        if(count($fields))
        {
            $tableFields = $fields[$prefix.$table_name];
        }

        /*
         * Add substitutes
         */
        $EasyProject->addSubstitute('ECR_ELEMENT_NAME', $element_name);
        $EasyProject->addSubstitute('_ECR_LOWER_ELEMENT_NAME_', strtolower($element_name));
        $EasyProject->addSubstitute('_ECR_TABLE_NAME_', $table_name);

        /*
         * Add manual substitutes
         */
//        $substitutes = array();
//        foreach($this->patterns as $pKey => $pReplacement)
//        {
//            $substitutes[$pKey] = '';
//        }//foreach

        $code = '';
        $acFields = array();

        foreach($req_table_fields as $field)
        {
            $f = new stdClass;
            $f->name = $field;
            $f->type = $req_table_fields_types[$field];

            $code .= str_replace('_ECR_KEY_', $f->name, $this->fieldsOptions[$f->type]);

$acFields[] = $f;

//            $acOptions[$field] = array();
//            $acOptions[$field]['type'] = $req_table_fields_types[$field];
        }//foreach

//        foreach($tableFields as $key => $value)
//        {
//            if( ! in_array($key, $req_table_fields)) { continue; }
//
//            foreach($this->patterns as $pKey => $pReplacement)
//            {
//                $substitutes[$pKey] .= str_replace('##ECR_KEY##', $key, $pReplacement);
//            }//foreach
//            $i ++;
//        }//foreach

 /*       $substitutes['##ECR_VIEW1_TMPL1_THS##'] .= '    <?php $coloumnCount += '.$i.'; ?>'.NL;
*/
        $AutoCode = new EcrProjectAutocode($this->group, $this->name, $table_name, $this->_scope);
//        $AutoCode->group = $this->group;
//        $AutoCode->name = $this->name;
//
//        $AutoCode->element = $table_name;
//        $AutoCode->scope = $this->_scope;

        $AutoCode->options = array();//$acOptions;

//        foreach($substitutes as $key => $value)
//        {
$key = '##ECR_OPTIONS##';
            $AutoCode->fields[$key] = $acFields;//array();
            $AutoCode->codes[$key] = $AutoCode->enclose($code, $key, true);

            $EasyProject->addSubstitute($key, $AutoCode->enclose($code, $key, true));
//        }

//        foreach($substitutes as $key => $value)
//        {
//            $EcrProject->addSubstitute($key, $AutoCode->enclose($code, $key, true));
//        }

        $EasyProject->addAutoCode($AutoCode, $this->key);

        return $EasyProject->insertPart($options, $logger);
    }//function

    /**
     * Edit the AutoCode.
     *
     * @param object $AutoCode The AutoCode
     *
     * @return boolean
     */
    public function edit($AutoCode)
    {
	    $input = JFactory::getApplication()->input;

        EcrHtmlSelect::scope($this->_scope);
        echo '<input type="hidden" name="element" value="'.$this->_element.'" />';

        /* Draws an input box for a name field */
        EcrHtmlSelect::name($this->_element, jgettext('Table'));

        $ecr_project = $input->get('ecr_project');
        $element_name = $input->get('element');
        $table_name = $input->get('table_name');
        //$req_table_fields = JxRequest::getVar('table_fields', array());
        //$req_table_fields_edits = JxRequest::getVar('table_fields_edits', array());
        //$req_table_fields_types = JxRequest::getVar('table_fields_types', array());

        if( ! $table_name)
        {
            $table_name = $element_name;
        }

        if( ! $table_name)
        {
            echo 'No table given';

            return false;
        }

        $basePath =($this->_scope == 'admin') ? JPATH_ADMINISTRATOR : JPATH_SITE;
        $basePath .= DS.'components'.DS.$ecr_project.DS.'views'.DS.$table_name;

        if( ! JFolder::exists($basePath))
        {
            JFactory::getApplication()->enqueueMessage(jgettext('View not found'), 'error');

            return false;
        }

        $key = '##ECR_OPTIONS##';

        $this->show_tablefields($table_name, $AutoCode->fields[$key]);

        EcrHtmlButton::submitParts();
    }//function

    /**
     * Shows the fields of a given table.
     *
     * @param string $table_name Name of the table
     * @param array $selecteds Array of selected table fields
     *
     * AJAX called
     *
     * @return void
     */
    public function show_tablefields($table_name, $selecteds = array())
    {
        if( ! $table_name)
        {
            return;
        }

        $db = JFactory::getDBO();

        $table_name = $db->getPrefix().$table_name;
        $fields = $db->getTableFields($table_name);

        $fieldTypes = array();

        $fieldTypes[] = JHTML::_('select.option', '', jgettext('Select...'));

        foreach($this->fieldsOptions as $key => $option)
        {
            $fieldTypes[] = JHTML::_('select.option', $key, $key);
        }//foreach

        if( ! count($fields)
            || ! count($fields[$table_name]))
        {
            JFactory::getApplication()->enqueueMessage(jgettext('No table fields found'), 'error');
        }

        ?>
        <table>
        <tr>
            <th colspan="2"><?php echo jgettext('Include')?></th>
            <th><?php echo jgettext('Type'); ?></th>
        </tr>
        <?php
        foreach($fields[$table_name] as $key => $value)
        {
            //@todo db type fields
            $javascript = "$('tblfield_type_".$key."').disabled=(this.checked )?false:true;";

            $checked =(array_key_exists($key, $selecteds)) ? true : false;
            $chkInclude =($checked) ? ' checked="checked"' : '';
            ?>
            <tr>
                <td>
                    <input type="checkbox" onchange="<?php echo $javascript; ?>"
                     name="table_fields[]" <?php echo $chkInclude; ?>
                     id="tblfield_<?php echo $key; ?>"
                     value="<?php echo $key; ?>"
                     >
                </td>
                <td>
                    <label for="tblfield_<?php echo $key; ?>">
                        <?php echo '<b>'.$key.'</b><br />('.$value.')'; ?>
                    </label>
                </td>
                <td>
                <?php
                $selected =(isset ($selecteds[$key])) ? $selecteds[$key]->type : '';
                $attribs = array();
                $attribs =($checked) ? null : array('disabled' => 'disabled');
                echo JHTML::_('select.genericlist', $fieldTypes, 'table_fields_types['.$key.']'
                , $attribs, 'value', 'text', $selected, 'tblfield_type_'.$key); ?>
                </td>
            </tr>
            <?php
        }//foreach
        ?>
        </table>
        <?php
    }//function
}//class
