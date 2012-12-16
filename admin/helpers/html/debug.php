<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers.HTML
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 16-May-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * HTML debug class.
 *
 * @package EasyCreator
 */
abstract class EcrHtmlDebug
{
    /**
     * Draws a debug console "window".
     *
     * @static
     * @return string
     */
    public static function logConsole()
    {
        JFactory::getDocument()->addScriptDeclaration(
            "window.addEvent('domready', function() {
                document.id('ecrDebugBoxConsole').makeResizable({
                    modifiers: {x: false, y: 'height'},
                    limit: {y: [1, 600]},
                    invert: true,
                    handle: 'pollStatusGrip'
                });
            });");

        ecrStylesheet('logconsole');

        $html = array();

        $html[] = '<div id="ecrDebugBoxConsole">';
        $html[] = '   <div id="pollStatusGrip">&uArr;&dArr;</div>';
        $html[] = '   <div id="pollStatus">idle</div>';
        $html[] = '   <div class="debugTitle">'.jgettext('Log console').'</div>';
        $html[] = '   <div id="ecrDebugBox"></div>';
        $html[] = '</div>';

        return implode(NL, $html);
    }

    /**
     * @static
     *
     * @param null $trace
     *
     * @return string
     */
    public static function printTrace($trace = null)
    {
        if(false == function_exists('debug_backtrace'))
            return '';

        if( ! $trace)
            $trace = debug_backtrace();

        $traces = array();

        $traces['Debug trace'] = debug_backtrace();

        if($trace)
            $traces['Exception trace'] = $trace;

        $linkFormat = ini_get('xdebug.file_link_format');

        foreach($traces as $traceType => $trace)
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
                    $link = str_replace(JPATH_ROOT, 'JROOT', $trace[$i]['file']);

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
                $s .= (isset($trace[$i]['class'])) ? $trace[$i]['class'] : '';
                $s .= (isset($trace[$i]['type'])) ? $trace[$i]['type'] : '';
                $s .= (isset($trace[$i]['function'])) ? $trace[$i]['function'] : '';
                $s .= '</td>';

                $s .= '<td>'.$link.'</td>';

                $s .= (isset($trace[$i]['line']))
                    ? '<td align="right"><tt>'.$trace[$i]['line'].'</tt></td>'
                    : '<td>&nbsp;</td>';

                $s .= '<td>';

                if(isset($trace[$i]['args']))
                {
                    foreach($trace[$i]['args'] as $arg)
                    {
                        $s .= str_replace(JPATH_ROOT.DS, '', $arg).BR;
                    }
                }

                $s .= '</td>';

                $s .= '</tr>';
            }

            $s .= '</table>';

            echo $s;
        }
    }
}
