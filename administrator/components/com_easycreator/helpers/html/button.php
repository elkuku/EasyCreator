<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers.HTML
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 16-May-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * HTML button class.
 *
 * @package EasyCreator
 */
abstract class EcrHtmlButton
{
    /**
     * @static
     *
     * @param $lang
     * @param $scope
     */
    public static function createLanguageFile($lang, $scope)
    {
        $button = '<a class="btn" href="javascript:;" ';
        $button .= 'onclick="document.adminForm.lngcreate_lang.value=\''.$lang.'\'; ';
        $button .= 'document.adminForm.lng_scope.value=\''.$scope.'\'; ';
        $button .= 'submitform(\'create_langfile\');">'
            .'<i class="img icon16-add"></i>'
            .jgettext('Create language file').'</a>';
        echo $button;
    }

    /**
     * @static
     *
     * @param $fileName
     */
    public static function removeBOM($fileName)
    {
        $tPath = substr($fileName, strlen(JPATH_ROOT));

        $link = 'See: <a href="http://www.w3.org/International/questions/qa-utf8-bom" '
            .'target="_blank">W3C FAQ: Display problems caused by the UTF-8 BOM</a>';

        $button = '<br /><span class="btn" '
            .'onclick="document.adminForm.file.value=\''.addslashes($tPath)
            .'\';easySubmit(\'remove_bom\', \'languages\');">'
            .'<i class="img icon16-delete"></i>'
            .'Remove BOM</span>';

        EcrHtml::message(array(jgettext('Found a BOM in languagefile'), $fileName, $link, $button), 'notice');
    }

    public static function createClassList()
    {
        $button = '<span class="btn" onclick="create_class_list();">'
            .'<i class="img icon16-add"></i>'
            .jgettext('Create class list file')
            .'</span>';

        EcrHtml::message(array(
            sprintf(jgettext('The class file for your Joomla version %s has not been build yet.'), JVERSION)
        , $button), 'notice');
    }

    /**
     * Draw a submit button
     *
     * @param array $requireds required field names separated by komma
     */
    public static function submitParts($requireds = array())
    {
        $requireds = (array)$requireds;
        $requireds = implode(',', $requireds);
        echo '<br />';
        echo '<div class="btn block" onclick="addNewElement(\''.$requireds.'\');">'
            .'<i class="img icon16-ecr_save"></i>'
            .jgettext('Save').'</div>';
    }

    /**
     * Draw a submit button
     *
     * @param array $requireds required field names separated by komma
     */
    public static function autoCode($requireds = array())
    {
        $requireds = (array)$requireds;
        $requireds = implode(',', $requireds);
        echo '<br />';
        echo '<div class="btn" onclick="updateAutoCode(\''.$requireds.'\');">'
            .'<i class="img icon16-ecr_save"></i>'
            .jgettext('Save').'</div>';
    }
}
