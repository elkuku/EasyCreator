<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
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
        //-- @Joomla!-version-check
        switch(ECR_JVERSION)
        {
            case '1.5':
                $table = JTable::getInstance('component');
                $table->loadByOption('com_easycreator');

                JLoader::register('JElement', JPATH_COMPONENT.'/helpers/parameter/element.php');

                $this->parameters = new JParameter($table->params, JPATH_COMPONENT.'/models/forms/config_15.xml');

                $this->setLayout('default_15');
                break;
            case '1.6':
            case '1.7':
            case '2.5':
                try
                {
                    $this->form = $this->get('Form');
                }
                catch(Exception $e)
                {
                    EcrHtml::displayMessage($e);

                    EcrHtml::easyFormEnd();

                    return;
                }//try
                break;

            default:
                EcrHtml::displayMessage(__METHOD__.' - Unknown J! version', 'error');

                EcrHtml::easyFormEnd();

                return;
                break;
        }//switch

        parent::display($tpl);

        EcrHtml::easyFormEnd();
    }//function
}//class
