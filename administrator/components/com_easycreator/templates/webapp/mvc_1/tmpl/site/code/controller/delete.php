<?php
##*HEADER*##
/**
 * ECR_COM_NAME delete controller.
 *
 * @package     ECR_COM_NAME
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
        $model = new ECR_CLASS_PREFIXModelECR_UCF_COM_NAME;

        /* @var ECR_CLASS_PREFIXApplicationWeb $application */
        $application = JFactory::getApplication();
        $input = $application->input;

        if(false === $model->delete())
        {
            $application->addMessage('mother...', 'error');

            JLog::add('An error occured while deleting a record', JLog::ERROR);
        }
        else
        {
            $application->addMessage('Your ECR_COM_NAME has been deleted', 'success');

            JLog::add('A record has been deleted');
        }

        $input->set('view', 'list');
    }
}
