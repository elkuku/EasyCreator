<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 06-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * Standard HTML stuff...
 *
 * @package EasyCreator
 */
abstract class EcrHtml
{
    /**
     * Draws a h1 tag with title and project name.
     *
     * @param string          $title
     * @param EcrProjectBase  $project
     * @param string          $class
     */
    public static function header($title, EcrProjectBase $project = null, $class = '')
    {
        $pName = ($project) ? $project->name : '';
        $pType = ($project) ? $project->translateType() : '';
        $pVersion = ($project) ? $project->version : '';

        $icon = ($class) ? '<span class="img32c icon32-'.$class.'"></span>' : '';

        $html = '';
        $html .= $icon;
        $html .= $title;
        $html .= ($pType) ? '&nbsp;<span style="color: black">'.$pType.'</span>' : '';
        $html .= ($pName) ? '&nbsp;<span style="color: green">'.$pName.'</span>' : '';
        $html .= ($pVersion) ? '&nbsp;<small><small>'.$pVersion.'</small></small>' : '';

        echo '<h1>'.$html.'</h1>';
    }

    /**
     * Draws the standard footer
     *
     */
    public static function footer()
    {
        $v = EcrHtml::getVersionFromCHANGELOG('com_easycreator');

        //-- If the version contains a hyphen, it must be a snapshot - color orange.
        $color = (false === strpos($v, '-')) ? 'green' : 'orange';

        $version = '<strong style="color: '.$color.';">'.$v.'</strong>';
        ?>
    <div class="ecrFooter">
        <span class="img icon16-easycreator">EasyCreator</span> <?php echo $version; ?>
	    is made and partially Copyright &copy; 2008 - 2015 by <a href="https://github.com/elkuku"
        class="external">El KuKu</a> and <a href="https://github.com/elkuku/EasyCreator/graphs/contributors"
	                                        class="external">
		    Others
	    </a>
        <br/>
        <small><em style="color: silver;"><span class="img icon16-joomla"></span>
            EasyCreator is not affiliated with or endorsed by the <a
                href="http://joomla.org" class="external">Joomla! Project</a>. It is
            not supported or warranted by the <a href="http://joomla.org"
                                                 class="external">Joomla! Project</a> or <a
                href="http://opensourcematters.org/" class="external">Open Source
                Matters</a>.<br/>
            <a
                href="http://www.joomla.org/about-joomla/the-project/conditional-use-logos.html"
                class="external">The Joomla! logo</a> is used under a limited license
            granted by <a href="http://opensourcematters.org/" class="external">Open
                Source Matters</a> the trademark holder in the United States and other
            countries.</em></small>
    </div>
    <?php

        if(defined('ECR_DEBUG') && ECR_DEBUG)
        {
            EcrDebugger::printSysVars('get');
            EcrDebugger::printSysVars('post');
        }

        echo "\n".'<!-- EasyCreator END -->'."\n";
    }

    /**
     * This will write the 'opening' tags for our form.
     * we also provide an id tag - as the name tag will be deprecated..
     */
    public static function formStart()
    {
        echo '<!-- EasyCreator START -->'.NL;

        echo '<div id="ecr_box">'.NL;

        echo '<form action="index.php?option=com_easycreator" method="post" '
            .'name="adminForm" id="adminForm" class="form-horizontal">'.NL;
    }

    /**
     * This will write the 'closing' tags for our form
     *
     * @param bool $closeDiv
     */
    public static function formEnd($closeDiv = true)
    {
        $input = JFactory::getApplication()->input;

        echo '<input type="hidden" name="task" value="" />'.NL;
        echo '<input type="hidden" name="controller" '
            .'value="'.$input->get('controller').'" />'.NL;
        echo '<input type="hidden" name="view"     '
            .'value="'.$input->get('view').'" />'.NL;
        echo '<input type="hidden" name="file_name" id="file_name" '
            .'value="'.$input->getPath('file_name').'" />'.NL;
        echo '<input type="hidden" name="file_path" id="file_path" '
            .'value="'.$input->getPath('file_path').'" />'.NL;
        echo '</form>'.NL;
        echo ($closeDiv) ? '</div>'.NL : '';
        echo '<div style="clear: both"></div>'.NL;
    }

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
     *
     * @param array $cfg
     */
    public static function loadEditArea($cfg)
    {
        $document = JFactory::getDocument();
        $document->addScript(JURI::root(true).$cfg['path'].'/'.$cfg['type']);

        $translates = array('txt' => 'brainfuck'
        , 'pot' => 'po');

        $syntax = (array_key_exists($cfg['syntax'], $translates)) ? $translates[$cfg['syntax']] : $cfg['syntax'];

        $debug = (ECR_DEBUG) ? ',debug: true'.NL : '';

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
    }

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

        EcrHtml::loadEditArea(array(
            'path' => '/media/com_easycreator/admin/js/editarea_'.$editAreaVersion,
            'type' => $editarea_type,
            'syntax' => '',
            'form' => 'adminForm',
            'textarea' => 'ecr_code_area',
            'font-size' => $config->get('editarea_font_size', 8)
        ));
        ?>
    <div id="sld_picture"><br/>
<span class="ecr_title_file" id="ecr_title_pic"> <?php echo jgettext('Select a file'); ?>
</span> <br/>
        <br/>

        <div id="container_pic"
             style="height: 100%; background-color: #ffffff; border: 1px solid grey;">
        </div>
    </div>
    <div id="sld_edit_area">
        <div style="float: right; margin-top: 10px;"><span id="ecr_status_msg"></span>
<span class="btn" onclick="save_file('save');">
    <i class="img icon16-ecr_save"></i>
    <?php echo jgettext('Save'); ?> </span></div>
        <br/>
<span class="ecr_title_file" id="ecr_title_file"> <?php echo jgettext('Select a file'); ?>
</span>

        <div style="clear: both; padding-bottom: 0.5em;"></div>

        <div id="ajaxDebug"></div>

        <textarea id="ecr_code_area" name="c_insertstring"
                  style="height: 500px; width: 100%;"></textarea>
    </div>
    <script type="text/javascript">
        var sld_edit_area = new Fx.Slide('sld_edit_area');
        var sld_picture = new Fx.Slide('sld_picture');
        sld_picture.hide();
    </script>
    <?php
    }

    /**
     * Extract strings from svn:property Id
     * OR a .git/hooks/pre-commit generated version file
     *
     * @param      $appName
     * @param bool $revOnly true to return revision number only
     *
     * @return string/bol propertystring or FALSE
     * like:
     * @ version $I d: CHANGELOG.php 362 2007-12-14 22:22:19Z elkuku $
     * [0] => Id: [1] => CHANGELOG.php [2] => 362 [3] => 2007-12-14 [4] => 22:22:19Z [5] => elkuku [6] => ;)
     */
    public static function getVersionFromCHANGELOG($appName, $revOnly = false)
    {
        //-- Check if we have a .git/hooks/pre-commit generated version file
        $path = JPATH_ADMINISTRATOR.'/components/'.$appName.'/version.txt';

        if(file_exists($path))
        {
            $contents = file_get_contents($path);

            $parts = explode('-', $contents);

            if(false == isset($parts[1]))
                return trim($contents);

            //-- If the second part is '0' we have a tagged version
            return ('0' != $parts[1]) ? trim($contents) : $parts[0];
        }

        //-- Check for a SVN id in changelog
        // TODO change to getVersionFromFile

        $file = JPATH_ADMINISTRATOR.DS.'components'.DS.$appName.DS.'CHANGELOG.php';

        if(false == file_exists($file))
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
            $ret .= ($revOnly) ? '' : '  / '.$svn_date;

            break;
        }

        fclose($f);

        return $ret;
    }

    /**
     * replaces opening and closing tags with entities - nothing else..
     *
     * @param string $string
     *
     * @return string cleaned string
     */
    public static function cleanHTML($string)
    {
        $cleaned = $string;
        $cleaned = str_replace('<', '&lt;', $cleaned);
        $cleaned = str_replace('>', '&gt;', $cleaned);

        return $cleaned;
    }

    /**
     * Displays a message with standard Joomla! backend css styles
     * Type can be:
     *
     * 'notice'    : YELLOW
     * 'error'    : RED
     * '[EMPTY]': BLUE [default]
     *
     * @param mixed  $messages
     * @param string $type empty, notice, error
     */
    public static function message($messages, $type = '')
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
            /* @var Exception $messages */
            $m = (JDEBUG || ECR_DEBUG) ? nl2br($messages) : $messages->getMessage();

            $trace = $messages->getTrace();
            $messages = array($messages->getMessage());

            $type = 'error';
        }

        $type = ($type) ? ' alert-'.$type : ' alert-info';

        if(false == is_array($messages))
            $messages = array($messages);

        echo '<div class="alert'.$type.'">';

        foreach($messages as $message)
        {
            echo '<p>'.$message.'</p>';
        }

        if($callFile)
            echo '<p><strong>'.$callFile.'</strong></p>';

        echo '</div>';

        if(ECR_DEBUG && $type == 'error')
            EcrHtmlDebug::printTrace($trace);
    }

    /**
     *
     * @param $ac
     * @param $newIndent
     *
     * @return string
     */
    public static function idt($ac = '', $newIndent = 0)
    {
        static $indent = 0;

        if($newIndent)
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
    }

    /**
     * converts a bytevalue into the highest possible unit and adds it's sign.
     *
     * @version  2009-01-27 03:50h
     *
     * @param    int|float  $bytes    -bytevalue to convert
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
            $exp_max = count($symbols) - 1;
        }

        $converted_value = 0;

        if($bytes > 0)
        {
            $exp = floor(log($bytes) / log(1024));

            if($exp > $exp_max)
                $exp = $exp_max;

            $converted_value = ($bytes / pow(1024, $exp));
        }

        return number_format($converted_value, 2, ',', '.').' '.$symbols[$exp];
    }
}
