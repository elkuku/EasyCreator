<?php
##*HEADER*##

/**
 * ECR_COM_NAME default Controller.
 *
 * @package    ECR_COM_NAME
 * @subpackage Controllers
 */
class ECR_COM_NAMEController extends JControllerLegacy
{
    /**
     * Method to display the view.
     *
     * @param bool $cachable
     * @param bool $urlparams
     *
     * @return void
     */
    public function display($cachable = false, $urlparams = false)
    {
	    $input = JFactory::getApplication()->input;

        //-- Setting the default view
        $input->set('view', $input->get('view', 'ECR_COM_NAMEECR_LOWER_LIST_POSTFIX'));

        parent::display($cachable, $urlparams);

        ECR_COM_NAMEHelper::addSubmenu('ECR_COM_TBL_NAMEECR_LOWER_LIST_POSTFIX');
    }
}
