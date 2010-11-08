<?php
/**
 * @version $Id$
 * @package    EasyCreator
 * @subpackage	AutoCodes
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 07-Mar-2010
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class AutoCodeAdminViewformTable extends EasyAutoCode
{
    protected $tags = array('start' => '<!--', 'end' => '-->');

    protected $enclose = 'php';

    private $indent = '            ';

    /**
     * Get the AutoCode to insert.
     *
     * @param string $type AutoCode type
     * @param EasyTable $table A EasyTable object
     *
     * @return string
     */
    public function getCode($type, EasyTable $table)
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
     * @param EasyAutoCode $AutoCode The AutoCode
     *
     * @return void
     */
    public function edit(EasyAutoCode $AutoCode)
    {
        /* Array with required fields */
        $requireds = array();

        $requireds[] = ecrHTML::drawSelectScope($this->scope);

        echo '<input type="hidden" name="element" value="'.$this->element.'" />';

        /* Draws an input box for a name field */
        $requireds[] = ecrHTML::drawSelectName($this->element, jgettext('Table'));

        $tableFields = EasyTableHelper::getTableFields($this->element);

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
        ecrHTML::drawSubmitAutoCode($requireds);
    }//function

    /**
     * Inserts the AutoCode into the project.
     *
     * @param EasyProject $project The project.
     * @param array $options Insert options.
     * @param EasyLogger $logger EasyLogger.
     *
     * @return boolean
     */
    public function insert(EasyProject $project, $options, EasyLogger $logger)
    {
        $table_name = JRequest::getCmd('element');
        $element_scope = JRequest::getCmd('element_scope');

        $element = 'row';

        if( ! $table_name)
        {
            JError::raiseWarning(100, jgettext('No table given'));

            return false;
        }

        $table = new EasyTable($table_name);

        $fields = EasyTableHelper::getTableFields($table_name);

        $reqFields = JRequest::getVar('field');

        $rows = '';

        $tags = array('<!--', '-->');
        $indent = '            ';

        if( ! count($fields))
        {
            JError::raiseWarning(100, 'No table fields found');

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
                $fieldHeader = new EasyTableField();
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
        $project->addSubstitute('_ECR_SUBPACKAGE_', 'Views');
        $project->addSubstitute('_ECR_TABLE_NAME_', $table_name);

        foreach($this->codes as $key => $code)
        {
            $project->addSubstitute($tags[0].$key.$tags[1], $code);
        }//foreach

        /* Insert the part to your project and return the results */
        return $project->insertPart($options, $logger);
    }//function
}//class
