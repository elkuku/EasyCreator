<?php
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 29-Dec-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

//-- INACTIVE
return;

//-- @Joomla!-compat 1.5
if ($this->project->JCompat != 1.5)
    return;
?>

<div class="ecr_floatbox">
    <div class="infoHeader img icon24-package_creation">
        <?php echo jgettext('Add extensions'); ?>
    </div>

    <table>
        <tr valign="top">
            <td>
                <table id="divPackageElementsModules">
                    <tr>
                        <th class="infoHeader">
                            <?php if (isset($this->projectList['module']) && count($this->projectList['module'])) : ?>
                            <span class="img icon12-module">
                            <?php echo jgettext('Modules'); ?>
                            </span>
                            <?php else : ?>
                            <?php echo jgettext('No module projects found'); ?>
                            <?php endif;?>
                        </th>
                    </tr>
                    <!--                            -->
                    <!--         Modules          -->
                    <!-- To be filled by javascript -->
                    <!--                            -->
                </table>
                <?php if (isset($this->projectList['module']) && count($this->projectList['module'])) : ?>
                <div class="btn-toolbar">
                    <div class="btn" onclick="addPackageElement('module', '', '', '', '', '');">
                        <i class="img icon16-add"></i>
                        <?php echo jgettext('Add Module'); ?>
                    </div>
                </div>
                <?php endif;?>
                <table id="divPackageElementsPlugins">
                    <tr>
                        <th class="infoHeader">
                            <?php if (isset($this->projectList['plugin']) && count($this->projectList['plugin'])) : ?>
                            <span class="img icon12-plugin">
                            <?php echo jgettext('Plugins'); ?>
                            </span>
                            <?php else : ?>
                            <?php echo jgettext('No plugin projects found'); ?>
                            <?php endif;?>
                        </th>
                    </tr>
                    <!--                            -->
                    <!--         Plugins          -->
                    <!-- To be filled by javascript -->
                    <!--                            -->
                </table>
                <?php if (isset($this->projectList['plugin']) && count($this->projectList['plugin'])) : ?>
                <div class="btn-toolbar">
                    <div class="btn" onclick="addPackageElement('plugin', '', '', '', '', '');">
                        <i class="img icon16-add"></i>
                        <?php echo jgettext('Add Plugin'); ?>
                    </div>
                </div>
                <?php endif;?>
            </td>
        </tr>
    </table>
    <?php
    if($this->project->modules || $this->project->plugins)
    {
        $found = false;

        foreach($this->installFiles['php'] as $file)
        {
            if($file->name == 'install.package.php')
            {
                $found = true;
                break;
            }
        }

        if(false == $found)
        {
            ?>
            <div style="color: orange;">
                <strong><?php echo jgettext('Package installer ?'); ?></strong>
                <br/>
                <?php echo sprintf(jgettext('Packages require a special install script which should be named <strong>%s</strong>'), 'install.package.php'); ?>
                <br/>
                <?php echo jgettext('EasyCreator can do this if you go to Files -> Add element -> Various -> Package installer'); ?>
            </div>
            <?php
        }
    }
    ?>
</div>
