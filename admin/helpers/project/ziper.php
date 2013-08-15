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
 * @property-read string                     $temp_dir
 * @property-read string                     $logFile
 * @property-read EcrLogger                  $logger
 * @property-read EcrProjectBase             $project
 * @property-read EcrProjectModelBuildpreset $preset
 */
class EcrProjectZiper extends JObject
{
    private $build_dir = '';

    private $temp_dir = '';

    private $logFile = '';

    private $downloadLinks = array();

    private $alternateDownloadLinks = array();

    /**
     * @var array of EcrProjectZiperCreatedfile objects
     */
    private $createdFiles = array();

    /**
     * @var EcrProjectBase
     */
    private $project;

    private $buildopts = array();

    /**
     * @var EcrProjectModelBuildpreset
     */
    private $preset = null;

    /**
     * @var EcrLogger
     */
    private $logger = null;

    private $profiler = null;

    private $runTime = 0;

    private $validBuild = true;

    private $failures = array();

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

        parent::__construct($properties);
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        $time = number_format(microtime(true) - $this->runTime, 2);

        JLog::add(sprintf('|___ Finished in %s sec.', $time));

        //-- Give the logger a chance to finish.
        sleep(1);
    }

    /**
     * Get a property.
     *
     * @param string $property
     *
     * @return mixed|string
     */
    public function __get($property)
    {
        if(in_array($property, array('temp_dir', 'logFile', 'logger', 'project', 'preset')))
        {
            return $this->$property;
        }

        return '';
    }

    /**
     * Create the package.
     *
     * @param EcrProjectBase             $project The project
     * @param EcrProjectModelBuildpreset $preset
     * @param array                      $buildOpts
     *
     * @return bool true on success
     */
    public function create(EcrProjectBase $project, EcrProjectModelBuildpreset $preset, array $buildOpts)
    {
        $this->project = $project;

        $this->preset = $preset;//snew EcrProjectModelBuildpreset($buildOpts);

        //@todo PHP 5.3
        $this->build_dir = $preset->buildFolder ? $preset->buildFolder : $this->project->getZipPath();

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
                ->setTempDir()->logStep(1)
                ->performActions('precopy')->logStep(2)
                ->copyCopies()
                ->copyLanguage()
                ->processInstall()
                ->copyMedia()->logStep(3)
                ->copyPackageElements()->logStep(4)
                ->cleanProject()->logStep(6)
                ->performActions('postcopy')->logStep(7)
                ->deleteManifest()
                ->createMD5()
                ->createManifest()->logStep(8)
                ->createArchive()->logStep(9)
                ->performActions('postbuild')
                ->removeBuildDir()->logStep(10);
        }
        catch(EcrExceptionZiper $e)
        {
            $this->logger->log('ABORT: '.$e->getMessage(), 'ERROR', JLog::ERROR);
            $this->logger->writeLog();

            $this->setError('ERROR: '.$e->getMessage());

            return false;
        }

        $this->logger->log('FINISHED');

        if(false == $this->validBuild)
        {
            $this->logger->log('*** FAILURE ***', '', JLog::ERROR);
            $this->logger->log('The package has NOT been build due to the following errors:', 'error', JLog::ERROR);

            foreach($this->failures as $failure)
            {
                $this->logger->log($failure, '', JLog::ERROR);
            }
        }

        $this->logger->writeLog();

        return true;
    }

    /**
     * @param $message
     */
    public function addFailure($message)
    {
        $this->failures[] = (string)$message;
    }

    public function setInvalid()
    {
        $this->validBuild = false;
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
        , 'archiveZip', 'archiveTgz', 'archiveBz2'
        , 'createIndexhtml', 'removeAutocode', 'includeEcrProjectfile'
        , 'createMD5', 'createMD5Compressed');

        foreach($stdOpts as $opt)
        {
            $this->buildopts[$opt] =(array_key_exists($opt, $buildopts) && (true === $buildopts[$opt]))
                ? true : false;
        }

        //-- Init profiler
        $this->buildopts['profiling'] = false;

        if(in_array('profile', $buildopts))
        {
//            jimport('joomla.error.profiler');
            $this->profiler = JProfiler::getInstance('EasyZipper');
            $this->buildopts['profiling'] = true;
        }

        return true;
    }

    /**
     * Performs the build actions.
     *
     * @param $event
     *
     * @throws UnexpectedValueException
     * @internal param $type
     *
     * @return \EcrProjectZiper
     */
    private function performActions($event)
    {
        if(0 == count($this->preset->actions))
            return $this;

        $this->logger->log('Performing build actions type: '.$event);

        $cnt = 0;

        $reqActions = JFactory::getApplication()->input->get('actions', array(), 'array');

        /* @var EcrProjectAction $action */
        foreach($this->preset->actions as $i => $action)
        {
            if($action->event != $event)
                continue;

            if(false == in_array($i, $reqActions))
                continue;

            $action->run($this);

            $cnt ++;
        }

        $this->logger->log(sprintf('%d actions executed', $cnt));

        return $this;
    }

    /**
     * Clean up unwanted stuff.
     *
     * @throws EcrExceptionZiper
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

        if($this->preset->createIndexhtml)
        {
            foreach($folders as $folder)
            {
                if(false == Jfile::exists($folder.DS.'index.html'))
                {
                    JFile::copy($stdHtmlPath, $folder.DS.'index.html');

                    $cntIndex ++;
                }
            }

            $this->logger->log(sprintf('%s index.html files created', $cntIndex));
        }

        if($this->preset->removeAutocode)
        {
            /**
             * @todo remove AutoCode
             */
        }

        //-- If we are building a "package package", override the preset settings from the package
        //-- with the setting from the request.
        if($this->preset->includeEcrProjectfile
            && true == $this->buildopts['include_ecr_projectfile'])
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
                    throw new EcrExceptionZiper(__METHOD__
                        .' - Neither admin or site dir found - Failed to copy EasyCreator project xml');
                }
            }

            if(false == JFile::copy($src, $dst))
                throw new EcrExceptionZiper(sprintf('%s - %s &rArr; %s Failed to copy EasyCreator project xml'
                    , __METHOD__, $src, $dst));

            $this->logger->log('EasyCreator project xml copied');
        }

        //-- Look for unwanted files
        $unwanted = array(
            // Windows :(
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

                    if(false == JFile::delete($file))
                    {
                        $this->logger->log('Unable to remove '.$file, 'ERROR');
                    }
                }
            }
        }

        //-- Clean up language version files
        $paths = array('admin/language', 'site/language');

        $cnt = 0;

        foreach($paths as $path)
        {
            if(false == JFolder::exists($this->temp_dir.'/'.$path))
                continue;

            $files = JFolder::files($this->temp_dir.'/'.$path, '.', true, true);

            foreach($files as $file)
            {
                if('ini' != JFile::getExt($file) && 'html' != JFile::getExt($file))
                {
                    if(false == JFile::delete($file))
                        throw new EcrExceptionZiper(__METHOD__.' - Can not delete language version file: '.$file);

                    $cnt ++;
                }
            }
        }

        $this->logger->log(sprintf('%d language version files deleted', $cnt));

        return $this;
    }

    /**
     * Tries to find the package manifest file.
     *
     * @throws EcrExceptionZiper
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

            if(false == is_null($manifest))
            {
                //-- Delete manifest file in temp folder
                if(false == JFile::delete($file))
                    throw new EcrExceptionZiper(__METHOD__.' - Unable to delete file '.$file);

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
        if(false == $xml)
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
     * @throws EcrExceptionZiper
     * @return EcrProjectZiper
     */
    private function createManifest()
    {
        $this->logger->log('Starting manifest');

        $manifest = new EcrProjectManifest;

        $this->project->basepath = $this->temp_dir;
        $this->project->creationDate = date('d-M-Y');
        $this->project->isNew = false;

        if(false == $manifest->create($this->project))
            throw new EcrExceptionZiper(__METHOD__.' - '.implode("\n", $manifest->getErrors()));

        $this->logger->logFileWrite('manifest.xml', $this->project->basepath.DS.'manifest.xml', $manifest->formatXML());

        return $this;
    }

    /**
     * Set the temp directory.
     *
     * @throws EcrExceptionZiper
     * @return EcrProjectZiper
     */
    private function setTempDir()
    {
        $this->temp_dir = JPath::clean(JFactory::getConfig()->get('tmp_path').DS.uniqid($this->project->comName));

        if(false == JFolder::create($this->temp_dir))
            throw new EcrExceptionZiper(__METHOD__.' - Can not create TempDir<br />'.$this->temp_dir);

        $this->logger->log('TempDir created<br />'.$this->temp_dir);

        return $this;
    }

    /**
     * Copy files and folders.
     *
     * @throws EcrExceptionZiper
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

                    if($this->preset->removeAutocode)
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
                throw new EcrExceptionZiper(__METHOD__.' - File not found: '.$copy);
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

        if(false == JFolder::exists($mediaPath))
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
     * @throws EcrExceptionZiper
     *
     * @return EcrProjectZiper
     */
    private function processInstall()
    {
        $installFiles = EcrProjectHelper::findInstallFiles($this->project);

        if(0 == count($installFiles['php']))
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

            if($this->buildopts['createMD5'])
            {
                $format = ('po' == $this->project->langFormat) ? '.po' : '';
                $compressed = ($this->buildopts['createMD5Compressed']) ? '_compressed' : '';
                $fileContents = JFile::read(
                    ECRPATH_EXTENSIONTEMPLATES.DS.'std'.DS.'md5check'.$compressed.$format.'.php');
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

            if(false == JFile::delete($srcPath))
                throw new EcrExceptionZiper(__METHOD__.' - Delete install file failed: '.$srcPath);

            $this->logger->log('INSTALL FILE DELETED<br />SRC: '.$srcPath);
        }

        return $this;
    }

    /**
     * Copy the elements of a package.
     *
     * @throws EcrExceptionZiper
     * @return EcrProjectZiper
     */
    private function copyPackageElements()
    {
        if($this->project->type != 'package')
            return $this;

        if(0 == count($this->project->elements))
            return $this;

        $this->logger->log('Copying Package elements');

        foreach($this->project->elements as $element => $path)
        {
            $this->ecr_project = JFactory::getApplication()->input->get('ecr_project');

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
            $result = $ziper->create($project, $project->getPreset(), $this->buildopts);
            $files = $ziper->getCreatedFiles();

            if(0 == count($files))
            {
                $this->logger->log(sprintf('No packages files have been created for project %s', $element)
                    , 'ERROR', JLog::WARNING);

                continue;
            }

            $src = $files[0]->path;
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
                throw new EcrExceptionZiper(__METHOD__.' - '.sprintf(
                    'Unable to create package %s try to copy from %s to %s'
                    , $element, $src, $dest));
            }
        }

        return $this;
    }

    /**
     * Copy language files.
     *
     * @deprecated
     *
     * @return EcrProjectZiper
     */
    private function copyLanguage()
    {
        //-- No languages defined
        if(false == is_array($this->project->langs))
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

                if(false == is_array($paths))
                    $paths = array($paths);

                if(0 == count($paths))
                    continue;

                $found = false;
                $srcPath = '';
                $srcFileName = '';

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

                if(false == $found)
                {
                    $this->logger->log('File: '.$srcPath.'/'.$srcFileName, 'copy failed');
                    $this->setError(sprintf(jgettext('File not found: %s'), $srcPath.'/'.$srcFileName));

                    continue;
                }

                $s = $scope;

                if(in_array($scope, array('menu', 'sys')))
                {
                    $s = ('admin' == $this->project->scope) ? 'admin' : 'site';
                }

                //$s = ($scope === 'menu' || $scope === 'sys') ? 'admin' : $scope;
                $tmp_dest = $this->temp_dir.DS.$s.DS.'language'.DS.$language;

                if($this->project->type == 'plugin')
                {
                    $tmp_dest = $this->temp_dir.DS.'site'.DS.'language'.DS.$language;
                }

                if(file_exists($tmp_dest.DS.$srcFileName))
                {
                    $this->logger->log('File: '.$srcFileName.' already exists');
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
     * @throws EcrExceptionZiper
     * @return EcrProjectZiper
     */
    private function createMD5()
    {
        if( ! $this->preset->createMD5)
            return $this;

        $md5Str = '';

        $fileList = JFolder::files($this->temp_dir, '.', true, true);

        foreach($fileList as $file)
        {
            $file = JPath::clean($file);

            if($this->preset->createMD5Compressed)
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

        if(false == JFile::write($this->temp_dir.DS.$subDir.DS.'MD5SUMS', $md5Str))
            throw new EcrExceptionZiper(__METHOD__.' - Can not create MD5SUMS File');

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

        if('' == $previous) //-- Init
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
     * @throws EcrExceptionZiper
     * @return EcrProjectZiper
     */
    private function createArchive()
    {
        if(false == $this->validBuild)
            return $this;

        $zipTypes = array(
            'Zip' => 'zip'
        , 'Tgz' => 'tar.gz'
        , 'Bz2' => 'bz2');

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

        if(false == JFolder::exists($zipDir))
        {
            if(false == JFolder::create($zipDir))
                throw new EcrExceptionZiper(__METHOD__.' - ERROR creating folder '.$zipDir);
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

        $customFileName = EcrProjectHelper::formatFileName($this->project
            , JFactory::getApplication()->input->getString('cst_format'));

        $fileName = $this->project->getFileName().$customFileName;

        foreach($zipTypes as $zipType => $ext)
        {
            if(false == $this->preset->{'archive'.$zipType})
                continue;

            $this->logger->log('creating '.$zipType);

            switch($ext)
            {
                case 'zip':

                    //-- Translate win path to unix path - for PEAR..
                    $p = str_replace('\\', '/', $this->temp_dir);

                    if(false == EcrArchive::createZip($zipDir.DS.$fileName.'.zip', $files, $p))
                        throw new EcrExceptionZiper(__METHOD__.' - ERROR Packing routine for '.$ext);

                    break;

                case 'bz2':
                    ecrLoadHelper('PEAR');

                    if(false == extension_loaded('bz2'))
                    {
                        PEAR::loadExtension('bz2');
                    }

                    if(false == extension_loaded('bz2'))
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

                        if( ! $result->listContent())
                            throw new EcrExceptionZiper(__METHOD__.'ERROR Packing routine for '.$ext);
                    }

                    break;

                case 'tar.gz':
                    $result = $archive = EcrArchive::createTgz($zipDir
                        .DS.$fileName.'.'.$ext, $files, 'gz', $this->temp_dir);

                    if( ! $result->listContent())
                        throw new EcrExceptionZiper(__METHOD__.'ERROR Packing routine for '.$ext);

                    break;

                default:
                    throw new EcrExceptionZiper(__METHOD__.'undefined packing type '.$ext);
                    break;
            }

            $this->logger->log('Packing routine for '.$ext.' finished');
            $this->downloadLinks[] = $hrefBase.'/'.$fileName.'.'.$ext;

            /*
            $f = new EcrProjectZiperCreatedfile($zipDir.DS.$fileName.'.'.$ext);
            $this->createdFiles[] = $zipDir.DS.$fileName.'.'.$ext;
            $f = new EcrProjectZiperCreatedfile($zipDir.DS.$fileName.'.'.$ext);
            */

            $this->createdFiles[] = new EcrProjectZiperCreatedfile($zipDir.DS.$fileName.'.'.$ext
                , $hrefBase.'/'.$fileName.'.'.$ext);
        }

        return $this;
    }

    /**
     * Remove the build directory.
     *
     *
     * @throws EcrExceptionZiper
     * @return \EcrProjectZiper
     */
    private function removeBuildDir()
    {
        if(ECR_DEBUG)
        {
            $this->logger->log('The build folder will not be deleted in debug mode.<br />'
                .$this->temp_dir, '', JLog::WARNING);

            return $this;
        }

        if(false == JFolder::delete($this->temp_dir))
            throw new EcrExceptionZiper(__METHOD__.'Unable to delete<br />'.$this->temp_dir);

        $this->logger->log('The build folder has been sucessfully deleted.');

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
     * @return array of EcrProjectZiperCreatedfile objects
     */
    public function getCreatedFiles()
    {
        return $this->createdFiles;
    }

    /**
     * Modifies a download link.
     *
     * @param integer $index
     *
     * @param string  $link
     *
     * @throws UnexpectedValueException
     * @return void
     */
    public function setAlternateDownloadLink($index, $link)
    {
        if(false == array_key_exists($index, $this->downloadLinks))
            throw new UnexpectedValueException(sprintf('%s - The download index %s does not exist'
            ,__METHOD__, $index));

        $this->alternateDownloadLinks[$index] = $link;
    }

    /**
     * Get an alternate download link.
     *
     * @param integer $index
     *
     * @return string
     */
    public function getAlternateDownloadLink($index)
    {
        return (array_key_exists($index, $this->alternateDownloadLinks))
            ? $this->alternateDownloadLinks[$index] : '';
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
        static $setUp = false;

        if(true == $setUp)
            return $this;

        $setUp = true;

        $path = JFactory::getConfig()->get('log_path');

        $fileName = 'ecr_log.php';
        $entry = '';

        $this->logFile = $path.DIRECTORY_SEPARATOR.$fileName;

        if('preserve' == JFactory::getApplication()->input->get('logMode')
            && JFile::exists($this->logFile)
        )
        {
            $entry = '----------------------------------------------';
        }
        else if(JFile::exists($this->logFile))
        {
            JFile::delete($this->logFile);
        }

        JLog::addLogger(
            array(
                'text_file' => $fileName
            , 'text_entry_format' => '{DATETIME}	{PRIORITY}	{MESSAGE}'
            , 'text_file_no_php' => true
            )
            , JLog::INFO | JLog::WARNING | JLog::ERROR
        );

        if('' != $entry)
            JLog::add($entry);

        //-- Init progress bar log file
        $s = '0';

        JFile::write($path.'ecr_steplog.txt', $s);

        return $this;
    }

    /**
     * Log a step for the progress bar.
     *
     * @param $num
     *
     * @return \EcrProjectZiper
     */
    private function logStep($num)
    {
        $path = JFactory::getConfig()->get('log_path');

        $s = (int)$num * 10;

        JFile::write($path.'/ecr_steplog.txt', $s);

        return $this;
    }
}
