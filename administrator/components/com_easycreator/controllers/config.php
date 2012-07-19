<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     Nikolai Plath
 * @author     Created on 12-Okt-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

jimport('joomla.application.component.controller');

/**
 * EasyCreator Controller.
 *
 * @package    EasyCreator
 * @subpackage Controllers
 */
class EasyCreatorControllerConfig extends EcrBaseController
{
    /**
     * Standard display method.
     *
     * @param bool       $cachable   If true, the view output will be cached
     * @param array|bool $urlparams  An array of safe url parameters and their variable types,
     *                               for valid values see {@link JFilterInput::clean()}.
     *
     * @return \JController|void
     */
    public function display($cachable = false, $urlparams = false)
    {
        if(class_exists('g11n'))
        {
            g11n::loadLanguage('com_easycreator.config');
        }

        JRequest::setVar('view', 'config');

        parent::display($cachable, $urlparams);
    }

    /**
     * Save the configuration.
     *
     * @throws Exception
     * @return void
     */
    public function save_config()
    {
        try
        {
            //-- @Joomla!-version-check
            switch(ECR_JVERSION)
            {
                case '1.5':
                    $table = JTable::getInstance('component');
                    $table->loadByOption('com_easycreator');
                    break;
                case '1.6':
                case '1.7':
                case '2.5':
                case '3.0':
                    $component = JComponentHelper::getComponent('com_easycreator');
                    $table = JTable::getInstance('extension');
                    $table->load($component->id);
                    break;

                default:
                    throw new Exception(__METHOD__.' - '.jgettext('Unsupported Joomla! version'));
                    break;
            }

            if( ! $table->bind(JRequest::get('post'))
                || ! $table->check()
                || ! $table->store()
            )
                throw new Exception($table->getError());

            $ecr_project = JRequest::getCmd('ecr_project');

            $adds = '';

            if(strpos($ecr_project, 'ecr') !== 0)
                $adds = ($ecr_project) ? '&view=stuffer&ecr_project='.$ecr_project : '';

            $this->setRedirect('index.php?option=com_easycreator'.$adds, jgettext('Configuration has been saved'));
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);

            EcrHtml::formEnd();
        }
    }

    public function cleanJoomlaCache()
    {
        $this->response->message = __METHOD__.' - Not imnplemented yet :(';

        echo $this->response;

        jexit();
    }

    public function cleanJoomlaTemp()
    {
        $tempPath = JFactory::getConfig()->get('tmp_path');

        $folders = JFolder::folders($tempPath);

        $cntFolders = 0;

        foreach($folders as $folder)
        {
            JFolder::delete($tempPath.'/'.$folder);

            $cntFolders ++;
        }

        $this->response->message = sprintf(
            jngettext('1 folder has been deleted', '%d folders have been deleted', $cntFolders)
            , $cntFolders);

        echo $this->response;

        jexit();
    }

    public function cleanEcrLogs()
    {
        $logfiles = JFolder::files(ECRPATH_LOGS, 'log', false, true);

        if(count($logfiles))
        {
            if(JFile::delete($logfiles))
            {
                $this->response->message = sprintf(
                    jngettext('1 logfile has been deleted', '%d logfiles have been deleted', count($logfiles))
                    , count($logfiles));
            }
            else
            {
                $this->response->message = jgettext('The logfiles could not be deleted');
                $this->response->status = 1;
            }
        }
        else
        {
            $this->response->message = jgettext('No logfiles found');
        }

        echo $this->response;

        jexit();
    }
}
