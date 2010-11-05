<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 18-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

// Register the element class with the loader.
JLoader::register('JElement', dirname(__FILE__).'/element.php');

/**
 * Enter description here ...
 *
 */
class ECRParameter extends JParameter
{
    /**
     * Render the form control.
     *
     * @param string $name An optional name of the HTML form control. The default is 'params' if not supplied.
     * @param string $group An optional group to render.  The default group is used if not supplied.
     *
     * @return string	HTML
     */
    public function render($name = 'params', $group = '_default')
    {
        if( ! isset($this->_xml[$group]))
        {
            return false;
        }

        $params = $this->getParams($name, $group);
        $html = array ();

        if($description = $this->_xml[$group]->attributes('description'))
        {
            // add the params description to the display
            $desc	= jgettext($description);
            $html[]	= '<p class="paramrow_desc">'.$desc.'</p>';
        }

        foreach($params as $param)
        {
            if($param[0])
            {
                $html[] = $param[0];
                $html[] = $param[1];
            }
            else
            {
                $html[] = $param[1];
            }
        }//foreach

        if(count($params) < 1)
        {
            $html[] = "<p class=\"noparams\">".jgettext('No parameters for this item')."</p>";
        }

        return implode(PHP_EOL, $html);
    }//function

    /**
     * Loads an element type.
     *
     * @param string $type The element type.
     * @param boolean $new False (default) to reuse parameter elements; true to load the parameter element type again.
     *
     * @return object
     */
    public function loadElement($type, $new = false)
    {
        $signature = md5($type);

        if((isset($this->_elements[$signature])
        && !($this->_elements[$signature] instanceof __PHP_Incomplete_Class))
        && $new === false)
        {
            return	$this->_elements[$signature];
        }

        $elementClass	=	'JElement'.$type;

        if( ! class_exists($elementClass))
        {
            if(isset($this->_elementPath))
            {
                $dirs = $this->_elementPath;
            }
            else
            {
                $dirs = array();
            }

            $file = JFilterInput::getInstance()->clean(str_replace('_', DS, $type).'.php', 'path');

            jimport('joomla.filesystem.path');

            if($elementFile = JPath::find($dirs, $file))
            {
                include_once $elementFile;
            }
            else
            {
                return false;
            }
        }

        if( ! class_exists($elementClass))
        {
            return false;
        }

        $this->_elements[$signature] = new $elementClass($this);

        return $this->_elements[$signature];
    }//function
}//class
