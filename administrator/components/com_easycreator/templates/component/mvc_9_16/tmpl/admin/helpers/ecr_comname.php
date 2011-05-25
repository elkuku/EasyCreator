<?php
##*HEADER*##

/**
 * _ECR_COM_NAME_ Helper.
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Helpers
 */
abstract class _ECR_COM_NAME_Helper
{
    /**
     * Configure the Linkbar.
     *
     * @param string $viewName The name of the active view.
     */
    public static function addSubmenu($viewName = '_ECR_COM_TBL_NAME__ECR_LOWER_LIST_POSTFIX_')
    {
        JSubMenuHelper::addEntry(
        JText::_('_ECR_UPPER_COM_COM_NAME__LINKBAR')
        , 'index.php?option=_ECR_COM_COM_NAME_&view=_ECR_COM_TBL_NAME__ECR_LOWER_LIST_POSTFIX_'
        , $viewName == '_ECR_COM_TBL_NAME__ECR_LOWER_LIST_POSTFIX_'
        );

        JSubMenuHelper::addEntry(
        JText::_('_ECR_UPPER_COM_COM_NAME__CATEGORIES')
        , 'index.php?option=com_categories&extension=_ECR_COM_COM_NAME_'
        , $viewName == 'categories'
        );
    }//function
}//class
