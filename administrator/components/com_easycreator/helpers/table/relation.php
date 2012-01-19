<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath
 * @author     Created on 19-Aug-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */


/**
 * EcrTableRelation class.
 */
class EcrTableRelation
{
    public $type;

    public $field;

    public $onTable;

    public $onField;

    public $aliases = array();

    /**
     * Add an alias to a table.
     *
     * @param EcrTableRelationalias $alias The alias
     *
     * @return void
     */
    public function addAlias(EcrTableRelationalias $alias)
    {
        $this->aliases[] = $alias;
    }//function
}//class
