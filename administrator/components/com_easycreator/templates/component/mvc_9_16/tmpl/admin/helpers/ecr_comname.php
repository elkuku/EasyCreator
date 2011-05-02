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
        JText::_('_ECR_COM_NAME_')
        , 'index.php?option=_ECR_COM_COM_NAME_&view=_ECR_COM_TBL_NAME__ECR_LOWER_LIST_POSTFIX_'
        , $viewName == '_ECR_COM_TBL_NAME__ECR_LOWER_LIST_POSTFIX_'
        );

        JSubMenuHelper::addEntry(
        JText::_('Categories')
        , 'index.php?option=com_categories&extension=_ECR_COM_COM_NAME_'
        , $viewName == 'categories'
        );

        if ($viewName == 'categories')
        {
            $document = JFactory::getDocument();

            $document->addStyleDeclaration(
        	'.icon-48-_ECR_LOWER_COM_NAME-categories '
        	.'{background-image: url(components/_ECR_COM_COM_NAME_/assets/images/_ECR_COM_COM_NAME_-48.png) !important;}'); //dirty ;(

        	//-- Fixed J! 1.6 ""constant""
        	$J_OneSix_CategoryLanguageKey = strtoupper('_ECR_COM_NAME_'.'_ADMINISTRATION_CATEGORIES');//@todo: do better =;)

        	$document->setTitle(JText::_($J_OneSix_CategoryLanguageKey));
        }
    }//function
}//class
