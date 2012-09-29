<?php
##*HEADER*##

/**
 * HTML View class for the ECR_COM_NAME component.
 *
 * @static
 * @package	ECR_COM_NAME
 * @subpackage	Views
 */
class ECR_COM_NAMEViewCategories extends JViewLegacy
{
	protected $image = '';

	protected $params;

	protected $categories;

    /**
     * ECR_COM_NAMEECR_LIST_POSTFIX view display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $categories	= $this->get('data');

        //-- Get the page/component configuration
        $params = JComponentHelper::getParams('ECR_COM_COM_NAME');

        $menu = JSite::getMenu()->getActive();

        //-- Because the application sets a default page title, we need to get it
        //-- right from the menu item itself
        if(is_object($menu))
        {
            if( ! $menu->params->get('page_title'))
            {
                $params->set('page_title', JText::_('ECR_COM_NAME'));
            }
        }
        else
        {
            $params->set('page_title', JText::_('ECR_COM_NAME'));
        }

        JFactory::getDocument()->setTitle($params->get('page_title'));

        //-- Set some defaults if not set for params
        $params->def('comp_description', JText::_('ECR_COM_NAME_DESC'));

        //-- Define image tag attributes
        if($params->get('image') != -1)
        {
            $attribs['align'] =($params->get('image_align') != '') ? $params->get('image_align') : '';
            $attribs['hspace'] = 6;

            //-- Use the static HTML library to build the image tag
            $this->image = JHTML::_('image', 'images/stories/'.$params->get('image'), JText::_('ECR_COM_NAME'), $attribs);
        }

        for($i = 0; $i < count($categories); $i++)
        {
            $category =& $categories[$i];
            $category->link = JRoute::_('index.php?option=ECR_COM_COM_NAME&view=category&id='.$category->slug);

            //-- Prepare category description
            $category->description = JHTML::_('content.prepare', $category->description);
        }//for

        $this->params = $params;
        $this->categories= $categories;

        parent::display($tpl);
    }
}
