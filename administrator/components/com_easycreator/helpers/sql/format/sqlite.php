<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath
 * @author     Created on 18-Jan-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * Format XML database dumps to SQLite format.
 *
 * @link http://www.sqlite.org/datatype3.html
 */
class EcrSqlFormatSQLite extends EcrSqlFormat
{
    protected $quoteString = '';

    /**
     * Format a create statement.
     *
     * @param \SimpleXMLElement $create
     *
     * @return string
     */
    public function formatCreate(SimpleXMLElement $create)
    {
        $tableName = (string)$create->attributes()->name;

        $tableName = str_replace($this->options->get('prefix'), '#__', $tableName);

        $fields = array();

        $primaryKeySet = false;

        $affinityTypes = array(
            'INTEGER' => array('int'),
            'TEXT' => array('char', 'text', 'clob'),
            'NONE' => array('blob'),
            'REAL' => array('real', 'floa', 'doub'),
        );

        foreach($create->field as $field)
        {
            $attribs = $field->attributes();

            $as = array();

            $as[] = (string)$attribs->Field;

            $type = (string)$attribs->Type;

            $type = str_replace(' unsigned', '', $type);

            $affinity = '';

            foreach($affinityTypes as $aType => $cTypes)
            {
                if($affinity)
                    continue;

                foreach($cTypes as $cType)
                {
                    if(false !== strpos($type, $cType))
                    {
                        $affinity = $aType;

                        continue 2;
                    }
                }
            }

            if('' == $affinity)
            {
                $affinity = 'NUMERIC';
            }

            $as[] = $affinity;

            if('PRI' == (string)$attribs->Key
                && ! $primaryKeySet
            )
            {
                $as[] = 'PRIMARY KEY';
                $primaryKeySet = true;
            }

            if(0) //@todo - we ditch NOT NULL for now,as SQLite is very strict about it :(
            {
                if('NO' == (string)$attribs->Null
                    && 'auto_increment' != (string)$attribs->Extra
                )
                    $as[] = 'NOT NULL';
            }

            $default = (string)$attribs->Default;

            if('' != $default)
                $as[] = "DEFAULT '$default'";

            if('auto_increment' == (string)$attribs->Extra)
                $as[] = 'AUTOINCREMENT';

            $fields[] = implode(' ', $as);
        }

        $s = array();

        $s[] = '';
        $s[] = '-- Table structure for table '.$tableName;
        $s[] = '';
        $s[] = 'CREATE TABLE IF NOT EXISTS '.$tableName.' (';
        $s[] = implode(",\n", $fields);
        $s[] = ');';

        return implode("\n", $s);
    }

    /**
     * Format the insert statement.
     *
     * @param \SimpleXMLElement $insert
     *
     * @return string
     */
    public function formatInsert(SimpleXMLElement $insert)
    {
        if(false == isset($insert->row->field))
            return '';

        $tableName = (string)$insert->attributes()->name;

        $tableName = str_replace($this->options->get('prefix'), '#__', $tableName);

        $keys = array();
        $values = array();

        foreach($insert->row->field as $field)
        {
            $keys[] = (string)$field->attributes()->name;
        }

        $s = array();

        $s[] = '';
        $s[] = '-- Table data for table '.$tableName;
        $s[] = '';
        $s[] = 'INSERT INTO '.$tableName;

        $started = false;

        foreach($insert->row as $row)
        {
            $vs = array();

            $i = 0;

            foreach($row->field as $field)
            {
                // ''escape'' single quotes by prefixing them with another single quote
                $f = str_replace("'", "''", (string)$field);

                $vs[] = ($started) ? "'".$f."'" : "'".$f."' AS ".$keys[$i ++];
            }

            if(false == $started)
            {
                $s[] = '      SELECT '.implode(', ', $vs);
            }
            else
            {
                $s[] = 'UNION SELECT '.implode(', ', $vs);
            }

            $started = true;
        }

        $s[] = ';';

        return implode("\n", $s);
    }

    /**
     * Format the truncate table statement.
     *
     * @param \SimpleXMLElement $tableStructure
     *
     * @return string
     */
    public function formatTruncate(SimpleXMLElement $tableStructure)
    {
        $tableName = str_replace($this->options->get('prefix'), '#__', (string)$tableStructure->attributes()->name);

        return 'DELETE FROM '.$tableName.";\n";
    }

    /**
     * Format the drop table statement.
     *
     * @param \SimpleXMLElement $tableStructure
     *
     * @return string
     */
    public function formatDropTable(SimpleXMLElement $tableStructure)
    {
        $tableName = str_replace($this->options->get('prefix'), '#__', (string)$tableStructure->attributes()->name);

        return 'DROP TABLE '.$tableName.";\n";
    }
}//class
