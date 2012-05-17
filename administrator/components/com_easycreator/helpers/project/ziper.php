<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 29-Feb-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * EcrZIPer - a packager.
 *
 * @package    EasyCreator
 * @subpackage Helpers
 *
 */
class EcrProjectZiper extends JObject
{
    private $build_dir = '';

    private $temp_dir = '';

    private $downloadLinks = array();

    private $createdFiles = array();

    /**
     * @var EcrProjectBase
     */
    private $project;

    private $buildopts = array();

    /**
     * @var EcrLogger
     */
    private $logger = null;

    private $profiler = null;

    private $runTime = 0;

    /**
     * Constructor.
     *
     * @param null $properties
     */
    public function __construct($properties = null)
    {
        $this->runTime = microtime(true);

        $this->setupLog();

        JLog::add('|¯¯¯ Starting');

        parent::__construct();
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        $time = number_format(microtime(true) - $this->runTime, 2);

        JLog::add(sprintf('|___ Finished in %s sec.', $time));

        //-- Give the logger a chance to finish.
        sleep(2);
    }

    /**
     * Create the package.
     *
     * @param EcrProjectBase $project The project
     * @param array          $buildOpts
     *
     * @return bool true on success
     */
    public function create(EcrProjectBase $project, array $buildOpts)
    {
        $this->project = $project;

        $this->build_dir = $this->project->getZipPath();

        //-- Init buildopts
        $this->initBuildOpts($buildOpts);

        //-- Setup logging
        $buildOpts['fileName'] = date('ymd_Hi').'_packing.log';

        $this->logger = EcrLogger::getInstance('ecr', $buildOpts);

        $this->logger->log('Start building');
        $this->logger->log('Build dir:<br />'.$this->build_dir);

        try
        {
            $this
                ->performPrebuildActions()
                ->setTempDir()
                ->copyCopies()
                ->copyLanguage()
                ->copyMedia()
                ->copyPackageModules()
                ->copyPackagePlugins()
                ->copyPackageElements()
                ->processInstall()
                ->cleanProject()
                ->deleteManifest()
                ->createMD5()
                ->createManifest()
                ->createArchive()
                ->removeBuildDir();
        }
        catch(EcrZiperException $e)
        {
            $this->logger->log('ERROR', $e->getMessage());
            $this->logger->writeLog();

            $this->setError('ERROR: '.$e->getMessage());

            return false;
        }

        $this->logger->log('FINISHED');

        $this->logger->writeLog();

        return true;
    }

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
            $this->buildopts[$opt] = (in_array($opt, $buildopts)) ? true : false;
        }

        //-- Init profiler
        $this->buildopts['profiling'] = false;

        if(in_array('profile', $buildopts))
        {
            jimport('joomla.error.profiler');
            $this->profiler = JProfiler::getInstance('EasyZipper');
            $this->buildopts['profiling'] = true;
        }

        return true;
    }

    /**
     * Performs the prebuild actions.
     *
     * @return EcrProjectZiper
     *
     * @throws UnexpectedValueException
     */
    private function performPrebuildActions()
    {
        $this->logger->log('Performing prebuild actions');

        foreach($this->project->actions as $action)
        {
            switch($action->type)
            {
                case 'script' :
                    $command = $action->script;
                    $command = str_replace('${temp_dir}', $this->temp_dir, $command);
                    $command = str_replace('${j_root}', JPATH_ROOT, $command);

                    $this->logger->log('Executing: '.$command);

                    $output = shell_exec($command.' 2>&1');

                    $this->logger->log(trim($output));
                    break;

                default :
                    throw new UnexpectedValueException(__METHOD__.' - unknown action: '.$action->type);
            }
        }

        return $this;
    }

    /**
     * Clean up unwanted stuff.
     *
     * @throws EcrZiperException
     * @return EcrProjectZiper
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
                if(! Jfile::exists($folder.DS.'index.html'))
                {
                    JFile::copy($stdHtmlPath, $folder.DS.'index.html');

                    $cntIndex ++;
                }
            }

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
                    //-- EasyCreator project file for packages goes to packageroot..
                    $dst = $this->temp_dir.DS.'easycreator.xml';
                }
                else
                {
                    throw new EcrZiperException(__METHOD__
                        .' - Neither admin or site dir found - Failed to copy EasyCreator project xml');
                }
            }

            if(! JFile::copy($src, $dst))
                throw new EcrZiperException(__METHOD__.' - '.$src.' => '.$dst, 'Failed to copy EasyCreator project xml');

            $this->logger->log('EasyCreator project xml copied');
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

                    if(! JFile::delete($file))
                    {
                        $this->logger->log('Unable to remove '.$file, 'ERROR');
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Tries to find the package manifest file.
     *
     * @throws EcrZiperException
     * @return EcrProjectZiper
     */
    private function deleteManifest()
    {
        //-- Get an array of all the xml files from the installation directory
        $xmlfiles = JFolder::files($this->temp_dir, '.xml$', 1, true);

        //-- No xml files were found in the install folder
        if(empty($xmlfiles))
        {
            return $this;
        }

        foreach($xmlfiles as $file)
        {
            //-- Is it a valid Joomla! installation manifest file ?
            $manifest = $this->_isManifest($file);

            if(! is_null($manifest))
            {
                //-- Delete manifest file in temp folder
                if(! JFile::delete($file))
                    throw new EcrZiperException(__METHOD__.' - Unable to delete file '.$file);

                $this->logger->log('File deleted '.$file);

                return $this;
            }
        }

        return $this;
    }

    /**
     * Is the xml file a valid Joomla installation manifest file ?.
     *
     * @param string $file An xmlfile path to check
     *
     * @return mixed A SimpleXMLElement, or null if the file failed to parse
     */
    private function _isManifest($file)
    {
        $xml = EcrProjectHelper::getXML($file);

        //-- If we can not load the xml file return null
        if(! $xml)
        {
            return null;
        }

        /*
         * Check for a valid XML root tag.
        */
        if($xml->getName() != 'install'
            && $xml->getName() != 'extension'
        )
        {
            //-- Free up xml parser memory and return null
            unset($xml);

            return null;
        }

        //-- Valid manifest file return the object
        return $xml;
    }

    /**
     * Create the Joomla! manifest.
     *
     * @throws EcrZiperException
     * @return EcrProjectZiper
     */
    private function createManifest()
    {
        $this->logger->log('Starting manifest');

        $manifest = new EcrProjectManifest;

        $this->project->basepath = $this->temp_dir;
        $this->project->creationDate = date('d-M-Y');
        $this->project->isNew = false;

        if(! $manifest->create($this->project))
            throw new EcrZiperException(__METHOD__.' - '.implode("\n", $manifest->getErrors()));

        $this->logger->logFileWrite('manifest.xml', $this->project->basepath.DS.'manifest.xml', $manifest->formatXML());

        return $this;
    }

    /**
     * Set the temp directory.
     *
     * @throws EcrZiperException
     * @return EcrProjectZiper
     */
    private function setTempDir()
    {
        $this->temp_dir = JPath::clean(JFactory::getConfig()->get('tmp_path').DS.uniqid($this->project->comName));

        if(! JFolder::create($this->temp_dir))
            throw new EcrZiperException(__METHOD__.' - Can not create TempDir<br />'.$this->temp_dir);

        $this->logger->log('TempDir created<br />'.$this->temp_dir);

        return $this;
    }

    /**
     * Copy files and folders.
     *
     * @throws EcrZiperException
     * @return EcrProjectZiper
     */
    private function copyCopies()
    {
        foreach($this->project->copies as $copy)
        {
            $dest = (strpos($copy, JPATH_ADMINISTRATOR) === 0) ? 'admin' : 'site';
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

                        $folders = array('data/builds', 'data/deploy', 'data/exports', 'data/logs', 'data/results'
                        , 'data/projects', 'data/sync', 'tests');

                        foreach($folders as $folder)
                        {
                            if(! JFolder::exists($ecrBase.DS.$folder))
                                continue;

                            $files = JFolder::files($ecrBase.DS.$folder, '.', true, true, array('.svn', 'readme.md'));

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
                            }
                        }
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
                            }

                            if($acFound)
                            {
                                $contents = implode('', $buffer);

                                if(JFile::write($file, $contents))
                                {
                                    $this->logger->log('Autocode removed from file '
                                        .str_replace($this->temp_dir.DS, '', $file));
                                }
                            }
                        }
                    }
                }
                else
                {
                    $this->logger->log('SRC: '.$copy.'<br />DST: '.$tmp_dest, 'COPY DIR FAILED');
                }
            }
            else if(file_exists($copy))
            {
                //-- Source is a file
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
                //-- Source does not exist - ABORT - TODO: rollback
                throw new EcrZiperException(__METHOD__.' - File not found: '.$copy);
            }
        }

        return $this;
    }

    /**
     * Copy media files.
     *
     * @return EcrProjectZiper
     */
    private function copyMedia()
    {
        $mediaPath = JPATH_ROOT.DS.'media'.DS.$this->project->comName;

        if(! JFolder::exists($mediaPath))
            return $this;

        $destination = $this->temp_dir.DS.'media';

        if(JFolder::copy($mediaPath, $destination))
        {
            $this->logger->log('Media folder copied to :'.$destination);
        }

        return $this;
    }

    /**
     * Process install files.
     *
     * @throws EcrZiperException
     * @return EcrProjectZiper
     */
    private function processInstall()
    {
        //-- @Joomla!-compat 1.5
        if(! $this->project->JCompat == '1.5')
        {
            return $this;
        }

        $installFiles = EcrProjectHelper::findInstallFiles($this->project);

        if(! count($installFiles['php']))
            return $this;

        $srcDir = $this->temp_dir.DS.'admin';
        $destDir = $this->temp_dir.DS.'install';

        //-- Create 'install' folder in temp dir
        JFolder::create($destDir);

        //-- Copy install files from 'admin' to 'temp'
        foreach($installFiles['php'] as $file)
        {
            $srcPath = $srcDir;
            $srcPath .= ($file->folder) ? DS.$file->folder : '';
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

                //-- Create the folder
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
                $format = ('po' == $this->project->langFormat) ? '.po' : '';
                $compressed = ($this->buildopts['create_md5_compressed']) ? '_compressed' : '';
                $fileContents = JFile::read(ECRPATH_EXTENSIONTEMPLATES.DS.'std'.DS.'md5check'.$compressed.$format.'.php');
                $fileContents = str_replace('<?php', '', $fileContents);
                $this->project->addSubstitute('##ECR_MD5CHECK_FNC##', $fileContents);

                $fileContents = JFile::read(ECRPATH_EXTENSIONTEMPLATES.DS.'std'.DS.'md5check_call'.$format.'.php');
                $fileContents = str_replace('<?php', '', $fileContents);
                $this->project->addSubstitute('##ECR_MD5CHECK##', $fileContents);

                $this->project->addSubstitute('ECR_COM_COM_NAME', $this->project->comName);

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
        }

        //-- Delete install files from 'admin'
        foreach($installFiles['php'] as $file)
        {
            $srcPath = $srcDir;
            $srcPath .= ($file->folder) ? DS.$file->folder : '';
            $srcPath .= DS.$file->name;

            if(! JFile::delete($srcPath))
                throw new EcrZiperException(__METHOD__.' - Delete install file failed: '.$srcPath);

            $this->logger->log('INSTALL FILE DELETED<br />SRC: '.$srcPath);
        }

        return $this;
    }

    /**
     * Copy the package modules.
     *
     * @deprecated in favor for J! 1.6 packages
     * @see        EasyZIPer::copyPackageElements
     *
     * @throws EcrZiperException
     * @return EcrProjectZiper
     */
    private function copyPackageModules()
    {
        if(! count($this->project->modules))
        {
            return $this;
        }

        $this->logger->log('<strong style="color: blue;">Copying Package modules</strong>');

        foreach($this->project->modules as $module)
        {
            $clientPath = ($module->scope == 'admin') ? JPATH_ADMINISTRATOR.DS.'modules' : JPATH_SITE.DS.'modules';

            if(JFolder::copy($clientPath.DS.$module->name, $this->temp_dir.DS.$module->name, '', true))
            {
                $this->logger->log('COPY DIR<br />SRC: '.$clientPath.DS.$module->name
                    .'<br />DST: '.$this->temp_dir.DS.$module->name);
            }
            else
            {
                throw new EcrZiperException(__METHOD__.' - COPY DIR FAILED SRC: '.$clientPath.DS.$module->name
                    .' - DST: '.$this->temp_dir);
            }

            //-- Get the project
            try
            {
                $modProject = EcrProjectHelper::getProject($s);
            }
            catch(Exception $e)
            {
                $this->logger->log('Unable to load the project '.$s.' - '.$e->getMessage(), 'ERROR');

                continue;
            }

            if(! is_array($modProject->langs))
            {
                continue;
            }

            $clientPath = ($module->scope == 'admin') ? JPATH_ADMINISTRATOR.DS.'language' : JPATH_SITE.DS.'language';

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
            }
        }

        return $this;
    }

    /**
     * Copy the package plugins.
     *
     * @deprecated in favor for J! 1.6 packages
     * @see        EasyZIPer::copyPackageElements
     *
     * @return EcrProjectZiper
     */
    private function copyPackagePlugins()
    {
        if(! count($this->project->plugins))
        {
            return $this;
        }

        $this->logger->log('<strong style="color: blue;">Copying Package plugins</strong>');

        foreach($this->project->plugins as $plugin)
        {
            $plgFolderName = 'plg_'.$plugin->scope.'_'.$plugin->name;

            //-- Get the project
            try
            {
                $plgProject = EcrProjectHelper::getProject($plgFolderName);
            }
            catch(Exception $e)
            {
                $this->logger->log('Unable to load the project - Error:'.$e->getMessage(), 'ERROR');

                continue;
            }

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
            }

            if(! count($plgProject->langs))
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
            }
        }

        return $this;
    }

    /**
     * Copy the elements of a package.
     *
     * For Joomla! 1.6 packages only.
     *
     * @throws EcrZiperException
     * @return EcrProjectZiper
     */
    private function copyPackageElements()
    {
        if($this->project->type != 'package')
            return $this;

        if(! count($this->project->elements))
            return $this;

        $this->logger->log('<strong style="color: blue;">Copying Package elements</strong>');

        foreach($this->project->elements as $element => $path)
        {
            $this->ecr_project = JRequest::getCmd('ecr_project');

            //-- Get the project
            try
            {
                $project = EcrProjectHelper::getProject($element);
            }
            catch(Exception $e)
            {
                $this->logger->log('Unable to load the project '.$element.' - '.$e->getMessage(), 'ERROR');

                continue;
            }

            $ziper = new EcrProjectZiper;
            $result = $ziper->create($project, $this->buildopts);
            $files = $ziper->getCreatedFiles();

            if(! count($files))
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
                throw new EcrZiperException(__METHOD__.' - '.sprintf(
                    'Unable to create package %s try to copy from %s to %s'
                    , $element, $src, $dest));
            }
        }

        return $this;
    }

    /**
     * Copy language files.
     *
     * @return EcrProjectZiper
     */
    private function copyLanguage()
    {
        //-- No languages defined
        if(! is_array($this->project->langs))
            return $this;

        //-- Only ini files needs to be copied
        if($this->project->langFormat != 'ini')
            return $this;

        foreach($this->project->langs as $language => $scopes)
        {
            foreach($scopes as $scope)
            {
                $this->logger->log('<strong>Processing language '.$language.' - '.$scope.'</strong>');

                $paths = $this->project->getLanguagePaths($scope);

                if(! is_array($paths))
                    $paths = array($paths);

                if(! count($paths))
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
                }

                if(! $found)
                {
                    $this->logger->log('File: '.$srcPath.'/'.$srcFileName, 'copy failed');
                    $this->setError(sprintf(jgettext('File not found: %s'), $srcPath.'/'.$srcFileName));

                    continue;
                }

                $s = ($scope === 'menu' || $scope === 'sys') ? 'admin' : $scope;
                $tmp_dest = $this->temp_dir.DS.$s.DS.'language'.DS.$language;

                if($this->project->type == 'plugin')
                {
                    $tmp_dest = $this->temp_dir.DS.'site'.DS.'language'.DS.$language;
                }

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
                        $this->setError(sprintf(jgettext('Failed to copy file %s to %s'), $srcPath.'/'.$srcFileName, $tmp_dest.DS.$srcFileName));
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Create a MD5 checksum file.
     *
     * @throws EcrZiperException
     * @return EcrProjectZiper
     */
    private function createMD5()
    {
        $md5Str = '';

        if(! $this->buildopts['create_md5'])
        {
            return $this;
        }

        $fileList = JFolder::files($this->temp_dir, '.', true, true);

        foreach($fileList as $file)
        {
            $file = JPath::clean($file);
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

            $md5Str = str_replace('\\', '/', $md5Str);
        }

        $subDir = (JFolder::exists($this->temp_dir.DS.'admin')) ? 'admin' : 'site';

        //-- @todo temp solution to put the md5 file in a sub folder for J! 1.6 not finding it...
        $subDir .= DS.'install';

        if(! JFile::write($this->temp_dir.DS.$subDir.DS.'MD5SUMS', $md5Str))
            throw new EcrZiperException(__METHOD__.' - Can not create MD5SUMS File');

        $this->logger->logFileWrite('MD5SUMS', $this->temp_dir.DS.'MD5SUMS', $md5Str);

        return $this;
    }

    /**
     * Tiny compression for MD5 files.
     *
     * @param string $path
     *
     * @return string
     */
    private function compressPath($path)
    {
        static $previous = '';

        if(! $previous) //-- Init
        {
            $previous = $path;

            return $previous;
        }

        //-- Same as previous path - maximun compression :)
        $compressed = '=';

        if($previous != $path)
        {
            //-- Different path - too bad..
            $subParts = explode(DS, $path);

            //-- One element at Root level
            $compressed = $path;

            if(count($subParts) > 1) //-- More elements...
            {
                $previousParts = explode(DS, $previous);

                $result = array();

                $foundDifference = false;

                foreach($subParts as $i => $part)
                {
                    if(isset($previousParts[$i])
                        && $part == $previousParts[$i]
                        && ! $foundDifference
                    ) //-- Same as previous sub path
                    {
                        $result[] = '-';
                    }
                    else
                    {
                        //-- Different sub path

                        //-- Add a separator
                        if(count($result) && $result[count($result) - 1] == '-')
                            $result[] = '|';

                        $result[] = $part.DS;

                        $foundDifference = true;
                    }
                }

                //-- Add a separator(no add path)
                if(count($result) && $result[count($result) - 1] == '-')
                    $result[] = '|';

                $compressed = implode('', $result);
            }
        }

        $previous = $path;

        return $compressed;
    }

    /**
     * Create the zip file.
     *
     * @throws EcrZiperException
     * @return EcrProjectZiper
     */
    private function createArchive()
    {
        $zipTypes = array(
            'zip' => 'zip'
        , 'tgz' => 'tar.gz'
        , 'bz' => 'bz2');

        $this->logger->log('Start adding files');

        if($this->build_dir != ECRPATH_BUILDS)
        {
            $zipDir = $this->build_dir.DS.$this->project->version;
        }
        else
        {
            $zipDir = $this->build_dir.DS.$this->project->comName.DS.$this->project->version;
        }

        //-- Build the file list
        $files = JFolder::files($this->temp_dir, '.', true, true);
        $this->logger->log('TOTAL: '.count($files).' files');

        if(! JFolder::exists($zipDir))
        {
            if(! JFolder::create($zipDir))
                throw new EcrZiperException(__METHOD__.' - ERROR creating folder '.$zipDir);
        }

        if(0 === strpos($this->project->getZipPath(), ECRPATH_BUILDS))
        {
            $hrefBase = JURI::root().str_replace(JPATH_ROOT, '', ECRPATH_BUILDS)
                .'/'.$this->project->comName.'/'.$this->project->version;
            $hrefBase = str_replace('/\\', '/', $hrefBase);
            $hrefBase = str_replace('\\', '/', $hrefBase);
        }
        else
        {
            $hrefBase = 'file://'.$this->project->getZipPath().DIRECTORY_SEPARATOR.$this->project->version;
        }

        $customFileName = EcrProjectHelper::formatFileName($this->project, JRequest::getVar('cst_format'));

        $fileName = $this->project->getFileName().$customFileName;

        foreach($zipTypes as $zipType => $ext)
        {
            if(! $this->buildopts['archive_'.$zipType])
            {
                continue;
            }

            $this->logger->log('creating '.$zipType);

            switch($ext)
            {
                case 'zip':

                    //-- Translate win path to unix path - for PEAR..
                    $p = str_replace('\\', '/', $this->temp_dir);

                    if(! EcrArchive::createZip($zipDir.DS.$fileName.'.zip', $files, $p))
                        throw new EcrZiperException(__METHOD__.' - ERROR Packing routine for '.$ext);

                    break;

                case 'bz2':
                    ecrLoadHelper('PEAR');

                    if(! extension_loaded('bz2'))
                    {
                        PEAR::loadExtension('bz2');
                    }

                    if(! extension_loaded('bz2'))
                    {
                        JFactory::getApplication()->enqueueMessage(
                            jgettext('The extension "bz2" couldn\'t be found.'), 'error');
                        JFactory::getApplication()->enqueueMessage(
                            jgettext('Please make sure your version of PHP was built with bz2 support.'), 'error');

                        $this->logger->log('PHP extension bz2 not found', 'PHP ERROR');
                    }
                    else
                    {
                        //-- Translate win path to unix path - for PEAR..
                        $p = str_replace('\\', '/', $this->temp_dir);

                        $result = $archive = EcrArchive::createTgz($zipDir.DS.$fileName.'.'.$ext, $files, 'bz2', $p);

                        if(! $result->listContent())
                            throw new EcrZiperException(__METHOD__.'ERROR Packing routine for '.$ext);
                    }

                    break;

                case 'tar.gz':
                    $result = $archive = EcrArchive::createTgz($zipDir
                        .DS.$fileName.'.'.$ext, $files, 'gz', $this->temp_dir);

                    if(! $result->listContent())
                        throw new EcrZiperException(__METHOD__.'ERROR Packing routine for '.$ext);

                    break;

                default:
                    throw new EcrZiperException(__METHOD__.'undefined packing type '.$ext);
                    break;
            }

            $this->logger->log('Packing routine for '.$ext.' finished');
            $this->downloadLinks[] = $hrefBase.'/'.$fileName.'.'.$ext;
            $this->createdFiles[] = $zipDir.DS.$fileName.'.'.$ext;
        }

        return $this;
    }

    /**
     * Remove the build directory.
     *
     * @throws EcrZiperException
     * @return boolean true on success
     */
    private function removeBuildDir()
    {
        if(ECR_DEBUG)
        {
            $this->logger->log('The build folder<br />'.$this->temp_dir.'<br />will not be deleted in debug mode.');

            return $this;
        }

        if(! JFolder::delete($this->temp_dir))
            throw new EcrZiperException(__METHOD__.'Unable to delete<br />'.$this->temp_dir);

        $this->logger->log('The build folder<br />'.$this->temp_dir.'<br />has been sucessfully deleted.');

        return $this;
    }

    /**
     * Dislays the download links for created packages.
     *
     * @return array
     */
    public function getDownloadLinks()
    {
        return $this->downloadLinks;
    }

    /**
     * Get a list of created files.
     *
     * @return array
     */
    public function getCreatedFiles()
    {
        return $this->createdFiles;
    }

    /**
     * Prints the log.
     *
     * @return string
     */
    public function printLog()
    {
        return $this->logger->printLog();
    }

    /**
     * Get the error log.
     *
     * @return array
     */
    public function getErrorLog()
    {
        return $this->_errors;
    }

    /**
     * Set up the log file.
     *
     * @return \EcrDeployer
     */
    private function setupLog()
    {
        $path = JFactory::getConfig()->get('log_path');

        $fileName = 'ecr_log.php';
        $entry = '';

        if('preserve' == JFactory::getApplication()->input->get('logMode')
            && JFile::exists($path.'/'.$fileName)
        )
        {
            $entry = '----------------------------------------------';
        }
        elseif(JFile::exists($path.'/'.$fileName))
        {
            JFile::delete($path.'/'.$fileName);
        }

        JLog::addLogger(
            array(
                'text_file' => $fileName
            , 'text_entry_format' => '{DATETIME}	{PRIORITY}	{MESSAGE}'
            , 'text_file_no_php' => true
            )
            , JLog::INFO | JLog::ERROR
        );

        if('' != $entry)
            JLog::add($entry);

        return $this;
    }

}//class
