<?php
/**
 * User: elkuku
 * Date: 22.05.12
 * Time: 19:26
 */

/**
 * EasyCreator project action base class.
 *
 * @property-read string $type
 * @property-read string $name
 * @property-read string $trigger
 */
abstract class EcrProjectAction
{
    protected $type = '';

    protected $name = '';

    protected $trigger = '';

    protected $internals = array('type', 'name', 'trigger');

    /**
     * Get the input fields
     *
     * @param int $cnt A counter value.
     *
     * @return string
     */
    abstract public function getFields($cnt);

    /**
     * Perform the action.
     *
     * @param EcrProjectZiper $ziper
     *
     * @return bool true if successful, false to interrupt the build process
     */
    abstract public function run(EcrProjectZiper $ziper);

    /**
     * @param string $type
     * @param string $trigger
     *
     * @throws RuntimeException
     * @throws UnexpectedValueException
     *
     * @return EcrProjectAction
     */
    public static function getInstance($type, $trigger = 'precopy')
    {
        if('' == $type)
            throw new UnexpectedValueException(__METHOD__.' - Empty type is not allowed');

        $className = 'EcrProjectAction'.ucfirst($type);

        if(false == class_exists($className))
            throw new RuntimeException(__METHOD__.' - Class not found: '.$className);

        return new $className($trigger);
    }

    /**
     * Constructor.
     *
     * @param $trigger
     */
    public function __construct($trigger)
    {
        $this->trigger = $trigger;
    }

    /**
     * Get the display name of the action.
     *
     * @param $property
     *
     * @return string
     */
    public function __get($property)
    {
        if(true == in_array($property, $this->internals))
        {
            return $this->$property;
        }

        return '';
    }

    /**
     * Get publicly available properties.
     *
     * @return array
     */
    public function getProperties()
    {
        $properties = array();

        foreach($this as $k => $v)
        {
            if('internals' == $k || in_array($k, $this->internals))
                continue;

            $properties[$k] = $v;
        }

        return $properties;
    }

    /**
     * Set publicly available options.
     *
     * @param mixed $options Array or iterable object
     *
     * @return EcrProjectAction
     */
    public function setOptions($options)
    {
        foreach($options as $k => $v)
        {
            $this->$k = (string)$v;
        }

        return $this;
    }

    /**
     * Replace variables in a string.
     *
     * @param string          $string
     * @param EcrProjectZiper $ziper
     *
     * @return string
     */
    protected function replaceVars($string, EcrProjectZiper $ziper)
    {
        $string = str_replace('${temp_dir}', $ziper->temp_dir, $string);
        $string = str_replace('${j_root}', JPATH_ROOT, $string);

        return $string;
    }
}
