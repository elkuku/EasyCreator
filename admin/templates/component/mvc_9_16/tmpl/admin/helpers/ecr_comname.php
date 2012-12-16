<?php
##*HEADER*##

/**
 * ECR_COM_NAME Helper.
 *
 * @package    ECR_COM_NAME
 * @subpackage Helpers
 */
abstract class ECR_COM_NAMEHelper
{
    /**
     * Configure the Linkbar.
     *
     * @param string $viewName The name of the active view.
     */
    public static function addSubmenu($viewName = 'ECR_COM_TBL_NAMEECR_LOWER_LIST_POSTFIX')
    {
        JSubMenuHelper::addEntry(
        JText::_('ECR_UPPER_COM_COM_NAME_LINKBAR')
        , 'index.php?option=ECR_COM_COM_NAME&view=ECR_COM_TBL_NAMEECR_LOWER_LIST_POSTFIX'
        , $viewName == 'ECR_COM_TBL_NAMEECR_LOWER_LIST_POSTFIX'
        );

        JSubMenuHelper::addEntry(
        JText::_('ECR_UPPER_COM_COM_NAME_CATEGORIES')
        , 'index.php?option=com_categories&extension=ECR_COM_COM_NAME'
        , $viewName == 'categories'
        );
    }//function
}//class
