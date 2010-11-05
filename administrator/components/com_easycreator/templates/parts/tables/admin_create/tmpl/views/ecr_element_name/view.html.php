<?php
##*HEADER*##

jimport('joomla.application.component.view');

/**
 * Enter description here ...@todo class doccomment.
 *
 */
class _ECR_COM_NAME_sView_ECR_ELEMENT_NAME_ extends JView
{
    /**
     * _ECR_COM_NAME_s view display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        JHTML::stylesheet('_ECR_COM_NAME_.css', 'administrator/components/com__ECR_COM_NAME_/assets/');

        //Data from model
        $item =& $this->get('Data');
        $isNew = ($item->id < 1);
        $text = $isNew ? JText::_('New') : JText::_('Edit');

        JToolBarHelper::title('&nbsp;&nbsp;'
        .JText::_('_ECR_ELEMENT_NAME_').': <small><small>[ '.$text.' ]</small></small>', '_ECR_ELEMENT_NAME_');

        JToolBarHelper::save();
        JToolBarHelper::cancel();

        ##ECR_SMAT_DESCRIPTION_VIEW0##

        ##ECR_SMAT_PUBLISHED_VIEW0##

        $this->assignRef('item', $item);
        parent::display($tpl);
    }//function
}//class
