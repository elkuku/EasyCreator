<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 03-Mar-08
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * HTML View class for the EasyCreator Component.
 *
 * @package EasyCreator
 * @subpackage Views
 */
class EasyCreatorViewEasyCreator extends JViewLegacy
{
    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        /*
         * Check for evil IE
         * sry - no support for this in penguin land =;)
         */

        jimport('joomla.environment.browser');

        $browser = new JBrowser;

        if($browser->getBrowser() == 'msie')
        {
            JFactory::getApplication()->enqueueMessage(
                jgettext('This extension is not compatible with MS internet explorer R'), 'warning');
        }

        //-- Check if we are on localhost - otherwise display a warning
        if(JComponentHelper::getParams('com_easycreator')->get('warn_livesite'))
        {
            $ip = getenv('REMOTE_ADDR');

	        if($ip && false == in_array($ip, array('127.0.0.1', '::1')))
	        {
                JFactory::getApplication()->enqueueMessage(
                    jgettext('Please use this extension only in local development environments.'), 'warning');
                JFactory::getApplication()->enqueueMessage(
                    sprintf(
                    jgettext("See: <a href=\"%s\">docs.joomla.org/Setting up your workstation for Joomla! development</a>")
                    , 'http://docs.joomla.org/Setting_up_your_workstation_for_Joomla!_development'), 'warning');
                JFactory::getApplication()->enqueueMessage(
                    jgettext('You may suppress this message in the configuration settings if you\'re shure'), 'warning');
            }
        }

        parent::display($tpl);

        EcrHtml::formEnd();
    }
}
