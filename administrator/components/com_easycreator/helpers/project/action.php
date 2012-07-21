<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 22-May-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator project action base class.
 *
 * @property-read string $type
 * @property-read string $name
 * @property-read string $event
 * @property-read string $fixedEvent
 * @property-read array  $replacements
 */
abstract class EcrProjectAction
{
    protected $type = '';

    protected $name = '';

    protected $event = '';

    protected $fixedEvent = '';

    protected $internals = array('type', 'name', 'event', 'fixedEvent', 'replacements');

    private $replacements = array();

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
     * @return EcrProjectAction
     */
    abstract public function run(EcrProjectZiper $ziper);

    /**
     * @param string $type
     * @param string $event
     *
     * @throws RuntimeException
     * @throws UnexpectedValueException
     *
     * @return EcrProjectAction
     */
    public static function getInstance($type, $event = 'precopy')
    {
        if('' == (string)$type)
            throw new UnexpectedValueException(__METHOD__.' - Empty type is not allowed');

        $aType = $type;

        if(0 === strpos($aType, 'ecr_custom_'))
        {
            $aType = substr($aType, 11);

            $fileName = $aType.'.php';

            require_once ECRPATH_DATA.'/actions/'.$fileName;
        }

        $className = 'EcrProjectAction'.ucfirst($aType);

        if(false == class_exists($className))
            throw new RuntimeException(__METHOD__.' - Class not found: '.$className);

        $class = new $className($type, $event);

        if(false == ($class instanceof EcrProjectAction))
            throw new UnexpectedValueException(
                sprintf('The class %s must extend the class %s', $className, 'EcrProjectAction'));

        return $class;
    }

    /**
     * Constructor.
     *
     * @param $type
     * @param $event
     */
    public function __construct($type, $event)
    {
        $this->type = $type;

        $this->event = $event;
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
     * @param       $cnt
     * @param       $name
     * @param       $title
     * @param array $options
     *
     * @return string
     */
    protected function getLabel($cnt, $name, $title, array $options = array())
    {
        $options = array_merge(array(
                'class' => 'inline'
            )
            , $options);

        $oString = '';

        foreach($options as $o => $v)
        {
            $oString .= ' '.$o.'="'.$v.'"';
        }

        return '<label'.$oString.' for="fields_'.$cnt.'_'.$name.'">'.$title.'</label>';
    }

    /**
     * @param       $cnt
     * @param       $name
     * @param       $value
     * @param array $options
     *
     * @return string
     */
    protected function getInput($cnt, $name, $value, array $options = array())
    {
        $options = array_merge(array(
                'type' => 'text'
            )
            , $options);

        $oString = '';

        foreach($options as $o => $v)
        {
            $oString .= ' '.$o.'="'.$v.'"';
        }

        return '<input'
            .$oString
            .' name="fields['.$cnt.']['.$name.']"'
            .' id="fields_'.$cnt.'_'.$name.'"'
            .' value="'.$value.'">';
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
        $string = str_replace('${package_path}', $ziper->preset->buildFolder, $string);

        /* @var EcrProjectZiperCreatedfile $file */
        foreach($ziper->getCreatedFiles() as $file)
        {
            /*
            $path = $download;

            if(0 === strpos($download, 'file://'))
                $path = substr($download, 7);
*/
            $string = str_replace('${package_'.JFile::getExt($file->name).'}', $file->path, $string);
        }

        return $string;
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
            if('internals' == $k)
                continue;

            $o->$k = $v;
        }

        return json_encode($o);
    }

    /**
     * Abort the build process if set.
     *
     * @param string          $msg
     * @param EcrProjectZiper $ziper
     *
     * @throws EcrExceptionZiper
     * @return EcrProjectAction
     */
    protected function abort($msg, EcrProjectZiper $ziper)
    {
        if($this->abort)
            throw new EcrExceptionZiper($msg, 1);

        $ziper->logger->log($msg, 'Action', JLog::ERROR);

        return $this;
    }
}
