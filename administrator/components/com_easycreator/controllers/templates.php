<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     Nikolai Plath
 * @author     Created on 23-Sep-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator Controller.
 *
 * @package    EasyCreator
 * @subpackage Controllers
 */
class EasyCreatorControllerTemplates extends JControllerLegacy
{
    /**
     * Standard display method.
     *
     * @param bool       $cachable  If true, the view output will be cached
     * @param array|bool $urlparams An array of safe url parameters and their variable types,
     *                              for valid values see {@link JFilterInput::clean()}.
     *
     * @return \JController|void
     */
    public function display($cachable = false, $urlparams = false)
    {
        JRequest::setVar('view', 'templates');

        parent::display($cachable, $urlparams);
    }//function

    /**
     * Save a file.
     *
     * @return void
     */
    public function save()
    {
        try
        {
            EcrFile::saveFile();

            EcrHtml::message(jgettext('The file has been saved'));
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);
        }//try

        JRequest::setVar('view', 'templates');
        JRequest::setVar('task', 'templates');

        parent::display();
    }//function

    /**
     * Delete a file.
     *
     * @return void
     */
    public function delete()
    {
        try
        {
            EcrFile::deleteFile();

            JFactory::getApplication()->enqueueMessage(jgettext('Template has been deleted.'));
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);
        }//try

        JRequest::setVar('view', 'templates');
        JRequest::setVar('task', 'export');

        parent::display();
    }//function

    /**
     * Export EasyCreator extension templates.
     *
     * @throws Exception
     * @return void
     */
    public function do_export()
    {
        try
        {
            if( ! $exports = (array)JRequest::getVar('exports'))
            throw new Exception(jgettext('No templates selected'));

            EcrProjectTemplateHelper::exportTemplates($exports);

            EcrHtml::message(jgettext('Templates have been exported.'));
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);
        }//try

        JRequest::setVar('view', 'templates');
        JRequest::setVar('task', 'export');

        parent::display();
    }//function

    /**
     * Installs EasyCreator extension templates.
     *
     * @return void
     */
    public function do_install()
    {
        try
        {
            EcrProjectTemplateHelper::installTemplates();

            EcrHtml::message(jgettext('Templates have been installed.'));

            JRequest::setVar('task', 'templates');
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);

            JRequest::setVar('task', 'tplinstall');
        }//try

        JRequest::setVar('view', 'templates');

        parent::display();
    }//function
}//class
