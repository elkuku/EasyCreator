<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     Nikolai Plath
 * @author     Created on 24-Sep-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EasyCreator Controller.
 *
 * @package    EasyCreator
 * @subpackage Controllers
 */
class EasyCreatorControllerLanguages extends JControllerLegacy
{
    /**
     * Standard display method.
     *
     * @param bool       $cachable  If true, the view output will be cached
     * @param array|bool $urlparams An array of safe url parameters and their variable types,
     *                              for valid values see {@link JFilterInput::clean()}.
     * @return \JController|void
     */
    public function display($cachable = false, $urlparams = false)
    {
        JFactory::getApplication()->input->set('view', 'languages');

        parent::display($cachable, $urlparams);
    }//function

    /**
     * Save the corrected language file.
     *
     * @return void
     */
    public function save_lang_corrected()
    {
        $input = JFactory::getApplication()->input;

        try
        {
            //-- Get the project
            $project = EcrProjectHelper::getProject();
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);

            parent::display();

            return;
        }//try

        $scope = $input->get('scope', 'admin');

        if( ! $scope)
        {
            $scope = 'admin';
        }

        $hideLangs = $input->get('hide_langs', array());

        if(count($project->langs))
        {
            $easyLanguage = new EcrLanguage($project, $scope, $hideLangs);
            $sel_language = $input->get('sel_language');
            $langfile = $input->getHtml('langfile', array());
            $easyLanguage->saveFile($sel_language, $langfile);
        }

        $input->set('task', 'languages');
        $input->set('view', 'languages');

        parent::display();
    }//function

    /**
     * Save the corrected default language.
     *
     * @return void
     */
    public function save_deflang_corrected()
    {
        $input = JFactory::getApplication()->input;

        try
        {
            //-- Get the project
            $project = EcrProjectHelper::getProject();
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);

            parent::display();

            return;
        }//try

        $scope = $input->get('scope', 'admin');

        if( ! $scope)
        {
            $scope = 'admin';
        }

        $hideLangs = $input->get('hide_langs', array());

        if(count($project->langs))
        {
            $easyLanguage = new EcrLanguage($project, $scope, $hideLangs);
            $langfile = $input->get('langfile', array());
            $easyLanguage->saveFile('en-GB', $langfile);
        }

        $input->set('task', 'languages');
        $input->set('view', 'languages');

        parent::display();
    }//function

    /**
     * Create a language file.
     *
     * @return void
     */
    public function create_langfile()
    {
        $input = JFactory::getApplication()->input;

        $ecr_project = $input->get('ecr_project');
        $oldTask = $input->get('old_task', 'languages');
        $type = '';

        try
        {
            EcrLanguage::createFileFromRequest();

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
        $input = JFactory::getApplication()->input;

        $fileName = $input->get('file');

        if( ! $fileName)
        {
            EcrHtml::message('No filename set', 'error');
        }
        else
        {
            if(EcrLanguageHelper::removeBOM_utf8($fileName))
            {
                EcrHtml::message(jgettext('The BOM has been removed'));
            }
            else
            {
                EcrHtml::message(jgettext('Unable to remove the BOM'), 'error');
            }
        }

        $input->set('view', 'languages');
        $input->set('task', 'languages');

        parent::display();
    }//function

    /**
     * Convert a language file.
     *
     * @return void
     */
    public function do_convert()
    {
        $input = JFactory::getApplication()->input;

        $input->set('task', 'convert');

        $options = $input->get('options', array());

        $options = JArrayHelper::toObject($options, 'JObject');

        //-- Get the project
        try
        {
            $project = EcrProjectHelper::getProject();
        }
        catch(Exception $e)
        {
            EcrHtml::message($e);

            parent::display();

            return;
        }//try

        $languages = JFactory::getLanguage()->getKnownLanguages();

        $converter = new EcrLanguageConverter($options, $project);

        $selLanguage = $input->get('sel_language');
        $selectedFile = $input->get('selected_file');
        $fileErrors = $input->get('file_errors', array());
        $scope = $input->get('scope');

        $selectedErrors = $input->get('selected_errors', array());

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

                EcrFile::saveVersion($fileName);
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
                EcrFile::saveVersion(JPATH_ROOT.DS.$selectedFile);
                JFile::write(JPATH_ROOT.DS.$selectedFile, $newCode);
            }
        }

        parent::display();
    }//function

    public function g11nUpdateLanguage()
    {
        $input = JFactory::getApplication()->input;

        try
        {
            $project = EcrProjectHelper::getProject();

            switch($project->type)
            {
                case 'template':
                    $comName = 'tpl_'.$project->comName;
                    break;

                default:
                    $comName = $project->comName;
                break;
            }//switch

            $scope = $input->get('scope');
            $lang = $input->get('langTag');

            $msg = Ecrg11nHelper::updateLanguage($comName, $scope, $lang);

            JFactory::getApplication()->enqueueMessage($msg);
        }
        catch(Exception $e)
        {
            JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }//try

        $input->set('task', 'g11nUpdate');

        parent::display();
    }//function

    public function g11nCreateTemplate()
    {
        $input = JFactory::getApplication()->input;

        try
        {
            $project = EcrProjectHelper::getProject();

            $scope = $input->get('scope');

            switch($project->type)
            {
                case 'template':
                    $comName = 'tpl_'.$project->comName;
                    break;

                default:
                    $comName = $project->comName;
                    break;
            }

            $parts = explode('.', $scope);

            if(2 == count($parts))
            {
                $scope = $parts[0];
                $comName .= '.'.$parts[1];
            }

            Ecrg11nHelper::createTemplate($comName, $scope, $project->version);
        }
        catch(Exception $e)
        {
            JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
        }

        $input->set('task', 'g11nUpdate');

        parent::display();
    }
}//class
