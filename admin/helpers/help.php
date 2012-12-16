<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 27-May-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator help helper class.
 */
class EcrHelp
{
    const NOPE = 0;

    const SOME = 1;

    const ALL = 2;

    /**
     * Display a help message.
     *
     * @param        $string
     * @param        $level
     * @param string $class
     *
     * @return string
     */
    public static function help($string, $level, $class = '')
    {
        if(ECR_HELP == EcrHelp::NOPE)
            return '';

        if(ECR_HELP != EcrHelp::ALL)
        {
            if(ECR_HELP != $level)
                return '';
        }

        return ($class) ? '<div class="'.$class.'">'.$string.'</div>' : $string;
    }

    /**
     * Display an Info tooltip
     *
     * @param string $text
     * @param string $title
     *
     * @return string
     */
    public static function info($text, $title = '')
    {
        if(ECR_HELP < self::SOME)
            return '';

        $tooltip = htmlspecialchars($text, ENT_COMPAT, 'UTF-8');

        if('' != $title)
        {
            $tooltip = htmlspecialchars($title, ENT_COMPAT, 'UTF-8').'::'.$tooltip;
        }

        return self::helpTip($tooltip);
    }

    /**
     * @static
     *
     * @param string $tooltip
     *
     * @return string
     */
    public static function helpTip($tooltip)
    {
        return '<span class="img16 icon16-ecr-help hasTip" title="'.$tooltip.'"></span>';
    }
}
