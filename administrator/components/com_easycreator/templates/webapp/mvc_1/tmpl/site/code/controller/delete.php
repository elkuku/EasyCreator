<?php
##*HEADER*##
/**
 * _ECR_COM_NAME_ delete controller.
 *
 * @package     _ECR_COM_NAME_
 * @subpackage  Controller
 */
class ECR_CLASS_PREFIXControllerDelete extends JControllerBase
{
    /**
     * Execute the controller.
     *
     * @return  boolean  True if controller finished execution, false if the controller did not
     *                   finish execution. A controller might return false if some precondition for
     *                   the controller to run has not been satisfied.
     *
     * @since            12.1
     * @throws  LogicException
     * @throws  RuntimeException
     */
    public function execute()
    {
        // TODO: Implement execute() method.

        $model = new ECR_CLASS_PREFIXModelECR_UCF_COM_NAME;
        $input = JFactory::getApplication()->input;

        if(false === $model->delete())
        {
            echo '<div class="alert alert-error">mother...</div>';

            JLog::add('An error occured while deleting a record', JLog::ERROR);
        }
        else
        {
            echo '<div class="alert alert-success">Your _ECR_COM_NAME_ has been deleted</div>';

            JLog::add('A record has been deleted');
        }

        $input->set('view', 'list');
    }
}
