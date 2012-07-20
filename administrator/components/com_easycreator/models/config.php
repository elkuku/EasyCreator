<?php  defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Models
 * @author     Nikolai Plath
 * @author     Created on 03-Mar-11
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//jimport('joomla.application.component.modeladmin');

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
     * @throws Exception
     * @return bool|\JForm|mixed A JForm object on success, false on failure
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
