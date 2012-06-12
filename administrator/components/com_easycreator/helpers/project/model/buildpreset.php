<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 02-Jun-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * Buildpreset model class.
 */
class EcrProjectModelBuildpreset
{
    public $buildFolder = '';

    public $archiveZip = false;

    public $archiveTgz = false;

    public $archiveBz2 = false;

    public $createIndexhtml = false;

    public $createMD5 = false;

    public $createMD5Compressed = false;

    public $custom_name_1 = '';

    public $custom_name_2 = '';

    public $custom_name_3 = '';

    public $custom_name_4 = '';

    public $includeEcrProjectfile = false;

    public $removeAutocode = false;

    public $actions = array();

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        if(count($options))
        {
            foreach($this as $k => $v)
            {
                if(array_key_exists($k, $options))
                    $this->$k = $options[$k];
            }
        }
    }

    /**
     * @param array $values
     *
     * @return EcrProjectModelBuildpreset
     */
    public function loadValues(array $values)
    {
        foreach($this as $k => $v)
        {
            if(array_key_exists($k, $values))
                $this->$k = $values[$k];

            if(is_bool($this->$k))
                $this->$k =(in_array($k, $values)) ? true : false;
        }

        return $this;
    }

    /**
     * Convert to JSON string.
     *
     * @return string
     */
    public function toJson()
    {
        $o = new stdClass;

        foreach($this as $k => $v)
        {
            if('actions' == $k)
            {
                $as = array();

                /* @var EcrProjectAction $action */
                foreach($v as $action)
                {
                    $as[] = $action->toJson();
                }

                $o->$k = $as;
            }
            else
            {
                $o->$k = $v;
            }
        }

        return json_encode($o);
    }
}
