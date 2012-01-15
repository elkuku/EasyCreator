<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath
 * @author     Created on 19-Aug-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Table helper.
 */
class EasyTableHelper
{
    public $scopes = array();

    public $types = array(
    'tableclass' => array('tables', 'classvar')
    , 'controller' => array('controllers', 'xyz')
    , 'modelform' => array('models', 'xyz')
    , 'modellist' => array('models', 'xyz')
    , 'viewform' => array('views', 'table')
    , 'viewlist' => array('views', 'table')
    , 'viewitem' => array('views', 'div')
    , 'catview' => array('views', 'table')
    );

    /**
     * Constructor.
     */
    public function __construct()
    {
        $s = new stdClass;
        $s->folder = DS.'administrator';
        $s->title = jgettext('Admin');
        $s->tag = 'admin';
        $this->scopes['admin'] = $s;

        $s = new stdClass;
        $s->folder = '';
        $s->title = jgettext('Site');
        $s->tag = 'site';
        $this->scopes['site'] = $s;
    }//function

    /**
     * Auto discover tables.
     *
     * @param EasyProject $project The Project.
     *
     * @return array
     */
    public function discoverTables(EasyProject $project)
    {
        $db = JFactory::getDBO();
        $dbTables = $db->getTableList();
        $dbPrefix = $db->getPrefix();
        $tables = array();

        $path = JPATH_ADMINISTRATOR.DS.'components'.DS.$project->comName;

        $listPostfix = $project->listPostfix;

        if( ! JFolder::exists($path))
        {
            return $tables;
        }

        $files = JFolder::files($path, '^install|.sql$|.xml$', true, true);

        if( ! count($files)
        && ! $project->tables)
        {
            return $tables;
        }

        if($project->tables)
        {
            $files[] = $project->tables;
        }

        foreach($files as $file)
        {
            if(is_array($file))
            {
                $tableNames = array();

                foreach($file as $f)
                {
                    $tableNames[] = (string)$f->name;
                }//foreach
            }
            else
            {
                if( ! $fContents = JFile::read($file))
                {
                    EcrHtml::displayMessage('File read error', 'error');

                    return $tables;
                }

                preg_match_all('%CREATE\sTABLE([A-Z\s]*)[`]?+\#__([a-z0-9_]*)[`]?+%'
                , $fContents, $matches);

                if( ! count($matches[2]))
                {
                    continue;
                }

                $tableNames = $matches[2];
            }

            foreach($tableNames as $m)
            {
                $m = (string)$m;

                if(array_key_exists($m, $tables))
                {
                    if( ! is_array($file))
                    {
                        $ext = JFile::getExt($file);
                        $tables[$m]->install = $ext;
                    }
                }
                else
                {
                    $t = new EasyTable($m);
                    $t->install =(is_array($file)) ? '' : JFile::getExt($file);
                    $t->inDB =(in_array($dbPrefix.$m, $dbTables)) ? true : false;

                    foreach($this->types as $tName => $tType)
                    {
                        $t->$tName = array();

                        foreach($this->scopes as $scope)
                        {
                            if($scope->tag == 'admin'
                            &&
                            ($tName == 'viewitem'
                            || $tName == 'catview'))
                            {
                                continue;
                            }

                            $path = JPATH_ROOT.$scope->folder.DS.'components'
                            .DS.$project->comName.DS.$tType[0];

                            switch($tType[0])
                            {
                                case 'views':
                                    $fName =($tName == 'viewform' || $tName == 'viewitem')
                                    ? $m
                                    : $m.$listPostfix;

                                    if(JFolder::exists($path.DS.$fName))
                                    {
                                        $t->{$tName}[] = $scope->tag;
                                    }

                                    break;

                                case 'models':
                                    $fName =($tName == 'modelform') ? $m : $m.$listPostfix;

                                    if(JFile::exists($path.DS.$fName.'.php'))
                                    {
                                        $t->{$tName}[] = $scope->tag;
                                    }

                                    break;

                                default:
                                    $fName = $m.'.php';

                                    if(file_exists($path.DS.$fName))
                                    {
                                        $t->{$tName}[] = $scope->tag;
                                    }

                                    break;
                            }//switch
                        }//foreach
                    }//foreach

                    $tables[$m] = $t;
                }
            }//foreach
        }//foreach

        return $tables;
    }//function

    /**
     * Gets the table fields of a given table.
     *
     * @param string $tableName Table name without prefix
     *
     * @return array
     */
    public static function getTableFields($tableName)
    {
        $db = JFactory::getDBO();

        $dbPrefix = $db->getPrefix();

        $fields = $db->getTableFields($dbPrefix.$tableName, false);

        return $fields[$dbPrefix.$tableName];
    }//function

    /**
     * Gets the table fields of a given table.
     *
     * @param string $tableName Table name without prefix
     *
     * @return array
     */
    public static function getTableFieldsNew($tableName)
    {
        $db = JFactory::getDBO();
        $columnQuery = "SHOW FULL COLUMNS FROM `%s`;";
        $db->setQuery(sprintf($columnQuery, $db->getPrefix().$tableName));
        $fields = $db->loadAssocList();
        //    $tableFields = array();
        //            foreach($fields as $field)
        //            {
        //           #     $f = array();
        //
        //                foreach($field as $k => $v)
        //                {
        //                    $k = strtolower($k);
        //                    if($k == 'field') $k = 'name';
        //                    $f[$k] = $v;
        //                }//foreach
        //
        //                $f = new EasyTableField($f);
        //                $tableFields[] = $f;
        //
        //            }//foreach
        //
        //            return $tableFields;
        return $fields;
    }//function

    /**
     * Get the CREATE string for a table.
     *
     * @param EasyTable $table The table
     *
     * @return string
     */
    public static function getTableCreate(EasyTable $table)
    {
        $db = JFactory::getDBO();
        $dbName = JFactory::getApplication()->getCfg('db');
        $tName = $db->getPrefix().$table->name;

        $s = '';
        $s .= 'CREATE TABLE IF NOT EXISTS `#__'.$table->name.'` ('.NL;

        $pri = '';

        $started = false;
        $indent = '';

        $db->setQuery('SHOW CREATE TABLE '.$tName);
$furz = $db->loadRowList();
var_dump($furz);
$furz = explode(NL, $furz[0][1]);
var_dump($furz);

        $engineQuery = 'SELECT ENGINE
 FROM information_schema.TABLES
 WHERE TABLE_SCHEMA = \''.$dbName.'\'
 AND TABLE_NAME = \''.$tName.'\'';
        $db->setQuery($engineQuery);

        $engine = $db->loadResult();
        echo 'A'.$engine;
//        $engineQuery = ' SHOW TABLE STATUS LIKE `'.$tName.'`
// FROM information_schema.TABLES
// WHERE TABLE_SCHEMA = \''.$dbName.'\'
// AND TABLE_NAME = \''.$tName.'\'';
//        $db->setQuery($engineQuery);
//
//        $engine = $db->loadResult();
//        echo 'B'.$engine;



        foreach($table->getFields() as $field)
        {
            if($field->key == 'PRI')
            $pri = $field->name;

            $s .=($started) ? $indent.', ' : $indent.'  ';
            $started = true;
            $s .= EasyTableHelper::formatSqlField($field);
            $s .= NL;
        }//foreach

        if($pri)
        $s .= ', PRIMARY KEY (`'.$pri.'`)';

// #       $s .= ') ENGINE='.$engine.' DEFAULT CHARSET='.

        return $s;
    }//function

    /**
     * Format variables with docComment To be displayed in class header.
     *
     * @param string $var The variable name
     * @param string $type Data type
     * @param string $adds Additional comments
     * @param string $varScope Set php4/5 var or access modifier
     *
     * @return string
     */
    public static function formatTableVar($var, $type, $adds = array(), $varScope = 'var')
    {
        $type = strtolower($type);

        switch($type)
        {
            case 'int':
            case 'tinyint':
            case 'bigint':
                $def = '0';
                break;

            default:
                $def = 'NULL';
                break;
                //@todo some more options
        }//switch

        $s = '';
        $s .= '   /**'.NL;

        foreach($adds as $a)
        {
            $s .= '    * '.$a.NL;
        }//foreach

        $s .= '    * '.NL;
        $s .= '    * @var '.$type.NL;
        $s .= '    */'.NL;
        $s .= '    '.$varScope.' $'.$var.' = '.$def.';'.NL.NL;

        return $s;
    }//function

    /**
     * Format a SQL field for the use in queries.
     *
     * @param EasyTableField $field The field to format
     *
     * @return string
     */
    public static function formatSqlField(EasyTableField $field)
    {
        $nNull = $field->null;
        $nNull =($nNull == 'NO') ? 'NOT NULL' : $nNull;

        $s = '';
        $s .= '`'.$field->name.'`';
        $s .= ' '.$field->type;
        $s .=($field->length) ? '('.$field->length.')' : '';
        $s .=($field->attributes) ? ' '.$field->attributes : '';
        $s .= ' '.str_replace('_', ' ', $nNull);
        $s .=($field->default) ? " DEFAULT '$field->default'" : '';
        $s .=($field->extra) ? ' '.$field->extra : '';
        $s .=($field->comment) ? " COMMENT '$field->comment'" : '';

        return $s;
    }//function

    /**
     * Draws a row wit standard fields.
     *
     * @deprecated move to template ?
     *
     * @return string
     */
    public static function drawStdInsertRow()
    {
        $field = new EasyTableField;
        $field->name = 'id';
        $field->label = 'Primary key';
        $field->type = 'INT';
        $field->length = '11';
        $field->attributes = 'UNSIGNED';
        $field->null = 'NOT_NULL';
        $field->extra = 'AUTO_INCREMENT';

        $html = '';
        $html .= '
<div class="addRow">
        <span class="ecr_button img icon-16-add" onclick="newRow(\'db_table_fields\');">'.jgettext('Add row').'</span>
</div>
<div id="db_table_fields">
<div class="ecr_dbRowCell head">'.jgettext('Name').'</div>
<div class="ecr_dbRowCell head">'.jgettext('Label').'</div>
<div class="ecr_dbRowCell head">'.jgettext('Type').'</div>
<div class="ecr_dbRowCell head">'.jgettext('Length/Set').'</div>
<div class="ecr_dbRowCell head">'.jgettext('Attributes').'</div>
<div class="ecr_dbRowCell head">'.jgettext('Null').'</div>
<div class="ecr_dbRowCell head">'.jgettext('Default').'</div>
<div class="ecr_dbRowCell head">'.jgettext('Extra').'</div>
<div class="ecr_dbRowCell head">'.jgettext('Comment').'</div>

<div style="clear: both;"></div>

</div>

<div style="background-color: #eee; font-weight: bold;">'.jgettext('Predefined fields').'</div>';

        $html .= self::drawPredefinedRow($field, 0);

return $html;
    }//function

    /**
     * Draw a row of predefined table fields.
     *
     * @param EasyTableField $field The EasyTableField field
     * @param integer $count Field count
     *
     * @return string
     */
    public static function drawPredefinedRow(EasyTableField $field, $count)
    {
        $ret = '';
        $ret .= '
<div class="ecr_dbRowCell" style="background-color: #eee;">'.$field->name.'&nbsp;</div>
<div class="ecr_dbRowCell" style="background-color: #eee;">'.$field->label.'&nbsp;</div>
<div class="ecr_dbRowCell" style="background-color: #eee;">'.$field->type.'&nbsp;</div>
<div class="ecr_dbRowCell" style="background-color: #eee;">'.$field->length.'&nbsp;</div>
<div class="ecr_dbRowCell" style="background-color: #eee;">'.$field->attributes.'&nbsp;</div>
<div class="ecr_dbRowCell" style="background-color: #eee;">'.$field->null.'&nbsp;</div>
<div class="ecr_dbRowCell" style="background-color: #eee;">'.$field->default.'&nbsp;</div>
<div class="ecr_dbRowCell" style="background-color: #eee;">'.$field->extra.'&nbsp;</div>
<div class="ecr_dbRowCell" style="background-color: #eee;">'.$field->comment.'&nbsp;</div>

<input type="hidden" name ="fields['.$count.'][name]" value="'.$field->name.'" />
<input type="hidden" name ="fields['.$count.'][label]" value="'.$field->label.'" />
<input type="hidden" name ="fields['.$count.'][type]" value="'.$field->type.'" />
<input type="hidden" name ="fields['.$count.'][length]" value="'.$field->length.'" />
<input type="hidden" name ="fields['.$count.'][attributes]" value="'.$field->attributes.'" />
<input type="hidden" name ="fields['.$count.'][null]" value="'.$field->null.'" />
<input type="hidden" name ="fields['.$count.'][default]" value="'.$field->default.'" />
<input type="hidden" name ="fields['.$count.'][extra]" value="'.$field->extra.'" />
<input type="hidden" name ="fields['.$count.'][comment]" value="'.$field->comment.'" />

<div style="clear: both;"></div>
';

        return $ret;
    }//function
}//class

/**
 * A.
 *
 */
class EasyTable
{
    public $name;

    public $foreign = false;

    private $fields = array();

    private $relations = array();

    /**
     * Constructor.
     *
     * @param string $name Table name.
     * @param boolean $foreign False if the table does not belong to thwe component.
     */
    public function __construct($name, $foreign = false)
    {
        $this->name = (string)$name;
        $this->foreign = (string)$foreign;
    }//function

    /**
     * Add a relation.
     *
     * @param EasyTableRelation $relation Relation to add
     *
     * @return void
     */
    public function addRelation(EasyTableRelation $relation)
    {
        $this->relations[] = $relation;
    }//function

    /**
     * Add a table field.
     *
     * @param EasyTableField $field The field to add
     *
     * @return void
     */
    public function addField(EasyTableField $field)
    {
        $this->fields[] = $field;
    }//function

    /**
     * Add a field from an indexed array.
     *
     * @param array $fields Table fields
     *
     * @return void
     */
    public function addFields($fields)
    {
        $ar = array();

        foreach($fields as $field)
        {
            foreach(array_keys($field) as $key)
            {
                $k = strtolower($key);

                if($k == 'field')
                $k = 'name';

                $ar[$k] = $field[$key];
            }//foreach

            $f = new EasyTableField($ar);

            $this->addField($f);
        }//foreach
    }//function

    /**
     * Get the table name.
     *
     * @deprecated use public var $table->name
     *
     * @return string
     */
    public function xgetName()
    {
        return $this->name;
    }//function

    /**
     * Get the table fields.
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }//function

    /**
     * Get only the field names.
     *
     * @return array
     */
    public function getFieldNames()
    {
        $ret = array();

        foreach($this->fields as $field)
        {
            $ret[] = $field->name;
        }//foreach

        return $ret;
    }//function

    /**
     * Get table relations.
     *
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }//function

    /**
     * ToString method.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }//function
}//class

/**
 * A.
 *
 */
class EasyTableRelation
{
    public $type;

    public $field;

    public $onTable;

    public $onField;

    public $aliases = array();

    /**
     * Add an alias to a table.
     *
     * @param EasyTableRelationAlias $alias The alias
     *
     * @return void
     */
    public function addAlias(EasyTableRelationAlias $alias)
    {
        $this->aliases[] = $alias;
    }//function
}//class

/**
 * A.
 *
 */
class EasyTableRelationAlias
{
    public $alias;

    public $aliasField;
}//class

/**
 * A.
 *
 */
class EasyTableField
{
    public $name;

    public $label;

    public $type;

    public $length;

    public $attributes;

    public $null;

    public $default;

    public $extra;

    public $key;

    public $comment;

    /*
     * Display options
     */
    public $display = true;

    public $width;

    public $inputType;

    public $extension = '';

    /**
     * Constructor.
     *
     * @param mixed $field Indexed array or object, null to create a blank.
     */
    public function __construct($field = null)
    {
        if(is_array($field))
        {
            if( ! count($field))
            {
                return;
            }

            foreach(array_keys(get_object_vars($this)) as $var)
            {
                if(array_key_exists($var, $field))
                {
                    $this->$var = $field[$var];
                }
            }//foreach
        }
        else if(is_object($field))
        {
            foreach(array_keys(get_object_vars($this)) as $var)
            {
                if(property_exists($field, $var))
                {
                    $this->$var = (string)$field->$var;
                }
            }//foreach
        }
        else if( ! is_null($field))
        {
            JFactory::getApplication()->enqueueMessage(__METHOD__.': Invalid option', 'error');
        }
    }//function
}//class
