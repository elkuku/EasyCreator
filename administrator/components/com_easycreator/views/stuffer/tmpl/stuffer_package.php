<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 29-Dec-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

?>

<?php echo ecrHTML::floatBoxStart(); ?>

<div class="infoHeader img icon-24-package_creation">
	<?php echo jgettext('Add extensions'); ?>
</div>

<table>
    <tr valign="top">
        <td>
            <table id="divPackageElementsModules">
                <tr>
                    <th class="infoHeader">
                        <?php if(isset($this->projectList['module']) && count($this->projectList['module'])) :?>
                        	<span class="img icon-12-module">
                            <?php echo jgettext('Modules'); ?>
                            </span>
                        <?php else :?>
                            <?php echo jgettext('No module projects found'); ?>
                        <?php endif;?>
                    </th>
                </tr>
                <!--                            -->
                <!--         Modules          -->
                <!-- To be filled by javascript -->
                <!--                            -->
            </table>
            <?php if(isset($this->projectList['module']) && count($this->projectList['module'])) :?>
                <div style="float: right;" class="ecr_button img icon-16-add"
                onclick="addPackageElement('module', '', '', '', '', '');">
                    <?php echo jgettext('Add Module'); ?>
                </div>
            <?php endif;?>
            <table id="divPackageElementsPlugins">
                <tr>
                    <th class="infoHeader">
                        <?php if(isset($this->projectList['plugin']) && count($this->projectList['plugin'])) :?>
                        	<span class="img icon-12-plugin">
                            <?php echo jgettext('Plugins'); ?>
                            </span>
                        <?php else :?>
                            <?php echo jgettext('No plugin projects found'); ?>
                        <?php endif;?>
                    </th>
                </tr>
                <!--                            -->
                <!--         Plugins          -->
                <!-- To be filled by javascript -->
                <!--                            -->
            </table>
            <?php if(isset($this->projectList['plugin']) && count($this->projectList['plugin'])) :?>
                <div style="float: right;" class="ecr_button img icon-16-add"
                onclick="addPackageElement('plugin', '', '', '', '', '');">
                    <?php echo jgettext('Add Plugin'); ?>
                </div>
            <?php endif;?>
        </td>
    </tr>
</table>
<?php
if($this->project->modules
|| $this->project->plugins)
{
    $found = false;

    foreach($this->installFiles['php'] as $file)
    {
        if($file->name == 'install.package.php')
        {
            $found = true;
            break;
        }
    }//foreach

    if( ! $found)
    {
        ?>
        <div style="color: orange;">
            <strong><?php echo jgettext('Package installer ?'); ?></strong>
            <br />
            <?php echo sprintf(jgettext('Packages require a special install script which should be named <strong>%s</strong>'), 'install.package.php'); ?>
            <br />
            <?php echo jgettext('EasyCreator can do this if you go to Files -> Add element -> Various -> Package installer'); ?>
        </div>
        <?php
    }
}
?>
<?php echo ecrHTML::floatBoxEnd();
