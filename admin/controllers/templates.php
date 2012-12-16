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
        JFactory::getApplication()->input->set('view', 'templates');

        parent::display($cachable, $urlparams);
    }//function

    /**
     * Save a file.
     *
     * @return void
     */
    public function save()
    {
        $input = JFactory::getApplication()->input;

        try
        {
            EcrFile::saveFile();

            EcrHtml::message(jgettext('The file has been saved'));
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);
        }//try

        $input->set('view', 'templates');
        $input->set('task', 'templates');

        parent::display();
    }//function

    /**
     * Delete a file.
     *
     * @return void
     */
    public function delete()
    {
        $input = JFactory::getApplication()->input;

        try
        {
            EcrFile::deleteFile();

            JFactory::getApplication()->enqueueMessage(jgettext('Template has been deleted.'));
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);
        }//try

        $input->set('view', 'templates');
        $input->set('task', 'tplarchive');

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
        $input = JFactory::getApplication()->input;

        try
        {
            if( ! $exports = $input->get('exports', array(), 'array'))
                throw new Exception(jgettext('No templates selected'));

            EcrProjectTemplateHelper::exportTemplates($exports);

            EcrHtml::message(jgettext('Templates have been exported.'));
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);
        }//try

        $input->set('view', 'templates');
        $input->set('task', 'export');

        parent::display();
    }//function

    /**
     * Installs EasyCreator extension templates.
     *
     * @return void
     */
    public function do_install()
    {
        $input = JFactory::getApplication()->input;

        try
        {
            EcrProjectTemplateHelper::installTemplates();

            EcrHtml::message(jgettext('Templates have been installed.'));

            $input->set('task', 'templates');
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);

            $input->set('task', 'tplinstall');
        }//try

        $input->set('view', 'templates');

        parent::display();
    }//function
}//class
