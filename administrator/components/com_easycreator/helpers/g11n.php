<?php
/**
 * @version SVN: $Id$
 * @package    EasyCreator
 * @subpackage Views
 * @author     Nikolai Plath {@link http://nik-it.de}
 * @author     Created on 15-Aug-2011
 * @license    GNU/GPL
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * The g11n Helper Class.
 */
class G11nHelper
{
    public static function getCachedFiles()
    {
        $paths = array(JPATH_ADMINISTRATOR, JPATH_SITE);

        $cachedFiles = array();

        foreach($paths as $path)
        {
            $scope =($path == JPATH_ADMINISTRATOR) ? 'admin' : 'site';

            $cachePath = $path.'/'.g11nStorage::getCacheDir();

            if( ! JFolder::exists($cachePath))
            {
                $cachedFiles[$scope] = array();

                continue;
            }

            $extensions = JFolder::folders($cachePath);

            foreach($extensions as $extension)
            {
                $cachedFiles[$extension][$scope] = JFolder::files($cachePath.'/'.$extension);
            }//foreach
        }//foreach

        return $cachedFiles;
    }//function

    public static function getLanguages()
    {
        $languages = array();

        $language = JFactory::getLanguage();

        $languages['admin'] = $language->getKnownLanguages(JPATH_ADMINISTRATOR);
        $languages['site'] = $language->getKnownLanguages(JPATH_SITE);

        $languages['all'] = $languages['site'];

        if(count($languages['admin']) > count($languages['site']))
        {
            $languages['all'] = $languages['admin'];
        }

        return $languages;
    }//function

    public static function updateLanguage($extension, $scope, $lang)
    {
        $languageFile = g11nExtensionHelper::findLanguageFile($lang, $extension, $scope);
        $templateFile = g11nStorage::getTemplatePath($extension, $scope);

        $msg = '';

        if(false == $languageFile)
        {
            //-- New file
            $scopePath = g11nExtensionHelper::getScopePath($scope);
            $extensionPath = g11nExtensionHelper::getExtensionLanguagePath($extension);

            $path = $scopePath.'/'.$extensionPath.'/'.$lang;

            if( ! JFolder::exists($path))
            {
                if( ! JFolder::create($path))
                throw new Exception('Can not create the language folder');
            }

            $fileName = $lang.'.'.$extension.'.po';

            $input = '--input='.$templateFile;
            $output = '--output='.$path.'/'.$fileName;

            $noWrap = '--no-wrap';

            $locale = '--locale='.$lang;

            $cmd = "msginit $input $output $locale $noWrap 2>&1";

            $msg .=(ECR_DEBUG) ? '<h3>'.$cmd.'</h3>' : '';

            ob_start();

            system($cmd);

            $msg .= ob_get_clean();

            $msg = str_replace("\n", BR, $msg);

            if( ! JFile::exists($templateFile))
            throw new Exception('Can not copy create the language file');

            $msg = jgettext('The language file has been created<br />').$msg;
        }
        else//
        {
            //-- Update existing file

            $msg .= jgettext('Updating language file...');

            $update = '--update';
            $backup = '--backup=numbered';
            $noFuzzy = '--no-fuzzy-matching';
            $verbose = '--verbose';
            $noWrap = '--no-wrap';

            $cmd = "msgmerge $update $noFuzzy $backup $verbose $noWrap $languageFile $templateFile  2>&1";

            $msg .=(ECR_DEBUG) ? '<h3>'.$cmd.'</h3>' : '';

            ob_start();

            system($cmd);

            $msg .= ob_get_clean();
        }

        return $msg;
    }//function

    public static function createTemplate($extension, $scope)
    {
        if(($scope != 'admin')
        && ($scope != 'site'))
        throw new Exception('Scope must be "admin" or "site"');

        $base = g11nExtensionHelper::getScopePath($scope);
        $templatePath = g11nStorage::getTemplatePath($extension, $scope);
        $extensionDir = g11nExtensionHelper::getExtensionPath($extension);

        $headerData = '';
        $headerData .= ' --copyright-holder="NiK(C)"';
        $headerData .= ' --package-name="'.$extension.' - '.$scope.'"';
        $headerData .= ' --package-version="123.456"';
        $headerData .= ' --msgid-bugs-address="info@nik.it.de"';

        $comments = ' --add-comments=TRANSLATORS:';

        $keywords = ' -k --keyword=jgettext --keyword=jngettext:1,2';

        $forcePo = ' --force-po --no-wrap';

        /*
         * KEYS="-k --keyword=jgettext --keyword=jngettext:1,2"

        find "$WORK_PATH/." -type f -iname "*.php" | xgettext $KEYS -o $WORK_PATH/language/$FNAME.pot -f -
        */

        if( ! JFolder::exists($base.DS.$extensionDir))
        throw new Exception('Invalid extension');

        $dirName = dirname($templatePath);

        if( ! JFolder::exists($dirName)
        && ! JFolder::create($dirName))
        throw new Exception(jgettext('Can not create the language template folder'));

        $subType =(strpos($extension, '.')) ? substr($extension, strpos($extension, '.') + 1) : '';

        $buildOpts = '';

        $excludes = array(
                '/editarea_0_8_1_1/'
        , '/highcharts-2.0.5/'
        , '/php2js.js'
        );

        switch($subType)
        {
            case '':
                $search = 'php';
                break;

            case 'js':
                $search = 'js';
                $buildOpts .= ' -L python';
                break;

            case 'config':
                //                    $search = 'xml';
                //                    $buildOpts .= ' -L Glade';
                //                    $keywords = ' -k --keyword=description --keyword=label';

                $excludes[] = '/templates/';
                $excludes[] = '/scripts/';
                break;

            default:
                break;
        }//switch

        $files = JFolder::files($base.DS.$extensionDir, '.'.$search.'$', true, true);

        if( ! $files)
        throw new Exception(jgettext('No files found'));

        $cleanFiles = array();

        foreach($files as $file)
        {
            $found = false;

            foreach($excludes as $exclude)
            {
                if(strpos($file, $exclude))
                $found = true;
            }//foreach

            if( ! $found)
            $cleanFiles[] = $file;
        }//foreach

        if('config' == $subType)
        {
            defined('NL') || define('NL', "\n");

            $parser = g11n::getParser('code', 'xml');
            $potParser = g11n::getParser('language', 'pot');

            $options = new JObject;

            $outFile = new g11nFileInfo;

            foreach($cleanFiles as $fileName)
            {
                $fileInfo = $parser->parse($fileName);

                if( ! count($fileInfo->strings))
                continue;

                $relPath = str_replace(JPATH_ROOT.DS, '', $fileName);

                foreach($fileInfo->strings as $key => $strings)
                {
                    foreach($strings as $string)
                    {
                        if(array_key_exists($string, $outFile->strings))
                        {
                            if(strpos($outFile->strings[$string]->info, $relPath.':'.$key) !== false)
                            continue;

                            $outFile->strings[$string]->info .= '#: '.$relPath.':'.$key.NL;
                            continue;
                        }

                        $t = new g11nTransInfo;

                        $t->info .= '#: '.$relPath.':'.$key.NL;
                        $outFile->strings[$string] = $t;
                    }//foreach
                }//foreach
            }//foreach

            $buffer = $potParser->generate($outFile, $options);

            if( ! JFile::write($templatePath, $buffer))
            throw new Exception('Unable to write the output file', $code);
        }
        else//
        {
            $fileList = implode("\n", $cleanFiles);

            $command = $keywords.$buildOpts.' -o '.$templatePath.$forcePo.$comments.$headerData;

            echo '<h3>'.$command.'</h3>';

            ob_start();

            system('echo "'.$fileList.'" | xgettext '.$command.' -f - 2>&1');

            $result = ob_get_clean();

            echo '<pre>'.$result.'</pre>';
        }

        if( ! JFile::exists($templatePath))
        throw new Exception('Could not create the template');

        //-- Manually strip the JROOT path - ... @todo: did i miss a parameter ¿
        $contents = JFile::read($templatePath);
        $contents = str_replace(JPATH_ROOT.DS, '', $contents);

        JFile::write($templatePath, $contents);

        JFactory::getApplication()->enqueueMessage(jgettext('Your template has been created'));
        ;
    }
}//class
