<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 03-Mar-08
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

jimport('joomla.application.component.view');

/**
 * HTML View class for the EasyCreator Component.
 *
 * @package EasyCreator
 * @subpackage Views
 */
class EasyCreatorViewConfig extends JView
{
    /**
     * Standard display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        ecrLoadHelper('parameter.parameter');

        switch(ECR_JVERSION)
        {
            case '1.5':
                $table = JTable::getInstance('component');
                $table->loadByOption('com_easycreator');
                break;
            case '1.6':
                $component = JComponentHelper::getComponent('com_easycreator');
                $table = JTable::getInstance('extension');
                $table->load($component->id);
                break;

            default:
                ecrHTML::displayMessage(sprintf(jgettext('Unsupported Joomla! version %s'), ECR_JVERSION));

                ecrHTML::easyFormEnd();

                return;
                break;
        }//switch

        $this->parameters = new ECRParameter($table->params, JPATH_COMPONENT.DS.'config.xml');

        parent::display($tpl);

        ecrHTML::easyFormEnd();
    }//function
}//class

/**
 * Enter description here ...@todo class description.
 *
 */
class EasyParameter extends JParameter {
    /**
     * Constructor.
     *
     * @param string $data The raw parms text.
     * @param string $path Path to the xml setup file.
     *
     * @since	1.5
     */
    public function __construct($data = '', $path = '')
    {
        parent::__construct($data, $path);

        return;
        // Set base path.
        $this->_elementPath[] = dirname(__FILE__).'/parameter/element';

        if($data = trim($data))
        {
            if(strpos($data, '{') === 0)
            {
                $this->loadJSON($data);
            }
            else
            {
                $this->loadINI($data);
            }
        }

        if($path)
        {
            $this->loadSetupFile($path);
        }

        $this->_raw = $data;
    }//function
}//class
