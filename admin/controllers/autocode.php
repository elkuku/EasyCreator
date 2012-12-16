<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     Nikolai Plath
 * @author     Created on 20-Apr-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator Controller.
 *
 * @package    EasyCreator
 */
class EasyCreatorControllerAutoCode extends JControllerLegacy
{
    /**
     * Displays AutoCode.
     *
     * @return void
     */
    public function show()
    {
        $input = JFactory::getApplication()->input;

        $group = $input->get('group');
        $part = $input->get('part');
        $element = $input->get('element');
        $scope = $input->get('scope');

        $key = "$scope.$group.$part.$element";

        $AutoCode = EcrProjectHelper::getAutoCode($key);

        if( ! $AutoCode)
        {
            echo '<h4 style="color: red;">'.sprintf('AutoCode %s not found', $key).'</h4>';

            return;
        }

        if(method_exists($AutoCode, 'info'))
        {
            $info = $AutoCode->info();

            if( ! get_class($info) == 'EcrProjectPart')
            {
                echo 'Part info must be a EcrProjectPart class.. not : ';

                echo get_class($info);

                return;
            }

            echo $info->format('erm', 'add');
        }
        else
        {
            echo '<div style="color: blue; font-weight: bold; text-align:center;">'
            .ucfirst($group).' - '.ucfirst($part)
            .'</div>';
        }

        //-- Additional request vars
        echo '<input type="hidden" name="group" value="'.$group.'" />';
        echo '<input type="hidden" name="part" value="'.$part.'" />';

        //-- Additional options from part file
        if( ! method_exists($AutoCode, 'getOptions'))
        {
            echo '<h4 style="color: red;"><tt>getOptions()</tt> not found</h4>';
        }
        else
        {
            echo $AutoCode->getOptions();
        }

        return;
    }//function

    /**
     * Edit AutoCode.
     *
     * @return void
     */
    public function edit()
    {
        $input = JFactory::getApplication()->input;

        try
        {
            //-- Get the project
            $project = EcrProjectHelper::getProject();
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);

            return;
        }//try

        $group = $input->get('group');
        $part = $input->get('part');
        $element = $input->get('element');
        $scope = $input->get('scope');

        $key = "$scope.$group.$part.$element";

        $AutoCode = EcrProjectHelper::getAutoCode($key);

        if( ! $AutoCode)
        {
            echo '<h4 style="color: red;">AutoCode not found</h4>';
            echo $key;

            return;
        }

        if( ! method_exists($AutoCode, 'edit'))
        {
            echo '<h4 style="color: red;">EDIT function not found</h4>';
            echo $key;

            return;
        }

        if( ! isset($project->autoCodes[$AutoCode->getKey()]))
        {
            echo '<h4 style="color: red;">AutoCode not found in project</h4>';
            echo $AutoCode->getKey();

            return;
        }

        echo '<div style="color: blue; font-weight: bold; text-align:center;">'
        .ucfirst($group).' - '.ucfirst($part)
        .'</div>';

        //-- Additional request vars
        echo '<input type="hidden" name="group" value="'.$group.'" />';
        echo '<input type="hidden" name="part" value="'.$part.'" />';

        echo $AutoCode->edit($project->autoCodes[$AutoCode->getKey()]);

        return;
    }//function
}//class
