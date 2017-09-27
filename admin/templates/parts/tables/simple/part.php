<?php
/**
 * @package    EasyCreator
 * @subpackage	Parts
 * @author		Nikolai Plath (elkuku)
 * @author		Created on 20.04.2009
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * _Simple table part.
 *
 * @package    EasyCreator
 * @subpackage Templates.Parts
 */
class PartTablesSimple extends EcrProjectPart
{
    public $group = 'tables';

    public $name = 'simple';

    private $_varScopes = array(
    'var'
    , 'private'
    , 'protected'
    , 'public'
    );

    /**
     * Constructor.
     *
     * @param string $element The element name
     * @param string $scope Scope name
     */
    public function __construct($element, $scope)
    {
        $this->_element = $element;
        $this->_scope = $scope;

        parent::__construct($this->group, $this->name, $element, $scope);
    }

    /**
     * Info about the thing.
     *
     * @return EcrProjectTemplateInfo
     */
    public function info()
    {
        $info = new EcrProjectTemplateInfo;

        $info->group = $this->group;
        $info->title = 'Data';
        $info->description = jgettext('This will map an existing table to a table class');

        return $info;
    }

    /**
     * Here you define custom options that will be displayed along with the input form.
     *
     * @return void
     */
    public function getOptions()
    {
        /* Array with required fields */
        $requireds = array();

        $requireds[] = EcrHtmlSelect::scope($this->_scope);

        if( ! $this->_element)
        {
            $db = JFactory::getDBO();
            $tables = $db->getTableList();

            echo '<strong id="element_label">'.jgettext('Table').'</strong> : ';
            echo '<select name="element" id="table_name" onchange="$(\'element_name\').value=$(\'element\').value;">';
            echo '<option value="">'.jgettext('Choose...').'</option>';

            $prefix = $db->getPrefix();

            foreach($tables as $table)
            {
                $v = str_replace($prefix, '', $table);
                echo '<option value="'.$v.'">'.$v.'</option>';
            }

            echo '</select>';
            echo '<br />';
        }
        else
        {
            echo '<input type="hidden" name="element" value="'.$this->_element.'" />';
        }

        /* Draws an input box for a name field */
        $requireds[] = EcrHtmlSelect::name($this->_element, jgettext('Table'));

        echo '<strong>Var Scope:</strong><br />';

        foreach($this->_varScopes as $vScope)
        {
            $checked =($vScope == 'var') ? ' checked="checked"' : '';
            echo '<input type="radio" name="var_scope" value="'.$vScope.'" id="vscope-'.$vScope.'"'
            .$checked.'> <label for="vscope-'.$vScope.'">'.$vScope.'</label><br />';
        }

        /*
         * Add your custom options
         * ...
         */

        /* Displays options for logging */
        EcrHtmlOptions::logging();

        /* Draws the submit button */
        EcrHtmlButton::submitParts($requireds);
    }

    /**
     * Open the part for edit.
     *
     * @param object $AutoCode The AutoCode
     *
     * @return void
     */
    public function edit($AutoCode)
    {
        $var_scope = $AutoCode->options['varScope'];

        /* Array with required fields */
        $requireds = array();

        $requireds[] = EcrHtmlSelect::scope($this->_scope);
        echo '<input type="hidden" name="element" value="'.$this->_element.'" />';

        /* Draws an input box for a name field */
        $requireds[] = EcrHtmlSelect::name($this->_element, jgettext('Table'));

        echo '<strong>Var Scope:</strong><br />';

        foreach($this->_varScopes as $vScope)
        {
            $checked =($vScope == $var_scope) ? ' checked="checked"' : '';
            echo '<input type="radio" name="var_scope" value="'.$vScope.'" id="vscope-'.$vScope.'"'
            .$checked.'> <label for="vscope-'.$vScope.'">'.$vScope.'</label><br />';
        }

        /* Draws the submit button */
        EcrHtmlButton::submitParts($requireds);
    }

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

        $table_name = $input->get('element');
        $var_scope = $input->get('var_scope');
        $element_scope = $input->get('element_scope');

        if( ! $table_name)
        {
            JFactory::getApplication()->enqueueMessage(jgettext('No table given'), 'error');

            return false;
        }

        $db = JFactory::getDBO();
        $prefix = $db->getPrefix();
        $fields = $db->getTableFields($prefix.$table_name);

        $table_vars = '';
        $autoCodeFields = array();

        if(count($fields))
        {
            foreach($fields[$prefix.$table_name] as $key => $value)
            {
                $table_vars .= $this->formatTableVar($key, $value, array(), $var_scope);
                $autoCodeFields[] = $key;
            }
        }

        $AutoCode = new EcrProjectAutocode($this->group, $this->name, $table_name, $element_scope);

        $AutoCode->options = array();
        $AutoCode->options['varScope'] = $var_scope;

        $AutoCode->fields['##ECR_TABLE_VARS##'] = array();//$autoCodeFields;
        $AutoCode->codes['##ECR_TABLE_VARS##'] = $AutoCode->enclose($table_vars, '##ECR_TABLE_VARS##');

        $AutoCode->fields['##ECR_FIELD_1##'] = array();
        $AutoCode->codes['##ECR_FIELD_1##'] = $AutoCode->enclose($this->formatTableVar('db'
        , 'Database object', array(), $var_scope), '##ECR_FIELD_1##');

        $project->addAutoCode($AutoCode, $this->key);

        /*
         * Add substitutes
         *
         * Define keys that will be substitutes in the code
         */
        $project->addSubstitute('ECR_SUBPACKAGE', 'Tables');
        $project->addSubstitute('_ECR_TABLE_NAME_', $table_name);

        foreach($AutoCode->codes as $key => $code)
        {
            $project->addSubstitute($key, $code);
        }

        /* Insert the part to your project and return the results */
        return $project->insertPart($options, $logger);
    }

    /**
     * Format variables to be displayed as docComment in class header.
     *
     * @param string       $var      Variable name
     * @param string       $type     Data type
     * @param array|string $adds     Additional comments
     * @param string       $varScope Variable scope
     *
     * @return string
     */
    private function formatTableVar($var, $type = '', $adds = array(), $varScope = 'var')
    {
        switch($type)
        {
            case 'int':
            case 'tinyint':
                $def = '0';
                break;

            default:
                $def = 'null';
                break;
        }

        $s = '';
        $s .= '	/**'.NL;
        $s .= '	 * @var '.$type.NL;

        foreach($adds as $a)
        {
            $s .= '	 * '.$a.NL;
        }

        $s .= '	 */'.NL;
        $s .= '	'.$varScope.' $'.$var.' = '.$def.';'.NL.NL;

        return $s;
    }
}
