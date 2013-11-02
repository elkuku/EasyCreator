<?php
/**
 * @package     EasyCreator
 * @subpackage  Frontent
 * @author      Nikolai Plath (elkuku) <der.el.kuku@gmail.com>
 * @created     24-Sep-2008
 * @copyright   2008 - now()
 * @license     GPL http://gnu.org
 */

defined('_JEXEC') || die('=;)');

jimport('joomla.application.component.view');

/**
 * HTML View class for the EasyCreator Component.
 *
 * @package     EasyCreator
 * @subpackage  Frontent
 */
class EasyCreatorViewEasyCreator extends JViewLegacy
{
    /**
     * Execute and display a template script.
     *
     * @param string $tpl The name of the template file to parse.
     *
     * @return mixed|void
     */
    public function display($tpl = null)
    {
        //--get vars from request
        $selectedProject = JFactory::getApplication()->input->get('ecr_project');

        if($selectedProject)
        {
            echo '<strong>'.$selectedProject.'</strong><hr />';
            $prefix = substr($selectedProject, 0, 3);

            switch($prefix)
            {
                case 'com':
                    $this->setLayout('component');
                    break;

                case 'mod':
                    $this->setLayout('module');
                    break;

                default:
                    echo '<h3 style="color: blue;">not yet...</h3>';
                    echo 'render: '.$selectedProject;
                    break;
            }
        }

        $this->selectedProject = $selectedProject;

        parent::display($tpl);
    }
}
