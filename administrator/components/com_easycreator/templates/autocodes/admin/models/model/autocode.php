<?php
/**
 * @package    EasyCreator
 * @subpackage	AutoCodes
 * @author     Nikolai Plath
 * @author     Created on 22-Mar-2010
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class AutoCodeAdminModelsModel extends EcrProjectAutocode
{
    protected $tags = array('start' => '/*', 'end' => '*/');

    protected $enclose = true;

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
}//class
