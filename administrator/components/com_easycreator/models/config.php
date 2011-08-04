<?php

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
    public function getForm($data = array(), $loadData = true)
    {
        JLoader::import('models.fields.formfield', JPATH_COMPONENT);

        $option = JRequest::getCmd('option');

        //-- Get the form.
        $form = $this->loadForm($option.'.config', 'config',
        array('control' => 'params', 'load_data' => $loadData));

        if(empty($form))
        throw new Exception(jgettext('Unable to load the config form'));

        $form->bind(JComponentHelper::getParams($option));

        return $form;
    }//function
}//class
