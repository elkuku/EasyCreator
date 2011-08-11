<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 20-Apr-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

jimport('joomla.application.component.controller');

/**
 * EasyCreator Controller.
 *
 * @package    EasyCreator
 */
class EasyCreatorControllerAutoCode extends JController
{
    /**
     * Displays AutoCode.
     *
     * @return void
     */
    public function show()
    {
        $group = JRequest::getCmd('group');
        $part = JRequest::getCmd('part');
        $element = JRequest::getCmd('element');
        $scope = JRequest::getCmd('scope');

        $key = "$scope.$group.$part.$element";

        $AutoCode = EasyProjectHelper::getAutoCode($key);

        if( ! $AutoCode)
        {
            echo '<h4 style="color: red;">'.sprintf('AutoCode %s not found', $key).'</h4>';

            return;
        }

        if(method_exists($AutoCode, 'info'))
        {
            $info = $AutoCode->info();

            if( ! get_class($info) == 'EasyPart')
            {
                echo 'Part info must be a EasyPart class.. not : ';

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

        //--Additional request vars
        echo '<input type="hidden" name="group" value="'.$group.'" />';
        echo '<input type="hidden" name="part" value="'.$part.'" />';

        //--Additional options from part file
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
        //--Get the project
        try
        {
            $project = EasyProjectHelper::getProject();
        }
        catch(Exception $e)
        {
            ecrHTML::displayMessage($e);

            return;
        }//try

        $group = JRequest::getCmd('group');
        $part = JRequest::getCmd('part');
        $element = JRequest::getCmd('element');
        $scope = JRequest::getCmd('scope');

        $key = "$scope.$group.$part.$element";

        $AutoCode = EasyProjectHelper::getAutoCode($key);

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

        //--Additional request vars
        echo '<input type="hidden" name="group" value="'.$group.'" />';
        echo '<input type="hidden" name="part" value="'.$part.'" />';

        echo $AutoCode->edit($project->autoCodes[$AutoCode->getKey()]);

        return;
    }//function
}//class
