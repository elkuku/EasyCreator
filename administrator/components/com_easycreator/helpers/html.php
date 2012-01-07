<?php
// @codingStandardsIgnoreStart

/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 06-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Standard HTML stuff...
 *
 * @package EasyCreator
 */
final class ecrHTML
{
    public $indent = 0;

    /**
     * Displays a menu based on Joomla! 1.5 Toolbar
     */
    public static function easyMenu()
    {
        /*   //                                                          //
         *  //--We start our form HERE ! this is for the whole app !    //
         * //                                                          //
         */
        ecrHTML::easyFormStart();

        $task = JRequest::getCmd('task');
        $ecr_project = JRequest::getCmd('ecr_project');
        $project = false;

        if($ecr_project)
        {
            try
            {
                $project = EasyProjectHelper::getProject();
            }
            catch(Exception $e)
            {
                echo '';//-- To satisfy the sniffer - aka: do nothing.
            }//try
        }

        //--Menu highlighting... set css class _active
        $actives = array();
        $tasks = array();
        $rightTasks = array();

        if($project instanceof EasyProject
        && $project->isValid)
        {
            //-- Left bar

            $tasks['stuffer'] = new stdClass;
            $tasks['stuffer']->title = jgettext('Project');
            $tasks['stuffer']->image = 'ecr_config';
            $tasks['stuffer']->tasks = array('stuffer', 'stufferstuff', 'projectinfo', 'files', 'save_config'
            , 'projectparams', 'projectdelete', 'tables', 'install');

            if('package' != $project->type)
            {
                $tasks['languages'] = new stdClass;
                $tasks['languages']->title = jgettext('Languages');
                $tasks['languages']->image = 'ecr_languages';
                $tasks['languages']->tasks = array('languages','translations','searchfiles', 'langcorrectdeforder'
                , 'langcorrectorder', 'show_version', 'show_versions', 'language_check', 'create_langfile', 'convert');

                $tasks['codeeye'] = new stdClass;
                $tasks['codeeye']->title = jgettext('CodeEye');
                $tasks['codeeye']->image = 'xeyes';
                $tasks['codeeye']->tasks = array('codeeye', 'phpcs', 'phpcpd', 'phpdoc', 'phpunit', 'stats');
            }

            $tasks['ziper'] = new stdClass;
            $tasks['ziper']->title = jgettext('Package');
            $tasks['ziper']->image = 'ecr_archive';
            $tasks['ziper']->tasks = array('ziper', 'ziperzip', 'delete', 'archive');

            foreach($tasks as $k=>$v)
            {
                $actives[$k]=(in_array($task, $v->tasks)) ? 'active' : '';
            }

        }

        //-- Right bar

        $rightTasks['config'] = new stdClass;
        $rightTasks['config']->title = jgettext('Configuration');
        $rightTasks['config']->image = 'ecr_config';
        $rightTasks['config']->tasks = array('config');

        $rightTasks['templates'] = new stdClass;
        $rightTasks['templates']->title = jgettext('Templates');
        $rightTasks['templates']->image = 'wizard';
        $rightTasks['templates']->tasks = array('templates', 'tplinstall', 'export');

        $rightTasks['logfiles'] = new stdClass;
        $rightTasks['logfiles']->title = jgettext('Logfiles');
        $rightTasks['logfiles']->image = 'menus';
        $rightTasks['logfiles']->tasks = array('logfiles');

        $rightTasks['help'] = new stdClass;
        $rightTasks['help']->title = jgettext('Help');
        $rightTasks['help']->image = 'help';
        $rightTasks['help']->tasks = array('help', 'quicky', 'credits');

        $rightTasks['sandbox'] = new stdClass;
        $rightTasks['sandbox']->title = jgettext('Sandbox');
        $rightTasks['sandbox']->image = 'default';
        $rightTasks['sandbox']->tasks = array();
        $rightTasks['sandbox']->href = JURI::root().'index.php?option=com_easycreator';
        $rightTasks['sandbox']->class = ' external';
        $rightTasks['sandbox']->js = '';
        $rightTasks['sandbox']->rel = ' target="_blank"';

        $rTasks = array();

        foreach($rightTasks as $k=>$v)
        {
            $actives[$k] = (in_array($task, $v->tasks)) ? 'active' : '';
            $rTasks = array_merge($rTasks, $v->tasks);
        }

        $js = 'onchange="submitbutton(\''.$task.'\')"';
        ?>
<div class="white_box" style="margin-bottom: 0.5em;">
<div class="ecr_easy_toolbar">
<ul>
    <li><a href="javascript:;"
        onclick="$('file_name').value=''; easySubmit('jhelp', 'help');"> <span
        class="icon-32-JHelp_btn" title="J Help"></span> <?php echo jgettext('J! API'); ?>
    </a></li>
</ul>
</div>
        <?php echo(ECR_DEBUG) ? '<div class="debug_ON">Debug</div>' : ''; ?>
<div style="float: left; margin-top: -7px;"><img
    src="<?php echo JURI::Root(); ?>administrator/components/com_easycreator/assets/images/ico/icon-64-easycreator.png"
    alt="EasyCreator Logo" /></div>
<div style="float: left; padding-left: 0.5em;"><span class="ecrTopTitle"
    style="font-size: 1.4em; font-weight: bold;">EasyCreator</span> <br />
        <?php self::drawProjectSelector(); ?> <br />
<span id="ecr_stat_project"></span></div>
<div style="float: left;"><?php
if( $ecr_project
&& $ecr_project != 'ecr_new_project'
&& $ecr_project != 'ecr_register_project'
)
{
    ?>
<div class="ecr_easy_toolbar">
<ul>
<?php
foreach($tasks as $k => $v)
{
    ?>
    <li class="<?php echo $actives[$k]; ?>"><?php
    echo '<a href="javascript:;" onclick="$(\'file_name\').value=\'\'; easySubmit(\''.$k.'\', \''.$k.'\');">';
    echo '<span class="icon-32-'.$v->image.'" title="'.$v->title.'"></span>';
    echo $v->title.NL;
    echo '</a>';
    ?></li>
    <?php
}//foreach
?>
</ul>
</div>
<?php
}//endif
?></div>
<?php
if( ! in_array($task, $rTasks))
{
    ?> <a href="javascript:;" style="float: left; margin-left: 5px;"
    class="ecr_button img icon-16-add hasTip"
    title="<?php echo jgettext('More...'); ?>::<?php echo jgettext('Click for more options'); ?>"
    onclick="this.setStyle('display', 'none'); ecr_options_box.toggle();">
    <?php echo jgettext('More...'); ?> </a> <?php
}

$stdJS = '';
$stdJS .= "$('adminForm').value='';";
$stdJS .= "$('file_name').value='';";
?>
<div id="ecr_options_box" class="ecr_easy_toolbar right">
<ul>
    <li class="divider"></li>
    <?php
    foreach($rightTasks as $k => $v)
    {
        $controller =(isset($v->controller)) ? $v->controller : $k;
        $cJS = " easySubmit('".$k."', '".$controller."');";
        $class =(isset($v->class)) ? $v->class : '';
        $href =(isset($v->href)) ? $v->href : 'javascript:;';
        $rel =(isset($v->rel)) ? $v->rel : '';
        $js =(isset($v->js)) ? $v->js : 'onclick="'.$stdJS.$cJS.'"';
        ?>
    <li class="<?php echo $actives[$k]; ?>"><?php
    echo '<a href="'.$href.'" '.$js.$rel.' class="'.$class.'">'.NL;
    echo '<span class="icon-32-'.$v->image.'" title="'.$v->title.'"></span>'.NL;
    echo $v->title.NL;
    echo '</a>'.NL;
    ?></li>
    <?php
    }//foreach
    ?>
</ul>
</div>
    <?php
    if( ! in_array($task, $rTasks))
    {
        ?> <script type="text/javascript">
                var ecr_options_box = new Fx.Slide('ecr_options_box');
                ecr_options_box.hide();
            </script> <?php
    }
    ?>

<div style="clear: both"></div>
    </div>
    <?php
    }//function

    public static function getSubBar($subTasks, $rightTasks = array())
    {
        $task = JRequest::getCmd('task');
        $html = '';
        $htmlDescriptionDivs = '';
        $jsVars = '';
        $jsEvents = '';

        if($rightTasks)
        {
            $html .= '<div class="ecr_easy_toolbar" style="float: right;">';

            foreach ($rightTasks as $rTask)
            {
                $html .= '<div class="ecr_button img icon-16-'.$rTask['icon'].'"';
                $html .= ' onclick="submitStuffer(\''.$rTask['task'].'\');">';
                $html .= $rTask['title'].'</div>';
                $html .= '</div>';
            }//foreach

            $html .= '</div>';
        }

        $html .= '<div id="ecr_sub_toolbar" style="margin-bottom: 1em; margin-top: 0.5em;">';

        foreach($subTasks as $sTask)
        {
            $selected =($sTask['task'] == $task) ? '_selected' : '';
            $html .= '<span id="btn_'.$sTask['task'].'" style="margin-left: 0.3em;"';
            $html .= ' class="ecr_button'.$selected.' img icon-16-'.$sTask['icon'].'"';
            $html .= ' onclick="submitbutton(\''.$sTask['task'].'\');">';
            $html .= $sTask['title'].'</span>';

            if(ECR_HELP > 1)
            {
                $htmlDescriptionDivs .= '<div class="hidden_div ecr_description" id="desc_'.$sTask['task'].'">'
                .$sTask['description'].'</div>';
                $jsVars .= "var desc_".$sTask['task']." = $('desc_".$sTask['task']."');\n";

                $jsEvents .= "$('btn_".$sTask['task']."').addEvents({\n"
                . "'mouseenter': showTaskDesc.bind(desc_".$sTask['task']."),\n"
                . "'mouseleave': hideTaskDesc.bind(desc_".$sTask['task'].")\n"
                . "});\n";
            }
        }//foreach

        $html .= $htmlDescriptionDivs;

        if(ECR_HELP > 1)
        {
            $html .= "<script type='text/javascript'>"
            ."window.addEvent('domready', function() {\n"
            ."function showTaskDesc(name) {\n"
            ."this.setStyle('display', 'block');\n"
            ."}\n"
            ."function hideTaskDesc(name) {\n"
            ."	this.setStyle('display', 'none');\n"
            ."}\n"
            . $jsVars
            . $jsEvents
            . "});\n"
            . "</script>";
        }

        $html .= '</div>';

        return $html;
    }//function

    /**
     * draws a checkbox
     * select if a backup version should be saved
     */
    public static function chkVersioned()
    {
        $params = JComponentHelper::getParams('com_easycreator');
        $save_versioned = JRequest::getInt('save_versioned', $params->get('save_versioned'));
        $checked =($save_versioned) ? ' checked="checked"' : '';
        $html = '<input type="checkbox" name="save_versioned" id="save_versioned" value="1"'.$checked.'>'
        .'<label for="save_versioned">'.jgettext('Save versioned').'</label>';
        return $html;
    }//function

    /**
     * draws a checkbox
     * select if the file remains open afer save
     */
    public static function chkGoonEdit()
    {
        $params = JComponentHelper::getParams('com_easycreator');
        $goon_edit = JRequest::getInt('goon_edit', $params->get('goon_edit'));
        $checked =($goon_edit) ? ' checked="checked"' : '';

        $html = '';
        $html .= '<input type="checkbox" name="goon_edit" id="goon_edit" value="1"'.$checked.' />';
        $html .= '<label for="goon_edit">'.jgettext('Continue editing').'</label>';
        echo $html;
    }//function

    /**
     * Draws a fileselector
     *
     * @param string $pathToDir path to directory
     * @param string $task task for javascript
     * @return void
     */
    public static function drawFileSelector($pathToDir, $task)
    {
        if( ! $pathToDir || ! is_dir($pathToDir))
        {
            return jgettext('Invalid path');
        }

        //--just for highlighting
        $the_file = JRequest::getVar('file', NULL);

        //--get the file list
        $files = JFolder::files($pathToDir);

        echo  NL.'<div id="ecr_filebutton">';
        echo NL.'<ul>';
        foreach($files as $file)
        {
            $style = '';
            if( $file == $the_file )//highlight ?
            {
                $style = ' style="color: red; font-weight: bold;"';
            }

            echo NL.'<li><a '.$style.'href="#" onclick="document.adminForm.file.value=\''
            .$file.'\'; submitbutton(\''.$task.'\');">'.$file.'</a></li>';
        }

        echo NL.'<ul>';
        echo NL.'</div>';
    }//function

    /**
     * Draws a minibutton
     *
     * @param string $img
     * @param string $title
     * @param string $task
     * @param string $javascript
     */
    public static function drawMiniButton($img, $title, $task, $javascript = '')
    {
        $js =($javascript) ? $javascript : 'onclick="javascript: submitbutton(\''.$task.'\')"';
        echo NL.'<a href="#" class="toolbar" '.$js.'>';
        echo NL.'<span class="icon-32-'.$img.'" title="'.$title.'"></span>';
        echo NL.$title;
        echo NL.'</a>';
    }//function

    public static function drawButtonCreateLanguageFile($lang, $scope)
    {
        $button = '<span class="ecr_button img icon-16-add" ';
        $button .= 'onclick="document.adminForm.lngcreate_lang.value=\''.$lang.'\'; ';
        $button .= 'document.adminForm.lng_scope.value=\''.$scope.'\'; ';
        $button .= 'submitform(\'create_langfile\');">'.jgettext('Create language file').'</span>';
        echo $button;
    }//function

    public static function drawButtonRemoveBOM($fileName)
    {
        $tPath = substr($fileName, strlen(JPATH_ROOT));
        $link = 'See: <a href="http://www.w3.org/International/questions/qa-utf8-bom" '
        .'target="_blank">W3C FAQ: Display problems caused by the UTF-8 BOM</a>';
        $button = '<br /><span class="ecr_button img icon-16-delete" '
        .'onclick="document.adminForm.file.value=\''.addslashes($tPath)
        .'\';easySubmit(\'remove_bom\', \'languages\');">Remove BOM</span>';
        self::displayMessage(array(jgettext('Found a BOM in languagefile'), $fileName, $link, $button), 'notice');
    }//function

    public static function drawButtonCreateClassList()
    {
        $button = '<br /><span class="ecr_button img icon-16-add" ';
        $button .= 'onclick="create_class_list();">'.jgettext('Create class list file').'</span>';
        self::displayMessage(array(sprintf(jgettext('The class file for your Joomla version %s has not been build yet.'), JVERSION)
        , $button), 'notice');
    }//function

    /**
     * Draws the standard footer
     *
     */
    public static function footer()
    {
        $version = '';
        $version .='<strong style="color: green;">'.ECR_VERSION.'</strong>';
        $version .=(strpos($version,'SVN')) ? ' <span style="color:red;">Rev. # '
        .ecrHTML::getVersionFromCHANGELOG('com_easycreator').'</span>' : '';
        ?>
<div class="ecrFooter">
<span class="img icon-16-easycreator">EasyCreator</span> <?php echo $version; ?> runs best on
<a href="http://www.mozilla-europe.org/firefox/" title="FireFox" class="external">
<span class="img icon-16-firefox">Firefox</span></a>
and <a href="http://opensuse.org" title="openSUSE" class="external">
<span class="img icon-16-opensuse">openSUSE</span></a> <br />
Made and partially Copyright &copy; 2008 - 2011 by <a
href="http://joomlacode.org/gf/project/elkuku"
    class="external">El KuKu</a><br />
<small> <em style="color: silver;"><span class="img icon-16-joomla"></span>
EasyCreator is not affiliated with or endorsed by the <a
    href="http://joomla.org" class="external">Joomla! Project</a>. It is
not supported or warranted by the <a href="http://joomla.org"
    class="external">Joomla! Project</a> or <a
    href="http://opensourcematters.org/" class="external">Open Source
Matters</a>.<br />
 <a
    href="http://www.joomla.org/about-joomla/the-project/conditional-use-logos.html"
    class="external">The Joomla! logo</a> is used under a limited license
granted by <a href="http://opensourcematters.org/" class="external">Open
Source Matters</a> the trademark holder in the United States and other
countries.</em></small>
</div>
        <?php

        if(defined('ECR_DEBUG') && ECR_DEBUG )
        {
            ecrDebugger::printSysVars('get');
            ecrDebugger::printSysVars('post');
        }

        echo NL.'<!-- EasyCreator END -->'.NL;
    }//function

    /**
     * Draws a h1 tag with title and project name.
     *
     * @param string $title
     * @param object $project EasyProject
     */
    public static function header($title, EasyProject $project = null, $class = '')
    {
        $pName =($project) ? $project->name : '';
        $pType =($project) ? ucfirst($project->type) : '';
        $pVersion =($project) ? $project->version : '';

        $icon =($class) ? '<span class="img32c icon-32-'.$class.'"></span>' : '';

        $html = '';
        $html .= $icon;
        $html .= $title;
        $html .=($pType) ? '&nbsp;<span style="color: black">'.jgettext($pType).'</span>' : '';
        $html .=($pName) ? '&nbsp;<span style="color: green">'.$pName.'</span>' : '';
        $html .=($pVersion) ? '&nbsp;<small><small>'.$pVersion.'</small></small>' : '';

        echo '<h1>'.$html.'</h1>';
    }//function

    /**
     * This will write the 'opening' tags for our form.
     * we also provide an id tag - as the name tag will be deprecated..
     */
    public static function easyFormStart()
    {
        echo '<!-- EasyCreator START -->'.NL;

        echo '<div id="ecr_box">'.NL;

        echo '<form action="index.php?option=com_easycreator" method="post" '
        .'name="adminForm" id="adminForm">'.NL;
    }//function

    /**
     * This will write the 'closing' tags for our form
     */
    public static function easyFormEnd($closeDiv = true)
    {
        echo '<input type="hidden" name="task" value="" />'.NL;
        echo '<input type="hidden" name="controller" '
        .'value="'.JRequest::getCmd('controller').'" />'.NL;
        echo '<input type="hidden" name="view"     '
        .'value="'.JRequest::getCmd('view').'" />'.NL;
        echo '<input type="hidden" name="file_name" id="file_name" '
        .'value="'.JRequest::getVar('file_name').'" />'.NL;
        echo '<input type="hidden" name="file_path" id="file_path" '
        . 'value="'.JRequest::getVar('file_path').'" />'.NL;
        echo '</form>'.NL;
        echo($closeDiv) ? '</div>'.NL : '';
        echo '<div style="clear: both"></div>'.NL;
    }//function

    /**
     * Display options for logging
     */
    public static function drawLoggingOptions()
    {
        $buildopts = array(
          'files' => jgettext('Log file contents')
        , 'profile'    => jgettext('Profile')
        );

        //--Get component parameters
        $params = JComponentHelper::getParams('com_easycreator');

        echo NL.'<div class="logging-options">';

        $js = "v =( $('div_buildopts').getStyle('display') == 'block') ? 'none' : 'block';";
        $js .= "$('div_buildopts').setStyle('display', v);";

        $checked =($params->get('logging')) ? ' checked="checked"' : '';
        echo NL.'<input type="checkbox" onchange="'.$js.'" name="buildopts[]"'.$checked.' value="logging" id="logging" />';
        echo NL.'<label for="logging">'.jgettext('Activate logging').'</label>';

        $style =($params->get('logging')) ? '' : ' style="display: none;"';
        echo NL.'   <div id="div_buildopts"'.$style.'>';
        foreach( $buildopts as $name=>$titel )
        {
            //--Get component parameters
            $checked =($params->get($name)) ? ' checked="checked"' : '';

            echo NL.'&nbsp;|__';
            echo NL.'<input type="checkbox" name="buildopts[]"'.$checked.' value="'.$name.'" id="'.$name.'" />';
            echo NL.'<label for="'.$name.'">'.$titel.'</label><br />';
        }//foreach
        echo NL.'   </div>';
        echo NL.'</div>';
    }//function

    /**
     * Display options for packing format
     */
    public static function drawPackOpts($projectParams = array())
    {
        //--Get component parameters
        $params = JComponentHelper::getParams('com_easycreator');

        $formats = array(
            'archive_zip' => 'zip'
            , 'archive_tgz' => 'tgz'
            , 'archive_bz2' => 'bz2'
        );

        $opts = array();

        foreach($formats as $name => $ext)
        {
            if(isset($projectParams[$name]))
            {
                $opts[$name] =($projectParams[$name] == 'ON') ? true : false;
            }
            else
           {
                  $opts[$name] =($params->get($name) == 'on') ? true : false;
            }
        }//foreach

        if( ! $opts['archive_zip']
        && ! $opts['archive_tgz']
        && ! $opts['archive_bz2'])
        {
            ecrHTML::displayMessage(jgettext('Please set a compression type'), 'notice');
            echo '<div style="float: right;">'
            .JHTML::tooltip(jgettext('You can set a default compression type in configuration'))
            .'</div>';
        }

        foreach($formats as $name => $ext)
        {
            $checked =($opts[$name]) ? ' checked="checked"' : '';

//             echo NL.'<div align="left">';
            echo NL.'   <input type="checkbox" name="buildopts[]"'.$checked.' value="'.$name.'" id="'.$name.'" />';
            echo NL.'   <label for="'.$name.'">'.$ext.'</label>';
//             echo NL.'</div>';
        }//foreach
    }//function

    /**
     * Load the great code editor EditArea.
     *
     *       **************
     *       ** EditArea **
     *       **************
     * CFG:
     * path        - to EditArea file
     * type        - EditArea file name
     * form        - name
     * textarea    - name
     * syntax    - for highlighting
     */
    public static function loadEditArea($cfg)
    {
        $document = JFactory::getDocument();
        $document->addScript(JURI::root(true).$cfg['path'].'/'.$cfg['type']);

        $translates = array('txt' => 'brainfuck'
        , 'pot' => 'po');

//        if(array_key_exists($cfg['syntax'], $translates))
        $syntax =(array_key_exists($cfg['syntax'], $translates)) ? $translates[$cfg['syntax']] : $cfg['syntax'];

//        $syntax =(in_array($cfg['syntax'], $translates)) ? $cfg['syntax'] : 'brainfuck';

        $debug =(ECR_DEBUG) ? ',debug: true'.NL : '';

        $js = <<<EOF
    <!-- **************** -->
    <!-- ****  load  **** -->
    <!-- *** EditArea *** -->
    <!-- **************** -->
    editAreaLoader.init({
        id : "{$cfg['textarea']}"
        ,syntax: "$syntax"
        ,start_highlight: true
        ,replace_tab_by_spaces: 3
        ,end_toolbar: 'html_select, autocompletion'
        ,plugins: "html, autocompletion"
        ,autocompletion: true
        ,font_size: {$cfg['font-size']}
  //      ,is_multi_files: true
        $debug
    });
EOF;

        $document->addScriptDeclaration($js);

        ecrScript('editor');
    }//function

    /**
     * Loads the file tree and adds the required css and js.
     *
     * @return void
     */
    public static function initFileTree()
    {
        ecrLoadHelper('php_file_tree');

        //-- Add css
        ecrStylesheet('php_file_tree');

        //-- Add javascript
        ecrScript('php_file_tree');
    }//function

    /**
     *
     * @return void
     */
    public static function prepareFileEdit()
    {
        $config = JComponentHelper::getParams('com_easycreator');

        $editarea_type = $config->get('editarea_type', 'edit_area_full.js');

        //-- Load EditArea code editor
        $editAreaVersion = '0_8_1_1';

        ecrHTML::loadEditArea(array(
        'path'        => '/administrator/components/com_easycreator/assets/js/editarea_'.$editAreaVersion,
        'type'        => $editarea_type,
        'syntax'    => '',
        'form'        => 'adminForm',
        'textarea'    => 'ecr_code_area',
        'font-size' => $config->get('editarea_font_size', 8)
        ));
        ?>
<div id="sld_picture"><br />
<span class="ecr_title_file" id="ecr_title_pic"> <?php echo jgettext('Select a file'); ?>
</span> <br />
<br />
<div id="container_pic"
    style="height: 100%; background-color: #ffffff; border: 1px solid grey;">
</div>
</div>
<div id="sld_edit_area">
<div style="float: right; margin-top: 10px;"><span id="ecr_status_msg"></span>
<span class="ecr_button img icon-16-save" onclick="save_file('save');">
        <?php echo jgettext('Save'); ?> </span></div>
<br />
<span class="ecr_title_file" id="ecr_title_file"> <?php echo jgettext('Select a file'); ?>
</span>

<div style="clear: both; padding-bottom: 0.5em;"></div>

<div id="ajaxDebug"></div>

<textarea id="ecr_code_area" name="c_insertstring"
    style="height: 500px; width: 100%;"></textarea>
</div>
<script>
            var sld_edit_area = new Fx.Slide('sld_edit_area');
            var sld_picture = new Fx.Slide('sld_picture');
            sld_picture.hide();
        </script>
        <?php
    }//function

    /**
     * Extract strings from svn:property Id
     *
     * @param string $path full path to CHANGELOG.php
     * @param bool $revOnly true to return revision number only
     * @return string/bol propertystring or FALSE
     * like:
     * @ version $I d: CHANGELOG.php 362 2007-12-14 22:22:19Z elkuku $
     * [0] => Id: [1] => CHANGELOG.php [2] => 362 [3] => 2007-12-14 [4] => 22:22:19Z [5] => elkuku [6] => ;)
     */
    public static function getVersionFromCHANGELOG($appName, $revOnly = false)
    {
        // TODO change to getVersionFromFile

        $file = JPATH_ADMINISTRATOR.DS.'components'.DS.$appName.DS.'CHANGELOG.php';

        if( ! file_exists($file))
        {
            return false;
        }

        //--we do not use JFile here cause we only need one line which is
        //--normally at the beginning..
        $f = fopen($file, 'r');
        $ret = false;

        while($line = fgets($f, 1000))
        {
            if(false == strpos($line, '@version'))
            continue;

                $parts = explode('$', $line);

                if(count($parts) < 2)
                continue;

                $parts = explode(' ', $parts[1]);

                if(count($parts) < 3)
                continue;

                $svn_rev = $parts[2];
                $svn_date = date('d-M-Y', strtotime($parts[3]));
                $ret = $svn_rev;
                $ret .=($revOnly) ? '' : '  / '.$svn_date;

                break;

        }// while

        fclose($f);

        return $ret;
    }//function

    /**
     * Wizard
     * Displays the project information introduced so far.
     *
     * @param JObject $project
     * @param array $formFieldNames fields already displayed
     */
    public static function displayResult(EasyProject $project, $formFieldNames = array())
    {
        ecrLoadHelper('easytemplatehelper');

        ?>
<div class="ecr_result">
<h3><?php echo jgettext('Your extension so far'); ?></h3>

        <?php
        echo ecrHTML::displayResultFieldRow(jgettext('Type'), 'type', 'tpl_type', $project, $formFieldNames);
        echo ecrHTML::displayResultFieldRow(jgettext('Template'), 'tplName', 'tpl_name', $project, $formFieldNames);
        echo ecrHTML::displayResultFieldRow(jgettext('JVersion'), 'JCompat', 'jcompat', $project, $formFieldNames);

        echo '<div style="background-color: #fff; border: 1px solid gray; padding-left: 0.5em;">';
        if( $info = easyTemplateHelper::getTemplateInfo($project->type, $project->tplName))
        {
            echo $info->description;
        }

        echo '</div>';

        echo '<div class=extension" style="background-color: #ffff99; padding: 1em; font-size: 1.2em;">';
        echo ecrHTML::displayResultFieldRow(jgettext('Name'), 'name', 'com_name', $project, $formFieldNames);
        echo ecrHTML::displayResultFieldRow(jgettext('Version'), 'version', 'version', $project, $formFieldNames);
        echo ecrHTML::displayResultFieldRow(jgettext('Description'), 'description', 'description', $project, $formFieldNames);
        echo '</div>';

        echo '<div class="credits" style="background-color: #ffc;">';
        echo ecrHTML::displayResultFieldRow(jgettext('Author'), 'author', 'author', $project, $formFieldNames);
        echo ecrHTML::displayResultFieldRow(jgettext('Author e-mail'), 'authorEmail', 'authorEmail', $project, $formFieldNames);
        echo ecrHTML::displayResultFieldRow(jgettext('Author URL'), 'authorUrl', 'authorUrl', $project, $formFieldNames);
        echo ecrHTML::displayResultFieldRow(jgettext('License'), 'license', 'license', $project, $formFieldNames);
        echo ecrHTML::displayResultFieldRow(jgettext('Copyright (C)'), 'copyright', 'copyright', $project, $formFieldNames);

        echo ecrHTML::displayResultFieldRow(jgettext('List postfix'), 'listPostfix', 'list_postfix', $project, $formFieldNames);

        echo '</div>';
        ?></div>
        <?php
    }//function

    /**
     * Wizard form
     * displays a table row with a hidden formfield if not included in $formFieldNames
     *
     * @param string $title
     * @param string $property
     * @param string $formFieldName
     * @param object $project
     * @param array $formFieldNames fields not to display
     */
    private static function displayResultFieldRow($title, $property, $formFieldName, EasyProject $project, $formFieldNames)
    {
        if( ! $project->$property)
        {
            return '';
        }

        $return = array();
        $return[] = '<div class="ecr_table-row">';
        $return[] = '<div class="ecr_table-cell" style="width: 25%; font-weight: bold;">';
        $return[] = $title;
        $return[] = '</div>';
        $return[] = '<div class="ecr_table-cell">';
        $return[] = $project->$property;

        if( ! in_array($formFieldName, $formFieldNames))
        {
        $return[] = '<input type="hidden" name="'.$formFieldName.'"'
        .' value="'.$project->$property.'" />';
        }

        $return[] = '</div>';
        $return[] = '</div>';

        return implode("\n", $return);
    }//function

    /**
     * replaces opening and closing tags with entities - nothing else..
     *
     * @param string $string
     * @return string cleaned string
     */
    public static function cleanHTML($string)
    {
        $cleaned = $string;
        $cleaned = str_replace('<', '&lt;', $cleaned);
        $cleaned = str_replace('>', '&gt;', $cleaned);

        return $cleaned;
    }//function

    /**
     * Draws a project selector
     *
     * @return void
     */
    public static function drawProjectSelector()
    {
        $projects = EasyProjectHelper::getProjectList();
        $projectTypes = EasyProjectHelper::getProjectTypes();
        $ecr_project = JRequest::getCmd('ecr_project');

        $class = '';

        if($ecr_project == 'ecr_new_project')
        {
            $class = 'img3 icon-16-add';
        }

        else if($ecr_project == 'ecr_register_project')
        {
            $class = 'img3 icon-16-install';
        }

        else if($ecr_project)
        {
            try
           {
                $project = EasyProjectHelper::getProject();

                $class = 'img3 icon-12-'.$project->type;
            }
            catch(Exception $e)
            {
                echo '';//-- To satisfy the sniffer - aka: do nothing.
            }//try
        }

        echo '<span class="'.$class.'">';
        echo NL.'<select style="font-size: 1.2em;" name="ecr_project" id="ecr_project" onchange="switchProject();">';
        echo NL.'<option value="">'.jgettext('Project').'...</option>';

        $selected =($ecr_project == 'ecr_new_project') ? ' selected="selected"' : '';
        $class = ' class="img3 icon-16-add"';
        echo NL.'<option'.$class.' value="ecr_new_project"'.$selected.'>'.jgettext('New Project').'</option>';

        $selected =($ecr_project == 'ecr_register_project') ? ' selected="selected"' : '';
        $class = ' class="img3 icon-16-install"';
        echo NL.'<option'.$class.' value="ecr_register_project"'.$selected.'>'.jgettext('Register Project').'</option>';

        foreach($projectTypes as $comType => $display)
        {
            if(isset($projects[$comType])
            && count($projects[$comType]))
            {
                echo NL.'<optgroup label="'.$display.'">';

                foreach($projects[$comType] as $project)
                {
                    $displayName = $project->name;

                    if($project->scope)
                    $displayName .= ' ('.$project->scope.')';

                    $selected =($project->fileName == $ecr_project) ? ' selected="selected"' : '';
                    $class = ' class="img12 icon-12-'.$comType.'"';
                    echo NL.'<option'.$class.' value="'.$project->fileName.'" label="'.$project->name.'"'.$selected.'>'
                    .$displayName.'</option>';
                }//foreach

                echo NL.'</optgroup>';
            }
        }//foreach
        echo NL.'</select></span>';
    }//function

    public static function drawSelectScope($scope = '')
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
<br />
        <?php
        return 'element_scope';
    }//function

    public static function drawSelectName($name = '', $title = '')
    {
        if( ! $title)
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
    type="text" id="element_name" name="element_name" value="" />
<br />
        <?php
        return 'element_name';
    }

    /**
     * Draw a submit button
     *
     * @param array $requireds required field names separated by komma
     */
    public static function drawSubmitParts($requireds = array())
    {
        $requireds = (array)$requireds;
        $requireds = implode(',', $requireds);
        echo '<br />';
        echo '<div class="ecr_button img icon-16-save" onclick="addNewElement(\''.$requireds.'\');">'
        .jgettext('Save').'</div>';
    }

    /**
     * Draw a submit button
     *
     * @param array $requireds required field names separated by komma
     */
    public static function drawSubmitAutoCode($requireds = array())
    {
        $requireds = (array)$requireds;
        $requireds = implode(',', $requireds);
        echo '<br />';
        echo '<div class="ecr_button img icon-16-save" onclick="updateAutoCode(\''.$requireds.'\');">'
        .jgettext('Save').'</div>';
    }

    /**
     * Displays a message with standard Joomla! backend css styles
     * Type can be:
     *
     * 'notice'    : YELLOW
     * 'error'    : RED
     * '[EMPTY]': BLUE [default]
     *
     * @param array $messages
     * @param string $type empty, notice, error
     */
    public static function displayMessage($messages, $type = '')
    {
        $callFile = '';
        $trace = false;

        if(ECR_DEBUG && function_exists('debug_backtrace'))
        {
            $trace = debug_backtrace();

            $callFile = str_replace(JPATH_COMPONENT.DS, '', $trace[0]['file']);
            $callFile .= ' ('.$trace[0]['line'].')';
        }

        if(is_a($messages, 'exception'))
        {
            $m =(JDEBUG || ECR_DEBUG) ? nl2br($messages) : $messages->getMessage();

            $trace = $messages->getTrace();
            $messages = array($messages->getMessage());

            $type = 'error';
        }

        if( ! is_array($messages))
        {
            $messages = array($messages);
        }
        ?>
<dl id="system-message">
    <dt class="<?php echo $type; ?>"><?php echo $type; ?></dt>
    <dd class="<?php echo $type; ?> message fade">
    <ul>
    <?php
    foreach($messages as $message)
    {
        echo '<li>'.$message.'</li>';
    }//foreach

    if($callFile)
    {
        echo '<li><strong>'.$callFile.'</strong></li>';
    }
    ?>
    </ul>
    </dd>
</dl>
    <?php
    if(ECR_DEBUG && $type == 'error')
    {
        self::printTrace($trace);
    }
    }//function

    public static function printTrace($trace = null)
    {
        if( ! function_exists('debug_backtrace'))
        return '';

        if( ! $trace)
        $trace = debug_backtrace();

        $traces = array();

        $traces['Debug trace'] = debug_backtrace();

        if($trace)
        $traces['Exception trace'] = $trace;

        $linkFormat = ini_get('xdebug.file_link_format');

        foreach ($traces as $traceType => $trace)
        {
        $s = '';
        $s = '<h2>'.$traceType.'</h2>';
        $s .= '<table border="1">';
        $s .= '<tr>';
        $s .= '<th>#</th><th>Function</th><th>File</th><th>Line</th><th>Args</th>';
        $s .= '</tr>';

        for($i = count($trace) - 1; $i >= 0; --$i)
        {
            $link = '&nbsp;';

            if(isset($trace[$i]['file']))
            {
                $link = str_replace(JPATH_ROOT.DS, '', $trace[$i]['file']);

                if($linkFormat)
                {
                    $href = $linkFormat;
                    $href = str_replace('%f', $trace[$i]['file'], $href);

                    if(isset($trace[$i]['line']))
                    {
                        $href = str_replace('%l', $trace[$i]['line'], $href);
                    }

                    $link = '<a href="'.$href.'">'.$link.'</a>';
                }
            }

            $s .= '<tr>';
            $s .= '<td align="right"><tt>'.$i.'</tt></td>';
            $s .= '<td>';
            $s .=(isset($trace[$i]['class'])) ? $trace[$i]['class'] : '';
            $s .=(isset($trace[$i]['type'])) ? $trace[$i]['type'] : '';
            $s .=(isset($trace[$i]['function'])) ? $trace[$i]['function'] : '';
            $s .= '</td>';

            $s .= '<td>'.$link.'</td>';

            $s .=(isset($trace[$i]['line']))
            ? '<td align="right"><tt>'.$trace[$i]['line'].'</tt></td>'
            : '<td>&nbsp;</td>';

            $s .= '<td>';

            if(isset($trace[$i]['args']))
            {
                foreach($trace[$i]['args'] as $arg)
                {
                    $s .= str_replace(JPATH_ROOT.DS, '', $arg).BR;
                }//foreach
            }

            $s .= '</td>';

            $s .= '</tr>';
        }//for

        $s .= '</table>';

        echo $s;
        }//foreach
    }//function

    /**
     * Context menu
     */
    public static function contextMenu()
    {
        //--Add css
        ecrStylesheet('contextmenu');

        //--Add javascript
        ecrScript('contextmenu');

        $ajaxLink = 'index.php?option=com_easycreator';
        $ajaxLink .= '&controller=ajax&tmpl=component';
        $ajaxLink .= '&old_task='.JRequest::getCmd('task');
        $ajaxLink .= '&old_controller='.JRequest::getCmd('controller');
        $ajaxLink .= '&ecr_project='.JRequest::getCmd('ecr_project');

        ?>
<script type="text/javascript">
            SimpleContextMenu.setup({'preventDefault':true, 'preventForms':false});
            SimpleContextMenu.attach('pft-file', 'CM1');
            SimpleContextMenu.attach('pft-directory', 'CM2');
        </script>

<!-- Context menu files -->
        <?php
        $menuEntries = array(
        array(jgettext('New folder'), 'new_folder', 'add')
        , array(jgettext('New file'), 'new_file', 'add')
        , array(jgettext('Rename'), 'rename_file', 'rename')
        , array(jgettext('Delete'), 'delete_file', 'logout')
        );
        ?>
<ul id="CM1" class="SimpleContextMenu">
    <li class="title"><?php echo jgettext('File'); ?></li>
    <?php
    foreach($menuEntries as $menuEntry)
    {
        self::contextMenuEntry($ajaxLink, $menuEntry[0], $menuEntry[1], $menuEntry[2]);
    }//foreach
    ?>
</ul>

<!-- Context menu folders -->
    <?php
    $menuEntries = array(
    array(jgettext('New folder'), 'new_folder', 'add')
    , array(jgettext('New file'), 'new_file', 'add')
    , array(jgettext('Rename'), 'rename_folder', 'rename')
    , array(jgettext('Delete'), 'delete_folder', 'logout')
    );
    ?>
<ul id="CM2" class="SimpleContextMenu">
    <li class="title"><?php echo jgettext('Folder'); ?></li>
    <?php
    foreach($menuEntries as $menuEntry)
    {
        self::contextMenuEntry($ajaxLink, $menuEntry[0], $menuEntry[1], $menuEntry[2]);
    }//foreach
    ?>
</ul>

<input
    type="hidden" name="act_folder" id="act_folder" />
<input
    type="hidden" name="act_file" id="act_file" />
    <?php
    }//function

    private static function contextMenuEntry($ajaxLink, $title, $task, $icon)
    {
        ?>
<li><a class="modal" onclick="SimpleContextMenu._hide();"
    rel="{handler: 'iframe', size: {x: 600, y: 150}}"
    href="<?php echo $ajaxLink.'&task='.$task; ?>"> <span
    class="img icon-16-<?php echo $icon; ?>"> <?php echo $title; ?> </span>
</a></li>
        <?php
    }//function

    /**
     *
     * @param $ac
     * @param $newIndent
     * @return unknown_type
     */
    public static function idt($ac = '', $newIndent = 0)
    {
        static $indent = 0;

        if( $newIndent )
        {
            $indent = $newIndent;
        }

        if($ac == '-')
        {
            $indent --;
        }

        $i = NL.str_repeat('   ', $indent);

        if($ac == '+')
        {
            $indent ++;
        }

        return $i;
    }//function

    /**
     * converts a bytevalue into the highest possible unit and adds it's sign.
     * @version  2009-01-27 03:50h
     *
     * @param    bigint|float  $bytes    -bytevalue to convert
     * @param    int           $exp_max  -maximal allowed exponent (0='B', 1='KB', 2='MB', ...)
     *
     * @return   string
     */
    public static function byte_convert($bytes, $exp_max = null)
    {
        $symbols = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

        $exp = 0;

        if($exp_max === null)
        {
            $exp_max = count($symbols)-1;
        }

        $converted_value = 0;

        if($bytes > 0)
        {
            $exp = floor( log($bytes)/log(1024) );

            if( $exp > $exp_max )
            $exp = $exp_max;

            $converted_value = ( $bytes/pow(1024,$exp) );
        }

        return number_format($converted_value,2,',','.').' '.$symbols[$exp];
    }//function

}// class

/**
 * Enter description here ...
 *
 */
class EasyTemplateInfo
{
    public $group = '';
    public $title = '';
    public $description = '';

    function info()
    {
        $ret = '';
        $ret .= '<div style="color: blue; font-weight: bold; text-align:center;">'
        .ucfirst($this->group).' - '.$this->title.'</div>';
        $ret .= '<div style="color: orange; font-weight: bold;">'
        .$this->description.'</div>';

        return $ret;
    }//function

    public function format($format, $type = 'new')
    {
        $ret = '';
        $ret .= '<span class="img icon-16-'.$type.'">';
        switch ($type)
        {
            case 'edit':
              $ret .= jgettext('Edit');
            break;

            case 'add':
              $ret .= jgettext('New');
            break;

            default:
                ;
            break;
        }//switch

        $ret .= '</span>';

        switch ($format)
        {
            case 'erm':
                $ret .= '<div style="color: blue; font-weight: bold; text-align:center;">'
                .ucfirst($this->group).' - '.$this->title.'</div>';
                $ret .= '<div style="color: orange; font-weight: bold;">'
                .$this->description.'</div>';
            break;

            default:
                return sprintf(jgettext('Undefined format: %s'), $format);
            break;
        }//switch

        return $ret;
    }//function

    public static function error($message)
    {
        echo $message.' in '.get_parent_class($this);

        var_dump(debug_print_backtrace());
    }//function

}//class
