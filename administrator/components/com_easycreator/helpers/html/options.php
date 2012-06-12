<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers.HTML
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 16-May-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * HTML options class.
 *
 * @package EasyCreator
 */
class EcrHtmlOptions
{
    /**
     * Display options for logging
     */
    public static function logging()
    {
        $buildopts = array(
            'files' => jgettext('Log file contents')
        , 'profile' => jgettext('Profile')
        );

        //--Get component parameters
        $params = JComponentHelper::getParams('com_easycreator');

        echo NL.'<div class="logging-options">';

        $js = "v =( $('div_buildopts').getStyle('display') == 'block') ? 'none' : 'block';";
        $js .= "$('div_buildopts').setStyle('display', v);";

        $checked = ($params->get('logging')) ? ' checked="checked"' : '';
        echo NL.'<input type="checkbox" onchange="'.$js.'" name="buildopts[]"'
            .$checked.' value="logging" id="logging" />';
        echo NL.'<label class="inline" for="logging">'.jgettext('Activate logging').'</label>';

        $style = ($params->get('logging')) ? '' : ' style="display: none;"';
        echo NL.'   <div id="div_buildopts"'.$style.'>';

        foreach($buildopts as $name => $titel)
        {
            //--Get component parameters
            $checked = ($params->get($name)) ? ' checked="checked"' : '';

            echo NL.'&nbsp;|__';
            echo NL.'<input type="checkbox" name="buildopts[]"'.$checked.' value="'.$name.'" id="'.$name.'" />';
            echo NL.'<label class="inline" for="'.$name.'">'.$titel.'</label><br />';
        }

        echo NL.'   </div>';
        echo NL.'</div>';
    }

    /**
     * Draw a file header template selector.
     *
     * @static
     *
     * @param string $selected
     *
     * @return string
     */
    public static function header($selected = 'git')
    {
        $html = array();

        foreach(new DirectoryIterator(ECRPATH_EXTENSIONTEMPLATES.'/std/header') as $fileInfo)
        {
            if($fileInfo->isDot())
                continue;

            $name = $fileInfo->getFilename();
            $checked = ($name == $selected) ? ' checked="checked"' : '';
            $html[] = '<input type="radio" name="headerType"'
                .'value="'.$name.'" id="headerType'.$name.'"'.$checked.'>'
                .'<label class="inline" for="headerType'.$name.'">'.$name.'</label>';
        }

        return implode(NL, $html);
    }

    /**
     * Draw database options.
     *
     * @static
     *
     * @param \EcrProjectBase $project
     *
     * @internal param array $projectParams
     *
     * @return string
     */
    public static function database(EcrProjectBase $project)
    {
        $formats = JFolder::files(JPATH_COMPONENT.'/helpers/sql/format');

        $options = array();

        foreach($formats as $format)
        {
            $f = JFile::stripExt($format);

            $checked = (in_array($f, $project->dbTypes)) ? ' checked="checked"' : '';

            $options[] = '<input type="checkbox" name="dbtypes[]"'.$checked.' value="'.$f.'" id="dbopt_'.$f.'" />';
            $options[] = '<label class="inline" for="dbopt_'.$f.'">'.ucfirst($f).'</label>';
        }

        return implode(NL, $options);
    }

    /**
     * Display options for packing format.
     *
     * @param EcrProjectModelBuildpreset $projectParams
     */
    public static function packing(EcrProjectModelBuildpreset $projectParams)
    {
        //--Get component parameters
        $params = JComponentHelper::getParams('com_easycreator');

        $opts = array();

        foreach(EcrEasycreator::$packFormats as $name => $ext)
        {
            if(isset($projectParams->$name))
            {
                $opts[$name] = ('1' == $projectParams->$name) ? true : false;
            }
            else
            {
                $opts[$name] = ($params->get($name) == 'on') ? true : false;
            }
        }

        if( ! $opts['archiveZip']
            && ! $opts['archiveTgz']
            && ! $opts['archiveBz2']
        )
        {
            EcrHtml::message(jgettext('Please set a compression type'), 'notice');
            echo '<div style="float: right;">'
                .EcrHelp::helpTip(jgettext('You can set a default compression type in configuration'))
                .'</div>';
        }

        foreach(EcrEasycreator::$packFormats as $name => $ext)
        {
            $checked = ('1' == $projectParams->$name) ? ' checked="checked"' : '';

            echo NL.'   <input type="checkbox" name="buildopts[]"'.$checked.' value="'.$name.'" id="'.$name.'" />';
            echo NL.'   <label class="inline" for="'.$name.'">'.$ext.'</label>';
        }
    }
}
