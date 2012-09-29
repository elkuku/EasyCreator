<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     Nikolai Plath
 * @author     Created on 24-Sep-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator Controller.
 *
 * @package    EasyCreator
 * @subpackage Controllers
 */
class EasyCreatorControllerZIPer extends JControllerLegacy
{
    /**
     * @var EcrResponseJson
     */
    private $response = null;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct($config = array())
    {
        $this->response = new EcrResponseJson;

        parent::__construct($config);
    }

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
        JFactory::getApplication()->input->set('view', 'ziper');

        parent::display($cachable, $urlparams);
    }

    /**
     * Zip dir view.
     *
     * @return void
     */
    public function zipdir()
    {
        JFactory::getApplication()->input->set('view', 'ziper');

        parent::display();
    }

    /**
     * Delete a zip file.
     *
     * @return void
     */
    public function delete()
    {
        ob_start();

        try
        {
            EcrFile::deleteFile();

            $this->response->message = jgettext('The file has been deleted');
        }
        catch(Exception $e)
        {
            $this->response->debug .= (ECR_DEBUG) ? nl2br($e) : '';
            $this->response->message = $e->getMessage();
            $this->response->status = 1;
        }

        $buffer = ob_get_clean();

        if($buffer)
        {
            $this->response->status = 1;
            $this->response->debug .= $buffer;
        }

        echo $this->response;

        jexit();
    }

    public function createPackage()
    {
        $input = JFactory::getApplication()->input;

        ob_start();

        try
        {
            $result = new stdClass;

            $buildopts = $input->get('buildopts', array(), 'array');
            $presetName = $input->get('preset');

            $buildOpts = array();

            foreach($buildopts as $v)
            {
                $buildOpts[$v] = true;
            }

            $project = EcrProjectHelper::getProject();

            $ziper = new EcrProjectZiper;

            $preset = $project->getPreset($presetName)->loadValues($buildopts);

            $result->result = $ziper->create($project, $preset, $buildOpts);
            $result->errors = $ziper->getErrors();

            $result->downloadLinks = $ziper->getCreatedFiles();
            $result->log = $ziper->printLog();

            if($result->errors)
            {
                $this->response->message = jgettext('Your ZIPfile has NOT been created');
                $this->response->status = 1;
                $this->response->debug = '<ul><li>'.implode('</li><li>', $result->errors).'</li></ul>';
            }
            else
            {
                if(count($result->downloadLinks))
                {
                    $m = '';

                    $m .= jgettext('Your ZIPfile has been created sucessfully');

                    $m .= '<ul class="downloadLinks">';
                    $m .= '<li><strong>'.jgettext('Downloads').'</strong></li>';

                    /* @var EcrProjectZiperCreatedfile $link */
                    foreach($result->downloadLinks as $link)
                    {
                        $alt =($link->alternateDownload)
                            ? ' (<a href="'.$link->alternateDownload.'">'.$link->alternateDownload.'</a>)'
                            : '';

                        $m .= '<li><a href="'.$link->downloadUrl.'">'.$link->name.'</a><'.$alt.'/li>';
                    }

                    $m .= '</ul>';

                    $this->response->message = $m;
                }
                else
                {
                    $this->response->message = jgettext('No download available');
                    $this->response->status = 1;
                }
            }

            if($result->log)
            {
                $m = '';
                $m .= '<div class="ecr_codebox_header" style="font-size: 1.4em;"'
                    .'onclick="toggleDiv(\'ecr_logdisplay\');">'
                    .jgettext('Log File')
                    .'</div>';
                $m .= '<div id="ecr_logdisplay" style="display: none;">'
                    .$result->log
                    .'</div>';
                $this->response->message .= $m;
            }
        }
        catch(Exception $e)
        {
            $this->response->debug = (ECR_DEBUG) ? nl2br($e) : '';
            $this->response->message = $e->getMessage();
            $this->response->status = 1;
        }

        $buffer = ob_get_clean();

        if($buffer)
        {
            $this->response->status = 1;
            $this->response->debug .= $buffer;
        }

        echo $this->response;

        jexit();
    }

    /**
     * Format a file name.
     *
     * @return void
     * @todo error handling
     */
    public function updateProjectName()
    {
        try
        {
            $project = EcrProjectHelper::getProject();
            $this->response->message = EcrProjectHelper::formatFileName(
                $project, JFactory::getApplication()->input->getString('cst_format'));
        }
        catch(Exception $e)
        {
            $this->response->status = 1;
            $this->response->message = $e->getMessage();
            $this->response->debug = $e->getTraceAsString();
        }

        echo $this->response;

        jexit();
    }
}
