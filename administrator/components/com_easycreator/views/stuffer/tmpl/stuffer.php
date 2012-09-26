<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/* @var EasyCreatorViewStuffer $this */

//-- Add CSS
ecrStylesheet('menu', 'stuffer');

$extProps['module'] = array('name' => 'comName', 'client' => 'scope', 'position' => 'position'
, 'ordering' => 'ordering');

$extProps['plugin'] = array('name' => 'comName', 'client' => 'scope', 'order' => 'ordering');

$js = array();

$js[] = "var definedProjects = {";

$komma1 = false;

foreach($extProps as $eName => $eProps) :
    if( ! isset($this->projectList[$eName]))
    continue;

    if($komma1) $js[] = '  , ';
    $komma1 = true;
    $js[] = "'".$eName."s' : {";

    $komma2 = false;

    $prs = array();

    foreach($this->projectList[$eName] as $ext) :

        $ps = array();
        foreach($eProps as $prop => $trans) :
            if($prop == 'client' && $ext->scope == 'frontend') $ext->scope = 'site';

            $ps[] = "'$prop' : '".$ext->$trans."'";
        endforeach;

        if($komma2) $js[] = '   , ';
        $komma2 = true;
        $js[] = "   '".$ext->name."' : {".implode(', ', $ps).' }';

    endforeach;
    $js[] = '    }';
endforeach;

$js[] = '};';

if('component' == $this->project->type):
    //-- The picture chooser
    $js[] = "window.addEvent('domready', function() {";
    //$js[] = "drawPicChooser('', '".$this->project->menu['img']."');";

    $js[] = '//--Submenus added by PHP';


    if(isset($this->project->submenu)) :
        foreach($this->project->submenu as $submenu) :
            $js[] = "addSubmenu('{$submenu['text']}', '".$submenu['link']
                ."', '".$submenu['img']."', '".$submenu['ordering']
                ."', '{$submenu['menuid']}', '{$this->project->menu['menuid']}');";
        endforeach;
    endif;

    $js[] = "sortSubMenu = new Sortables('#divSubmenu', {
        constrain: true,
        clone: true,
        revert: true,

        onStart: function(el) {
            el.setStyle('background','#add8e6');
        },
        onComplete: function(el) {
            el.setStyle('background','#fff');
        }

    });";

    $js[] = '//--Package modules added by PHP';

    foreach($this->project->modules as $module) :
        $js[] = "addPackageElement('module', '".$module->scope."', '"
            .$module->name."', '".$module->title."', '"
            .$module->position."', '".$module->ordering."');";
    endforeach;

    $js[] = '//--Package plugins added by PHP';

    foreach($this->project->plugins as $plugin) :
        $js[] = "addPackageElement('plugin', '".$plugin->scope."', '"
        .$plugin->name."', '".$plugin->title."', '', '".$plugin->ordering."');";
    endforeach;

    $js[] = '});';

    ?>
<?php endif;

$js[] = "window.addEvent('domready', function() {";
$js[] = '   Stuffer.init();';
$js[] = '});';

JFactory::getDocument()->addScriptDeclaration(implode(NL, $js));

ecrScript('addelement', 'menu');

?>
<!-- just for js.. -->
<input type="hidden" value="0" id="totalCopys" />
<input type="hidden" value="0" id="totalSubmenu" />
<input type="hidden" value="0" id="totalPackageElementsModules" />
<input type="hidden" value="0" id="totalPackageElementsPlugins" />


<div class="row-fluid">
    <div id="actionButtons" class="span3">

        <a class="btn btn-large" coords="info">
            <i class="img24 icon24-info"></i>
            <?php echo jgettext('Info') ?>
        </a>
        <div class="display" title="info">
            <?php echo $this->loadTemplate('info'); ?>
            <?php echo $this->loadTemplate('credits'); ?>
        </div>

        <a class="btn btn-large" coords="options">
            <i class="img24 icon24-various"></i>
            <?php echo jgettext('Options') ?>
        </a>
        <div class="display" title="options">
            <?php
            if('component' == $this->project->type):
                echo $this->loadTemplate('autocode');
                echo $this->loadTemplate('dbtypes');
            endif;

            echo $this->loadTemplate('language');
            ?>

        </div>

        <?php if('component' == $this->project->type): ?>
            <a class="btn btn-large" coords="menu">
                <i class="img24 icon24-menu"></i>
                <?php echo jgettext('Menu') ?>
            </a>
            <div class="display" title="menu">
                <?php echo $this->loadTemplate('menu'); ?>
            </div>
        <?php endif; ?>

        <a class="btn btn-large" coords="package">
            <i class="img24 icon24-package_creation"></i>
            <?php echo jgettext('Package') ?>
        </a>
        <div class="display" title="package">
            <?php
            echo $this->loadTemplate('packing');
            echo $this->loadTemplate('packageelements');
            echo $this->loadTemplate('actions');
            ?>
        </div>

        <a class="btn btn-large" coords="update">
            <i class="img24 icon24-update"></i>
            <?php echo jgettext('Update') ?>
        </a>
        <div class="display" title="update">
            <?php echo $this->loadTemplate('update'); ?>
        </div>

        <a class="btn btn-large" coords="deploy">
            <i class="img24 icon24-ecr_deploy"></i>
            <?php echo jgettext('Deploy') ?>
        </a>
        <div class="display" title="deploy">
            <?php echo $this->loadTemplate('deploy'); ?>
        </div>

    </div>

    <div id="actionWindow" class="span9">
    </div>
</div>

<div style="clear: both; height: 1em;"></div>

<?php
ECR_DEBUG ? EcrDebugger::varDump($this->project, '$this->project') : null;
