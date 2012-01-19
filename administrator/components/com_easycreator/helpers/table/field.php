<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath
 * @author     Created on 19-Aug-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EcrTableField class.
 */
class EcrTableField
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
