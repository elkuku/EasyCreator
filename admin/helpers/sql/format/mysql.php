<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath
 * @author     Created on 18-Jan-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * Format XML database dumps to MySQL format.
 */
class EcrSqlFormatMySQL extends EcrSqlFormat
{
    protected $quoteString = '`';

    /**
     * Format a create statement.
     *
     * @param \SimpleXMLElement $create
     *
     * @return string
     */
    public function formatCreate(SimpleXMLElement $create)
    {
        $tableName = str_replace($this->options->get('prefix'), '#__', (string)$create->attributes()->name);

        $s = array();

        $s[] = '';
        $s[] = '-- Table structure for table '.$tableName;
        $s[] = '';

        $s[] = 'CREATE TABLE IF NOT EXISTS '.$this->quote($tableName).' (';

        $fields = array();

        foreach($create->field as $field)
        {
            $attribs = $field->attributes();

            $as = array();

            $as[] = $this->quote($attribs->Field);

            $type = (string)$attribs->Type;

            $as[] = $type;

            if('PRI' == (string)$attribs->Key)
                $as[] = 'PRIMARY KEY';

            if('NO' == (string)$attribs->Null
                && 'auto_increment' != (string)$attribs->Extra
            )
                $as[] = 'NOT NULL';

            $default = (string)$attribs->Default;

            if('' != $default)
                $as[] = "DEFAULT '$default'";

            if('auto_increment' == (string)$attribs->Extra)
                $as[] = 'AUTO INCREMENT';

            if((string)$attribs->Comment)
                $as[] = 'COMMENT \''.$attribs->Comment.'\'';

            $fields[] = implode(' ', $as);
        }

        $primaries = array();
        $uniques = array();
//         $indices = array();
        $keys = array();

        foreach($create->key as $key)
        {
            $n = (string)$key->attributes()->Key_name;
            $c = (string)$key->attributes()->Column_name;

            if('PRIMARY' == $n)
                $primaries[] = $c;
            else if('0' == (string)$key->attributes()->Non_unique)
                $uniques[$n][] = $c;
//             elseif('1' == (string)$key->attributes()->Seq_in_index)
//             $indices[$n][] = $c;
            else
                $keys[$n][] = $c;
        }

        $s[] = implode(",\n", $fields);

        if($primaries)
            $s[] = 'PRIMARY KEY ('.$this->quote(implode($this->quoteString.','.$this->quoteString, $primaries)).'),';

//         foreach ($indices as $kName => $columns)
//         {
//             $s[] = 'INDEX '.$this->quote($kName).' (`'.implode('`,`', $columns).'`),';
//         }//foreach

        foreach($uniques as $kName => $columns)
        {
            $s[] = 'UNIQUE KEY '.$this->quote($kName)
                .' ('.$this->quote(implode($this->quoteString.','.$this->quoteString, $columns)).'),';
        }

        foreach($keys as $kName => $columns)
        {
            $s[] = 'KEY '.$this->quote($kName)
                .' ('.$this->quote(implode($this->quoteString.','.$this->quoteString, $columns)).'),';
        }

        /*
          $collation = (string)$create->options->attributes()->Collation;

          $collation =($collation) ? ' DEFAULT CHARSET='.$collation : '';

          $s[] = ')'.$collation.';';
           */

        $s[] = ');';
        $s[] = '';

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

        $s = array();

        $s[] = '';
        $s[] = '-- Table data for table '.$tableName;
        $s[] = '';

        $keys = array();

        foreach($insert->row->field as $field)
        {
            $keys[] = $this->quote($field->attributes()->name);
        }

        $s[] = 'INSERT INTO '.$this->quote($tableName).' ('.implode(', ', $keys).')';

        $fields = array();

        $values = array();

        foreach($insert->row as $row)
        {
            $vs = array();

            foreach($row->field as $field)
            {
                $f = (string)$field;

                if($f != (string)(int)$field)
                    $f = $this->quote($f);

                $vs[] = $f;
            }

            $values[] = '('.implode(', ', $vs).')';
        }

        $s[] = 'VALUES';

        $s[] = implode(",\n", $values);

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

        return 'TRUNCATE TABLE '.$tableName.";\n";
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
