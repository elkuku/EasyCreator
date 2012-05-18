<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     Nikolai Plath
 * @author     Created on 24-Sep-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

jimport('joomla.application.component.controller');

/**
 * EasyCreator Controller.
 *
 * @package    EasyCreator
 * @subpackage Controllers
 */
class EasyCreatorControllerZIPer extends JController
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
        JRequest::setVar('view', 'ziper');

        parent::display($cachable, $urlparams);
    }

    /**
     * Zip dir view.
     *
     * @return void
     */
    public function zipdir()
    {
        JRequest::setVar('view', 'ziper');

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
    }

    public function createPackage()
    {
        ob_start();

        try
        {
            $result = new stdClass;

            $buildopts = JRequest::getVar('buildopts', array());

            $project = EcrProjectHelper::getProject();

            $ziper = new EcrProjectZiper;

            $result->result = $ziper->create($project, $buildopts);
            $result->errors = $ziper->getErrors();

            $result->downloadLinks = $ziper->getDownloadLinks();
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

                    foreach($result->downloadLinks as $link)
                    {
                        $m .= '<li><a href="'.$link.'">'.JFile::getName(JPath::clean($link)).'</a></li>';
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
                $m .= '<div class="ecr_codebox_header" style="font-size: 1.4em;" onclick="toggleDiv(\'ecr_logdisplay\');">'
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
    }
}
