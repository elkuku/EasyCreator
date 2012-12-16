<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 23-May-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * EasyCreator's tiny debugger.
 *
 */
class EcrDebugger
{
    public $log = array();

    /**
     * Debug print a string.
     *
     * @param string $string The string to print
     * @param string $title An optional title
     *
     * @return void
     */
    public static function dPrint($string, $title = '')
    {
        //-- Test if JDump is installed
        if(file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_dump'.DS.'helper.php'))
        {
            dump($string, $title);
        }
        else
        {
            //-- It's not ;(
            $html = '';
            $html .= '<div class="debugprint">';
            $html .=($title) ? '<h2>'.$title.'</h2>': '';
            $html .= '<pre>';
            $html .= $string;
            $html .= '</pre>';
            $html .= '</div>';
            echo $html;
        }
    }//function

    /**
     * Debug echo.
     *
     * @param string $string The string to echo
     *
     * @return void
     */
    public static function dEcho($string)
    {
        echo '<span style="background-color: yellow;">'.$string.'</span>';
    }//function

    /**
     * Print out system variables.
     *
     * @param string $type The type e.g. "get", "post" etc.
     *
     * @return void
     */
    public static function printSysVars($type = 'all')
    {
        //-- Get debugger type
        $debug_type = JComponentHelper::getParams('com_easycreator')->get('ecr_debug_type', 'easy');

        switch($debug_type)
        {
            case 'jdump':
                //-- Test if JDump is installed
                if( ! file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_dump'.DS.'helper.php'))
                {
                    EcrHtml::message(jgettext('JDump not found'), 'error');
                }
                else
                {
                    switch($type)
                    {
                        // @codingStandardsIgnoreStart - use of superglobals
                        case 'get':
                            dump($_GET, 'GET');
                            break;

                        case 'post':
                            dump($_POST, 'POST');
                            break;

                        case 'request':
                            dump($_REQUEST, 'REQUEST');
                            break;

                        case 'backTrace':
                            dumpBacktrace();
                            break;

                        case 'sysInfo':
                        default:
                            dumpSysinfo();
                            break;

                            // @codingStandardsIgnoreEnd
                    }//switch
                }

            case 'easy':
                include_once 'Var_Dump.php';

                if(class_exists('Var_Dump'))
                {
                    Var_Dump::displayInit(
                    array('display_mode' => 'HTML4_Table')
                    , array(
                        'show_caption'   => FALSE,
                        'bordercolor'    => '#ccc',
                        'bordersize'     => '2',
                        'captioncolor'   => 'black',
                        'cellpadding'    => '8',
                        'cellspacing'    => '5',
                        'color1'         => '#000',
                        'color2'         => '#000',
                        'before_num_key' => '<span style="color: #fff; font-weight: bold;">',
                        'after_num_key'  => '</span>',
                        'before_str_key' => '<span style="color: #5450cc; font-weight: bold;">',
                        'after_str_key'  => '</span>',
                        'before_value'   => '<span style="color: #5450cc;">',
                        'after_value'    => '</span>'
                        )
                        );

                        echo '<div class="ecr_debug">';

                        switch($type)
                        {
                            // @codingStandardsIgnoreStart - use of superglobals
                            case 'get':
                                echo '<h3><tt>$_GET</tt></h3>';
                                Var_Dump::display($_GET);
                                break;
                            case 'post':
                                echo '<h3><tt>$_POST</tt></h3>';
                                Var_Dump::display($_POST);
                                break;
                            case 'request':
                                echo '<h3><tt>$_REQUEST</tt></h3>';
                                Var_Dump::display($_REQUEST);
                                break;
                            case 'all' :
                                echo '<h3><tt>$_REQUEST</tt></h3>';
                                Var_Dump::display($_REQUEST);
                                echo '<h3><tt>$_SESSION</tt></h3>';
                                Var_Dump::display($_SESSION);
                                break;
                                // @codingStandardsIgnoreEnd
                        }//switch
                        echo '</div>';
                }
                else
                {
                    echo '<div class="ecr_debug"><pre>';
                    // @codingStandardsIgnoreStart - use of superglobals
                    print_r($_REQUEST);
                    // @codingStandardsIgnoreEnd
                    echo '</pre></div>';
                }
                break;

            case 'debugtools':
                EcrHtml::message(jgettext('DebugTools not found'), 'error');
                break;

            case 'krumo' :
                ecrLoadHelper('krumo_0_2.krumo');

                switch($type)
                {
                    case 'get':
                        krumo::get();
                        break;
                    case 'post':
                        krumo::post();
                        break;
                    case 'request':
                        krumo::request();
                        break;
                    case 'all':
                        krumo::get();
                        krumo::post();
                        krumo::session();
                        break;
                }//switch

                break;

            default:
                EcrHtml::message(jgettext('No debugger set'), 'error');
                break;
        }//switch
    }//function

    /**
     * Add a string to the internal log.
     *
     * @param string $string The string to add
     *
     * @return void
     */
    public static function addLog($string)
    {
        self::$log[] = $string;
    }//function

    /**
     * Print out log events.
     *
     * @return void
     */
    public static function printLog()
    {
        $html = '';
        $html .= '<hr />';
        $html .= '<h3>LoG</h3>';
        $html .= '<ul class="loglist">';

        foreach(self::$log as $logentry)
        {
            $html .= '<li>'.$logentry.'</li>';
        }//foreach

        $html .= '</ul>';
        $html .= '<hr />';

        echo $html;
    }//function

    /**
     * Dumps a var with PEAR::Var_Dump.
     *
     * @param mixed $var The variable to dump
     * @param string $title An optional title
     *
     * @return void
     */
    public static function varDump($var, $title = '')
    {
        echo ($title) ? '<h3><tt>'.$title.'</tt></h3>' : '';

        $debug_type = JComponentHelper::getParams('com_easycreator')->get('ecr_debug_type', 'easy');

        if($debug_type == 'krumo')
        {
            ecrLoadHelper('krumo_0_2.krumo');

            krumo::dump($var);

            return;
        }

        include_once 'Var_Dump.php';

        if(class_exists('Var_Dump'))
        {
            Var_Dump::displayInit(
            array('display_mode' => 'HTML4_Table')
            , array(
                'show_caption'   => FALSE,
                'bordercolor'    => '#ccc',
                'bordersize'     => '2',
                'captioncolor'   => 'black',
                'cellpadding'    => '8',
                'cellspacing'    => '5',
                'color1'         => '#000',
                'color2'         => '#000',
                'before_num_key' => '<span style="color: #fff; font-weight: bold;">',
                'after_num_key'  => '</span>',
                'before_str_key' => '<span style="color: #5450cc; font-weight: bold;">',
                'after_str_key'  => '</span>',
                'before_value'   => '<span style="color: #5450cc;">',
                'after_value'    => '</span>'
                )
                );

                Var_Dump::display($var);
        }
        else
        {
            echo '<pre>'.print_r($var, true).'</pre>';
        }
    }//function
}//class
