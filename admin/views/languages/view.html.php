<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * HTML View class for the EasyCreator Component.
 *
 * @package EasyCreator
 * @subpackage Views
 */
class EasyCreatorViewLanguages extends JViewLegacy
{
    protected $versions = array();

    protected $scope;

    protected $scopes = array();

    protected $hideLangs = array();

    private $paths = array(
      'admin' => JPATH_ADMINISTRATOR
    , 'site' => JPATH_SITE);

    /**
     * @var EcrProjectBase
     */
    protected $project;

    /**
     * @var EcrLanguage
     */
    protected $easyLanguage;

    /**
     * Execute and display a template script.
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @throws Exception
     * @return  void
     */
    public function display($tpl = null)
    {
        $input = JFactory::getApplication()->input;

        $task = $input->get('task');

        $this->hideLangs = $input->get('hide_langs', array(), 'array');
        $this->scope = $input->get('scope');

        try
        {
            $this->project = EcrProjectHelper::getProject();

            if('ini' != $this->project->langFormat
                && ! class_exists('g11nExtensionHelper'))
            {
                throw new Exception(
                    sprintf('The g11n library must be available to process %s language files.'
                        , $this->project->langFormat));
            }

            if('ini' != $this->project->langFormat)
            {
                //-- Here goes g11n =;)
                echo $this->displayBarG11n($task);
            }
            else
            {
                if('' == $this->scope)
                {
                    $this->scope = 'site';

                    if($this->project->type == 'plugin')
                    {
                        //-- @todo special treatment for plugins
                        $this->scope = 'admin';
                    }
                }

                if(0 == count($this->project->langs))
                {
                    $this->easyLanguage = false;
                }
                else
                {
                    $this->easyLanguage = new EcrLanguage($this->project, $this->scope, $this->hideLangs);

                    if($input->get('tmpl') != 'component')
                    {
                        //--draw selector
                        echo $this->displayBar($task);
                    }
                }
            }

            /**
             * Handle the 'task' value -> call a function
             * Softly exit on undefined
             */
            if(in_array($task, get_class_methods($this)))
            {
                //--Execute the task
                $this->$task();
            }
            else if($task)
            {
                echo 'UNDEFINED: '.$task.'<br />';
                EcrHtml::formEnd();

                return;
            }
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);

            EcrHtml::formEnd();

            return;
        }//try

        parent::display($tpl);

        EcrHtml::formEnd();
    }//function

    /**
     * Languages View - Default.
     *
     * @return void
     */
    private function languages()
    {
        if('ini' != $this->project->langFormat)
        {
            $this->g11nInfo = $this->getG11nInfo();

            $this->setLayout('g11n');
        }
    }//function

    /**
     * @return \stdClass
     */
    private function getG11nInfo()
    {
        $info = new stdClass;

        $this->languages = Ecrg11nHelper::getLanguages();

        $baseLink = '';

        $info->scope =($this->project->scope) ? $this->project->scope : 'admin';
        $info->id = $this->project->dbId;
        $info->extension = $this->project->comName;

        switch($this->project->type)
        {
            case 'component':
                $comName = $this->project->comName;
            break;

            case 'plugin':
                $comName = 'plg_content_'.$this->project->comName;
            break;

            case 'template':
                $comName = 'tpl_'.$this->project->comName;
            break;

            default:
                echo 'undefined type: '.$this->project->type;
            $comName = $this->project->comName;
            break;
        }//switch

        $info->exists = g11nExtensionHelper::isExtension($comName, $info->scope);

        $info->templateLink =($info->exists)
        ? $baseLink.'&task=g11n.createTemplate&extension='.$this->project->comName
        : '';

        $cachedFiles = Ecrg11nHelper::getCachedFiles();

        $this->scopes = array('admin' => JPATH_ADMINISTRATOR, 'site' => JPATH_SITE);

        $baseLink = 'index.php?option=com_g11n';

        $info->editLink = $baseLink.'&task=g11n.edit';
        $info->templateCommands = array();
        $info->updateLinks = array();
        $info->cacheLinks = array();

        $types = array('');

        $options = new JRegistry($this->project->buildOpts);

        if('ON' == $options->get('lng_separate_javascript'))
            $types[] = '.js';

        $types[] = '.config';

        $extensionName = $this->project->comName;

        if(strpos($extensionName, '.'))
            $extensionName = substr($extensionName, 0, strpos($extensionName, '.'));

        foreach($this->scopes as $scope => $path)
        {
            foreach($types as $type)
            {
                if('.config' == $type && 'admin' != $scope)
                    continue;

                $scopeType = $scope.$type;

                if( ! isset($this->languages[$scopeType]))
                    $this->languages[$scopeType] = $this->languages[$scope];

            try
            {
                $info->templateExists[$scopeType] = g11nStorage::templateExists($comName.$type, $scope);
            }
            catch(Exception $e)
            {
                $info->templateCommands[$scopeType] = $e->getMessage();
                $info->templateLink = '';
            }//try

            try
            {
                $info->templateStatus[$scopeType] = g11nStorage::templateExists($comName.$type, $scope);
            }
            catch(Exception $e)
            {
                $info->templateStatus[$scopeType] = $e->getMessage();
                echo '';
            }//try

            foreach($this->languages[$scopeType] as $lang)
            {
                $exists = g11nExtensionHelper::findLanguageFile($lang['tag']
                , $comName.$type, $scope);

                $info->fileStatus[$scopeType][$lang['tag']] =($exists) ? true : false;
                //                     g11nExtensionHelper::findLanguageFile($lang['tag']
                //                    , $item->extension, $scope);

                $link = $baseLink.'&task=utility.updateLanguage';
                $link .= '&extension='.$info->extension.'&scope='.$scope;
                $link .= '&langTag='.$lang['tag'];

                $info->updateLinks[$scopeType][$lang['tag']] = $link;

                if( ! array_key_exists($extensionName, $cachedFiles)
                || ! array_key_exists($scope, $cachedFiles[$extensionName]))
                {
                    $info->cacheStatus[$scopeType][$lang['tag']] = false;
                    continue;
                }

                $info->cacheStatus[$scopeType][$lang['tag']] = false;

                $fName = $lang['tag'].'.'.$this->project->comName;

                foreach($cachedFiles[$extensionName][$scope] as $file)
                {
                    if(strpos($file, $fName) === 0)
                    {
                        $info->cacheStatus[$scope][$lang['tag']] = true;
                    }
                }//foreach
            }//foreach
        }//foreach
    }

        return $info;
    }//function

    /**
     * Convert language files View.
     *
     * @return void
     */
    private function convert()
    {
        $input = JFactory::getApplication()->input;

        $this->selected_file = $input->getPath('selected_file');

        $options = $input->get('options', array(), 'array');

        $this->options = JArrayHelper::toObject($options, 'JObject');

        $this->converter = new EcrLanguageConverter($this->options, $this->project);

        $files = array();

        foreach($this->project->copies as $copy)
        {
            switch($this->scope)
            {
                case 'admin':
                case 'menu':
                    if(strpos($copy, JPATH_ADMINISTRATOR) === false)
                    {
                        continue;
                    }
                    break;

                case 'site':
                    if(strpos($copy, JPATH_ADMINISTRATOR) === 0)
                    {
                        continue;
                    }
                    break;
                default:
                    break;
            }//switch

            $files = array_merge(JFolder::files($copy, '\.php$', true, true), $files);
        }//foreach

        $fileList = array();
        $badDefinitions = array();

        foreach($files as $fileName)
        {
            $fileList[$fileName] = 0;
            $definitions = $this->easyLanguage->getDefinitions($fileName);

            foreach($definitions as $definition => $file)
            {
                if( ! $this->converter->isCleanKey($definition))
                {
                    $fileList[$file] ++;
                    $badDefinitions[] = $definition;
                }
            }//foreach
        }//foreach

        $this->fileList = $fileList;
        $this->badDefinitions = $badDefinitions;

        $this->menuBoxes = array();

        $html = '';
        $html .= '<div class="ecr_menu_box" style="margin-left: 0.3em;">';
        $html .= jgettext('File');
        $html .= '<select onchange="submitform(\'convert\');" name="selected_file">';
        $html .= '<option value="">'.jgettext('Select').'</option>';

        foreach($this->fileList as $file => $errors)
        {
            if( ! $errors)
            continue;

            $s = str_replace(JPATH_ROOT.DS, '', $file);
            $selected =($s == $this->selected_file) ? 'selected="selected"' : '';
            $html .= '<option '.$selected.' value="'.$s.'">'.$s.' ('.$errors.')</option>';
        }//foreach

        $html .= '</select>';
        $html .= '</div>';

        $this->menuBoxes['file'] = $html;

        $origCode = JFile::read(JPATH_ROOT.DS.$this->selected_file);
        $this->fileErrors = $this->converter->findPHPErrors($origCode, $this->badDefinitions);

        $html = '';
        $this->diff = '';

        if(count($this->fileErrors))
        {
            $html .= '<div style="border: 1px dashed orange; padding: 0.3em; margin-top:'
            .' 1em; margin-bottom: 1em; background-color: #fff;">';
            $html .= '<b style="color: red;">'.jgettext('Errors').'</b>'.BR;
            $html .= jgettext('Please select the errors you wish to correct').BR;

            $newCode = $origCode;

            foreach($this->fileErrors as $errorKey => $errorJText)
            {
                //-- Draw a hidden field
                $s = htmlspecialchars(str_replace('"', '__QQ__', $errorJText));
                $s1 = htmlspecialchars(str_replace('"', '__QQ__', $errorKey));
                $html .= '<input type="hidden" name="file_errors['.$s1.']" value="'.$s.'" />';
                $html .= '<input type="checkbox" checked="checked" id="error_'.$s1.'" name="selected_errors['
                .$s1.']" /><labellabel class="inline" for="error_'.$s1.'">'.$s1.'</label>'.BR;

                $newJText = str_replace($errorKey, $this->converter->cleanKey($errorKey), $errorJText);
                $newCode = str_replace($errorJText, $newJText, $newCode);
            }//foreach

            $html .= '</div>';
            $this->diff = $this->converter->getDiffTable($origCode, $newCode, $this->options->get('php_show_all'));
        }

        $this->menuBoxes['file_errors'] = $html;

        $this->setLayout('convert');
    }//function

    /*
     * Task methods
    */

    /**
     * Translations View.
     *
     * @return void
     */
    private function translations()
    {
        if($this->project->langs)
        {
            $this->prepareTranslation();
            $this->default_file = $this->easyLanguage->getDefaultFile();
        }

        $this->setLayout('translations');
    }//function

    /**
     * Search files View.
     *
     * @return void
     */
    private function searchfiles()
    {
        $this->prepareTranslation();
        $this->setLayout('searchfiles');
    }//function

    /**
     * JALHOO View.
     *
     * @return void
     */
    private function jalhoo()
    {
        $input = JFactory::getApplication()->input;

        $this->lang = $input->get('language', 'en-GB');

        $this->fileFilter = $input->get('filefilter');
        $this->statusFilter = $input->get('statusfilter');
        $this->excludeDirs = $input->getString('excludeDirs');

        $this->langFormatIn = $input->get('langFormatIn');
        $this->langFormatOut = $input->get('langFormatOut');

        jimport('jalhoo.language');

        try//
        {
            $parser = '';//g11n::getParser($this->langFormatOut);

            $this->parser = $parser;

            //echo $parser->__toString();;
        }
        catch(Exception $e)
        {
            $m =(JDEBUG) ? nl2br($e) : $e->getMessage();

            JFactory::getApplication()->enqueueMessage($m, 'error');
        }//try

        $this->checks = new JObject;
        $this->buildOpts = new JObject;

        $buildOpts = $input->get('buildOpts', array(), 'array');

        foreach($buildOpts as $opt => $v)
        {
            $this->buildOpts->$opt = true;
        }//foreach

        $scopes = array('admin', 'site');

        if( ! in_array($this->scope, $scopes))
        $this->scope = '';

        //        if( ! $this->component || ! $this->scope)
        //        {
        //            parent::display();
        //
        //            return;
        //        }

        try
        {
            //            $checker = new LanguageCheckerHelper($this->component, $this->scope
            //            , $this->subScope, $this->lang, $this->fileFilter, $this->excludeDirs
            //            , $this->langFormatIn, $this->langFormatOut, $this->buildOpts);
            //#var_dump($checker);
            //
            //            $this->strings = $checker->getStrings();
            //            $this->stringsPlural = $checker->getStringsPlural();
            //            $this->translations = $checker->getTranslations();
            //            $this->strangeTHINGS = $checker->getStrangeTHINGS();
            //            $this->loadedLanguageFiles = $checker->loadedFiles;
            //
            //            $this->checker = $checker;
            //
            //            $this->comPath = $this->paths[$this->scope].DS.'components'.DS.$this->component;
            //
            //            $fs = JFolder::folders($this->comPath, '.', 1, true);
            //            $js = $this->generateTree($fs);
            //            $document = JFactory::getDocument();
            //            $document->addScriptDeclaration($js);
        }
        catch(Exception $e)
        {
            $m =(JDEBUG) ? nl2br($e) : $e->getMessage();

            JFactory::getApplication()->enqueueMessage($m, 'error');
        }//try

        $this->buildLists();
        $this->setLayout('jalhoo');
    }//function

    /**
     * Build the select lists.
     *
     * @return void
     */
    private function buildLists()
    {
        $componentList = array_unique(array_merge(
        JFolder::folders($this->paths['admin'].'/components')
        , JFolder::folders($this->paths['site'].'/components')));

        sort($componentList);

        $options = array();

        //        if( ! in_array($this->component, $componentList)) $this->component = '';
        //
        //        $options[] = JHtml::_('select.option', '', JxText::_('jlc_Select_an_extension'));
        //
        //        foreach($componentList as $c)
        //        {
        //            $options[] = JHtml::_('select.option', $c, $c);
        //        }
        //
        //        $this->lists['components'] = JHtml::_('select.genericlist', $options, 'component'
        //        , 'onchange="submitform();"', 'value', 'text', $this->component);

        $options = array();

        $options[] = JHtml::_('select.option', 'admin', JText::_('Admin'));
        $options[] = JHtml::_('select.option', 'site', JText::_('Site'));

        $this->lists['scopes'] = JHtml::_('select.genericlist', $options, 'scope'
        , 'onchange="submitform();"', 'value', 'text', $this->scope);

        $options = array();

        $langs = JFactory::getLanguage()->getKnownLanguages();

        foreach($langs as $lang)
        {
            $options[] = JHtml::_('select.option', $lang['tag'], $lang['name']);
        }//foreach

        $this->lists['language'] = JHtml::_('select.genericlist', $options, 'language'
        , 'onchange="submitform();"', 'value', 'text', $this->lang);

        $options = array();

        $options[] = JHtml::_('select.option', '', JText::_('jlc_All'));
        $options[] = JHtml::_('select.option', 'php', '*.PHP');
        $options[] = JHtml::_('select.option', 'xml', '*.XML');
        $options[] = JHtml::_('select.option', 'js', '*.JS');

        $this->lists['filefilter'] = JHtml::_('select.genericlist', $options, 'filefilter'
        , 'onchange="submitform();"', 'value', 'text', $this->fileFilter);

        $options = array();

        $options[] = JHtml::_('select.option', '', JText::_('All'));
        $options[] = JHtml::_('select.option', 'untranslated', JText::_('Untranslated only'));

        $this->lists['statusfilter'] = JHtml::_('select.genericlist', $options, 'statusfilter'
        , 'onchange="submitform();"', 'value', 'text', $this->statusFilter);

        $options = array();

        $options[] = JHtml::_('select.option', 'ini', 'INI');
        $options[] = JHtml::_('select.option', 'nafuini', 'NAFUINI');
        $options[] = JHtml::_('select.option', 'po', 'PO');

        $this->lists['langFormatIn'] = JHtml::_('select.genericlist', $options, 'langFormatIn'
        , 'onchange="submitform();"', 'value', 'text', $this->langFormatIn);

        $options[] = JHtml::_('select.option', 'pot', 'POT');
        $this->lists['langFormatOut'] = JHtml::_('select.genericlist', $options, 'langFormatOut'
        , 'onchange="submitform();"', 'value', 'text', $this->langFormatOut);

        $c = '';
        $checked =($this->buildOpts->get('includeLineNumbers')) ? 'checked="checked"' : '';
        $c .= '<input type="checkbox"'.$checked.' id="chkincludeLineNumbers"'
        .'name="buildOpts[includeLineNumbers]" onchange="submitform();" />';
        $c .= '<label class="inline" for="chkincludeLineNumbers">Include line numbers</label>';

        $this->checks->includeLineNumbers = $c;

        $c = '';
        $checked =($this->buildOpts->get('markFuzzy')) ? 'checked="checked"' : '';
        $c .= '<input type="checkbox"'.$checked.' id="chkMarkFuzzy"'
        .' name="buildOpts[markFuzzy]" onchange="submitform();" />';
        $c .= '<label class="inline" for="chkMarkFuzzy">Mark fuzzy</label>';

        $this->checks->markFuzzy = $c;

        $c = '';
        $checked =($this->buildOpts->get('markKeyDiffers')) ? 'checked="checked"' : '';
        $c .= '<input type="checkbox"'.$checked.' id="chkMarkKeyDiffers"'
        .' name="buildOpts[markKeyDiffers]" onchange="submitform();" />';

        $c .= '<label class="inline" for="chkMarkKeyDiffers">Mark key difference(s)</label>';

        $this->checks->markKeyDiffers = $c;
    }//function

    /**
     * Preparethe translation.
     *
     * @return void
     */
    private function prepareTranslation()
    {
        $this->easyLanguage->_readStrings();

        $this->languages = $this->easyLanguage->getLanguages();
        $this->hideLangs = $this->easyLanguage->getHideLangs();
        $this->definitions = $this->easyLanguage->getDefinitions();
        $this->strings = $this->easyLanguage->getStrings();
    }//function

    /**
     * Correct language file order View.
     *
     * @return void
     */
    private function langcorrectorder()
    {
        $sel_language = JFactory::getApplication()->input->get('sel_language');

        if($sel_language == 'en-GB')
        {
            $sel_language = '';
        }

        $default_language = array();
        $translated_language = array();

        if($sel_language)
        {
            $fileNameDef = $this->easyLanguage->getFileName('en-GB', $this->scope, $this->project);
            $fileName = $this->easyLanguage->getFileName($sel_language, $this->scope, $this->project);

            $default_language = $this->easyLanguage->parseFile($fileNameDef);
            $translated_language = $this->easyLanguage->parseFile($fileName);
            $corrected_language = $this->easyLanguage->correctTranslation($default_language, $translated_language);
        }

        $this->default_language = $default_language;
        $this->translated_language = $translated_language;
        $this->corrected_language = $corrected_language;

        $this->sel_language = $sel_language;

        $this->setLayout('ordertranslation');
    }//function

    /**
     * Default language file order View.
     *
     * @return void
     */
    private function langcorrectdeforder()
    {
        $fileName = $this->easyLanguage->getFileName('en-GB', $this->scope, $this->project);

        $this->default_language = $this->easyLanguage->parseFile($fileName);

        $this->setLayout('orderdefault');
    }//function

    /**
     * Translation View.
     *
     * @return void
     */
    private function translate()
    {
        $this->trans_lang = JFactory::getApplication()->input->get('trans_lang');
        $this->trans_key = JFactory::getApplication()->input->getString('trans_key');

        if($this->trans_lang != 'en-GB')
        {
            $this->trans_default = $this->easyLanguage->getTranslation('en-GB', $this->trans_key);
        }

        $this->translation = $this->easyLanguage->getTranslation($this->trans_lang, $this->trans_key);

        $this->setLayout('translator');
    }//function

    /**
     * Show version View.
     *
     * @return void
     */
    private function show_versions()
    {
        $this->sel_language = JFactory::getApplication()->input->get('sel_language');
        $this->selected_version = JFactory::getApplication()->input->getInt('selected_version', 0);

        if($this->sel_language)
        {
            $this->versions = $this->easyLanguage->getVersions($this->sel_language);
        }

        $this->setLayout('versions');
    }//function

    /**
     * Check languages View.
     *
     * @return void
     */
    private function language_check()
    {
        if($this->easyLanguage)
        {
            $this->languages = $this->easyLanguage->getLanguages();
        }

        $this->setLayout('check');
    }//function

    /**
     * Display the bar View.
     *
     * @param string $task The actual task
     *
     * @todo move
     *
     * @return string
     */
    private function displayBar($task)
    {
        $sel_language = JFactory::getApplication()->input->get('sel_language');
        $this->sel_language = $sel_language;

        $subTasks = array(
            array('title' => jgettext('Setup')
            , 'description' => jgettext('Setup your language files')
            , 'icon' => 'apply'
            , 'task' => 'languages'
            )
            , array('title' => jgettext('Files and menus')
            , 'description' => jgettext('Searches inside the source files and menus for translatable strings')
            , 'icon' => 'ecr_language'
            , 'task' => 'searchfiles'
            )
            , array('title' => jgettext('Translations')
            , 'description' =>
            jgettext('Manage translations in ini files and inspect your php / xml files for JText strings to translate.')
            , 'icon' => 'ecr_language'
            , 'task' => 'translations'
            )
            , array('title' => jgettext('Default file order')
            , 'description' => jgettext('Change the order of your default language file and add comments to create a structure.')
            , 'icon' => 'text'
            , 'task' => 'langcorrectdeforder'
            )
            , array('title' => jgettext('Translation order')
            , 'description' => jgettext('Change the order of your translated language files according to your default file.')
            , 'icon' => 'text'
            , 'task' => 'langcorrectorder'
            )
            , array('title' => jgettext('Versions')
            , 'description' => jgettext('Compare saved versions of your language files.')
            , 'icon' => 'sig'
            , 'task' => 'show_versions'
            )
            , array('title' => jgettext('Convert')
            , 'description' => jgettext('Convert your language files and your code simultaneously.')
            , 'icon' => 'rename'
            , 'task' => 'convert'
            )
        );

        //@todo - unify..
        $html = '';
        $html .= EcrHtmlMenu::sub($subTasks);

        $html .= '<div style="clear: both; height: 1em;"></div>';

        //-- Scope selector
        if($task != 'languages')
        {
            $html .= '<div class="ecr_menu_box" style="margin-left: 0.3em;">';
            $html .= jgettext('Scope').'&nbsp;';
            $html .= '<select name="scope" class="span1" onchange="submitbutton(\''.$task.'\');">';

            foreach($this->project->getLanguagePaths() as $scope => $p)
            {
                $html .= '<option value="'.$scope.'" ';
                $html .=($this->scope == $scope) ? ' selected="selected"': '';
                $html .= '>';
                $html .= $scope.'</option>';
            }

            $html .= '</select>';
            $html .= '</div>';
        }

        switch($task)
        {
            case 'translations':
            case 'searchfiles':
                if(count($this->project->langs) > 2)
                {
                    $html .= '<div class="ecr_menu_box">';
                    $html .= jgettext('Do NOT show').': ';

                    foreach($this->project->langs as $lang => $scopes)
                    {
                        if($lang == 'en-GB')
                        {
                            //-- Always show default language
                            continue;
                        }

                        $checked =(in_array($lang, $this->hideLangs)) ? 'checked="checked"' : '';
                        $color =(in_array($lang, $this->hideLangs)) ? 'red' : 'green';

                        $html .= '<input type="checkbox" name="hide_langs[]"'
                        .' id="hide_langs_'.$lang.'"'
                        .' value="'.$lang.'" '.$checked
                        .' onclick="submitbutton(\''.$task.'\');">';

                        $html .= '<label class="inline" for="hide_langs_'.$lang.'" style="color: '.$color.';">'
                            .$lang
                            .'</label>';
                    }

                    $html .= '</div>';
                }

                break;

            case 'langcorrectorder':
            case 'save_lang_corrected':
                $html .= '<div class="ecr_menu_box">';
                $html .= $this->drawLangSelector($sel_language, 'langcorrectorder');
                $html .= '  </div>';

                if($sel_language)
                {
                    $html .= '<div class="ecr_menu_box">';
                    $html .= jgettext('Cut');
                    $cut_after = JFactory::getApplication()->input->getInt('cut_after', 30);
                    $html .= '<select name="cut_after" onchange="submitbutton(\'langcorrectorder\');">';

                    for($i = 10; $i < 62; $i = $i + 2)
                    {
                        $selected =($cut_after == $i) ? ' selected="selected"' : '';
                        $html .= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
                    }//for

                    $html .= '</select>';
                    $html .= '</div>';
                    $html .= EcrHtmlCheck::versioned();
                    $html .= '&nbsp;&nbsp;<span class="ecr_button img icon16-ecr_save"'
                    .' onclick="submitbutton(\'save_lang_corrected\');" style="display: inline !important;">';
                    $html .= jgettext('Save');
                    $html .= '  </span>';
                }
                break;

            case 'langcorrectdeforder':
            case 'save_deflang_corrected':
                $html .= EcrHtmlCheck::versioned();
                $html .= '<span class="btn img icon16-ecr_save"';
                $html .= 'onclick="submitbutton(\'save_deflang_corrected\');">'.jgettext('Save').'</span>';
                $html .= '<br /><br /><div class="explanation">'
                .jgettext('Drag and drop elements to rearrange. Add new comments.').'</div>';
                break;

            case 'show_versions':
            case 'show_version':
                $html .= '<div class="ecr_menu_box">';
                $html .= $this->drawLangSelector($sel_language, 'show_versions', true);
                $html .= '</div>';
                break;

            case 'translate':
            case 'language_check':
            case 'languages':
            case 'convert':
            case 'jalhoo' :
                //-- Do nothing
                break;

            default:
                echo 'UNDEFINED: '.$task;
            break;
        }//switch

        return $html;
    }//function

    private function g11nUpdate()
    {
        $this->g11nInfo = $this->getG11nInfo();

        $this->setLayout('g11nupdate');
    }//function

    /**
     * Display the bar View.
     *
     * @param string $task The actual task
     *
     * @todo move
     *
     * @return string
     */
    private function displayBarG11n($task)
    {
        $this->sel_language = JFactory::getApplication()->input->get('sel_language');

        $subTasks = array(
        array('title' => jgettext('Status')
        , 'description' => jgettext('Displays the status of your language files including cache.')
        , 'icon' => 'apply'
        , 'task' => 'languages'
        )
            /*
        , array('title' => jgettext('Cache')
        , 'description' => jgettext('Displays the cache status of your language files.')
        , 'icon' => 'ecr_language'
        , 'task' => 'g11nCache'
        )
            */
        , array('title' => jgettext('g11n')
        , 'description' =>
        jgettext('Utility to create and update your language files.')
        , 'icon' => 'ecr_language'
        , 'task' => 'g11nUpdate'
        )
        );

        return EcrHtmlMenu::sub($subTasks);
    }//function

    /**
     * Draw a language selector.
     *
     * @param string $selected Selected value
     * @param string $task The actual task
     * @param boolean $showDefault Show the default value
     *
     * @return string
     */
    private function drawLangSelector($selected, $task, $showDefault = false)
    {
        $html = '';

        if(count($this->project->langs) > 1
        || $showDefault)
        {
            $html .= NL.jgettext('Language').':&nbsp;';
            $html .= '<select name="sel_language" onchange="submitbutton(\''.$task.'\');">';
            $html .= '<option value="">'.jgettext('Choose').'</option>';

            foreach($this->project->langs as $lang => $scopes)
            {
                if($lang == 'en-GB' && ! $showDefault)
                {
                    //-- Default language ordered separated
                    continue;
                }

                $sSelected =($lang == $selected) ? ' selected="selected"' : '';
                $html .= '<option value="'.$lang.'" '.$sSelected.'>'.$lang.'</option>';
            }//foreach

            $html .= '</select>';
        }

        return $html;
    }//function

    /**
     * Draw a menu box.
     *
     * @param string $task The actual task
     * @param string $title Title to display
     *
     * @return string
     */
    protected function menuBox($task, $title)
    {
        $checked =($this->options->get($task)) ? ' checked="checked"' : '';

        $html = '';
        $html .= '<div class="ecr_menu_box" style="background-color: #ccc; margin-left: 0.3em;">';
        $html .= '<input type="checkbox" id="'.$task.'" name="options['.$task.']"'
        .' onclick="submitform(\'convert\');" '.$checked.'" />';
        $html .= '<label class="inline" for="'.$task.'">'.$title.'</label>';
        $html .= '</div>';

        return $html;
    }//function
}//class
