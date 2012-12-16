<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers.HTML
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 16-May-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * HTML wizard class.
 *
 * @package EasyCreator
 */
abstract class EcrHtmlWizard
{
    /**
     * Wizard
     * Displays the project information introduced so far.
     *
     * @param \EcrProjectBase  $project
     * @param array            $formFieldNames fields already displayed
     */
    public static function displayResult(EcrProjectBase $project, $formFieldNames = array())
    {
        ?>
    <div class="ecr_result">
        <h3><?php echo jgettext('Your extension so far'); ?></h3>

        <?php
        echo self::displayRow(jgettext('Type'), 'type', 'tpl_type', $project, $formFieldNames);
        echo self::displayRow(jgettext('Template'), 'tplName', 'tpl_name', $project, $formFieldNames);
        echo self::displayRow(jgettext('JVersion'), 'JCompat', 'jcompat', $project, $formFieldNames);

        echo '<div style="background-color: #fff; border: 1px solid gray; padding-left: 0.5em;">';

        $info = EcrProjectTemplateHelper::getTemplateInfo($project->type, $project->tplName);

        echo ($info) ? $info->description : '';

        echo '</div>';

        echo '<div class=extension" style="background-color: #ffff99; padding: 1em; font-size: 1.2em;">';
        echo self::displayRow(jgettext('Name'), 'name', 'com_name', $project, $formFieldNames);
        echo self::displayRow(jgettext('Version'), 'version', 'version', $project, $formFieldNames);
        echo self::displayRow(jgettext('Description'), 'description', 'description', $project, $formFieldNames);
        echo '</div>';

        echo '<div class="credits" style="background-color: #ffc;">';
        echo self::displayRow(jgettext('Author'), 'author', 'author', $project, $formFieldNames);
        echo self::displayRow(jgettext('Author e-mail'), 'authorEmail', 'authorEmail', $project, $formFieldNames);
        echo self::displayRow(jgettext('Author URL'), 'authorUrl', 'authorUrl', $project, $formFieldNames);
        echo self::displayRow(jgettext('License'), 'license', 'license', $project, $formFieldNames);
        echo self::displayRow(jgettext('Copyright (C)'), 'copyright', 'copyright', $project, $formFieldNames);

        echo self::displayRow(jgettext('List postfix'), 'listPostfix', 'list_postfix', $project, $formFieldNames);

        echo '</div>';
        ?></div>
    <?php
    }

    /**
     * Wizard form
     * displays a table row with a hidden formfield if not included in $formFieldNames
     *
     * @param string           $title
     * @param string           $property
     * @param string           $formFieldName
     * @param \EcrProjectBase  $project
     * @param array            $formFieldNames fields not to display
     *
     * @return string
     */
    private static function displayRow($title, $property, $formFieldName
        , EcrProjectBase $project, $formFieldNames)
    {
        if( ! $project->$property)
            return '';

        $return = array();
        $return[] = '<div class="ecr_table-row">';
        $return[] = '<div class="ecr_table-cell" style="width: 25%; font-weight: bold;">';
        $return[] = $title;
        $return[] = '</div>';
        $return[] = '<div class="ecr_table-cell">';
        $return[] = $project->$property;

        if(false == in_array($formFieldName, $formFieldNames))
        {
            $return[] = '<input type="hidden" name="'.$formFieldName.'"'
                .' value="'.$project->$property.'" />';
        }

        $return[] = '</div>';
        $return[] = '</div>';

        return implode("\n", $return);
    }
}
