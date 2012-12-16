<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 25-May-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * EcrLanguage helper class.
 *
 * @package    EasyCreator
 * @subpackage Helpers
 *
 */
class EcrLanguage
{
    private $_languages = '';

    private $_fileList = array();

    private $_com_name = '';

    private $_default_lang = 'en-GB';

    private $_strings = array();

    private $_default_file = array();

    /**
     *  definitions found in php and xml files
     */
    private $_definitions = array();

    private $_coreStrings = array();

    /*
     * scope for translation admin/site/menu
     */
    private $_scope = '';

    /*
     * languages to hide
     */
    private $_hideLangs;

    private $project = null;

    /**
     * Constructor.
     *
     * @param EcrProjectBase $project The project
     * @param string $scope Scope e.g. admin or site
     * @param array $hideLangs Do not show these languages
     */
    public function __construct(EcrProjectBase $project, $scope, $hideLangs)
    {
        $this->_hideLangs = $hideLangs;
        $this->_languages = $this->setLangs($project->langs);

        $this->project = $project;
        $this->_scope = $scope;

        if($this->_scope == 'menu')
        {
            $this->readMenu();
        }
        else
        {
            $this->_fileList = $this->_buildFileList();
        }
    }//function

    /**
     * Sets the languages.
     *
     * Ensures that en-GB is always in first place.
     *
     * @param array $languages Languages to set
     *
     * @return array
     */
    private function setLangs($languages)
    {
        if( ! is_array($languages))
        {
            return array();
        }

        if( ! array_key_exists('en-GB', $languages))
        {
            JFactory::getApplication()->enqueueMessage('Default language en-GB is not present', 'error');

            return array();
        }

        //--assure that default 'en-GB' is in first place
        $result = array('en-GB');

        foreach($languages as $key => $scopes)
        {
            if($key != 'en-GB')
            {
                $result[] = $key;
            }
        }//foreach

        return $result;
    }//function

    /**
     * Get a translation.
     *
     * @param string $lang Language key e.g. en-GB
     * @param string $key Key to translate
     *
     * @return string
     */
    public function getTranslation($lang, $key)
    {
        $path =($this->_scope == 'admin' || $this->_scope == 'menu') ? JPATH_ADMINISTRATOR : JPATH_SITE;
        $this->_readStrings();

        $translation =(isset($this->_strings[$key][$lang])) ? $this->_strings[$key][$lang] : '';

        return $translation;
    }//function

    /**
     * Gets the saved versions.
     *
     * @param string $lang Language tag e.g. en_gb
     *
     * @return array array of versions
     */
    public function getVersions($lang)
    {
        $versions = array();
        $fileName = $this->getFileName($lang, $this->_scope, $this->project);

        if( ! JFile::exists($fileName))
        {
            JFactory::getApplication()->enqueueMessage(
                sprintf(jgettext('The file %s could not be found'), $fileName), 'error');

            return $versions;
        }

        $r = 1;

        while($r > 0)
        {
            $test = '.r'.$r;

            if(JFile::exists($fileName.$test))
            {
                $version = new JObject;
                $lastMod = date("d-M-y H:i.", filectime($fileName.$test));

                $size = $this->byte_convert(filesize($fileName.$test));
                $version->fileName = JFile::getName($fileName.$test);
                $version->revNo = substr(JFile::getExt($test), 1);
                $version->lastMod = $lastMod;
                $version->size = $size;

                $versions[] = $version;
                $r++;
            }
            else
            {
                $r = 0;
            }
        }//while

        return $versions;
    }//function

    /**
     * Get defined languages.
     *
     * @return string
     */
    public function getLanguages()
    {
        return $this->_languages;
    }//function

    /**
     * Get hidden languages.
     *
     * @return string
     */
    public function getHideLangs()
    {
        return $this->_hideLangs;
    }//function

    /**
     * Get defined translations.
     *
     * @return array
     */
    public function getDefinitions()
    {
        return $this->_definitions;
    }//function

    /**
     * Get defined strings.
     *
     * @return array
     */
    public function getStrings()
    {
        return $this->_strings;
    }//function

    /**
     * Get the defined core translations.
     *
     * @return array
     */
    public function getCoreStrings()
    {
        return $this->_coreStrings;
    }//function

    /**
     * Get the default file.
     *
     * @return array
     */
    public function getDefaultFile()
    {
        return $this->_default_file;
    }//function

    /**
     * Displays the actual file and a selected version side by side.
     *
     * @param integer $revNo Revision number
     * @param string $lang Language tag e.g. en-GB
     *
     * @return void
     */
    public function displayVersion($revNo, $lang)
    {
        $fileName = $this->getFileName($lang, $this->_scope, $this->project);
        $sRev = '.r'.$revNo;

        $fileNameOrig = $fileName;
        $fileNameRev = $fileName.'.r'.$revNo;

        $fileOrig = '';
        $fileRev = '';

        if(JFile::exists($fileNameOrig))
        {
            $fileOrig = JFile::read($fileNameOrig);

            if($fileOrig)
            {
                $fileOrig = explode("\n", $fileOrig);
            }
        }

        if(JFile::exists($fileNameRev))
        {
            $fileRev = JFile::read($fileNameRev);

            if($fileRev)
            {
                $fileRev = explode("\n", $fileRev);
            }
        }

        ecrLoadHelper('DifferenceEngine');

        //--we are adding a blank line to the end.. this is somewhat 'required' by PHPdiff
        if($fileOrig[count($fileOrig) - 1] != '')
        {
            $fileOrig[] = '';
        }

        if($fileRev[count($fileRev) - 1] != '')
        {
            $fileRev[] = '';
        }

        $dwDiff = new Diff($fileRev, $fileOrig);
        $dwFormatter = new TableDiffFormatter;

        ?>

<table class="diff">
    <tr>
        <th colspan="2"><?php echo sprintf(jgettext('Version No. %s'), $revNo); ?></th>
        <th colspan="2"><?php echo jgettext('Actual file'); ?></th>
    </tr>
    <?php echo $dwFormatter->format($dwDiff); ?>
</table>
<?php
/* PHP */

?>
<!--
        <table width="100%">
            <tr>
                <th><?php echo jgettext('Actual file'); ?></th>
                <th><?php echo sprintf(jgettext('Version No. %s'), $revNo); ?></th>
            </tr>
            <tr valign="top">
            <?php

            ?>
                <td><?php if( ! $fileOrig)
                {
                    echo '<strong style="color: red;">'.jgettext('File not found').'</strong>';
                }
                else
              {
                    $this->displayFields($lang, $fileOrig);
                }
                ?></td>
                <td><?php if( ! $fileRev)
                {
                    echo '<strong style="color: red;">'.jgettext('File not found').'</strong>';
                }
                else
              {
                    $this->displayFields($lang, $fileRev);
                }
                ?></td>
            </tr>
        </table>
        -->
                <?php
    }//function

    /**
     * Converts bytes to higher units.
     *
     * @param integer $bytes Ammount of bytes
     *
     * @deprecated use EcrHtml::byte_convert()
     *
     * @return string
     */
    private function byte_convert($bytes)
    {
        $symbol = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB');

        $exp = 0;
        $converted_value = 0;

        if($bytes > 0)
        {
            $exp = floor(log($bytes) / log(1024));
            $converted_value = ($bytes / pow(1024, floor($exp)));
        }

        return sprintf('%.2f '.$symbol[$exp], $converted_value);
    }//function

    /**
     * Corrects the line positions of a translated language file according to the default language file.
     *
     * @param array $defaultLanguage Default language file
     * @param array $translatedLanguage Translated language file
     *
     * @return return_type
     */
    public function correctTranslation($defaultLanguage, $translatedLanguage)
    {
        $correctedLanguage = array();

        //--read the header from translated language
        foreach($translatedLanguage as $line)
        {
            $corrected = new JObject;

            if($line->key == '#')
            {
                $corrected->key = '#';
                $corrected->value = $line->value;
                $correctedLanguage[] = $corrected;
            }
            else
            {
                break;
            }
        }//foreach

        $isHeader = true;

        foreach($defaultLanguage as $line)
        {
            $corrected = new JObject;

            if($line->key == '#')
            {
                if( ! $isHeader)
                {
                    $corrected->key = '#';
                    $corrected->value = $line->value;
                    $correctedLanguage[] = $corrected;
                }
            }
            else
            {
                $isHeader = false;
                $trans = '**TRANSLATE**';

                foreach($translatedLanguage as $tLine)
                {
                    if($tLine->key == $line->key)
                    {
                        $trans = $tLine->value;
                        break;
                    }
                }//foreach

                $corrected->key = $line->key;
                $corrected->value = $trans;
                $correctedLanguage[] = $corrected;
            }
        }//foreach

        return $correctedLanguage;
    }//function

    /**
     * Save a language file.
     *
     * @param string $lang Language tag e.g. en-GB
     * @param string $fileContents The file contents
     *
     * @return boolean
     */
    public function saveFile($lang, $fileContents)
    {
        $fileName = $this->getFileName($lang, $this->_scope, $this->project);

        //--Save a version ?
        $saveVersion = JFactory::getApplication()->input->getInt('save_version', '1');

        if($saveVersion)
        {
            if( ! EcrFile::saveVersion($fileName))
            {
                return false;
            }
        }

        $fileContents = implode("\n", $fileContents);

        if( ! JFile::write($fileName, $fileContents))
        {
            JFactory::getApplication()->enqueueMessage(sprintf(jgettext('Unable to write file %s'), $fileName), 'error');

            return false;
        }

        JFactory::getApplication()->enqueueMessage(jgettext('The file has been saved'));

        return true;
    }//function

    /**
     * Saves a single translation item to a file.
     *
     * @param string $lang        Language tag e.g. en-GB
     * @param string $key         The key to save
     * @param string $translation The translation to save
     *
     * @throws Exception
     * @return return_type
     */
    public function saveTranslation($lang, $key, $translation)
    {
        $fileName = $this->getFileName($lang, $this->_scope, $this->project);

        $origFile = $this->parseFile($fileName);

        if( ! $origFile)
        throw new Exception(sprintf(jgettext('Unable to parse file %s'), $fileName));

        $resultFile = array();
        $pos = 1;
        $found = false;

        $translation = '"'.$translation.'"';

        foreach($origFile as $line)
        {
            switch($line->key)
            {
                case '#':
                case '-':
                    $resultFile[] = $line->value;
                    $pos ++;
                    break;
                default:
                    if($line->key == $key)
                    {
                        //--Found it
                        $resultFile[] = $line->key.'='.$translation;
                        $pos ++;
                        $found = true;
                    }
                    else
                    {
                        $resultFile[] = $line->key.'='.$line->value;
                    }
                    break;
            }//switch
        }//foreach

        if( ! $found)
        {
            //--New translation - append
            $resultFile[] = $key.'='.$translation;
        }

        if( ! EcrFile::saveVersion($fileName))
        throw new Exception(sprintf(jgettext('Unable to save backup version for file %s'), $fileName));

        $contents = implode(NL, $resultFile);

        if( ! JFile::write($fileName, $contents))
        throw new Exception(sprintf(jgettext('Unable to write the file %s'), $fileName));

        return true;
    }//function

    /**
     * Delete a translation by key.
     *
     * @param string $lang Language tag e.g. en-GB
     * @param string $key The key to delete
     *
     * @throws Exception
     * @return boolean
     */
    public function deleteTranslation($lang, $key)
    {
        $fileName = $this->getFileName($lang, $this->_scope, $this->project);

        $origFile = $this->parseFile($fileName);
        $resultFile = array();
        $pos = 1;
        $found = false;

        foreach($origFile as $line)
        {
            switch($line->key)
            {
                case '#':
                case '-':
                    $resultFile[] = $line->value;
                    $pos ++;
                    break;

                default:
                    if($line->key == $key)
                    {
                        //--Found it
                        $found = true;
                    }
                    else
                    {
                        $resultFile[] = $line->key.'='.$line->value;
                    }
                    break;
            }//switch
        }//foreach

        if( ! $found)
        {
            //-- Item to delete has not been found !
            throw new Exception(sprintf(jgettext('Key not found: %s'), $key));
        }

        //-- Save a version ?
        if(JFactory::getApplication()->input->getInt('save_version', '1'))
        {
            if( ! EcrFile::saveVersion($fileName))
            {
                throw new Exception(jgettext('Can not save version'));
            }
        }

        $contents = implode("\n", $resultFile);

        if( ! JFile::write($fileName, $contents))
        {
            throw new Exception(sprintf(jgettext('Unable to write file %s'), $fileName));
        }

        return true;
    }//function

    /**
     * Reads the strings from language files.
     *
     * @return void
     */
    public function _readStrings()
    {
        foreach($this->_languages as $lang)
        {
            if(in_array($lang, $this->_hideLangs))
            {
                continue;
            }

            //--Read the file
            $file = $this->_getFile($lang);

            if( ! $file)
            {
                continue;
            }

            foreach($file as $line)
            {
                $line = trim($line);

                if(strpos($line, '#') === 0)
                {
                    if(strpos($line, '@version'))
                    {
                        //--Version string found
                        if($lang == $this->_default_lang)
                        {
                            $this->_default_file[] = array('version' => $line);
                        }

                        continue;
                    }

                    if($lang == $this->_default_lang)
                    {
                        $this->_default_file[] = array('comment' => $line);
                    }

                    continue;
                }

                $eqpos = strpos($line, '=');

                if($eqpos)
                {
                    //--Found a pair
                    $key = trim(substr($line, 0, $eqpos));
                    $value = trim(substr($line, $eqpos + 1));

                    if( ! array_key_exists($key, $this->_strings))
                    {
                        $this->_default_file[] = array('key' => array($key => $value));
                    }

                    $this->_strings[$key][$lang] = $value;

                    continue;
                }

                if($lang == $this->_default_lang)
                {
                    $this->_default_file[] = array('etc' => $line);
                }
            }//foreach
        }//foreach
    }//function

    /**
     * Parse a language file.
     *
     * @param string $path Path to language file
     *
     * @throws Exception
     * @return array
     */
    public function parseFile($path)
    {
        if( ! JFile::exists($path))
        throw new Exception(sprintf(jgettext('File %s not found'), $path));

        //--Read the file
        $file = explode("\n", JFile::read($path));
        $parsed = array();

        foreach($file as $line)
        {
            $line = trim($line);
            $translation = new stdClass;

            if(strpos($line, '#') === 0)
            {
                //--Comment line
                $translation->key = '#';
                $translation->value = $line;
                $parsed[] = $translation;

                continue;
            }

            $eqpos = strpos($line, '=');

            if($eqpos)
            {
                //--Found a pair
                $key = substr($line, 0, $eqpos);
                $value = substr($line, $eqpos + 1);
                $translation->key = $key;
                $translation->value = $value;
                $parsed[] = $translation;

                continue;
            }

            $translation->key = '-';
            $translation->value = $line;

            $parsed[] = $translation;
        }//foreach

        return $parsed;
    }//function

    /**
     * Draw the language file table.
     *
     * @param string $path Path to..
     *
     * @deprecated move to a template
     *
     * @return void
     */
    private function drawTableLanguageFiles($path)
    {
        $input = JFactory::getApplication()->input;

        //$lang_first_line_comment = 3;
        $lang_first_line_comment_cnt = 0;
        $tableHeader = '';
        $tableHeader .= NL.'<table class="adminlist">';
        $tableHeader .= NL.'<thead>';
        $tableHeader .= '<tr>';
        $tableHeader .= '<th style="background-color: #CCE5FF;">'.jgettext('KEY').'</th>';

        foreach($this->_languages as $lang)
        {
            if(in_array($lang, $this->_hideLangs))
            {
                continue;
            }

            $tableHeader .= '<th width="'.(100 / (count($this->_languages) + 2)).'%">'.$lang.'</th>';
        }//foreach
        $tableHeader .= '<th style="background-color: #FFFFB2;">'.jgettext('Used in File').'</th>';
        $tableHeader .= '</tr>';
        $tableHeader .= '</thead>'.NL;
        $sliderDrawed = false;
        $started = false;

        $lang_fileanalysis_fold = $input->getInt('lang_fileanalysis_fold');
        $lang_fileanalysis_comment_num = $input->getInt('lang_fileanalysis_comment_num', 0);
        $lang_fileanalysis_active = $input->getInt('lang_fileanalysis_active', 0);

        $checked =($lang_fileanalysis_fold) ? ' checked="checked"' : '';

        if($checked)
        {
            jimport('joomla.html.pane');
        }
        ?>
<table>
    <tr>
        <td>
        <h2><?php echo jgettext('Language files analysis'); ?></h2>
        </td>
        <td><?php echo sprintf(jgettext('Treat the first %s lines as comment')
        , '<select name="lang_fileanalysis_comment_num"
                  onchange="submitbutton(\'languages\');">');

        for($i = 0; $i < 10; $i++)
        {
            $selected =($lang_fileanalysis_comment_num == $i) ? ' selected="selected"' : '';
            echo '<option'.$selected.'>'.$i.'</option>';
        }//for

        echo '</select>';
        ?> <br />
        <input type="checkbox" name="lang_fileanalysis_fold"
            id="lang_fileanalysis_fold" value="lang_fileanalysis_fold"
            onclick="submitbutton('languages');" <?php echo $checked; ?> /> <label
            for="lang_fileanalysis_fold"><?php echo jgettext('Fold'); ?></label>
        </td>
    </tr>
</table>
        <?php
        $k = 0;
        $folder_num = -1;
        $fieldID = 0;
        $ecr_project = $input->get('ecr_project');

        foreach($this->_default_file as $line)
        {
            foreach($line as $key => $value)
            {
                switch($key)
                {
                    case 'comment':
                        if($lang_first_line_comment_cnt < $lang_fileanalysis_comment_num)
                        {
                            echo $value.'<br />';
                            $lang_first_line_comment_cnt ++;
                        }
                        else
                        {
                            if($lang_fileanalysis_fold)
                            {
                                $value = substr($value, 1);

                                if($sliderDrawed)
                                {
                                    echo '</table>';
                                    echo $pane2->endPanel();
                                    echo $pane2->startPanel($value, $value.'-ini-analysis-page');
                                    echo $tableHeader;
                                }
                                else
                                {
                                    $pane2 = JPane::getInstance('sliders', array(
                                    'startOffset' => $lang_fileanalysis_active
                                    , 'startTransition' => ''));
                                    echo $pane2->startPane($path.'-pane');
                                    echo $pane2->startPanel($value, $value.'-analysis-page');
                                    echo $tableHeader;
                                    $sliderDrawed = true;
                                }

                                $k = 0;
                                $folder_num ++;
                            }
                            else
                            {
                                if( ! $started)
                                {
                                    echo $tableHeader;
                                    $started = true;
                                }

                                echo NL.'<tr>';
                                echo '<td colspan="'.(count($this->_languages) + 2).'"'
                                .' style="background-color: #FFE5B2;">'.$value.'</td>';
                                echo '</tr>';
                            }
                        }
                        break;

                    case 'key':
                        $lang_first_line_comment_cnt = $lang_fileanalysis_comment_num;

                        if( ! $sliderDrawed && ! $started)
                        {
                            echo $tableHeader;
                            $started = true;
                        }

                        echo NL.'<tr class="row'.$k.'">';

                        foreach($value as $skey => $svalue)
                        {
                            echo '<td align="left"><span style="color: #666666;">'.$skey.'</span></td>';

                            foreach($this->_languages as $lang)
                            {
                                if(in_array($lang, $this->_hideLangs))
                                {
                                    continue;
                                }

                                echo '<td>';
                                $fieldID ++;
                                $link = 'index.php?option=com_easycreator&amp;task=translate'
                                .'&amp;tmpl=component&amp;view=languages&amp;controller=languages';
                                $link .= '&amp;ecr_project='.$ecr_project;
                                $link .= '&amp;trans_lang='.$lang;
                                $link .= '&amp;trans_key='.$skey;
                                $link .= '&amp;field_id='.$fieldID;

                                JHTML::_('behavior.modal', 'a.ecr_modal');
                                ?>

<a class="ecr_modal" title="<?php echo jgettext('Click to translate'); ?>"
    href="<?php echo $link; ?>"
    rel="{handler: 'iframe', size: {x: 900, y: 310}}"
    id="trfield_<?php echo $fieldID; ?>"> <?php
    $tmpStrings =(isset($this->_strings[$skey][$lang])) ? $this->_strings[$skey][$lang] : array();
    $this->_displayField($lang, $skey, $tmpStrings);
    ?> </a>
    <?php
    echo '</td>';
                            }//foreach
                            $used = false;
                            echo '<td>';

                            foreach($this->_definitions as $definition)
                            {
                                if($skey == strtoupper($definition->definition))
                                {
                                    foreach($this->_languages as $lang)
                                    {
                                        if(in_array($lang, $this->_hideLangs))
                                        {
                                            continue;
                                        }

                                        if(isset($this->_strings[$skey][$lang])
                                            && $this->_strings[$skey][$lang])
                                        {
                                            $definition->translated[] = $lang;
                                        }

                                        if(isset($this->_coreStrings[$skey][$lang])
                                            && $this->_coreStrings[$skey][$lang])
                                        {
                                            $definition->coreTranslated[] = $lang;
                                        }
                                    }//foreach
                                    foreach($definition->files as $fName => $fCount)
                                    {
                                        if($this->_scope == 'menu')
                                        {
                                            echo '<span style="background-color: #FFFFB2;">'.$fCount.'</span><br />';
                                        }
                                        else
                                        {
                                            echo '<span style="background-color: #FFFFB2;">'
                                            .JFile::getName($fName).'('.$fCount.') '
                                            .'(<strong class="hasTip" style="color:blue;" title="'.$fName.'">'
                                            .'aaaXXW'.jgettext('PATH').'</strong>)</span><br />';
                                        }
                                    }//foreach
                                    $used = true;
                                }
                            }//foreach

                            if( ! $used)
                            {
                                echo '<strong style="color: red;">'.jgettext('NOT USED').'</strong>';
                            }
                        }//foreach
                        echo '</td>';
                        echo '</tr>';
                        break;

case 'version':
case 'etc':

    break;
                }//switch
                $k = 1 - $k;
            }//foreach
        }//foreach
        echo '</table>';

        if($sliderDrawed)
        {
            echo $pane2->endPanel();
            echo $pane2->endPane();
        }
    }//function

    /**
     * Show language file in a path.
     *
     * @param string $path The path
     *
     * @deprecated move to a template
     *
     * @return void
     */
    private function _showFiles($path)
    {
        ?>
<table>
    <tr valign="top">
    <?php
    foreach($this->_languages as $lang)
    {
        if(in_array($lang, $this->_hideLangs))
        {
            continue;
        }
        ?>
            <td><?php
                echo $lang.'<br />';

    //--read the file
    $file = $this->_getFile($lang, $path);

    if($file)
    {
        $this->displayFields($lang, $file);
        $this->displayRaw($file);
    }
    else
    {
        echo '<p style="color: red">'.jgettext('File not found').'</p>';
        ?>
        <div class="btn"
            onclick="document.adminForm.lngcreate_lang.value='<?php echo $lang; ?>'; submitform('create_langfile');">
            <?php echo jgettext('Create language file'); ?></div>
            <?php
        }
        ?></td>
        <?php
    }//foreach
    ?>
    </tr>
</table>
    <?php
    }//function

    /**
     * Add menu entries as translatable items.
     *
     * @return void
     */
    private function readMenu()
    {
        if(isset($this->project->menu['text']) && $this->project->menu['text'])
        {
            $text = str_replace('com_', '', $this->project->comName);
            $text = $this->project->comName;
            $this->_addDefinition($text, 'menu');
        }

        if(isset($this->project->submenu) && count($this->project->submenu))
        {
            foreach($this->project->submenu as $subMenu)
            {
                $text = $this->project->comName.'.'.$subMenu['text'];
                $this->_addDefinition($text, 'submenu');
            }//foreach
        }
    }//function

    /**
     * Build a list of files to search for translation strings.
     *
     * @return void
     */
    private function _buildFileList()
    {
        $scope = $this->_scope;
        $type = '';

        if(strpos($scope, '_'))
        {
            $parts = explode('_', $scope);
            $type = $parts[0];
            $scope = $parts[1];
        }

        foreach($this->project->copies as $copyItem)
        {
            if($scope == 'admin')
            {
                //--admin scope - only load files from folders starting with 'admin'
                if($this->project->type != 'plugin'
                && strpos($copyItem, JPATH_ADMINISTRATOR) === false)
                {
                    continue;
                }
            }
            else
            {
                //--site scope - only load files from folders NOT starting with 'admin'
                if(strpos($copyItem, JPATH_ADMINISTRATOR) === 0)
                {
                    continue;
                }
            }

            if(JFolder::exists($copyItem))
            {
                //--Add all PHP and XML files from a given folder
                if(isset($this->project->buildOpts['lng_separate_javascript'])
                && $this->project->buildOpts['lng_separate_javascript'])
                {
                    $filter =($type == 'js') ? '\.js$' : '\.php$|\.xml$';
                }
                else
                {
                    $filter = '\.php$|\.xml$|\.js$';
                }

                $files = JFolder::files($copyItem, $filter, true, true);

                $this->_fileList = array_merge($this->_fileList, $files);
            }
            else if(JFile::exists($copyItem))
            {
                //--Add a single file
                if( ! in_array($copyItem, $this->_fileList))
                {
                    $this->_fileList[] = $copyItem;
                }
            }
        }//foreach

        if($type != 'js')
        {
            $manifest = EcrProjectHelper::findManifest($this->project);

            if($manifest && ! in_array(JPATH_ROOT.DS.$manifest, $this->_fileList))
            {
                $this->_fileList[] = JPATH_ROOT.DS.$manifest;
            }
        }

        if( ! count($this->_fileList))
        {
            return;
        }

        foreach($this->_fileList as $fileName)
        {
            $definitions = $this->getKeys($fileName);

            foreach($definitions as $definition => $fileName)
            {
                $this->_addDefinition($definition, $fileName);
            }//foreach
        }//foreach
    }//function

    /**
     * Get JText Keys from a given file.
     *
     * @param string $fileName Full path to file
     *
     * @return array keys as key, file names as value
     */
    public function getKeys($fileName)
    {
        $definitions = array();

        $cmds = array();

        if($this->project->langFormat == 'ini')
        {
            $cmds['php1'] = 'JText::_';
            $cmds['php2'] = 'JText::sprintf';
            $cmds['php3'] = 'JText::printf';
            $cmds['js'] = 'Joomla.JText._';
        }
        else
        {
            $cmds['php1'] = 'jgettext';
            $cmds['php2'] = 'jgettext';
            $cmds['php3'] = 'jgettext';
            $cmds['js'] = 'jgettext';
        }

        //--RegEx pattern for JText in PHP files
        $patternPHP =
        //-- Regular JText JText_('foo')
            "/".$cmds['php1']."\(\s*\'(.*)\'\s*\)|".$cmds['php1']."\(\s*\"(.*)\"\s*\)"
        //-- JText with parameters JText_('foo', ...)
            ."|".$cmds['php1']."\(\s*\'(.*)\'\s*\,|".$cmds['php1']."\(\s*\"(.*)\"\s*\,"
        //-- JText sprintf
            ."|".$cmds['php2']."\(\s*\'(.*)\'|".$cmds['php2']."\(\s*\"(.*)\""
        //-- JText printf
            ."|".$cmds['php3']."\(\s*\'(.*)\'|".$cmds['php3']."\(\s*\"(.*)\"";

        if($this->project->langFormat == 'ini')
        {
            // JHtml::_('grid.sort', 'FOO', ...)
            $patternPHP .= "|JHtml::_\(\'grid\.sort\'\, \'(.*)\'"
            //-- JToolBarHelper::custom('users.activate', 'xxx.png', 'xxx.png', 'FOO'...
            ."|JToolBarHelper::custom\(\'.*\'\,\s*\'.*\'\,\s*\'.*'\,\s*\'(.*)\'/iU";
            //(.*))\'/iU";//, 'publish.png', 'publish_f2.png', 'COM_USERS_TOOLBAR_ACTIVATE', true);/iU";
        }
        else
        {
            $patternPHP .= '/iU';
        }

        //--RegEx pattern for Joomla.JText in Javascript files
        $patternJs =
        //--In case there is the second parameter (default) set
            "/".$cmds['js']."\(\s*\"(.*)\"|".$cmds['js']."\(\s*\'(.*)\'"
        //--'''normal''' use...
            ."|".$cmds['js']."\(\s*\'(.*)\'\s*\)|".$cmds['js']."\(\s*\"(.*)\"\s*\)/iU";

        //--RegEx pattern for JText in PHP files
        //        $pattern = "/JText::_\(\s*\'(.*)\'\s*\)|JText::_\(\s*\"(.*)\"\s*\)".
        //            "|JText::sprintf\(\s*\"(.*)\"|JText::sprintf\(\s*\'(.*)\'".
        //            "|JText::printf\(\s*\'(.*)\'|JText::printf\(\s*\"(.*)\"/iU";
        //
        //        //--RegEx pattern for Joomla.JText in Javascript files
        //        $patternJs =
        //        //--In case there is the second parameter (default) set
        //            "/Joomla.JText._\(\s*\"(.*)\"|Joomla.JText._\(\s*\'(.*)\'".
        //        //--'''normal''' use...
        //               "|Joomla.JText._\(\s*\'(.*)\'\s*\)|JText::_\(\s*\"(.*)\"\s*\)/iU";

        switch(JFile::getExt($fileName))
        {
            case 'php':
                //--Search PHP files
                $contents = JFile::read($fileName);

                preg_match_all($patternPHP, $contents, $matches, PREG_SET_ORDER);

                foreach($matches as $match)
                {
                    foreach($match as $key => $m)
                    {
                        $m = ltrim($m);
                        $m = rtrim($m);

                        if($m == '' || $key == 0)
                        continue;

                        $definitions[$m] = $fileName;
                    }//foreach
                }//foreach
                break;

            case 'js':
                //--Search Javascript files
                $contents = JFile::read($fileName);

                preg_match_all($patternJs, $contents, $matches, PREG_SET_ORDER);

                foreach($matches as $match)
                {
                    foreach($match as $key => $m)
                    {
                        $m = ltrim($m);
                        $m = rtrim($m);

                        if($m == '' || $key == 0)
                        continue;

                        $definitions[$m] = $fileName;
                    }//foreach
                }//foreach
                break;

            case 'xml':
                //--Search XML files
                $xmlDoc = EcrProjectHelper::getXML($fileName);

                if($xmlDoc)
                {
                    if((string)$xmlDoc->description)
                    {
                        $definitions[(string)$xmlDoc->description] = $fileName;
                    }

                    foreach($xmlDoc->params as $params)
                    {
                        $s = (string)$params->attributes()->group;

                        if($s)
                        $definitions[$s] = $fileName;

                        foreach($params->param as $param)
                        {
                            $s = (string)$param->attributes()->label;

                            if($s)
                            $definitions[$s] = $fileName;

                            $s = (string)$param->attributes()->default;

                            if($s != (int)($s))
                            {
                                //-- Don't add numbers
                                if($s)
                                $definitions[$s] = $fileName;
                            }

                            $s = (string)$param->attributes()->description;

                            if($s)
                            $definitions[$s] = $fileName;

                            foreach($param->option as $option)
                            {
                                $s = (string)$option;

                                if($s)
                                $definitions[$s] = $fileName;
                            }//foreach
                        }//foreach
                    }//foreach
                }
                break;

            default :
                EcrHtml::message('Unknown file extension: '.JFile::getExt($fileName), 'error');
                break;
        }//switch

        return $definitions;
    }//function

    /**
     * Add a definition.
     *
     * @param string $definition The definition
     * @param string $file File name where the definition is defined
     *
     * @return void
     */
    private function _addDefinition($definition, $file)
    {
        $def = new stdClass;
        $def->definition = $definition;
        $def->translated = array();
        $def->coreTranslated = array();

        if( ! count($this->_definitions))
        {
            if($file != 'menu' && $file != 'submenu')
            {
                $def->files = array(substr($file, strlen(JPATH_ROOT)) => 1);
            }
            else
            {
                $def->files = array($file);
            }

            $this->_definitions[] = $def;
        }
        else
        {
            $exists = false;

            foreach($this->_definitions as $a_def)
            {
                if($a_def->definition == $def->definition)
                {
                    $exists = true;

                    if(array_key_exists(substr($file, strlen(JPATH_ROOT)), $a_def->files))
                    {
                        //-- definition exists - increase counter
                        $a_def->files[substr($file, strlen(JPATH_ROOT))] += 1;
                    }
                    else
                    {
                        $a_def->files[substr($file, strlen(JPATH_ROOT))] = 1;
                    }

                    continue;
                }
            }//foreach

            if( ! $exists)
            {
                //-- new definition
                if($file != 'menu' && $file != 'submenu')
                {
                    $def->files = array(substr($file, strlen(JPATH_ROOT)) => 1);
                }
                else
                {
                    $def->files = array($file);
                }

                $this->_definitions[] = $def;
            }
        }
    }//function

    /**
     * Raw display a language file.
     *
     * @param string $lang Language tag e.g. en-GB
     * @param array $file File contents
     *
     * @return void
     */
    private function displayFields($lang, $file)
    {
        echo '<div style="background-color: #eeeeee; padding: 10px;">';

        foreach($file as $line)
        {
            if(strpos($line, '@version'))
            {
                echo '<div style="color: green;">'.$line.'</div>';
                continue;
            }

            echo htmlentities($line).'<br />';
        }//foreach

        echo '</div>';
    }//function

    /**
     * Read a language file.
     *
     * @param string $lang Single language eg. 'en-GB'
     *
     * @return mixed array of lines / false on error
     */
    private function _getFile($lang)
    {
        $fileName = $this->getFileName($lang, $this->_scope, $this->project);

        if(JFile::exists($fileName))
        {
            //--Read the file
            $file = file($fileName);

            return $file;
        }
        else
        {
            //--FileNotFound
            //            #         EcrHtml::drawButtonCreateLanguageFile($lang, $this->_scope);

            return false;
        }
    }//function

    /**
     * Creates a new file from request parameters.
     *
     * @return bool true on success
     * @throws Exception
     */
    public static function createFileFromRequest()
    {
        $input = JFactory::getApplication()->input;

        $project = EcrProjectHelper::getProject();

        if( ! $scope = $input->get('lng_scope'))
        throw new Exception(jgettext('No scope given'));

        if( ! $lang = $input->get('lngcreate_lang'))
        throw new Exception(jgettext('No language given'));

        $fileName = self::getFileName($lang, $scope, $project);

        $fileContents = '';
        $fileContents .= '; @version $Id'.'$'.NL;//Splitted to avoid property being setted
        $fileContents .= '; '.$project->comName.' '.$scope.' language file'.NL;
        $fileContents .= '; @created on '.date('d-M-Y').NL;

        if(JFile::exists($fileName))
        throw new Exception(sprintf(jgettext('The file %s already exists'), $fileName));

        if( ! JFile::write($fileName, $fileContents))
        throw new Exception(sprintf(jgettext('Unable to write the file %s'), $fileName));

        return true;
    }//function

    /**
     * Output raw file as array.
     *
     * @param string $file The filename
     *
     * @deprecated
     *
     * @return void
     */
    private function displayRaw($file)
    {
        echo '<pre style="background-color: #eeeeee; padding: 10px;">';

        foreach($file as $line)
        {
            echo $line;
        }//foreach

        echo '</pre>';
    }//function

    /**
     * Generates a language file name.
     *
     * @param string $lang Language code eg. en-GB
     * @param string $scope Eg. admin
     * @param EcrProjectBase $project The EcrProject
     *
     * @return string full path to file
     */
    public static function getFileName($lang, $scope, EcrProjectBase $project)
    {
        $paths = $project->getLanguagePaths($scope);

        if( ! is_array($paths))
        $paths = array($paths);

        if($project->langFormat != 'ini')
        {
            //-- Special g11n Language
            $addPath = $lang.'/'.$lang.'.'.$project->getLanguageFileName($scope);
        }
        else
        {
            $addPath = 'language/'.$lang.'/'.$lang.'.'.$project->getLanguageFileName($scope);
        }

        foreach($paths as $path)
        {
            if(file_exists($path.DS.$addPath))
            {
                return $path.DS.$addPath;
            }
        }//foreach

        //-- No existing file found.
        //-- Return a valid new file name based on the scope

        if(isset($paths[$scope]))
        {
            return $paths[$scope].DS.$addPath;
        }

        if(isset($paths[0]))
        {
            return $paths[0].DS.$addPath;
        }

        //-- Found nothing :(
        return '';
    }//function
}//class
