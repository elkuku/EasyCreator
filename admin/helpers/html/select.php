<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers.HTML
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 16-May-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * HTML select class.
 *
 * @package EasyCreator
 */
abstract class EcrHtmlSelect
{
    /**
     * Draws a project selector
     *
     * @return void
     */
    public static function project()
    {
        $projects = EcrProjectHelper::getProjectList();
        $projectTypes = EcrProjectHelper::getProjectTypes();
        $ecr_project = JFactory::getApplication()->input->get('ecr_project');

        $class = '';

        if($ecr_project == 'ecr_new_project')
        {
            $class = 'img3 icon16-add';
        }
        else if($ecr_project == 'ecr_register_project')
        {
            $class = 'img3 icon16-import';
        }
        else if($ecr_project)
        {
            try
            {
                $project = EcrProjectHelper::getProject();

                $class = 'img3 icon12-'.$project->type;
            }
            catch(Exception $e)
            {
                $do = 'nothing';
                unset($do);
            }
        }

        echo '<span class="'.$class.'">';
        echo NL.'<select style="font-size: 1.2em;" name="ecr_project" id="ecr_project" onchange="switchProject();">';
        echo NL.'<option value="">'.jgettext('Project').'...</option>';

        $selected = ($ecr_project == 'ecr_new_project') ? ' selected="selected"' : '';
        $class = ' class="img3 icon16-add"';
        echo NL.'<option'.$class.' value="ecr_new_project"'.$selected.'>'.jgettext('New Project').'</option>';

        $selected = ($ecr_project == 'ecr_register_project') ? ' selected="selected"' : '';
        $class = ' class="img3 icon16-import"';
        echo NL.'<option'.$class.' value="ecr_register_project"'.$selected.'>'.jgettext('Register Project').'</option>';

        /* @var EcrProjectBase $pType */
        foreach($projectTypes as $pTag => $pType)
        {
            if(isset($projects[$pTag])
                && count($projects[$pTag])
            )
            {
                echo NL.'<optgroup label="'.$pType->translateTypePlural().'">';

                /* @var EcrProjectBase $project */
                foreach($projects[$pTag] as $project)
                {
                    $displayName = $project->name;

                    if($project->scope)
                        $displayName .= ' ('.$project->scope.')';

                    $selected = ($project->fileName == $ecr_project) ? ' selected="selected"' : '';
                    $class = ' class="img12 icon12-'.$pTag.'"';
                    echo NL.'<option'.$class.' value="'.$project->fileName.'" label="'.$project->name.'"'.$selected.'>'
                        .$displayName.'</option>';
                }

                echo NL.'</optgroup>';
            }
        }

        echo NL.'</select></span>';
    }

    /**
     * @static
     *
     * @param string $scope
     *
     * @return string
     */
    public static function scope($scope = '')
    {
        if($scope)
        {
            echo jgettext('Scope').': <strong>'.$scope.'</strong>';
            echo '<input type="hidden" name="element_scope" value="'.$scope.'" />'.BR;

            return '';
        }
        ?>
    <strong id="element_scope_label"><?php echo jgettext('Scope');?></strong>
    &nbsp;:
    <select name="element_scope" id="element_scope">
        <option value=""><?php echo jgettext('Select'); ?></option>
        <option value="admin"><?php echo jgettext('Admin'); ?></option>
        <option value="site"><?php echo jgettext('Site'); ?></option>
    </select>
    <br/>
    <?php
        return 'element_scope';
    }

    /**
     * @static
     *
     * @param string $name
     * @param string $title
     *
     * @return string
     */
    public static function name($name = '', $title = '')
    {
        if('' == $title)
        {
            $title = jgettext('Name');
        }

        if($name)
        {
            echo '<div class="table_name">'.$title.' <big>'.$name.'</big></div>';
            echo '<input type="hidden" name="element_name" value="'.ucfirst($name).'" />';

            return '';
        }
        ?>
    <strong id="element_name_label"><?php echo $title; ?></strong>
    &nbsp;:
    <input
        type="text" id="element_name" name="element_name" value=""/>
    <br/>
    <?php
        return 'element_name';
    }

    /**
     * @static
     *
     * @param array $attribs
     *
     * @return string
     */
    public static function actions(array $attribs = array())
    {
        //-- aka autoload :P
        JHtml::_('select.option', 'foo');

        $options = array();

        $options[] = JHtmlSelect::option('', jgettext('Select...'));

        /* @var DirectoryIterator $fileInfo */
        foreach(new DirectoryIterator(JPATH_COMPONENT.'/helpers/project/action') as $fileInfo)
        {
            if($fileInfo->isDot())
                continue;

            $type = JFile::stripExt($fileInfo->getFilename());

            $action = EcrProjectAction::getInstance($type);

            $options[] = JHtmlSelect::option($type, EcrProjectAction::getInstance($type)->name);
        }

        if(JFolder::exists(ECRPATH_DATA.'/actions'))
        {
            $fileNames = JFolder::files(ECRPATH_DATA.'/actions', 'php');

            if($fileNames)
            {
                foreach($fileNames as $fileName)
                {
                    require_once ECRPATH_DATA.'/actions/'.$fileName;

                    $type = JFile::stripExt($fileName);

                    $action = EcrProjectAction::getInstance($type);

                    $options[] = JHtmlSelect::option('ecr_custom_'.$type, EcrProjectAction::getInstance($type)->name);
                }
            }
        }

        $attribs = array_merge(array(
                'class' => 'span2'
            )
            , $attribs);

        return JHtmlSelect::genericlist($options, 'sel_actions', array('list.attr' => $attribs));
    }

    /**
     * @static
     *
     * @param       $name
     * @param       $selected
     *
     * @param array $attribs
     *
     * @return string
     */
    public static function yesno($name, $selected, array $attribs = array())
    {
        //-- aka autoload :P
        JHtml::_('select.option', 'foo');

        $options = array(
            JHtmlSelect::option(0, jgettext('No')),
            JHtmlSelect::option(1, jgettext('Yes')),
        );

        $attribs = array_merge(array('class' => 'span4'), $attribs);

        return JHtmlSelect::genericlist($options, $name
            , array('list.attr' => $attribs)
            , 'value', 'text', $selected);
    }

    /**
     * @static
     *
     * @param EcrProjectBase $project
     * @param array          $attribs
     *
     * @return string
     */
    public static function presets(EcrProjectBase $project, array $attribs = array())
    {
        //-- aka autoload :P
        JHtml::_('select.option', 'foo');

        $options = array();

      //  $attribs = array_merge($attribs, array('class' => 'span4'));

        foreach($project->presets as $k => $v)
        {
            $options[] = JHtmlSelect::option($k, ucfirst($k));
        }

        $selected = $project->defaultPreset;

        return JHtmlSelect::genericlist($options, 'preset'
            , array('list.attr' => $attribs)
            , 'value', 'text', $selected);
    }

    /**
     * A release state selector.
     *
     * @static
     *
     * @param array $attribs
     *
     * @internal param string $name
     * @internal param string $selected
     *
     * @internal param string $releaseState
     *
     * @return string
     */
    //$name = 'releaseState', $id = 'releaseState', $selected = 'release')
    public static function releaseStates(array $attribs = array())
    {
        //-- aka autoload :P
        JHtml::_('select.option', 'foo');

        $attribs = array_merge(array(
                'name' => 'releaseState',
                'id' => 'releaseState',
                'selected' => 'release',
                )
            , $attribs
        );

        //$name = isset($attribs['name']) ? $attribs['name']

        $options = array();

        $states = array('release', 'development');

        foreach($states as $state)
        {
            $options[] = JHtmlSelect::option($state, ucfirst($state));
        }

        return JHtmlSelect::genericlist($options, $attribs['name']
            , $attribs
            , 'value', 'text', $attribs['selected'], $attribs['id']);
    }
}
