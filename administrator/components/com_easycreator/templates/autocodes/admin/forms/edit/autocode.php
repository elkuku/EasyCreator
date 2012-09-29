<?php
/**
 * @package    EasyCreator
 * @subpackage AutoCodes
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 07-Mar-2010
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class AutoCodeAdminFormsEdit extends EcrProjectAutocode
{
    protected $tags = array('start' => '<!--', 'end' => '-->');

    protected $enclose = '';// 'php';

    private $indent = '        ';

    /**
     * Get the AutoCode to insert.
     *
     * @param string $type AutoCode type
     * @param EcrTable $table A EcrTable object
     *
     * @return string
     */
    public function getCode($type, EcrTable $table)
    {
        $element = $this->getElement($type, dirname(__FILE__));

        if( ! $element)
        {
            return false;
        }

        return $element->getCode($table, $this->indent);
    }//function

    /**
     * Open the AutoCode for edit.
     *
     * @param EcrProjectAutocode $AutoCode The AutoCode
     *
     * @return void
     */
    public function edit(EcrProjectAutocode $AutoCode)
    {
        /* Array with required fields */
        $requireds = array();

        $requireds[] = EcrHtmlSelect::scope($this->scope);

        echo '<input type="hidden" name="element" value="'.$this->element.'" />';

        /* Draws an input box for a name field */
        $requireds[] = EcrHtmlSelect::name($this->element, jgettext('Table'));

        $tableFields = EcrTableHelper::getTableColumns($this->element);

        echo '<br />';

        $key = $AutoCode->getKey().'.row';

        if(array_key_exists($key, $AutoCode->fields))
        {
            $acFields = $AutoCode->fields[$key];
        }
        else
        {
            $acFields = array();
        }

        echo '<div style="background-color: #f3fbe6;">Header</div>';
        echo '<table>';
        echo '<tr>';
        echo '<th>'.jgettext('Field').'</th>';
        echo '<th>'.jgettext('Label').'</th>';
        echo '<th>'.jgettext('Input type').'</th>';
        echo '</tr>';

        foreach($tableFields as $name => $tableField)
        {
            if(array_key_exists($name, $acFields))
            {
                //-- Autocode present
                $label = $acFields[$name]->label;
                $inputType = $acFields[$name]->inputType;
                $width = $acFields[$name]->width;
            }
            else
            {
                //-- New field
                $label = $tableField->Field;
                $inputType = 'text';
                $width = 0;
            }

            echo '<tr>';
            echo '<th>';
            echo $name;
            echo '</th>';

            echo '<td>';
            echo '<input type="text" name="field['.$name.'][label]" value="'.$label.'" />';
            echo '</td>';

            echo '<td>';
            echo '<select name="field['.$name.'][input_type]">';
            $selected =($inputType == 'text') ? ' selected="selected"' : '';
            echo '<option value="text"'.$selected.'>&lt;text&gt;</option>';
            $selected =($inputType == 'hidden') ? ' selected="selected"' : '';
            echo '<option value="hidden"'.$selected.'>&lt;hidden&gt;</option>';
            $selected =($inputType == 'category') ? ' selected="selected"' : '';
            echo '<option value="category"'.$selected.'>Catergory select</option>';
            echo '</select>';
            echo '</td>';

            echo '</tr>';
        }//foreach

        echo '</table>';

        /* Draws the submit button */
        EcrHtmlButton::autoCode($requireds);
    }//function

    /**
     * Inserts the AutoCode into the project.
     *
     * @param EcrProjectBase $project The project.
     * @param array $options Insert options.
     * @param EcrLogger $logger EcrLogger.
     *
     * @return boolean
     */
    public function insert(EcrProjectBase $project, $options, EcrLogger $logger)
    {
        $input = JFactory::getApplication()->input;

        $table_name = $input->get('element');
        $element_scope = $input->get('element_scope');

        $element = 'row';

        if( ! $table_name)
        {
            JFactory::getApplication()->enqueueMessage(jgettext('No table given'), 'error');

            return false;
        }

        $table = new EcrTable($table_name);

        $fields = EcrTableHelper::getTableColumns($table_name);

        $reqFields = $input->get('field', array(), 'array');

        $rows = '';

        $tags = array('<!--', '-->');
        $indent = '            ';

        if( ! count($fields))
        {
            JFactory::getApplication()->enqueueMessage('No table fields found', 'error');

            return false;
        }

        foreach($fields as $name => $field)
        {
            $reqFieldHeader = $reqFields[$name];

            if(isset($project->autoCodes[$this->key]->fields[$this->key.'.'.$element][$name]))
            {
                $fieldHeader = $project->autoCodes[$this->key]->fields[$this->key.'.'.$element][$name];
            }
            else
            {
                $fieldHeader = new EcrTableField;
                $fieldHeader->name = $name;
            }

            $fieldHeader->label = $reqFieldHeader['label'];
            $fieldHeader->inputType = $reqFieldHeader['input_type'];

            $autoCodeFieldsHeader[] = $fieldHeader;

            $table->addField($fieldHeader);
        }//foreach

            $rows .= $this->getCode($element, $table, $indent);

        $this->fields[$this->key.'.'.$element] = $autoCodeFieldsHeader;
        $this->codes[$this->key.'.'.$element] = $this->enclose($rows, $this->key.'.'.$element);

        $project->addAutoCode($this);

        /*
         * Add substitutes
         *
         * Define keys that will be substitutes in the code
         */
        $project->addSubstitute('ECR_SUBPACKAGE', 'Views');
        $project->addSubstitute('_ECR_TABLE_NAME_', $table_name);

        foreach($this->codes as $key => $code)
        {
            $project->addSubstitute($tags[0].$key.$tags[1], $code);
        }//foreach

        /* Insert the part to your project and return the results */
        return $project->insertPart($options, $logger);
    }//function
}//class
