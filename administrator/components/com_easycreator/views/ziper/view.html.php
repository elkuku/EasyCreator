<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * HTML View class for the EasyCreator Component.
 *
 * @package    EasyCreator
 * @subpackage Views
 */
class EasyCreatorViewZiper extends JViewLegacy
{
    protected $zipResult = false;

    /**
     * @var EcrProjectBase
     */
    protected $project = null;

    /**
     * @var EcrProjectModelBuildpreset
     */
    protected $preset = null;

    /**
     * Standard display method.
     *
     * @param null|string $tpl The name of the template file to parse;
     *
     * @return mixed|void
     */
    public function display($tpl = null)
    {
        ecrLoadMedia('ziper');

        $task = JFactory::getApplication()->input->get('task');

        try
        {
            $this->project = EcrProjectHelper::getProject();
            $this->preset = $this->project->presets['default'];
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);

            EcrHtml::formEnd();

            return;
        }

        if(in_array($task, get_class_methods($this)))
            $this->$task();

        //-- Draw the submenu
        echo $this->displayBar();

        parent::display($tpl);

        EcrHtml::formEnd();
    }

    /**
     * Zipper view.
     *
     * @return void
     */
    private function ziper()
    {
        ecrScript('stuffer');

        $this->setLayout('ziper');
    }

    /**
     * Archive view.
     *
     * @return void
     */
    private function archive()
    {
        $this->setLayout('archive');
    }

    /**
     * Deletes a zip file.
     *
     * @return void
     */
    private function delete()
    {
        $this->setLayout('ziper');
    }

    /**
     * Displays the submenu.
     *
     * @return string html
     */
    private function displayBar()
    {
        $subTasks = array(
            array('title' => jgettext('Package')
            , 'description' => jgettext('Automatically create a package of your extension.')
            , 'icon' => 'package'
            , 'task' => array('ziper')
            )
            , array('title' => jgettext('Archive')
            , 'description' => jgettext('View archived versions of your extension.')
            , 'icon' => 'ecr_archive'
            , 'task' => 'archive'
            )
        );

        return EcrHtmlMenu::sub($subTasks);
    }
}
