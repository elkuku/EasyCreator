<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath
 * @author     Created on 30-May-2010
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

if ('package' != $this->project->type)
    return;

$projects = EcrProjectHelper::getProjectList();
$projectTypes = EcrProjectHelper::getProjectTypes();

?>

<div class="ecr_floatbox">
    <div class="infoHeader imgbarleft icon24-package_creation"><?php echo jgettext('Package elements') ?></div>
    <input type="hidden" name="package_elements" id="packageElements"/>

    <div style="float: left;">
        <h4><?php echo jgettext('Your package'); ?></h4>
        <ul id="package-to" class="sortable">
            <?php
            foreach ($this->project->elements as $element)
            {
                foreach ($projectTypes as $comType => $display)
                {
                    if ($comType == 'package') continue;

                    if (!isset($projects[$comType])) continue;
                    if (!count($projects[$comType])) continue;

                    foreach ($projects[$comType] as $project)
                    {
                        if ($project->fileName != $element) continue;

                        $displayName = $project->name;

                        if ($project->scope)
                            $displayName .= ' (' . $project->scope . ')';

                        echo NL . '<li class="img12 icon12-' . $comType . '"'
                            . ' id="' . $project->fileName . '">' . $displayName . '</li>';
                    }
                    //foreach
                }
                //foreach
            }//foreach
            ?>
        </ul>
    </div>

    <div style="float: left; margin-left: 0.5em;">
        <h4><?php echo jgettext('Available projects'); ?></h4>
        <ul id="package-from" class="sortable">
            <?php
            foreach ($projectTypes as $comType => $display)
            {
                if ($comType == 'package') continue;

                if (isset($projects[$comType])
                    && count($projects[$comType])
                )
                {
                    $class = '' . $comType . '"';

                    foreach ($projects[$comType] as $project)
                    {
                        if (in_array($project->fileName, $this->project->elements))
                            continue;

                        $displayName = $project->name;

                        if ($project->scope)
                            $displayName .= ' (' . $project->scope . ')';

                        echo NL . '<li class="img12 icon12-' . $comType . '"'
                            . ' id="' . $project->fileName . '">' . $displayName . '</li>';
                    }
                    //foreach
                }
            }//foreach
            ?>
        </ul>
    </div>
    <div style="clear: both;"></div>
</div>
