<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers.HTML
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 16-May-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * HTML menu class.
 *
 * @package EasyCreator
 */
class EcrHtmlMenu
{
    public static function main()
    {
        $input = JFactory::getApplication()->input;

        /*   //                                                          //
         *  //--We start our form HERE ! this is for the whole app !    //
         * //                                                          //
         */
        EcrHtml::formStart();

        $task = $input->get('task', 'stuffer');
        $ecr_project = $input->get('ecr_project');
        $project = false;

        if($ecr_project)
        {
            try
            {
                $project = EcrProjectHelper::getProject();
            }
            catch(Exception $e)
            {
                echo ''; //-- To satisfy the sniffer - aka: do nothing.
            }
        }

        //--Menu highlighting... set css class _active
        $actives = array();
        $tasks = array();
        $rightTasks = array();

        if($project instanceof EcrProjectBase
            && $project->isValid
        )
        {
            //-- Left bar

            $tasks['stuffer'] = new stdClass;
            $tasks['stuffer']->title = jgettext('Project');
            $tasks['stuffer']->image = 'ecr_settings';
            $tasks['stuffer']->tasks = array('stuffer', 'stufferstuff', 'projectinfo', 'files', 'save_config'
            , 'projectparams', 'projectdelete', 'tables', 'install');

            if('package' != $project->type)
            {
                $tasks['languages'] = new stdClass;
                $tasks['languages']->title = jgettext('Languages');
                $tasks['languages']->image = 'ecr_languages';
                $tasks['languages']->tasks = array('languages', 'translations', 'searchfiles', 'langcorrectdeforder'
                , 'langcorrectorder', 'show_version', 'show_versions', 'language_check', 'create_langfile', 'convert'
                , 'g11nUpdate');

                $tasks['codeeye'] = new stdClass;
                $tasks['codeeye']->title = jgettext('CodeEye');
                $tasks['codeeye']->image = 'xeyes';
                $tasks['codeeye']->tasks = array('codeeye', 'phpcs', 'phpcpd', 'phpunit', 'selenium', 'phpdoc'
                , 'phploc', 'stats', 'stats2', 'reflection', 'runcli', 'runwap');
            }

            $tasks['ziper'] = new stdClass;
            $tasks['ziper']->title = jgettext('Package');
            $tasks['ziper']->image = 'ecr_package';
            $tasks['ziper']->tasks = array('ziper', 'delete', 'archive');

            $tasks['deploy'] = new stdClass;
            $tasks['deploy']->title = jgettext('Deploy');
            $tasks['deploy']->image = 'ecr_deploy';
            $tasks['deploy']->tasks = array('deploy', 'package');

            foreach($tasks as $k => $v)
            {
                $actives[$k] = (in_array($task, $v->tasks)) ? ' active' : '';
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
        $rightTasks['logfiles']->image = 'text';
        $rightTasks['logfiles']->tasks = array('logfiles');

        $rightTasks['help'] = new stdClass;
        $rightTasks['help']->title = jgettext('Help');
        $rightTasks['help']->image = 'ecr_help';
        $rightTasks['help']->tasks = array('help', 'quicky', 'credits');

        $rightTasks['sandbox'] = new stdClass;
        $rightTasks['sandbox']->title = jgettext('Sandbox');
        $rightTasks['sandbox']->image = 'sandbox';
        $rightTasks['sandbox']->tasks = array();
        $rightTasks['sandbox']->href = JURI::root().'index.php?option=com_easycreator';
        $rightTasks['sandbox']->class = ' external';
        $rightTasks['sandbox']->js = '';
        $rightTasks['sandbox']->rel = ' target="_blank"';

        $rTasks = array();

        foreach($rightTasks as $k => $v)
        {
            $actives[$k] = (in_array($task, $v->tasks)) ? ' active' : '';
            $rTasks = array_merge($rTasks, $v->tasks);
        }

        $helpActive = ('jhelp' == $task) ? ' active' : '';

        ?>
    <div class="white_box">
        <div style="float: right;">
            <a class="btn<?php echo ECR_TBAR_SIZE.$helpActive; ?>" href="javascript:;"
               onclick="document.id('file_name').value=''; easySubmit('jhelp', 'help');">
                <?php echo (ECR_TBAR_ICONS) ? '<div class="img32d icon32-JHelp_btn"></div>' : ''; ?>
                <?php echo jgettext('J! API'); ?>
            </a>
        </div>

        <?php echo (ECR_DEBUG) ? '<div class="debug_ON">Debug</div>' : ''; ?>

        <div style="float: left; margin-top: -7px;"><img
            src="<?php echo JURI::Root(); ?>media/com_easycreator/admin/images/ico/icon-64-easycreator.png"
            alt="EasyCreator Logo"/>
        </div>

        <div style="float: left; padding-left: 0.5em;">
            <span class="ecrTopTitle" style="font-size: 1.4em; font-weight: bold;">EasyCreator</span>
            <br/>
            <?php EcrHtmlSelect::project(); ?> <br/>
            <span id="ecr_stat_project"></span>
        </div>

        <div style="float: left; width: 0.5em;">&nbsp;</div>

        <div style="float: left;"><?php
            if($ecr_project
                && $ecr_project != 'ecr_new_project'
                && $ecr_project != 'ecr_register_project'
            )
            {
                ?>
                <div class="btn-group">
                    <?php
                    foreach($tasks as $k => $v)
                    {
                        echo '<a class="btn'.ECR_TBAR_SIZE.$actives[$k].'" href="javascript:;"'
                            .'onclick="$(\'file_name\').value=\'\'; easySubmit(\''.$k.'\', \''.$k.'\');">';

                        echo (ECR_TBAR_ICONS)
                            ? '<div class="img32d icon32-'.$v->image.'" title="'.$v->title.'"></div>'
                            : '';

                        echo $v->title.NL;
                        echo '</a>';
                    }
                    ?>
                </div>
                <?php
            }
            ?>
        </div>

        <div style="float: left; width: 0.5em;">&nbsp;</div>

        <?php
        if(false == in_array($task, $rTasks))
        {
            ?> <a class="hasTip btn<?php echo ECR_TBAR_SIZE; ?>" href="javascript:;"
                  title="<?php echo jgettext('More...').'::'.jgettext('Click for more options'); ?>"
                  onclick="this.setStyle('display', 'none'); ecr_options_box.toggle();">
            <?php echo (ECR_TBAR_ICONS) ? '<i class="img icon16-add"></i>' : ''; ?>
            <?php echo jgettext('More...'); ?> </a> <?php
        }

        $stdJS = '';
        $stdJS .= "$('adminForm').value='';";
        $stdJS .= "$('file_name').value='';";
        ?>
        <div id="ecr_options_box" class="btn-group" style="margin-left: 1em;">
            <?php
            foreach($rightTasks as $k => $v)
            {
                $controller = (isset($v->controller)) ? $v->controller : $k;
                $cJS = " easySubmit('".$k."', '".$controller."');";
                $class = (isset($v->class)) ? $v->class : '';
                $href = (isset($v->href)) ? $v->href : 'javascript:;';
                $rel = (isset($v->rel)) ? $v->rel : '';
                $js = (isset($v->js)) ? $v->js : 'onclick="'.$stdJS.$cJS.'"';
                echo '<a class="btn '.$class.ECR_TBAR_SIZE.$actives[$k].'" href="'.$href.'" '.$js.$rel.' >'.NL;

                if(ECR_TBAR_ICONS) :
                    echo '<div class="img32d icon32-'.$v->image.'" title="'.$v->title.'"></div>'.NL;
                endif;

                echo $v->title.NL;
                echo '</a>'.NL;
                ?>
                <?php
            }
            ?>
        </div>
        <?php
        if(false == in_array($task, $rTasks))
        {
            ?>
            <script type="text/javascript">
                var ecr_options_box = new Fx.Slide('ecr_options_box');
                ecr_options_box.hide();
            </script> <?php
        }
        ?>

        <div style="clear: both"></div>
    </div>
    <?php
    }

    /**
     * @static
     *
     * @param       $subTasks
     * @param array $rightTasks
     *
     * @return string
     */
    public static function sub($subTasks, $rightTasks = array())
    {
        $task = JFactory::getApplication()->input->get('task', 'stuffer');
        $html = array();
        $htmlDescriptionDivs = '';
        $jsVars = '';
        $jsEvents = '';

        $html[] = '<div id="ecr_sub_toolbar" class="btn-group">';

        if($rightTasks)
        {
            $html[] = '<div style="float: right;">';

            foreach($rightTasks as $rTask)
            {
                $class =(isset($rTask['class'])) ? ' '.$rTask['class'] : '';
                $html[] = '<a class="btn'.ECR_TBAR_SIZE.$class.'"';
                $html[] = ' href="javascript:;"';
                $html[] = ' onclick="submitStuffer(\''.$rTask['task'].'\', this);">';

                if(ECR_TBAR_ICONS)
                    $html[] = '<i class="img16a icon16-ecr_'.$rTask['icon'].'"></i><br />';

                $html[] = $rTask['title'].'</a>';
            }

            $html[] = '</div>';
        }

        foreach($subTasks as $sTask)
        {
            $tasks = (array)$sTask['task'];

            $selected = (in_array($task, $tasks)) ? ' active' : '';

            $html[] = '<a id="btn_'.$tasks[0].'" href="javascript:;"';
            $html[] = ' class="btn'.ECR_TBAR_SIZE.$selected.'"';
            $html[] = ' onclick="submitbutton(\''.$tasks[0].'\');">';

            if(ECR_TBAR_ICONS)
                $html[] = '<i class="img-btn icon16-'.$sTask['icon'].'"></i><br />';

            $html[] = $sTask['title'].'</a>';

            if(ECR_HELP >= EcrHelp::ALL)
            {
                $htmlDescriptionDivs .= '<div class="clr hidden_div ecr_description" id="desc_'.$tasks[0].'">'
                    .$sTask['description'].'</div>';
                $jsVars .= "var desc_".$tasks[0]." = $('desc_".$tasks[0]."');\n";

                $jsEvents .= "$('btn_".$tasks[0]."').addEvents({\n"
                    ."'mouseenter': showTaskDesc.bind(desc_".$tasks[0]."),\n"
                    ."'mouseleave': hideTaskDesc.bind(desc_".$tasks[0].")\n"
                    ."});\n";
            }
        }

        $html[] = $htmlDescriptionDivs;

        if(ECR_HELP >= EcrHelp::ALL)
        {
            $html[] = "<script type='text/javascript'>"
                ."window.addEvent('domready', function() {\n"
                ."function showTaskDesc(name) {\n"
                ."   this.setStyle('display', 'block');\n"
                ."}\n"
                ."function hideTaskDesc(name) {\n"
                ."   this.setStyle('display', 'none');\n"
                ."}\n"
                .$jsVars
                .$jsEvents
                ."});\n"
                ."</script>";
        }

        $html[] = '</div>';

        return implode(NL, $html);
    }
}
