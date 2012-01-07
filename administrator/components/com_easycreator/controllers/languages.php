<?php
/**
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     Nikolai Plath {@link http://www.nik-it.de}
 * @author     Created on 24-Sep-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

jimport('joomla.application.component.controller');

/**
 * EasyCreator Controller.
 *
 * @package    EasyCreator
 * @subpackage Controllers
 */
class EasyCreatorControllerLanguages extends JController
{
    /**
     * Standard display method.
     *
     * @param boolean $cachable If true, the view output will be cached
     * @param array $urlparams An array of safe url parameters and their variable types,
     * for valid values see {@link JFilterInput::clean()}.
     *
     * @return void
     * @see JController::display()
     */
    public function display($cachable = false, $urlparams = false)
    {
        JRequest::setVar('view', 'languages');

        parent::display($cachable, $urlparams);
    }//function

    /**
     * Save the corrected language file.
     *
     * @return void
     */
    public function save_lang_corrected()
    {
        //--Get the project
        try
        {
            $project = EasyProjectHelper::getProject();
        }
        catch(Exception $e)
        {
            ecrHTML::displayMessage($e);

            parent::display();

            return;
        }//try

        //--Read vars from request
        $scope = JRequest::getVar('scope', 'admin');

        if( ! $scope)
        {
            $scope = 'admin';
        }

        $hideLangs = JRequest::getVar('hide_langs', array());

        if(count($project->langs))
        {
            ecrLoadHelper('language');
            $easyLanguage = new EasyELanguage($project, $scope, $hideLangs);
            $sel_language = JRequest::getVar('sel_language', '');
            $langfile = JRequest::getVar('langfile', array(), 'post', 'array', JREQUEST_ALLOWRAW);
            $easyLanguage->saveFile($sel_language, $langfile);
        }

        JRequest::setVar('task', 'languages');
        JRequest::setVar('view', 'languages');

        parent::display();
    }//function

    /**
     * Save the corrected default language.
     *
     * @return void
     */
    public function save_deflang_corrected()
    {
        //--Get the project
        try
        {
            $project = EasyProjectHelper::getProject();
        }
        catch(Exception $e)
        {
            ecrHTML::displayMessage($e);

            parent::display();

            return;
        }//try

        //--read vars from request
        $scope = JRequest::getVar('scope', 'admin');

        if( ! $scope)
        {
            $scope = 'admin';
        }

        $hideLangs = JRequest::getVar('hide_langs', array());

        if( ! count($project->langs))
        {
            $easyLanguage = false;
        }
        else
        {
            ecrLoadHelper('language');
            $easyLanguage = new EasyELanguage($project, $scope, $hideLangs);
            $langfile = JRequest::getVar('langfile', array(), 'post', 'array', JREQUEST_ALLOWRAW);
            $easyLanguage->saveFile('en-GB', $langfile);
        }

        JRequest::setVar('task', 'languages');
        JRequest::setVar('view', 'languages');

        parent::display();
    }//function

    /**
     * Create a language file.
     *
     * @return void
     */
    public function create_langfile()
    {
        ecrLoadHelper('language');

        $ecr_project = JRequest::getCmd('ecr_project');
        $oldTask = JRequest::getCmd('old_task', 'languages');
        $type = '';

        try
        {
            EasyELanguage::createFileFromRequest();

            $msg = jgettext('The file has been created');
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            $type = 'error';
        }//try

        $this->setRedirect('index.php?option=com_easycreator&controller=languages'
        .'&task='.$oldTask.'&ecr_project='.$ecr_project, $msg, $type);

        parent::display();
    }//function

    /**
     * Remove a BOM from a language file.
     *
     * @return void
     */
    public function remove_bom()
    {
        ecrLoadHelper('language');

        $fileName = JRequest::getVar('file');

        if( ! $fileName)
        {
            ecrHTML::displayMessage('No filename set', 'error');
        }
        else
        {
            if(EasyELanguage::removeBOM_utf8($fileName))
            {
                ecrHTML::displayMessage(jgettext('The BOM has been removed'));
            }
            else
            {
                ecrHTML::displayMessage(jgettext('Unable to remove the BOM'), 'error');
            }
        }

        JRequest::setVar('view', 'languages');
        JRequest::setVar('task', 'languages');

        parent::display();
    }//function

    /**
     * Convert a language file.
     *
     * @return void
     */
    public function do_convert()
    {
        ecrLoadHelper('languageconverter');
        ecrLoadHelper('file');

        JRequest::setVar('task', 'convert');

        $options = JArrayHelper::toObject(JRequest::getVar('options', array()), 'JObject');

        //--Get the project
        try
        {
            $project = EasyProjectHelper::getProject();
        }
        catch(Exception $e)
        {
            ecrHTML::displayMessage($e);

            parent::display();

            return;
        }//try

        $languages = JFactory::getLanguage()->getKnownLanguages();

        $converter = new ECRLanguageConverter($options, $project);

        $selLanguage = JRequest::getCmd('sel_language');
        $selectedFile = JRequest::getVar('selected_file');
        $fileErrors = JRequest::getVar('file_errors', array());
        $scope = JRequest::getCmd('scope');

        $selectedErrors = JRequest::getVar('selected_errors', array());

        /*
         * Clean language files
        */
        $paths = (array)$project->getLanguagePaths();

        foreach($languages as $tag => $language)
        {
            foreach($paths[$scope] as $path)
            {
                $fileName = $path.DS.'language'.DS.$tag.DS.$tag.'.'.$project->getLanguageFileName($scope);

                if( ! JFile::exists($fileName))
                continue;

                $fileContents = JFile::read($fileName);

                $lines = explode("\n", $fileContents);

                $newLines = $converter->cleanLines($lines);
                $newLines = $converter->cleanLangFileErrors($newLines, array_keys($selectedErrors));

                $newFileContents = implode("\n", $newLines);

                EasyFile::saveVersion($fileName);
                JFile::write($fileName, $newFileContents);
            }//foreach
        }//foreach

        if($selectedFile && count($selectedErrors))
        {
            /*
             * Clean PHP file
            */
            $origCode = JFile::read(JPATH_ROOT.DS.$selectedFile);

            $errors = $converter->findPHPErrors($origCode, array_keys($selectedErrors));

            $newCode = $origCode;

            foreach($errors as $errorKey => $errorJText)
            {
                $newJText = str_replace($errorKey, $converter->cleanKey($errorKey), $errorJText);
                $newCode = str_replace($errorJText, $newJText, $newCode);
            }//foreach

            if($newCode != $origCode)
            {
                EasyFile::saveVersion(JPATH_ROOT.DS.$selectedFile);
                JFile::write(JPATH_ROOT.DS.$selectedFile, $newCode);
            }
        }

        parent::display();
    }//function

    public function g11nUpdateLanguage()
    {
        try
        {
            ecrLoadHelper('g11n');

            $project = EasyProjectHelper::getProject();

            switch($project->type)
            {
                case 'template':
                    $comName = 'tpl_'.$project->comName;
                    break;

                default:
                    $comName = $project->comName;
                break;
            }//switch

            $scope = JRequest::getCmd('scope');
            $lang = JRequest::getCmd('langTag');

            $msg = G11nHelper::updateLanguage($comName, $scope, $lang);

            JFactory::getApplication()->enqueueMessage($msg);
        }
        catch(Exception $e)
        {
            JError::raiseWarning(0, $e->getMessage());
        }//try

        JRequest::setVar('task', 'g11nUpdate');

        parent::display();
    }//function

    public function g11nCreateTemplate()
    {
        try
        {
            ecrLoadHelper('g11n');

            $project = EasyProjectHelper::getProject();

            $scope = JRequest::getCmd('scope');

            switch($project->type)
            {
                case 'template':
                    $comName = 'tpl_'.$project->comName;
                    break;

                default:
                    $comName = $project->comName;
                break;
            }//switch

            $msg = G11nHelper::createTemplate($comName, $scope);

            JFactory::getApplication()->enqueueMessage($msg);
        }
        catch(Exception $e)
        {
            JError::raiseWarning(0, $e->getMessage());
        }//try

        JRequest::setVar('task', 'g11nUpdate');

        parent::display();
    }//function
}//class
