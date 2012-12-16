<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath
 * @author     Created on 10-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...
 *
 */
class EcrLanguageHelper
{
    /**
     * Discover the languages for a project.
     *
     * @param EcrProjectBase $project The project
     *
     * @return array
     */
    public static function discoverLanguages(EcrProjectBase $project)
    {
        static $languages = array();

        $pKey = $project->type.$project->scope.$project->comName;

        if(isset($languages[$pKey]))
        {
            return $languages[$pKey];
        }

        $langs = JFactory::getLanguage()->getKnownLanguages();

        if(count($langs > 1))
        {
            //-- We have more than one language.. order en-GB at first position
            $enGB = array('en-GB' => $langs['en-GB']);
            unset($langs['en-GB']);
            $langs = $enGB + $langs;
        }

        $languages[$pKey] = array();

        $langPaths = $project->getLanguagePaths();

        if( ! $langPaths)
        return array();

        foreach($langs as $tag => $lang)
        {
            foreach($langPaths as $scope => $paths)
            {
                if( ! is_array($paths))
                $paths = array($paths);

                foreach($paths as $path)
                {
                    if($project->langFormat != 'ini')
                    {
                        //-- Special g11n Language
                        $addPath = $tag.'/'.$tag.'.'.$project->getLanguageFileName($scope);
                    }
                    else
                    {
                        $addPath = 'language/'.$tag.'/'.$tag.'.'.$project->getLanguageFileName($scope);
                    }

                    $fileName = JPath::clean($path.'/'.$addPath);

                    if(JFile::exists($fileName))
                    {
                        $languages[$pKey][$tag][] = $scope;
                    }
                }//foreach
            }//foreach
        }//foreach

        return $languages[$pKey];
    }//function

    /**
     * Check a language file for common problems.
     *
     * @param EcrProjectBase $project The project
     * @param string $lang Language tag e.g. en-GB
     * @param string $scope Scope e.g. admin site
     *
     * @return void
     */
    public static function checkFile(EcrProjectBase $project, $lang, $scope)
    {
        $fileName = EcrLanguage::getFileName($lang, $scope, $project, false);

        //-- Get component parameters
        $params = JComponentHelper::getParams('com_easycreator');

        $file = new stdClass;

        $file->fileName = $fileName;
        $file->lang = $lang;
        $file->scope = $scope;
        $file->exists =(JFile::exists($fileName)) ? true : false;
        $file->isUFT8 = false;
        $file->hasBOM = false;

        if($file->exists)
        {
            //--Check if file is UTF-8 encoded
            $file->isUFT8 =
            $params->get('langfiles_chk_utf8') ?
            self::is_utf8(JFile::read($fileName))
            : jgettext('Not checked');

            //--Detect BOM
            $file->hasBOM =
            $params->get('langfiles_chk_bom') ?
            self::detectBOM_utf8($fileName)
            : jgettext('Not checked');
        }

        self::displayResults($file);
    }//function

    /**
     * Display the check results.
     *
     * @param JObject $file A file object
     *
     * @return void
     */
    public static function displayResults(stdClass $file)
    {
        //--Check if file exists
        if($file->exists)
        {
            echo '<span class="img icon16-check_ok hasTip" title="'.jgettext('Found').'"/>';
        }
        else
        {
            echo '<span class="img icon16-check_fail hasTip" title="'.jgettext('Not found').'" />';
            EcrHtmlButton::createLanguageFile($file->lang, $file->scope);

            return;
        }

        //--Check if file is UTF-8 encoded
        if($file->isUFT8)
        {
            if($file->isUFT8 == 'NOT CHECKED')
            {
                echo '<span class="img icon16-yellowled hasTip" title="'.jgettext('Not checked for UTF-8').'" />';
            }
            else
            {
                echo '<span class="img icon16-check_ok hasTip" title="'.jgettext('UTF-8').'" />';
            }
        }
        else
        {
            EcrHtml::message(array(jgettext('File is not UTF-8 encoded'), $file->name), 'error');
        }

        //--Detect BOM
        if($file->hasBOM)
        {
            if($file->hasBOM == 'NOT CHECKED')
            {
                echo '<span class="img icon16-yellowled hasTip" title="'.jgettext('Not checked for BOM').'" />';
            }
            else
            {
                EcrHtmlButton::removeBOM($file->fileName);
            }
        }
        else
        {
            echo '<span class="img icon16-check_ok hasTip" title="'.jgettext('No BOM').'" />';
        }
    }//function

    /**
     * Simple UTF-8-ness checker using a regular expression created by the W3C.
     *
     * @param string $string A string
     *
     * @author php-note-2005 at ryandesign dot com
     *
     * @return bool true if $string is valid UTF-8
     */
    private static function is_utf8($string)
    {
        $test =(is_array($string)) ? implode("\n", $string) : $string;

        //-- Using only the first 1000 characters.
        //-- strange error happens sometimes leading to server collapse :( @todo: investigate...
        $test = substr($test, 0, 100);

        return preg_match('%^(?:
              [\x09\x0A\x0D\x20-\x7E]            # ASCII
            | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
            |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
            | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
            |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
            |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
            | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
            |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
        )*$%xs', $test);
    }//function

    /**
     * Searches for a UTF-8 BOM/Signature in a given file.
     *
     * PHP Function to remove UTF-8 BOM/Signature from the beginning of a file.
     *
     * @param string $filename Filename
     *
     * @author http://develobert.blogspot.com/
     *
     * @return bool true if a BOM is detected
     */
    public static function detectBOM_utf8($filename)
    {
        $size = filesize($filename);

        if($size < 3)
        {
            // BOM not possible
            return false;
        }

        if($fh = fopen($filename, 'r+b'))
        {
            $test = bin2hex(fread($fh, 3));

            if(trim($test) == 'efbbbf')
            {
                if($size == 3 && ftruncate($fh, 0))
                {
                    // Empty other than BOM
                    fclose($fh);

                    return false;
                }
                else
                {
                    //---------------
                    //-- BOM found --
                    //---------------
                    fclose($fh);

                    return true;
                }
            }
            else
            {
                // No BOM found
                fclose($fh);

                return false;
            }
        }
        else
        {
            echo 'unable to open file '.$filename.'<br />';

            return false;
        }
    }//function

    /**
     * Searches for a UTF-8 BOM/Signature in a given file and removes it.
     *
     * @param string $filename Filename
     *
     * @author:   http://develobert.blogspot.com/
     *
     * @return bool true if a BOM is detected and removed
     */
    public static function removeBOM_utf8($filename)
    {
        $filename = JPATH_ROOT.$filename;
        $size = filesize($filename);

        if($size < 3)
        {
            //-- BOM not possible
            return true;
        }

        if($fh = fopen($filename, 'r+b'))
        {
            if(bin2hex(fread($fh, 3)) == 'efbbbf')
            {
                if($size == 3 && ftruncate($fh, 0))
                {
                    //-- Empty other than BOM
                    fclose($fh);

                    return true;
                }
                else if($buffer = fread($fh, $size))
                {
                    //-- BOM found
                    //-- Shift file contents to beginning of file
                    if(ftruncate($fh, strlen($buffer)) && rewind($fh))
                    {
                        if(fwrite($fh, $buffer))
                        {
                            fclose($fh);

                            return true;
                        }
                    }
                }
            }
            else
            {
                //-- No BOM found
                fclose($fh);

                return true;
            }
        }
        else
        {
            echo 'unable to open file '.$filename.'<br />';

            return false;
        }
    }//function
}//class
