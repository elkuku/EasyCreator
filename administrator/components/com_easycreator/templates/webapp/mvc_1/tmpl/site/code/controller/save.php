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
        $input = JFactory::getApplication()->input;

        try
        {
            $model->save();

            echo '<div class="alert alert-success">Your ECR_COM_NAME has been saved</div>';

            $input->set('view', 'list');

            JLog::add('A record has been saved');
        }
        catch(UnexpectedValueException $e)
        {
            echo '<div class="alert alert-error">'.$e->getMessage().'</div>';

            $input->set('view', 'item');

            JLog::add($e->getMessage(), JLog::ERROR);
        }
    }
}
