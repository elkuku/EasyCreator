<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author     Created on 29-Feb-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * EasyZIPer.
 *
 * @package    EasyCreator
 * @subpackage Helpers
 *
 */
class EasyZIPer extends JObject
{
    private $build_dir = '';

    private $temp_dir = '';

    private $downloadLinks = array();

    private $createdFiles = array();

    /**
     * @var EasyProject
     */
    private $project;

    private $buildopts = array();

    private $logger = null;

    private $profiler = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
    }//function

    /**
     * Create the package.
     *
     * @param EasyProject $project The project
     *
     * @return bool true on success
     */
    public function create(EasyProject $project)
    {
        $this->project = $project;

        //$this->build_dir = substr(JPATH_COMPONENT_ADMINISTRATOR, strlen(JPATH_SITE) + 1).DS.'builds';

        $this->build_dir = $this->project->getZipPath();

        //-- Init buildopts
        $buildopts = JRequest::getVar('buildopts', array());
        $this->initBuildOpts($buildopts);

        //-- Setup logging
        ecrLoadHelper('logger');

        $buildOpts['fileName'] = date('ymd_Hi').'_packing.log';

        $this->logger = easyLogger::getInstance('ecr', $buildopts);

        $this->logger->log('Start building');
        $this->logger->log('Build dir:<br />'.$this->build_dir);

        $steps = array(
          'setTempDir'
        , 'copyCopies'
        , 'copyLanguage'
        , 'copyMedia'
        , 'copyPackageModules'
        , 'copyPackagePlugins'
        , 'copyPackageElements' // 1.6
        , 'processInstall'
        , 'cleanProject'
        , 'deleteManifest'
        , 'createMD5'
        , 'createManifest'
        , 'createArchive'
        , 'removeBuildDir'
        );

        foreach($steps as $step)
        {
            if( ! $this->$step())
            {
                $this->logger->log('FINISHED with ERRORS');
                $this->setError('Error in '.$step);

                $this->logger->writeLog();

                return false;
            }
        }//foreach

        $this->logger->log('FINISHED');

        $this->logger->writeLog();

        return true;
    }//function

    /**
     * Initialize build options.
     *
     * @param array $buildopts Options
     *
     * @return boolean
     */
    private function initBuildOpts($buildopts)
    {
        $stdOpts = array('verbose', 'files'
        , 'archive_zip', 'archive_tgz', 'archive_bz'
        , 'create_indexhtml', 'remove_autocode', 'include_ecr_projectfile'
        , 'create_md5', 'create_md5_compressed');

        foreach($stdOpts as $opt)
        {
            $this->buildopts[$opt] =(in_array($opt, $buildopts)) ? true : false;
        }//foreach

        //--Init profiler
        if(in_array('profile', $buildopts))
        {
            jimport('joomla.error.profiler');
            $this->profiler = JProfiler::getInstance('EasyZipper');
            $this->buildopts['profiling'] = true;
        }
        else
        {
            $this->buildopts['profiling'] = false;
        }

        return true;
    }//function

    /**
     * Clean up unwanted stuff.
     *
     * @return boolean
     */
    private function cleanProject()
    {
        $this->logger->log('Starting CleanUp');

        $folders = JFolder::folders($this->temp_dir, '.', true, true);
        $files = JFolder::files($this->temp_dir, '.', true, true);

        $stdHtmlPath = ECRPATH_EXTENSIONTEMPLATES.DS.'std'.DS.'std_index.html';

        $cntIndex = 0;
        $cntAautoCode = 0;

        if($this->buildopts['create_indexhtml'])
        {
            foreach($folders as $folder)
            {
                if( ! Jfile::exists($folder.DS.'index.html'))
                {
                    JFile::copy($stdHtmlPath, $folder.DS.'index.html');

                    $cntIndex ++;
                }
            }//foreach

            $this->logger->log(sprintf('%s index.html files created', $cntIndex));
        }

        if($this->buildopts['remove_autocode'])
        {
            /**
             * @todo remove AutoCode
             */
        }

        if($this->buildopts['include_ecr_projectfile'])
        {
            $src = ECRPATH_SCRIPTS.DS.$this->project->getEcrXmlFileName();

            if(JFolder::exists($this->temp_dir.DS.'admin'))
            {
                $dst = $this->temp_dir.DS.'admin'.DS.'easycreator.xml';
            }
            else if(JFolder::exists($this->temp_dir.DS.'site'))
            {
                $dst = $this->temp_dir.DS.'site'.DS.'easycreator.xml';
            }
            else
            {
                $s = JFile::getName($src);

                if(substr($s, 0, 3) == 'pkg')
                {
                    //--- EasyCreator project file for packages goes to packageroot..
                    $dst = $this->temp_dir.DS.'easycreator.xml';
                }
                else//
                {
                    $this->logger->log('neither admin or site dir found', 'Failed to copy EasyCreator project xml');

                    return false;
                }
            }

            if(JFile::copy($src, $dst))
            {
                $this->logger->log('EasyCreator project xml copied');
            }
            else
            {
                $this->logger->log($src.' => '.$dst, 'Failed to copy EasyCreator project xml');

                return false;
            }
        }

        //-- Look for unwanted files
        $unwanted = array(
            'Thumbs.db'
        );

        foreach($files as $file)
        {
            foreach($unwanted as $item)
            {
                //-- Simple check if the full path contains an 'unwanted' string
                if(strpos($file, $item))
                {
                    $this->logger->log('Removing unwanted '.$item.' at '.$file);

                    if( ! JFile::delete($file))
                    {
                        $this->logger->log('Unable to remove '.$file, 'ERROR');
                    }
                }
            }//foreach
        }//foreach

        return true;
    }//function

    /**
     * Tries to find the package manifest file.
     *
     * @return boolean
     */
    private function deleteManifest()
    {
        //-- Get an array of all the xml files from the installation directory
        $xmlfiles = JFolder::files($this->temp_dir, '.xml$', 1, true);

        //-- No xml files were found in the install folder
        if(empty($xmlfiles))
        {
            return true;
        }

        foreach($xmlfiles as $file)
        {
            //-- Is it a valid Joomla! installation manifest file ?
            $manifest = $this->_isManifest($file);

            if( ! is_null($manifest))
            {
                //-- Delete manifest file in temp folder
                if(JFile::delete($file))
                {
                    $this->logger->log('File deleted '.$file);

                    return true;
                }
                else
                {
                    $this->logger->log('Unable to delete file '.$file, 'ERROR');

                    return false;
                }
            }
        }//foreach

        return true;
    }//function

    /**
     * Is the xml file a valid Joomla installation manifest file ?.
     *
     * @param string $file An xmlfile path to check
     *
     * @return mixed A SimpleXMLElement, or null if the file failed to parse
     */
    private function _isManifest($file)
    {
        $xml = EasyProjectHelper::getXML($file);

        //-- If we can not load the xml file return null
        if( ! $xml)
        {
            return null;
        }

        /*
         * Check for a valid XML root tag.
        */
        if($xml->getName() != 'install'
        && $xml->getName() != 'extension')
        {
            //-- Free up xml parser memory and return null
            unset($xml);

            return null;
        }

        //-- Valid manifest file return the object
        return $xml;
    }//function

    /**
     * Create the Joomla! manifest.
     *
     * @return boolean
     */
    private function createManifest()
    {
        $this->logger->log('Starting manifest');

        ecrLoadHelper('manifest');

        $manifest = new JoomlaManifest;

        $this->project->basepath = $this->temp_dir;
        $this->project->creationDate = date('d-M-Y');
        $this->project->isNew = false;

        if( ! $manifest->create($this->project))
        {
            $errors = $manifest->getErrors();

            foreach($errors as $error)
            {
                $this->setError($error);
                $this->logger->log('Error creating manifest file: '.$error, 'Error creating manifest file');
            }//foreach

            return false;
        }

        $this->logger->logFileWrite('manifest.xml', $this->project->basepath.DS.'manifest.xml', $manifest->formatXML());

        return true;
    }//function

    /**
     * Set the temp directory.
     *
     * @return boolean
     */
    private function setTempDir()
    {
        $this->temp_dir = JFactory::getConfig()->getValue('config.tmp_path').DS.uniqid($this->project->comName);

        if( ! JFolder::create($this->temp_dir))
        {
            $this->logger->log('Creating TempDir<br />'.$this->temp_dir, 'ERROR');

            return false;
        }

        $this->logger->log('TempDir created<br />'.$this->temp_dir);

        return true;
    }//function

    /**
     * Copy files and folders.
     *
     * @return boolean
     */
    private function copyCopies()
    {
        foreach($this->project->copies as $copy)
        {
            $dest =(strpos($copy, JPATH_ADMINISTRATOR) === 0) ? 'admin' : 'site';
            $copy = str_replace('/', DS, $copy);
            $tmp_dest = $this->temp_dir.DS.$dest;

            JFolder::create($tmp_dest);

            if(is_dir($copy))
            {
                //-- Source is a directory
                //-- Copy with force overwrite..
                if(JFolder::copy($copy, $tmp_dest, '', true))
                {
                    $this->logger->log('COPY DIR<br />SRC: '.$copy.'<br />DST: '.$tmp_dest);

                    /*
                     * We are packing EasyCreator.. need to strip off some stuff =;)
                    */
                    if($this->project->comName == 'com_easycreator' && $dest == 'admin')
                    {
                        $ecrBase = $this->temp_dir.DS.'admin';
                        $folders = array('logs', 'scripts', 'builds', 'templates/exports', 'tests', 'results');

                        foreach($folders as $folder)
                        {
                            if( ! JFolder::exists($ecrBase.DS.$folder))
                            continue;

                            $files = JFolder::files($ecrBase.DS.$folder, '.', true, true, array('.svn', 'index.html'));

                            foreach($files as $file)
                            {
                                if(JFile::delete($file))
                                {
                                    $this->logger->log('EasyCreator file deleted '.$file);
                                }
                                else
                                {
                                    $this->logger->log('error removing EasyCreator file :(<br />'.$file, 'ERROR');
                                }
                            }//foreach
                        }//foreach
                    }

                    if($this->buildopts['remove_autocode'])
                    {
                        $files = JFolder::files($tmp_dest, '.', true, true);

                        foreach($files as $file)
                        {
                            $lines = file($file);

                            $buffer = array();
                            $acFound = false;

                            foreach($lines as $line)
                            {
                                if(strpos($line, 'ECR AUTOCODE'))
                                {
                                    $acFound = true;
                                }
                                else
                                {
                                    $buffer[] = $line;
                                }
                            }//foreach

                            if($acFound)
                            {
                                if(JFile::write($file, implode('', $buffer)))
                                {
                                    $this->logger->log('Autocode removed from file '
                                    .str_replace($this->temp_dir.DS, '', $file));
                                }
                            }
                        }//foreach
                    }
                }
                else
                {
                    $this->logger->log('SRC: '.$copy.'<br />DST: '.$tmp_dest, 'COPY DIR FAILED');
                }
            }
            else if(file_exists($copy))
            {
                //--source is a file
                if(JFile::copy($copy, $tmp_dest.DS.JFile::getName($copy)))
                {
                    $this->logger->log('COPY FILE<br />SRC: '.$copy.'<br />DST: '.$tmp_dest);
                }
                else
                {
                    $this->logger->log('COPY FILE FAILED<br />SRC: '.$copy.'<br />DST: '.$tmp_dest, 'ERROR copy file');
                }
            }
            else
            {
                //--source does not exist - ABORT - TODO: rollback
                $this->logger->log('<div class="ebc_error">NOT FOUND :<br />>'.$copy.'</div>', 'not found');

                return false;
            }
        }//foreach

        return true;
    }//function

    /**
     * Copy media files.
     *
     * @return boolean
     */
    private function copyMedia()
    {
        $mediaPath = JPATH_ROOT.DS.'media'.DS.$this->project->comName;

        if( ! JFolder::exists($mediaPath))
        return true;

        $destination = $this->temp_dir.DS.'media';

        if(JFolder::copy($mediaPath, $destination))
        {
            $this->logger->log('Media folder copied to :'.$destination);
        }

        return true;
    }//function

    /**
     * Process install files.
     *
     * @return boolean
     */
    private function processInstall()
    {
        if( ! $this->project->JCompat == '1.5')
        {
            return true;
        }

        $installFiles = EasyProjectHelper::findInstallFiles($this->project);

        if( ! count($installFiles['php']))
        return true;

        $srcDir = $this->temp_dir.DS.'admin';
        $destDir = $this->temp_dir.DS.'install';

        //-- Create 'install' folder in temp dir
        JFolder::create($destDir);

        //-- Copy install files from 'admin' to 'temp'
        foreach($installFiles['php'] as $file)
        {
            $srcPath = $srcDir;
            $srcPath .=($file->folder) ? DS.$file->folder : '';
            $srcPath .= DS.$file->name;

            $destPath = $destDir;

            if($file->folder == 'install')
            {
                $folder = '';
            }
            else
            {
                $folder = str_replace('install'.DS, '', $file->folder);
            }

            if($folder)
            {
                $destPath .= DS.$folder;

                // Create the folder
                JFolder::create($destPath);
            }

            if(JFile::copy($srcPath, $destPath.DS.$file->name))
            {
                $this->logger->log('COPY INSTALL FILE<br />SRC: '.$srcPath.'<br />DST: '.$destPath.DS.$file->name);
            }
            else
            {
                $this->logger->log('COPY INSTALL FILE<br />SRC: '.$srcPath
                .'<br />DST: '.$destPath.DS.$file->name, 'ERROR copy file');

                continue;
            }

            if(0 != strpos($file->name, 'install'))
            continue;

            if($this->buildopts['create_md5'])
            {
                $format =('po' == $this->project->langFormat) ? '.po' : '';
                $compressed =($this->buildopts['create_md5_compressed']) ? '_compressed' : '';
                $fileContents = JFile::read(ECRPATH_EXTENSIONTEMPLATES.DS.'std'.DS.'md5check'.$compressed.$format.'.php');
                $fileContents = str_replace('<?php', '', $fileContents);
                $this->project->addSubstitute('##ECR_MD5CHECK_FNC##', $fileContents);

                $fileContents = JFile::read(ECRPATH_EXTENSIONTEMPLATES.DS.'std'.DS.'md5check_call'.$format.'.php');
                $fileContents = str_replace('<?php', '', $fileContents);
                $this->project->addSubstitute('##ECR_MD5CHECK##', $fileContents);

                $this->project->addSubstitute('_ECR_COM_COM_NAME_', $this->project->comName);

                $fileContents = JFile::read($destPath.DS.$file->name);
                $fileContents = $this->project->substitute($fileContents);
            }
            else
            {
                $this->project->addSubstitute('##ECR_MD5CHECK_FNC##', '');
                $this->project->addSubstitute('##ECR_MD5CHECK##', '');

                $fileContents = JFile::read($destPath.DS.$file->name);
                $fileContents = $this->project->substitute($fileContents);
            }

            if(JFile::write($destPath.DS.$file->name, $fileContents))
            {
                $this->logger->logFileWrite('', 'install/install.php', $fileContents);
            }
            else
            {
                $this->logger->log('Failed to add MD5 install check routine to install.php', 'error');
            }
        }//foreach

        //-- Delete install files from 'admin'
        foreach($installFiles['php'] as $file)
        {
            $srcPath = $srcDir;
            $srcPath .=($file->folder) ? DS.$file->folder : '';
            $srcPath .= DS.$file->name;

            if(JFile::delete($srcPath))
            {
                $this->logger->log('INSTALL FILE DELETED<br />SRC: '.$srcPath);
            }
            else
            {
                $this->logger->log('DELETE INSTALL FILE<br />SRC: '.$srcPath, 'ERROR deleting file');

                return false;
            }
        }//foreach

        return true;
    }//function

    /**
     * Copy the package modules.
     *
     * @deprecated in favor for J! 1.6 packages
     * @see EasyZIPer::copyPackageElements
     *
     * @return boolean
     */
    private function copyPackageModules()
    {
        if( ! count($this->project->modules))
        {
            return true;
        }

        $this->logger->log('<strong style="color: blue;">Copying Package modules</strong>');

        foreach($this->project->modules as $module)
        {
            $clientPath =($module->scope == 'admin') ? JPATH_ADMINISTRATOR.DS.'modules' : JPATH_SITE.DS.'modules';

            if(JFolder::copy($clientPath.DS.$module->name, $this->temp_dir.DS.$module->name, '', true))
            {
                $this->logger->log('COPY DIR<br />SRC: '.$clientPath.DS.$module->name
                .'<br />DST: '.$this->temp_dir.DS.$module->name);
            }
            else
            {
                $this->logger->log('COPY DIR FAILED<br />SRC: '.$clientPath.DS.$module->name
                .'<br />DST: '.$this->temp_dir, 'ERROR copy dir');

                return false;
            }

            //--Get the project
            try
            {
                $modProject = EasyProjectHelper::getProject($s);
            }
            catch(Exception $e)
            {
                $this->logger->log('Unable to load the project '.$s.' - '.$e->getMessage(), 'ERROR');

                continue;
            }//try

            if( ! is_array($modProject->langs))
            {
                continue;
            }

            $clientPath =($module->scope == 'admin') ? JPATH_ADMINISTRATOR.DS.'language' : JPATH_SITE.DS.'language';

            if(count($modProject->langs))
            {
                $tmp_dest = $this->temp_dir.DS.$module->name.DS.'language';
                JFolder::create($tmp_dest);
            }

            foreach($modProject->langs as $tag => $scopes)
            {
                $this->logger->log('Processing module language '.$tag);

                $tmp_fname = $tag.'.'.$module->name.'.ini';

                if(file_exists($clientPath.DS.$tag.DS.$tmp_fname))
                {
                    JFile::copy($clientPath.DS.$tag.DS.$tmp_fname, $tmp_dest.DS.$tmp_fname);
                    $this->logger->log('copy: '.$clientPath.DS.$tag.DS.$tmp_fname);
                }
                else
                {
                    $this->logger->log('NOT FOUND: '.$clientPath.DS.$tag.DS.$tmp_fname, 'copy langfile');
                }
            }//foreach
        }//foreach

        return true;
    }//function

    /**
     * Copy the package plugins.
     *
     * @deprecated in favor for J! 1.6 packages
     * @see EasyZIPer::copyPackageElements
     *
     * @return boolean
     */
    private function copyPackagePlugins()
    {
        if( ! count($this->project->plugins))
        {
            return true;
        }

        $this->logger->log('<strong style="color: blue;">Copying Package plugins</strong>');

        foreach($this->project->plugins as $plugin)
        {
            $plgFolderName = 'plg_'.$plugin->scope.'_'.$plugin->name;

            //--Get the project
            try
            {
                $plgProject = EasyProjectHelper::getProject($plgFolderName);
            }
            catch(Exception $e)
            {
                $this->logger->log('Unable to load the project - Error:'.$e->getMessage(), 'ERROR');

                continue;
            }//try

            if(JFolder::create($this->temp_dir.DS.$plgFolderName))
            {
                $this->logger->log('Plugin folder created: '.$this->temp_dir.DS.$plgFolderName);
            }
            else
            {
                $this->logger->log('ERROR creating folder'.$this->temp_dir.DS.$plgFolderName, 'ERROR');
            }

            foreach($plgProject->copies as $copy)
            {
                if(JFolder::exists($copy))
                {
                    $tName = str_replace('plugins'.DS.$plugin->scope.DS, '', $copy);

                    if(JFolder::copy($copy, $this->temp_dir.DS.$plgFolderName.DS.$tName))
                    {
                        $this->logger->log('COPY folder<br />SRC: '.$copy
                        .'<br />DST: '.$this->temp_dir.DS.$plgFolderName.DS.$tName);
                    }
                    else
                    {
                        $this->logger->log('COPY FOLDER FAILED<br />SRC: '.$copy
                        .'<br />DST: '.$this->temp_dir.DS.$plgFolderName.DS.$tName
                        , 'ERROR copy folder');
                    }
                }
                else if(JFile::exists($copy))
                {
                    $tName = JFile::getName($copy);

                    if(JFile::copy($copy, $this->temp_dir.DS.$plgFolderName.DS.$tName))
                    {
                        $this->logger->log('COPY file<br />SRC: '.$copy
                        .'<br />DST: '.$this->temp_dir.DS.$plgFolderName.DS.$tName);
                    }
                    else
                    {
                        $this->logger->log('COPY FILE FAILED<br />SRC: '.$copy
                        .'<br />DST: '.$this->temp_dir.DS.$plgFolderName.DS.$tName
                        , 'ERROR copy file');
                    }
                }
                else
                {
                    $this->logger->log('Not found<br />SRC: '.$copy, 'FILE NOT FOUND');
                }
            }//foreach

            if( ! count($plgProject->langs))
            {
                $this->logger->log('No languages found');
            }

            foreach($plgProject->langs as $tag => $scopes)
            {
                $this->logger->log('<strong>Processing plugin language '.$tag.'</strong>');

                $tmp_src = JPATH_ADMINISTRATOR.DS.'language'.DS.$tag;
                $tmp_dest = $this->temp_dir.DS.$plgFolderName.DS.'language';
                JFolder::create($tmp_dest);

                $tmp_fname = $tag.'.plg_'.$plugin->scope.'_'.$plugin->name.'.ini';

                if(file_exists($tmp_src.DS.$tmp_fname))
                {
                    JFile::copy($tmp_src.DS.$tmp_fname, $tmp_dest.DS.$tmp_fname);
                    $this->logger->logFileWrite($tmp_src.DS.$tmp_fname, $tmp_dest.DS.$tmp_fname);
                }
                else
                {
                    $this->logger->log('NOT FOUND: '.$tmp_src.DS.$tmp_fname, 'copy langfile');
                }
            }//foreach
        }//foreach

        return true;
    }//function

    /**
     * Copy the elements of a package.
     *
     * For Joomla! 1.6 packages only.
     *
     * @return boolean
     */
    private function copyPackageElements()
    {
        if($this->project->type != 'package')
        return true;

        if( ! count($this->project->elements))
        return true;

        $this->logger->log('<strong style="color: blue;">Copying Package elements</strong>');

        foreach($this->project->elements as $element => $path)
        {
            $this->ecr_project = JRequest::getCmd('ecr_project');

            //--Get the project
            try
            {
                $project = EasyProjectHelper::getProject($element);
            }
            catch(Exception $e)
            {
                $this->logger->log('Unable to load the project '.$element.' - '.$e->getMessage(), 'ERROR');

                continue;
            }//try

            $ziper = new EasyZIPer;
            $result = $ziper->create($project);
            $files = $ziper->getCreatedFiles();

            if( ! count($files))
            {
                $this->logger->log(sprintf('No packages files have been created for project %s', $element), 'ERROR');

                continue;
            }

            $src = $files[0];
            $fName = JFile::getName($src);

            //-- Set the elemnent path for manifest class
            $this->project->elements[$element] = $fName;

            $dest = $this->temp_dir.DS.$fName;

            if(JFile::copy($src, $dest))
            {
                $this->logger->log(sprintf('Package %s copied from %s to %s', $element, $src, $dest));
            }
            else
            {
                $this->logger->log(sprintf('Unable to create package %s try to copy from %s to %s'
                , $element, $src, $dest), 'ERROR');

                return false;
            }
        }//foreach

        return true;
    }//function

    /**
     * Copy language files.
     *
     * @return boolean
     */
    private function copyLanguage()
    {
        //-- No languages defined
        if( ! is_array($this->project->langs))
        return true;

        //-- Only ini files needs to be copied
        if($this->project->langFormat != 'ini')
        return true;

        foreach($this->project->langs as $language => $scopes)
        {
            foreach($scopes as $scope)
            {
                $this->logger->log('<strong>Processing language '.$language.' - '.$scope.'</strong>');

                $paths = $this->project->getLanguagePaths($scope);

                if( ! is_array($paths))
                $paths = array($paths);

                if( ! count($paths))
                continue;

                $found = false;

                foreach($paths as $path)
                {
                    $srcFileName = $language.'.'.$this->project->getLanguageFileName($scope);
                    $srcPath = $path.'/language/'.$language;

                    if(file_exists($srcPath.'/'.$srcFileName))
                    {
                        $found = true;
                        break;
                    }
                }//foreach

                if( ! $found)
                {
                    $this->logger->log('File: '.$srcPath.'/'.$srcFileName, 'copy failed');
                    $this->setError(sprintf(jgettext('File not found: %s'), $srcPath.'/'.$srcFileName));

                    continue;
                }

                //                $tmp_src =($scope === 'admin' || $scope === 'menu')
                //? JPATH_ADMINISTRATOR : JPATH_SITE;
                //                $tmp_src .= DS.'language'.DS.$language;

                $s =($scope === 'menu' || $scope === 'sys') ? 'admin' : $scope;
                $tmp_dest = $this->temp_dir.DS.$s.DS.'language'.DS.$language;

                //                $s =($scope == 'menu') ? '.menu' : '';
                //
                //                $tmp_fname = $language.'.'.$this->project->comName.$s.'.ini';
                $tmp_fname = $srcFileName;

                if($this->project->type == 'plugin')
                {
                    //-- Plugin language files come from admin and go to site..
                    //                    #                    $tmp_src = JPATH_ADMINISTRATOR.DS.'language'.DS.$language;
                    $tmp_dest = $this->temp_dir.DS.'site'.DS.'language'.DS.$language;
                    //                    #
                    //$tmp_fname = $language.'.plg_'.$this->project->scope.'_'.$this->project->comName.'.ini';
                }

                //                if($this->project->type == 'plugin')
                //                {
                //                    //-- Plugin language files come from admin and go to site..
                //                    $tmp_src = JPATH_ADMINISTRATOR.DS.'language'.DS.$language;
                //                    $tmp_dest = $this->temp_dir.DS.'site'.DS.'language';
                //                    $tmp_fname = $language.'.plg_'.$this->project->scope.'_'.
                //$this->project->comName.'.ini';
                //                }

                if(file_exists($tmp_dest.DS.$srcFileName))
                {
                    $this->logger->log('File: '.$srcFileName, 'already exists');
                }
                else
                {
                    JFolder::create($tmp_dest);

                    if(JFile::copy($srcPath.'/'.$srcFileName, $tmp_dest.DS.$srcFileName))
                    {
                        $this->logger->log('copy: '.$srcFileName);
                    }
                    else
                    {
                        $this->logger->log('File: '.$srcFileName, 'copy failed');
                        $this->setError(sprintf(jgettext('Failed to copy file %s to %s'), $test, $tmp_dest.DS.$srcFileName));
                    }
                }

                //                if(file_exists($tmp_src.DS.$tmp_fname))
                //                {
                //                    JFile::copy($tmp_src.DS.$tmp_fname, $tmp_dest.DS.$tmp_fname);
                //                    $this->logger->log('copy: '.$tmp_fname);
                //                }
                //                else
                //                {
                //                    $this->logger->log('File: '.$tmp_fname, 'copy failed');
                //                    $this->setError(jgettext('Failed to copy file %s to %s'
                //, $tmp_src.DS.$tmp_fname, $tmp_dest.DS.$tmp_fname));
                //                }
            }//foreach
        }//foreach

        return true;
    }//function

    /**
     * Create a MD5 checksum file.
     *
     * @return boolean
     */
    private function createMD5()
    {
        $md5Str = '';

        if( ! $this->buildopts['create_md5'])
        {
            return true;
        }

        $fileList = JFolder::files($this->temp_dir, '.', true, true);

        foreach($fileList as $file)
        {
            if($this->buildopts['create_md5_compressed'])
            {
                $path = str_replace($this->temp_dir.DS, '', $file);
                $parts = explode(DS, $path);
                $fName = array_pop($parts);
                $path = implode('/', $parts);
                $md5Str .= md5_file($file).' '.$this->compressPath($path).'@'.$fName.NL;
            }
            else
            {
                $md5Str .= md5_file($file).' '.str_replace($this->temp_dir.DS, '', $file).NL;
            }
        }//foreach

        $subDir =(JFolder::exists($this->temp_dir.DS.'admin')) ? 'admin' : 'site';

        //--@todo temp solution to put the md5 file in a sub folder for J! 1.6 not finding it...
        $subDir .= DS.'install';

        if( ! JFile::write($this->temp_dir.DS.$subDir.DS.'MD5SUMS', $md5Str))
        {
            $this->logger->log('Can not create MD5SUMS File', 'Error');
            $this->setError('Can not create MD5SUMS File');

            return false;
        }

        $this->logger->logFileWrite('MD5SUMS', $this->temp_dir.DS.'MD5SUMS', $md5Str);

        return true;
    }//function

    private function compressPath($path)
    {
        static $previous = '';

        if( ! $previous) //-- Init
        {
            $previous = $path;

            return $previous;
        }

        $compressed = '=';//-- Same as previous path - maximun compression :)

        if($previous != $path) //-- Different path - too bad..
        {
            $subParts = explode(DS, $path);

            $compressed = $path;//-- One element at Root level

            if(count($subParts) > 1) //-- More elements...
            {
                $previousParts = explode(DS, $previous);

                $result = array();

                $foundDifference = false;

                foreach($subParts as $i => $part)
                {
                    if(isset($previousParts[$i])
                    && $part == $previousParts[$i]
                    && ! $foundDifference) //-- Same as previous sub path
                    {
                        $result[] = '-';
                    }
                    else //-- Different sub path
                    {
                        if(count($result) && $result[count($result) - 1] == '-')
                        $result[] = '|'; //-- Add a separator

                        $result[] = $part.DS;

                        $foundDifference = true;
                    }
                }//foreach

                if(count($result) && $result[count($result) - 1] == '-')
                $result[] = '|'; //-- Add a separator(no add path)

                $compressed = implode('', $result);
            }
        }

        $previous = $path;

        return $compressed;
    }//function

    /**
     * Create the zip file.
     *
     * @return boolean
     */
    private function createArchive()
    {
        ecrLoadHelper('archive');

        $zipTypes = array(
        'zip' => 'zip'
        , 'tgz' => 'tar.gz'
        , 'bz' => 'bz2');

        $this->logger->log('Start adding files');

        //$zipDir = JPATH_ROOT.DS.$this->build_dir.DS.$this->project->comName.DS.$this->project->version;

        if($this->build_dir != ECRPATH_BUILDS)
        {
            $zipDir = $this->build_dir.DS.$this->project->version;
        }
        else
        {
            $zipDir = $this->build_dir.DS.$this->project->comName.DS.$this->project->version;
        }

        //--Build the file list
        $files = JFolder::files($this->temp_dir, '.', true, true);
        $this->logger->log('TOTAL: '.count($files).' files');

        if( ! is_dir($zipDir))
        {
            if( ! JFolder::create($zipDir))
            {
                $this->logger->log('ERROR creating folder '.$zipDir, 'ERROR packing routine');

                return false;
            }
        }

        if(0 === strpos($this->project->getZipPath(), ECRPATH_BUILDS))
        {
            $hrefBase = JURI::Root().'administrator/components/com_easycreator/builds/'
            .$this->project->comName.'/'.$this->project->version;
        }
        else
        {
            $hrefBase = 'file://'.$this->project->getZipPath().'/'.$this->project->version;
        }

        $customFileName = EasyProjectHelper::formatFileName($this->project, JRequest::getVar('cst_format'));

        $fileName = $this->project->getFileName().$customFileName;

        foreach($zipTypes as $zipType => $ext)
        {
            if( ! $this->buildopts['archive_'.$zipType])
            {
                continue;
            }

            $this->logger->log('creating '.$zipType);

            switch($ext)
            {
                case 'zip':

                    //-- Translate win path to unix path - for PEAR..
                    $p = str_replace('\\', '/', $this->temp_dir);

                    if( ! EasyArchive::createZip($zipDir.DS.$fileName.'.zip', $files, $p))
                    {
                        $this->logger->log('ERROR Packing routine for '.$ext, 'ERROR packing routine');

                        return false;
                    }

                    break;

                case 'bz2':
                    ecrLoadHelper('PEAR');

                    if( ! extension_loaded('bz2'))
                    {
                        PEAR::loadExtension('bz2');
                    }

                    if( ! extension_loaded('bz2'))
                    {
                        JError::raiseWarning(100, jgettext('The extension "bz2" couldn\'t be found.'));
                        JError::raiseWarning(100
                        , jgettext('Please make sure your version of PHP was built with bz2 support.'));
                        $this->logger->log('PHP extension bz2 not found', 'PHP ERROR');
                    }
                    else
                    {
                        //-- Translate win path to unix path - for PEAR..
                        $p = str_replace('\\', '/', $this->temp_dir);

                        $result = $archive = EasyArchive::createTgz($zipDir.DS.$fileName.'.'.$ext, $files, 'bz2', $p);

                        if( ! $result->listContent())
                        {
                            $this->logger->log('ERROR Packing routine for '.$ext, 'ERROR packing routine');

                            return false;
                        }
                    }

                    break;

                case 'tar.gz':
                    $result = $archive = EasyArchive::createTgz($zipDir
                    .DS.$fileName.'.'.$ext, $files, 'gz', $this->temp_dir);

                    if( ! $result->listContent())
                    {
                        $this->logger->log('ERROR Packing routine for '.$ext, 'ERROR packing routine');

                        return false;
                    }

                    break;

                default:
                    JError::raiseWarning(100, 'undefined packing type '.$ext);

                return false;
                break;
            }//switch

            $this->logger->log('Packing routine for '.$ext.' finished');
            $this->downloadLinks[] = $hrefBase.'/'.$fileName.'.'.$ext;
            $this->createdFiles[] = $zipDir.DS.$fileName.'.'.$ext;
        }//foreach

        return true;
    }//function

    /**
     * Remove the build directory.
     *
     * @return boolean true on success
     */
    private function removeBuildDir()
    {
        if(ECR_DEBUG)
        {
            $this->logger->log('The build folder<br />'.$this->temp_dir.'<br />will not be deleted in debug mode.');

            return true;
        }

        if( ! JFolder::delete($this->temp_dir))
        {
            $this->logger->log('Unable to delete<br />'.$this->temp_dir, 'cannot delete');

            return false;
        }

        $this->logger->log('The build folder<br />'.$this->temp_dir.'<br />has been sucessfully deleted.');

        return true;
    }//function

    /**
     * Dislays the download links for created packages.
     *
     * @return void
     */
    public function getDownloadLinks()
    {
        return $this->downloadLinks;
    }//function

    /**
     * Get a list of created files.
     *
     * @return array
     */
    public function getCreatedFiles()
    {
        return $this->createdFiles;
    }//function

    /**
     * Prints the log.
     *
     * @return string
     */
    public function printLog()
    {
        return $this->logger->printLog();
    }//function

    /**
     * Get the error log.
     *
     * @return array
     */
    public function getErrorLog()
    {
        return $this->_errors;
    }//function
}//class
