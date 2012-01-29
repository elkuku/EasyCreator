<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath
 * @author     Created on 19-Aug-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EcrTable class.
 */
class EcrTable
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
     * @param EcrTableRelation $relation Relation to add
     *
     * @return void
     */
    public function addRelation(EcrTableRelation $relation)
    {
        $this->relations[] = $relation;
    }//function

    /**
     * Add a table field.
     *
     * @param EcrTableField $field The field to add
     *
     * @return void
     */
    public function addField(EcrTableField $field)
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

            $f = new EcrTableField($ar);

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
