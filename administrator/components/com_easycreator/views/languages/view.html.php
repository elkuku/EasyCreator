<?php
/**
 * @version $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     EasyJoomla {@link http://www.easy-joomla.org Easy-Joomla.org}
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

jimport('joomla.application.component.view');

/**
 * HTML View class for the EasyCreator Component.
 *
 * @package EasyCreator
 * @subpackage Views
 */
class EasyCreatorViewLanguages extends JView
{
    protected $versions = array();

    private $paths = array(
      'admin' => JPATH_ADMINISTRATOR
    , 'site' => JPATH_SITE);

    /**
     * Standard display method.
     *
     * @param string $tpl The name of the template file to parse;
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $task = JRequest::getCmd('task');

        $this->hideLangs = JRequest::getVar('hide_langs', array());
        $this->scope = JRequest::getCmd('scope');
        $this->_showCore = JRequest::getCmd('showCore');

        try
        {
            $this->project = EasyProjectHelper::getProject();

            if( ! $this->scope)
            {
                $this->scope = 'site';

                if($this->project->type == 'plugin')
                {
                    //-- todo special treatment for plugins
                    $this->scope = 'admin';
                }
            }

            //--Draw h1 header
            ecrHTML::header(jgettext('Languages'), $this->project);

            if( ! count($this->project->langs))
            {
                $this->easyLanguage = false;
            }
            else
            {
                ecrLoadHelper('language');
                $this->easyLanguage = new EasyELanguage($this->project, $this->scope, $this->hideLangs, $this->_showCore);

                if(JRequest::getCmd('tmpl') != 'component')
                {
                    //--draw selector
                    echo $this->displayBar($task);
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
                ecrHTML::easyFormEnd();

                return;
            }
        }
        catch(Exception $e)
        {
            $m =(ECR_DEBUG) ? nl2br($e) : $e->getMessage;
            ecrHTML::displayMessage($m, 'error');

            ecrHTML::easyFormEnd();

            return;
        }//try

        parent::display($tpl);

        ecrHTML::easyFormEnd();
    }//function

    /**
     * Convert language files View.
     *
     * @return void
     */
    private function convert()
    {
        $this->selected_file = JRequest::getVar('selected_file');

        $this->options = JArrayHelper::toObject(JRequest::getVar('options', array()), 'JObject');

        ecrLoadHelper('languageconverter');

        $this->converter = new ECRLanguageConverter($this->options, $this->project);

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

        $this->assignRef('fileList', $fileList);
        $this->assignRef('badDefinitions', $badDefinitions);

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
                .$s1.']" /><label for="error_'.$s1.'">'.$s1.'</label>'.BR;

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
     * Languages View.
     *
     * @return void
     */
    private function languages()
    {
    }//function

    /**
     * Stuffer View.
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
     * Stuffer View.
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
        $this->lang = JRequest::getCmd('language', 'en-GB');

        $this->fileFilter = JRequest::getCmd('filefilter');
        $this->statusFilter = JRequest::getCmd('statusfilter');
        $this->excludeDirs = JRequest::getVar('excludeDirs');

        $this->langFormatIn = JRequest::getCmd('langFormatIn');
        $this->langFormatOut = JRequest::getCmd('langFormatOut');

        jimport('jalhoo.language');

        try//
        {
            $parser = JALHOO::getParser($this->langFormatOut);

            $this->parser = $parser;

            //echo $parser->__toString();;
        }
        catch(Exception $e)
        {
            $m =(JDEBUG) ? nl2br($e) : $e->getMessage();

            JError::raiseWarning(0, $m);
        }//try

        $this->checks = new JObject();
        $this->buildOpts = new JObject();

        $buildOpts = JRequest::getVar('buildOpts', array());

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

            JError::raiseWarning(0, $m);
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
        $c .= '<label for="chkincludeLineNumbers">Include line numbers</label>';

        $this->checks->includeLineNumbers = $c;

        $c = '';
        $checked =($this->buildOpts->get('includeCoreLanguage')) ? 'checked="checked"' : '';
        $c .= '<input type="checkbox"'.$checked.' id="chkIncludeCoreLanguage"'
        .' name="buildOpts[includeCoreLanguage]" onchange="submitform();" />';
        $c .= '<label for="chkIncludeCoreLanguage">Include Joomla! core language</label>';

        $this->checks->includeCoreLanguage = $c;

        $c = '';
        $checked =($this->buildOpts->get('markFuzzy')) ? 'checked="checked"' : '';
        $c .= '<input type="checkbox"'.$checked.' id="chkMarkFuzzy" name="buildOpts[markFuzzy]" onchange="submitform();" />';
        $c .= '<label for="chkMarkFuzzy">Mark fuzzy</label>';

        $this->checks->markFuzzy = $c;

        $c = '';
        $checked =($this->buildOpts->get('markKeyDiffers')) ? 'checked="checked"' : '';
        $c .= '<input type="checkbox"'.$checked.' id="chkMarkKeyDiffers"'
        .' name="buildOpts[markKeyDiffers]" onchange="submitform();" />';

        $c .= '<label for="chkMarkKeyDiffers">Mark key difference(s)</label>';

        $this->checks->markKeyDiffers = $c;

        //        $options = array();
        //
        //        $options[] = JHtml::_('select.option', '', 'Standard');
        //        $options[] = JHtml::_('select.option', 'js', 'JavaScript');
        //
        //        $this->lists['subScope'] = JHtml::_('select.genericlist', $options, 'subScope'
        //        , 'onchange="submitform();"', 'value', 'text', $this->subScope);
    }//function

    /**
     * Preparethe translation.
     *
     * @return void
     */
    private function prepareTranslation()
    {
        $workPath = $this->project->getLanguagePaths($this->scope);

        $this->showCore = JRequest::getCmd('showCore');

        $this->easyLanguage->_readStrings();

        if($this->showCore)
        {
            $this->easyLanguage->_readStrings(true);
        }

        $this->languages = $this->easyLanguage->getLanguages();
        $this->hideLangs = $this->easyLanguage->getHideLangs();
        $this->definitions = $this->easyLanguage->getDefinitions();
        $this->strings = $this->easyLanguage->getStrings();
        $this->coreStrings = $this->easyLanguage->getCoreStrings();
    }//function

    /**
     * Correct language file order View.
     *
     * @return void
     */
    private function langcorrectorder()
    {
        $sel_language = JRequest::getCmd('sel_language');

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

        $this->assignRef('default_language', $default_language);
        $this->assignRef('translated_language', $translated_language);
        $this->assignRef('corrected_language', $corrected_language);

        $this->assignRef('sel_language', $sel_language);

        $this->setLayout('ordertranslation');
    }//function

    /**
     * Default language file order View.
     *
     * @return void
     */
    private function langcorrectdeforder()
    {
        $fileName = JPATH_ROOT;

        $fileName = $this->easyLanguage->getFileName('en-GB', $this->scope, $this->project);

        $default_language = $this->easyLanguage->parseFile($fileName);

        $this->assignRef('default_language', $default_language);

        $this->setLayout('orderdefault');
    }//function

    /**
     * Translation View.
     *
     * @return void
     */
    private function translate()
    {
        $this->trans_lang = JRequest::getCmd('trans_lang');
        $this->trans_key = JRequest::getString('trans_key');

        if($this->trans_lang != 'en-GB')
        {
            $this->trans_default = $this->easyLanguage->getTranslation('en-GB', $this->trans_key);
        }

        $this->translation = $this->easyLanguage->getTranslation($this->trans_lang, $this->trans_key);

        $this->setLayout('translator');
    }//function

    /**
     * Show versions View.
     *
     * @return void
     */
    private function show_versions()
    {
        $this->show_version();
    }//function

    /**
     * Show version View.
     *
     * @return void
     */
    private function show_version()
    {
        $this->sel_language = JRequest::getCmd('sel_language', '');
        $this->selected_version = JRequest::getInt('selected_version', 0);

        if($this->sel_language)
        {
            $this->versions = $this->easyLanguage->getVersions($this->sel_language);
            //            $this->assignRef('versions', $versions);
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
        $task = JRequest::getCmd('task');
        $sel_language = JRequest::getCmd('sel_language');
        $this->sel_language = $sel_language;

        $imgBase = JURI::root().'administrator/components/com_easycreator/assets/images/ico/';

        //--Get component parameters
        $ecr_help = JComponentHelper::getParams('com_easycreator')->get('ecr_help');

        $subTasks = array(
        array('title' => jgettext('Setup')
        , 'description' => jgettext('Setup your language files')
        , 'icon' => 'apply'
        , 'tasks' => array('languages')
        )
        , array('title'=> jgettext('Files and menus')
        , 'description' => jgettext('Searches inside the source files and menus for translatable strings')
        , 'icon' => 'ecr_language'
        , 'tasks' => array('searchfiles')
        )
        , array('title'=> jgettext('Translations')
        , 'description' =>
        jgettext('Manage translations in ini files and inspect your php / xml files for JText strings to translate.')
        , 'icon' => 'ecr_language'
        , 'tasks' => array('translations', 'dotranslate')
        )
        , array('title' => jgettext('Default file order')
        , 'description' => jgettext('Change the order of your default language file and add comments to create a structure.')
        , 'icon' => 'text'
        , 'tasks' => array('langcorrectdeforder', 'save_deflang_corrected')
        )
        , array('title' => jgettext('Translation order')
        , 'description' => jgettext('Change the order of your translated language files according to your default file.')
        , 'icon' => 'text'
        , 'tasks' => array('langcorrectorder', 'save_lang_corrected')
        )
        , array('title' => jgettext('Versions')
        , 'description' => jgettext('Compare saved versions of your language files.')
        , 'icon' =>'sig'
        , 'tasks' => array('show_versions', 'show_version')
        )
        , array('title' => jgettext('Convert')
        , 'description' => jgettext('Convert your language files and your code simultaneously.')
        , 'icon' =>'rename'
        , 'tasks' => array('convert', 'convert')
        )
        , array('title' => 'JALHOO'
        , 'description' => jgettext('JALHOO is an experimental language handler.')
        , 'icon' =>'ecr_language'
        , 'tasks' => array('jalhoo')
        )
        );

        $html = '';
        $htmlDescriptionDivs = '';
        $jsVars = '';
        $jsMorphs = '';
        $jsEvents = '';

        foreach($subTasks as $sTask)
        {
            $selected =(in_array($task, $sTask['tasks'])) ? '_selected' : '';
            $html .= '<span id="btn_'.$sTask['tasks'][0].'" style="margin-left: 0.3em;" class="ecr_button'
            .$selected.' img icon-16-'.$sTask['icon'].'" onclick="submitbutton(\''.$sTask['tasks'][0].'\');">';

            $html .= $sTask['title'].'</span>';

            if($ecr_help == 'all'
            || $ecr_help == 'some')
            {
                $htmlDescriptionDivs .= '<div class="hidden_div ecr_description"'
                .' id="desc_'.$sTask['tasks'][0].'">'.$sTask['description'].'</div>';

                $jsVars .= "var desc_".$sTask['tasks'][0]." = $('desc_".$sTask['tasks'][0]."');\n";

                $jsEvents .= "$('btn_".$sTask['tasks'][0]."').addEvents({\n"
                . "'mouseenter': showTaskDesc.bind(desc_".$sTask['tasks'][0]."),\n"
                . "'mouseleave': hideTaskDesc.bind(desc_".$sTask['tasks'][0].")\n"
                . "});\n";
            }
        }//foreach

        $html .= '<div style="clear: both; height: 1em;"></div>';

        //-- Scope selector
        if($this->project->type == 'component'
        && $task != 'languages')
        {
            $html .= '<div class="ecr_menu_box" style="margin-left: 0.3em;">';
            $html .= jgettext('Scope').'&nbsp;';
            $html .= '<select name="scope" onchange="submitbutton(\''.$task.'\');">';

            foreach($this->project->getLanguagePaths() as $scope => $p)
            {
                $html .= '<option value="'.$scope.'" ';
                $html .=($this->scope == $scope) ? ' selected="selected"': '';
                $html .= '>';
                $html .= $scope.'</option>';
            }//foreach

            //            $html .= '<option value="site" ';
            //            $html .=($this->scope == 'site') ? ' selected="selected"' : '';
            //            $html .= '>';
            //            $html .= 'Site</option>';
            //
            //            $html .= '<option value="menu" ';
            //            $html .=($this->scope == 'menu') ? ' selected="selected"' : '';
            //            $html .= '>';
            //            $html .= jgettext('Menu').'</option>';

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
                            //--always show default language
                            continue;
                        }

                        $checked =(in_array($lang, $this->hideLangs)) ? 'checked="checked"' : '';
                        $color =(in_array($lang, $this->hideLangs)) ? 'red' : 'green';

                        $html .= '<input type="checkbox" name="hide_langs[]" id="hide_langs_'.$lang
                        .'" value="'.$lang.'" '.$checked.' onclick="submitbutton(\''.$task.'\');">';

                        $html .= '<label for="hide_langs_'.$lang.'" style="color: '.$color.';">'.$lang.'</label>';
                    }//foreach
                    $html .= '</div>';
                }

                if($task == 'searchfiles')
                {
                    $html .= '<div class="ecr_menu_box">';

                    if($this->_showCore)
                    {
                        $checked = ' checked="checked"';
                        $style = ' style="color: red;"';
                    }
                    else
                    {
                        $checked = '';
                        $style = ' style="color: blue;"';
                    }

                    $html .= '<input type="checkbox" name="showCore" id="showCore"'
                    .' value="show_core" onclick="submitbutton(\'searchfiles\');" '.$checked.'>';

                    $html .= '<label for="showCore" '.$style.'>'.jgettext('Load core language').'</label>';
                    $html .= '<img src="'.$imgBase.'icon-16-info.png" class="hasEasyTip" title="'
                    .jgettext('Load core language').'::'
                    .jgettext('Also load the core language file to check for translations (displayed in orange)').'" />';

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
                    $cut_after = JRequest::getInt('cut_after', 30);
                    $html .= '<select name="cut_after" onchange="submitbutton(\'langcorrectorder\');">';

                    for($i = 10; $i < 62; $i = $i + 2)
                    {
                        $selected =($cut_after == $i) ? ' selected="selected"' : '';
                        $html .= '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
                    }//for

                    $html .= '</select>';
                    $html .= '</div>';
                    $html .= ecrHTML::chkVersioned();
                    $html .= '&nbsp;&nbsp;<span class="ecr_button img icon-16-save"'
                    .' onclick="submitbutton(\'save_lang_corrected\');" style="display: inline !important;">';
                    $html .= jgettext('Save');
                    $html .= '  </span>';
                }
                break;

            case 'langcorrectdeforder':
            case 'save_deflang_corrected':
                $html .= ecrHTML::chkVersioned();
                $html .= '<span class="ecr_button img icon-16-save"';
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
                //            case 'convert':
                //                $html .= '<div class="ecr_menu_box">';
                //                $html .= $this->drawLangSelector($sel_language, 'convert', true);
                //                $html .= '</div>';
                //                break;

            case 'translate':
            case 'language_check':
            case 'languages':
            case 'convert':
            case 'jalhoo' :
                //--Do nothing
                break;

            default:
                echo 'UNDEFINED: '.$task;
                break;
        }//switch

        $html .= $htmlDescriptionDivs;

        if($ecr_help == 'all'
        || $ecr_help == 'some')
        {
            $html .= "<script type='text/javascript'>"
            ."window.addEvent('domready', function() {\n"
            ."function showTaskDesc(name) {\n"
            ."this.setStyle('display', 'block');\n"
            ."}\n"
            ."function hideTaskDesc(name) {\n"
            ."  this.setStyle('display', 'none');\n"
            ."}\n"
            . $jsVars
            . $jsEvents
            . "});\n"
            . "</script>";
        }

        return $html;
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

            foreach($this->project->langs as $lang=>$scopes)
            {
                if($lang == 'en-GB' && ! $showDefault)
                {
                    //--default language ordered separated
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
        $html .= '<label for="'.$task.'">'.$title.'</label>';
        $html .= '</div>';

        return $html;
    }//function
}//class
