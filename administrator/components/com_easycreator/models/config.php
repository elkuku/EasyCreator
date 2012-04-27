<?php

//-- @Joomla!-compat 1.5
if('1.5' == ECR_JVERSION)
{
    /**
     * Extending JModel.
     */
    class EasyCreatorModelConfig extends JModel
    {
    }//class

    return;
}

jimport('joomla.application.component.modeladmin');

/**
 * Prototype admin model.
 */
class EasyCreatorModelConfig extends JModelAdmin
{
    /**
     * Method for getting the form from the model.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm|bool  A JForm object on success, false on failure
     *
     * @since   11.1
     */
    public function getForm($data = array(), $loadData = true)
    {
        JLoader::import('models.fields.formfield', JPATH_COMPONENT);

        $option = JRequest::getCmd('option');

        //-- Get the form.
        $form = $this->loadForm($option.'.config', 'config'
        , array('control' => 'params', 'load_data' => $loadData));

        if(empty($form))
        throw new Exception(jgettext('Unable to load the config form'));

        $form->bind(JComponentHelper::getParams($option));

        return $form;
    }//function
}//class
