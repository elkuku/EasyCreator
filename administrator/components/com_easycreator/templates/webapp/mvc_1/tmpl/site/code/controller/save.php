<?php
##*HEADER*##
/**
 * ECR_COM_NAME save controller.
 *
 * @package     ECR_COM_NAME
 * @subpackage  Controller
 */
class ECR_CLASS_PREFIXControllerSave extends JControllerBase
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

        try
        {
            $model->save();

            $application->addMessage('Your ECR_COM_NAME has been saved', 'success');

            $input->set('view', 'list');

            JLog::add('A record has been saved');
        }
        catch(UnexpectedValueException $e)
        {
            $application->addMessage($e->getMessage(), 'error');

            $input->set('view', 'item');

            JLog::add($e->getMessage(), JLog::ERROR);
        }
    }
}
