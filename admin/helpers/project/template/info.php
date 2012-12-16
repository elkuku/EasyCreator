<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath
 * @author     Created on 12-Feb-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator template info.
 *
 * @package    EasyCreator
 */
class EcrProjectTemplateInfo
{
    public $group = '';

    public $title = '';

    public $description = '';

    /**
     * @return string
     */
    public function info()
    {
        $ret = '';

        $ret .= '<div style="color: blue; font-weight: bold; text-align:center;">'
            .ucfirst($this->group).' - '.$this->title.'</div>';

        $ret .= '<div style="color: orange; font-weight: bold;">'
            .$this->description.'</div>';

        return $ret;
    }//function

    /**
     * @param        $format
     * @param string $type
     *
     * @return string
     */
    public function format($format, $type = 'new')
    {
        $ret = '';
        $ret .= '<span class="img icon16-'.$type.'">';

        switch($type)
        {
            case 'edit':
                $ret .= jgettext('Edit');
                break;

            case 'add':
                $ret .= jgettext('New');
                break;

            default:
                break;
        }//switch

        $ret .= '</span>';

        switch($format)
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

    /**
     * @static
     *
     * @param $message
     */
    public function error($message)
    {
        echo $message.' in '.get_parent_class($this);

        var_dump(debug_print_backtrace());
    }//function
}//class
