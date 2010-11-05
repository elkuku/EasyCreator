<?php
##*HEADER*##

/**
 * _ECR_COM_NAME_ component helper.
 */
class _ECR_COM_NAME_Helper
{
    /**
     * Configure the Linkbar.
     */
    public static function addSubmenu($submenu)
    {
        JSubMenuHelper::addEntry(
        JText::_('_ECR_COM_NAME_')
        , 'index.php?option=_ECR_COM_COM_NAME_'
        , $submenu == '_ECR_COM_NAME_');

        JSubMenuHelper::addEntry(
        JText::_('_ECR_COM_NAME_ Categories')
        , 'index.php?option=com_categories&view=categories&extension=_ECR_COM_COM_NAME_'
        , $submenu == 'categories');

        $document = JFactory::getDocument();

        $document->addStyleDeclaration('.icon-48-_ECR_COM_COM_NAME_ '
        .'{background-image: url(../media/_ECR_COM_COM_NAME_/images/_ECR_COM_COM_NAME_-48x48.png);}');

        if($submenu == 'categories')
        {
            $document->setTitle(JText::_('_ECR_COM_NAME_').' - '.JText::_('Categories'));

            JToolBarHelper::title(JText::_('_ECR_COM_NAME_ Manager')
            . ': <small><small>[ '.JText::_('Categories').' ]</small></small>'
            , '_ECR_COM_COM_NAME_'
            );

            JToolBarHelper::preferences('_ECR_COM_COM_NAME_');
        }
    }//function
}//class
