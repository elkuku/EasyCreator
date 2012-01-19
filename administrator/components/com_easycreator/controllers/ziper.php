<?php
/**
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     Nikolai Plath
 * @author     Created on 24-Sep-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

jimport('joomla.application.component.controller');

/**
 * EasyCreator Controller.
 *
 * @package    EasyCreator
 * @subpackage Controllers
 */
class EasyCreatorControllerZIPer extends JController
{
    private $response = array('status' => 0, 'message' => '', 'debug' => '');

    /**
     * Standard display method.
     *
     * @param boolean    $cachable  If true, the view output will be cached
     * @param array|bool $urlparams An array of safe url parameters and their variable types,
     *                              for valid values see {
     *
     * @link JFilterInput::clean()}.
     *
     * @return void
     * @see  JController::display()
     */
    public function display($cachable = false, $urlparams = false)
    {
        $ecr_project = JRequest::getCmd('ecr_project');

        if( ! $ecr_project)
        {
            //---NO PROJECT SELECTED - ABORT
            EcrHtml::easyFormEnd();

            return;
        }

        JRequest::setVar('view', 'ziper');

        parent::display($cachable, $urlparams);
    }//function

    /**
     * Zip dir view.
     *
     * @return void
     */
    public function zipdir()
    {
        JRequest::setVar('view', 'ziper');

        parent::display();
    }//function

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

            $this->response['message'] = jgettext('The file has been deleted');
        }
        catch(Exception $e)
        {
            $this->response['debug'] =(ECR_DEBUG) ? $this->response['debug'] = nl2br($e) : '';
            $this->response['message'] = $e->getMessage();
            $this->response['status'] = 1;
        }//try

        $buffer = ob_get_clean();

        if($buffer)
        {
            $this->response['status'] = 1;
            $this->response['debug'] .= $buffer;
        }

        echo json_encode($this->response);
    }//function
}//class
