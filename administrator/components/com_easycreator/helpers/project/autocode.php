<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath
 * @author     Created on 04-Sep-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Adds AutoCode to the project.
 */
class EcrProjectAutocode
{
    public $group = '';

    public $name = '';

    public $element = '';

    public $elements = array();

    public $scope = '';

    public $options = array();

    public $table = null;

    public $tables = array();

    public $fields = array();

    public $codes = array();

    protected $key = '';

    protected $tags = array();

    protected $enclose = false;

    /**
     * Constructor.
     *
     * @param string $scope Scope name
     * @param string $group Group name
     * @param string $name The name
     * @param string $element Element name
     */
    public function __construct($scope, $group, $name, $element)
    {
        $this->group = $group;
        $this->name = $name;
        $this->element = $element;
        $this->scope = $scope;

        $this->key = "$scope.$group.$name.$element";
    }//function

    /**
     * Get the autocode key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }//function

    /**
     * To be overridden.
     *
     * Subsequent classes will define text strings to be inserted.
     *
     * @param string     $type Code type
     * @param EcrTable $table
     *
     * @return string
     */
    public function getCode($type, EcrTable $table)
    {
    }//function

    /**
     * Encloses a string in AutoCode tags.
     *
     * @param string $text The text to enclose.
     * @param string $tag Title to be used in tag.
     * @param boolean $addPHPTags Enclose in PHP tags
     *
     * @return string
     */
    public function enclose($text, $tag, $addPHPTags = null)
    {
        if( ! $this->enclose)
        return $text;

        $pOpen =($this->enclose === 'php') ? '<?php ' : '';
        $pClose =($this->enclose === 'php') ? ' ?>' : '';
        $start = $pOpen.str_replace('TTTT', $tag, $this->_startTag).$pClose;
        $end = $pOpen.str_replace('TTTT', $tag, $this->_endTag).$pClose;

        return $start.NL.$text.$end;
    }//function

    /**
     * Format a key.
     *
     * @param string $key The key to be formatted
     *
     * @return string
     */
    public function getFormattedKey($key)
    {
        return $this->tags['start'].$key.$this->tags['end'];
    }//function

    /**
     * Replace AutoCode of a given key.
     *
     * @param string $string String to be replaced
     * @param string $key The replacing key
     *
     * @return string
     */
    public function replaceCode($string, $key)
    {
        if( ! isset($this->codes[$key]))
        {
            JFactory::getApplication()->enqueueMessage(sprintf(jgettext('AutoCode %s not found'), $key), 'error');

            return $string;
        }

        $sA = explode(NL, $string);
        $result = array();

        $started = false;

        foreach($sA as $s)
        {
            if(strpos($s, str_replace('TTTT', $key, $this->_endTag)) !== false)
            {
                //-- End tag found
                if( ! $started)
                {
                    JFactory::getApplication()->enqueueMessage(sprintf(jgettext('Match mismatch on %s'), $key), 'error');

                    return $string;
                }

                $started = false;

                continue;
            }

            if(strpos($s, str_replace('TTTT', $key, $this->_startTag)) !== false)
            {
                //-- Start tag found
                $result = array_merge($result, explode(NL, $this->codes[$key]));

                $started = true;

                continue;
            }

            if( ! $started)
            {
                $result[] = $s;
            }
        }//foreach

        return implode(NL, $result);
    }//function

    /**
     * Get the contents.
     *
     * @param string $string The string to search
     *
     * @return mixed [string Autocode | boolean false on errors]
     */
    public function getContents($string)
    {
        $results = array();

        $startExp = '%'.$this->getRegEx($this->_startTag, array('TTTT')).'%';
        $endExp = '%'.$this->getRegEx($this->_endTag, array('TTTT')).'%';

        $aString = explode("\n", $string);
        $actItem = '';

        foreach($aString as $str)
        {
            preg_match($startExp, $str, $startMatch);
            preg_match($endExp, $str, $endMatch);

            if($startMatch)
            {
                if( ! $actItem)
                {
                    $actItem = $startMatch[1];
                    $results[$actItem] = '';

                    continue;
                }
                else
                {
                    echo '<h3 style="color: red;">Match mismatch..</h3>missing end tag';

                    return false;
                }
            }

            if($endMatch)
            {
                if($actItem)
                {
                    $actItem = '';

                    continue;
                }
                else
                {
                    echo '<h3 style="color: red;">Match mismatch..</h3>missing start tag';

                    return false;
                }
            }

            if($actItem)
            {
                $results[$actItem] .= $str.NL;
            }
        }//foreach

        return $results;
    }//function

    /**
     * Get the fields contained in code.
     *
     * @param string $pattern Regex pattern to search for
     * @param string $string The string to search
     * @param array $keys The keys to search for
     *
     * @return array
     */
    public function getFields($pattern, $string, $keys)
    {
        $fields = array();

        if( ! count($keys))
        {
            return array();
        }

        $testExp = '%'.$this->getRegEx($pattern, $keys).'%';

        preg_match_all($testExp, $string, $matches);

        array_shift($matches);

        return $matches;
    }//function

    /**
     * Convert a string into a regular expression.
     *
     * @param string $string The string to convert.
     * @param array $tags Expected tags and values.
     *
     * @return string
     */
    protected function getRegEx($string, $tags)
    {
        $escapes = array('*', '[', ']', '.', '$', '?', '(', ')');

        foreach($escapes as $escape)
        {
            $string = str_replace($escape, "\\".$escape, $string);
        }//foreach

        foreach($tags as $t)
        {
            $string = str_replace($t, "([\#_A-Za-z0-9]*)", $string);
        }//foreach

        if(strpos($string, NL))
        {
            $ss = explode(NL, $string);
            $sa = array();

            foreach($ss as $s)
            {
                if($s)
                $sa[] = trim($s);
            }//foreach

            $string = implode('[\s]*+', $sa);
        }

        $string = str_replace(' ', '\s', $string);

        return $string;
    }//function

    /**
     * Get an element.
     *
     * @param string $name Element name
     * @param string $path Element path
     *
     * @return EcrProjectAutocode
     */
    protected function getElement($name, $path)
    {
        static $elements = array();

        if(isset($elements[$name]))
        {
            return $elements[$name];
        }

        $fileName = $path.DS.'elements'.DS.$name.'.php';

        if( ! JFile::exists($fileName))
        {
            JFactory::getApplication()->enqueueMessage(sprintf('Element %s not found', $name), 'error');

            $elements[$name] = false;

            return false;
        }

        require_once $fileName;

        $className = get_class($this).'Element'.ucfirst($name);

        if( ! class_exists($className))
        {
            JFactory::getApplication()->enqueueMessage(sprintf('Required class %s not found', $className), 'error');

            return false;
        }

        $elements[$name] = new $className;

        return $elements[$name];
    }//function
}//class
