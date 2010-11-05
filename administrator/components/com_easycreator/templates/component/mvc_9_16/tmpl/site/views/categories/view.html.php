<?php
##*HEADER*##

jimport('joomla.application.component.view');

/**
 * HTML View class for the _ECR_COM_NAME_ component.
 *
 * @static
 * @package	_ECR_COM_NAME_
 * @subpackage	Views
 * @since 1.0
 */
class _ECR_COM_NAME_ViewCategories extends JView
{
    /**
     * _ECR_COM_NAME__ECR_LIST_POSTFIX_ view display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $document =& JFactory::getDocument();

        $categories	=& $this->get('data');
        $total =& $this->get('total');
        $state =& $this->get('state');

        //-- Get the page/component configuration
        $params = JComponentHelper::getParams('_ECR_COM_COM_NAME_');

        $menus = &JSite::getMenu();
        $menu = $menus->getActive();

        //-- Because the application sets a default page title, we need to get it
        //-- right from the menu item itself
        if(is_object($menu))
        {
            $menu_params = new JParameter($menu->params);

            if( ! $menu_params->get('page_title'))
            {
                $params->set('page_title', JText::_('_ECR_COM_NAME_'));
            }
        }
        else
        {
            $params->set('page_title', JText::_('_ECR_COM_NAME_'));
        }

        $document->setTitle($params->get('page_title'));

        //-- Set some defaults if not set for params
        $params->def('comp_description', JText::_('_ECR_COM_NAME__DESC'));

        //-- Define image tag attributes
        if($params->get('image') != -1)
        {
            $attribs['align'] =($params->get('image_align') != '') ? $params->get('image_align') : '';
            $attribs['hspace'] = 6;

            //-- Use the static HTML library to build the image tag
            $image = JHTML::_('image', 'images/stories/'.$params->get('image'), JText::_('_ECR_COM_NAME_'), $attribs);
        }

        for($i = 0; $i < count($categories); $i++)
        {
            $category =& $categories[$i];
            $category->link = JRoute::_('index.php?option=_ECR_COM_COM_NAME_&view=category&id='.$category->slug);

            //-- Prepare category description
            $category->description = JHTML::_('content.prepare', $category->description);
        }//for

        $this->assignRef('image', $image);
        $this->assignRef('params', $params);
        $this->assignRef('categories', $categories);

        parent::display($tpl);
    }//function
}//class
