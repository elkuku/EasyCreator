<?php
use Joomla\Uri\Uri;

defined('_JEXEC') || die('=;)');
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
    }

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
        }

        $input->set('view', 'templates');
        $input->set('task', 'templates');

        parent::display();
    }

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
        }

        $input->set('view', 'templates');
        $input->set('task', 'tplarchive');

        parent::display();
    }

    /**
     * Export EasyCreator extension templates.
     *
     * @since 0.0.1
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
            {
                throw new Exception(jgettext('No templates selected'));
            }

            $zipName = EcrProjectTemplateHelper::exportTemplates($exports, $input->getCmd('custom_name'));

            EcrHtml::message(sprintf('Templates have been exported to <b>%s</b>.', $zipName));
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);
        }

        $input->set('view', 'templates');
        $input->set('task', 'export');

        parent::display();
    }

    /**
     * Installs EasyCreator extension templates.
     *
     * @since 0.0.25.6
     *
     * @return void
     */
    public function do_installweb()
    {
        $input = JFactory::getApplication()->input;
        try
        {
            $packages = $input->get('packages', array(), 'array');

            if (!$packages) {
                throw new Exception('No packages selected');
            }

            $result = array();

            foreach ($packages as $package) {
                $result = array_merge($result, EcrProjectTemplateHelper::installPackageFromWeb($package));
            }

            if ($result['errors']) {
                EcrHtml::message($result['errors'], 'warning');

            }

            if ($result['installs']) {
                EcrHtml::message($result['installs']);
            }

            $input->set('task', 'templates');
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);

            $input->set('task', 'tplinstall');
        }

        $input->set('view', 'templates');

        parent::display();
    }

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
            EcrProjectTemplateHelper::installPackageFromUpload();

            EcrHtml::message(jgettext('Templates have been installed.'));

            $input->set('task', 'templates');
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);

            $input->set('task', 'tplinstall');
        }

        $input->set('view', 'templates');

        parent::display();
    }

    /**
     * Fetch template packages from GitHub.
     *
     * @since 0.0.25.6
     *
     * @return void
     */
    public function fetchTemplates()
    {
        $options = new JRegistry();

        // GitHub API calls require a user agent - this is agent kuku =;)
        $options->set('userAgent', 'elkuku');

        $github = new EcrGithubHelper($options);

        $releases = $github->repositories->releases->getList('EasyCreator', 'templates');

        jexit(json_encode($releases));
    }
}
