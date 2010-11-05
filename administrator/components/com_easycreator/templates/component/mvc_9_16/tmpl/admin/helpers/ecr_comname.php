<?php
##*HEADER*##

/**
 * _ECR_COM_NAME_ Helper.
 *
 * @package    _ECR_COM_NAME_
 * @subpackage Helpers
 */
class _ECR_COM_NAME_Helper
{
    /**
     * Configure the Linkbar.
     *
     * @param string $vName The name of the active view.
     */
    public static function addSubmenu($vName = '_ECR_COM_TBL_NAME__ECR_LOW_LIST_POSTFIX_')
    {
        JSubMenuHelper::addEntry(
        JText::_('_ECR_COM_NAME_')
        , 'index.php?option=_ECR_COM_COM_NAME_&view=_ECR_COM_TBL_NAME__ECR_LOW_LIST_POSTFIX_'
        , $vName == '_ECR_COM_TBL_NAME__ECR_LOW_LIST_POSTFIX_'
        );

        JSubMenuHelper::addEntry(
        JText::_('Categories')
        , 'index.php?option=com_categories&extension=_ECR_COM_COM_NAME_'
        , $vName == 'categories'
        );
    }//function
}//class
