<?php defined('_JEXEC') || die('=;)');
/**
 * @package
 * @subpackage
 * @author     Nikolai Plath
 * @author     Created on 09.04.2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * HTML View class for the EasyCreator Component.
 *
 * @package EasyCreator
 * @subpackage Views
 */
class EasyCreatorViewCodeEye extends JViewLegacy
{
    /**
     * @var EcrProjectBase
     */
    protected $project = null;

    /**
     * @var EcrProjectMatrix
     */
    protected $matrix;

    /**
     * Standard display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $response = array();

        //-- Get the project
        try
        {
            $project = EcrProjectHelper::getProject();
        }
        catch(Exception $e)
        {
            $response['status'] = 0;
            $response['text'] = $e->getMessage();

            EcrHtml::formEnd();

            return;
        }//try

        $this->matrix = new EcrProjectMatrix($project, 'pcharts');

        $response['status'] = 1;

        $files = array();
        $size = array();
        $lines = array();

        foreach($this->matrix->projectData as $type => $data)
        {
            $files[] = $data['files'];
            $size[] = $data['size'];
            if(isset($data['lines'])) $lines[] = $data['lines'];
        }//foreach

        $response['files'] = implode(',', $files);
        $response['size'] = implode(',', $size);
        $response['lines'] = implode(',', $lines);
        $response['labels'] = implode(',', array_keys($this->matrix->projectData));

        ob_start();
        parent::display();
        $response['table'] = ob_get_contents();
        ob_end_clean();
        $response['text'] = 'Hi U';

        echo json_encode($response);
    }//function
}//class
