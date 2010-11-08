<?php
/**
 * @version SVN: $Id$
 * @package    LanguageChecker
 * @subpackage Views
 * @author     Nikolai Plath {@link http://easy-joomla.org}
 * @author     Created on 12-Sep-10
 * @license    GNU/GPL
 */



// @codingStandardsIgnoreStart


//-- No direct access
defined('_JEXEC') || die('=;)');

jimport('joomla.application.component.view');

define('BR', '<br />');

class LanguageCheckerViewLanguageChecker extends JView
{
    protected $componentList = array();

    protected $lists = array();
    protected $checks = null;
    protected $buildOpts = null;

    protected $strings = array();
    protected $translations = array();
    protected $strangeTHINGS = array();
    protected $loadedLanguageFiles = array();

    protected $comPath = '';

    protected $fileFilter = '';
    public $statusFilter = '';

    protected $excludeDirs = '';

    protected $langFormatIn = '';
    protected $langFormatOut = '';
    protected $includeLineNumbers = false;
    protected $includeCoreLanguage = false;

    private $paths = array(
      'admin' => JPATH_ADMINISTRATOR
    , 'site' => JPATH_SITE);

    private $component = '';
    private $scope = '';
    private $subScope = '';
    private $lang = '';

    /**
     * Display the view.
     *
     * @see libraries/joomla/application/component/JView::display()
     */
    function display($tpl = null)
    {
        $this->component = JRequest::getCmd('component');
        $this->scope = JRequest::getCmd('scope', 'admin');
        $this->subScope = JRequest::getCmd('subScope');
        $this->lang = JRequest::getCmd('language', 'en-GB');

        $this->fileFilter = JRequest::getCmd('filefilter');
        $this->statusFilter = JRequest::getCmd('statusfilter');
        $this->excludeDirs = JRequest::getVar('excludeDirs');

        $this->langFormatIn = JRequest::getCmd('langFormatIn');
        $this->langFormatOut = JRequest::getCmd('langFormatOut', 'pot');

        jimport('jalhoo.language');

        try//
        {
            $parser = JALHOO::getParser('language', $this->langFormatOut);

            $this->parser = $parser;

            #  echo $parser->__toString();;

        }
        catch (Exception $e)
        {
            $m =(JDEBUG) ? nl2br($e) : $e->getMessage();

            JError::raiseWarning(0, $m);
        }//try

        $this->checks = new JObject();
        $this->buildOpts = new JObject();

        $buildOpts = JRequest::getVar('buildOpts', array());

        foreach ($buildOpts as $opt => $v)
        {
            $this->buildOpts->$opt = true;
        }//foreach

        $scopes = array('admin', 'site');

        if( ! in_array($this->scope, $scopes)) $this->scope = '';

        $this->buildToolbar();
        $this->buildLists();

        if( ! $this->component || ! $this->scope)
        {
            parent::display();

            return;
        }

        try
        {
            $checker = new LanguageCheckerHelper($this->component, $this->scope
            , $this->subScope, $this->lang, $this->fileFilter, $this->excludeDirs
            , $this->langFormatIn, $this->langFormatOut, $this->buildOpts);
#var_dump($checker);

            $this->strings = $checker->getStrings();
            $this->stringsPlural = $checker->getStringsPlural();
            $this->translations = $checker->getTranslations();
            $this->strangeTHINGS = $checker->getStrangeTHINGS();
            $this->loadedLanguageFiles = $checker->loadedFiles;

            $this->checker = $checker;

            $this->comPath = $this->paths[$this->scope].DS.'components'.DS.$this->component;

            $fs = JFolder::folders($this->comPath, '.', 1, true);
            $js = $this->generateTree($fs);
            $document = JFactory::getDocument();
            $document->addScriptDeclaration($js);
        }
        catch(Exception $e)
        {
            $m =(JDEBUG) ? nl2br($e) : $e->getMessage();

            JError::raiseWarning(0, $m);
        }//try

        parent::display($tpl);
    }//function

    private function generateTree($dirs) {
        static $tree = array();

        if( ! $tree)
        {
            //-- Init
            $tree[] = "window.addEvent('domready',function(){";
            $tree[] = 'var json=[';
            $tree[] = '{';
            $tree[] = '   "property": {';
            $tree[] = '      "name": "root",
						"hasCheckbox": false';
            $tree[] = '   },';
            $tree[] = '   "children": [';

        }

        $propstarted = false;

        foreach ($dirs as $dir)
        {
            $p = '';

            if( ! $propstarted)
            {
                $propstarted = true;
            }
            else
            {
                $p = ',';
            }

            $d = str_replace($this->comPath.DS, '', $dir);
            $tree[] = $p.'{';
            $tree[] = ' "property": {
						"name": "'.$d.'",
						"path" : "'.str_replace(JPATH_ROOT.DS, '', $dir).'"
					}';
            $tree[] = '}';
        }//foreach
        //$p = '';
        //$tree[] = $p.'"property": {
        //						"name": "node1"
        //					}';
        $tree[] = "
         ]//children
		}
	];
	// load tree from json.
	excludeDirTree.load({
		json: json
	});

	});//domready function";

        return implode("\n", $tree);
    }//function

    /**
     * Build the toolbar.
     */
    private function buildToolbar()
    {
        JToolBarHelper::title('JLanguageChecker', 'locale');
    }//function

    /**
     * Build the select lists.
     */
    private function buildLists()
    {
        $componentList = array_unique(array_merge(
        JFolder::folders($this->paths['admin'].'/components')
        , JFolder::folders($this->paths['site'].'/components')));

        sort($componentList);

        $options = array();

        if( ! in_array($this->component, $componentList)) $this->component = '';

        $options[] = JHtml::_('select.option', '', JText::_('jlc_Select_an_extension'));

        foreach ($componentList as $c)
        {
            $options[] = JHtml::_('select.option', $c, $c);
        }

        $this->lists['components'] = JHtml::_('select.genericlist', $options, 'component'
        , 'onchange="submitform();"', 'value', 'text', $this->component);

        $options = array();

        $options[] = JHtml::_('select.option', 'admin', JText::_('jlc_Admin'));
        $options[] = JHtml::_('select.option', 'site', JText::_('jlc_Site'));

        $this->lists['scopes'] = JHtml::_('select.genericlist', $options, 'scope'
        , 'onchange="submitform();"', 'value', 'text', $this->scope);

        $options = array();

        $langs = JFactory::getLanguage()->getKnownLanguages();

        foreach ($langs as $lang)
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

        $options[] = JHtml::_('select.option', '', JText::_('jlc_All'));
        $options[] = JHtml::_('select.option', 'untranslated', JText::_('jlc_Untranslated_only'));

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
        $c .= '<input type="checkbox"'.$checked.' id="chkincludeLineNumbers" name="buildOpts[includeLineNumbers]" onchange="submitform();" />';
        $c .= '<label for="chkincludeLineNumbers">Include line numbers</label>';

        $this->checks->includeLineNumbers = $c;

        $c = '';
        $checked =($this->buildOpts->get('includeCoreLanguage')) ? 'checked="checked"' : '';
        $c .= '<input type="checkbox"'.$checked.' id="chkIncludeCoreLanguage" name="buildOpts[includeCoreLanguage]" onchange="submitform();" />';
        $c .= '<label for="chkIncludeCoreLanguage">Include Joomla! core language</label>';

        $this->checks->includeCoreLanguage = $c;

        $c = '';
        $checked =($this->buildOpts->get('markFuzzy')) ? 'checked="checked"' : '';
        $c .= '<input type="checkbox"'.$checked.' id="chkMarkFuzzy" name="buildOpts[markFuzzy]" onchange="submitform();" />';
        $c .= '<label for="chkMarkFuzzy">Mark fuzzy</label>';

        $this->checks->markFuzzy = $c;

        $c = '';
        $checked =($this->buildOpts->get('markKeyDiffers')) ? 'checked="checked"' : '';
        $c .= '<input type="checkbox"'.$checked.' id="chkMarkKeyDiffers" name="buildOpts[markKeyDiffers]" onchange="submitform();" />';
        $c .= '<label for="chkMarkKeyDiffers">Mark key difference(s)</label>';

        $this->checks->markKeyDiffers = $c;

        $options = array();

        $options[] = JHtml::_('select.option', '', 'Standard');
        $options[] = JHtml::_('select.option', 'js', 'JavaScript');

        $this->lists['subScope'] = JHtml::_('select.genericlist', $options, 'subScope'
        , 'onchange="submitform();"', 'value', 'text', $this->subScope);

    }//function

}//class
