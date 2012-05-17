<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 16-Jul-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Database adapter for MySQL.
 */
class EcrDbadapterMysql extends EcrDbadapterBase
{
    public function __construct()
    {
        $foo = '';
        parent::__construct();
    }//function

    public function parseCreate()
    {
        //-- Invoke the PEAR parser
//        ecrLoadHelper('SQL.Parser');

        $parser = new EcrSqlParser($this->query->processed, 'MySQL');

        $parsed = $parser->parseCreate();

        $result = new stdClass;

        $result->name = $parsed['table_names'][0];
//        $result->fields = $parsed['column_defs'];

        $result->fields = array();

        foreach($parsed['column_defs'] as $name => $defs)
        {
            $d = new stdClass;
            $d->type =(isset($defs['type'])) ?  $defs['type'] : '';
            $d->length =(isset($defs['length'])) ? $defs['length'] : '';
            $d->constraints = $defs['constraints'];

            $result->fields[$name] = $d;
        }//foreach

        $result->raw = $this->query->raw;

        return $result;
    }//function

    public function getStatement($type, $name, $field = null)
    {
        switch($type)
        {
            case 'addColumn' :
                return 'ADD '
                .$this->quote($name)
                .$this->parseField($field).NL;
                break;

            case 'modifyColumn' :
                return 'MODIFY '
                .$this->quote($name)
                .$this->parseField($field).NL;
                break;

            case 'dropColumn' :
                return 'DROP COLUMN '
                .$this->quote($name).NL;
                break;
default :
                EcrHtml::message(__METHOD__.': Unknown type '.$type);

                return '';
                break;
        }//switch
    }//function

    public function getAlterTable($table, $alters)
    {
        if( ! $alters)
        return '';

        return 'ALTER TABLE '.$this->quote($table->name).NL.implode(', ', $alters).NL;
    }//function

    private function parseField($field)
    {
        $parsed = ' ';

        $parsed .= strtoupper($field->type);
        $parsed .= '('.$field->length.')';

        foreach($field->constraints as $c)
        {
            if( ! isset($c['type']))
            continue;

            switch($c['type'])
            {
                case 'not_null' :
                    $parsed .= ' NOT NULL';
                    break;

                case 'comment' :
                    $parsed .= " COMMENT '".$c['value']."'";
                    break;

                default:
                    EcrHtml::message('Unknown field type '.$c['type'], 'error');
                    break;
            }//switch
        }//foreach

        return $parsed;
    }//function
}//class
