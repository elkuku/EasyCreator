<?php
// @codingStandardsIgnoreStart

/**
 * @version $Id: scanner.php 298 2010-12-17 04:41:07Z elkuku $
 * @package    g11n
 * @subpackage	Utilities
 * @author		Nikolai Plath (elkuku) {@link http://www.nik-it.de NiK-IT.de}
 * @author		Created on 10-Sep-2010
 */

error_reporting(E_ALL | E_STRICT);

define('JPATH_BASE', 1);//to include the JVersion file
define('DS', DIRECTORY_SEPARATOR);
define('NL', "\n");

include 'evi_lang_checker.php';

$langChecker = new eviLangChecker(dirname(__FILE__));

$languages = $langChecker->getTags();

try
{
    //-- Create an empty array if we have no CLI arguments
    if( ! isset($argv))
    $argv = array();

    $builder = new EviBuilder($argv, $languages);

    echo 'Available languages: '.implode(', ', $languages).BR;

    $args = $builder->getArgs();

    if($args->help)//-- Display help only
    throw new g11nException($builder->printInfo());

    $builder->build();

    $builder->createPackage();

    echo '*** Finished - Life\'s Great =;)'.BR;
}
catch(Exception $e)
{
    echo $e->getMessage().BR;
}//try

/**
 * g11n code scanner.
 *
 * @package g11n
 */
class g11nCodeScanner
{
    private $interface = '';

    private $basePath = '';

    private $resultFileName = 'evi.php';

    private $shortArgs = array(
         'h' => 'help'
         , 'l' => 'lang'
         , 'a' => 'all'
         , 'p' => 'pack'
         , 't' => 'test'
         , 'v' => 'verbose'
         );

         private $argDescs = array(
    'a' =>   'Create all language files |'
    , 'h' => 'Displays this help        |'
    , 'l' => 'Only specifiv language    |'
    , 'p' => 'Creates a zip package     |'
    , 't' => 'Test only                 |'
    , 'v' => 'Verbose mode              |'
    );

    private $template = '';

    private $searches = array();

    private $args = false;

    private $languages = array();

    /**
     * Constructor.
     *
     * @param array $argv Command line args
     * @param array $languages A
     */
    public function __construct($argv = array(), $languages = array())
    {
        if( ! $languages)
        $languages = array('en-GB');

        $this->languages = $languages;

        $this->basePath = dirname(__FILE__);

        $this->parseArgs($argv);

        if(PHP_SAPI == 'cli')
        {
            //-- CLI
            $this->interface = 'cli';

            define('BR', "\n");
        }
        else//
        {
            //-- Other (e.g. screen)
            $this->interface = 'screen';

            define('BR', '<br />');
        }

        echo '*** EviBuilder '.EVI_VERSION.' ***'.BR;

        if($this->getInterface() == 'screen')
        {
            echo '<p>This script can also be used on the command line interface (CLI) - it\'s even better !</p>';
            echo '<strong>Commands:</strong>';

            echo $this->printInfo();

            echo 'Note: you may also pass these arguments to your browser e.g.: <tt>evi_builder.php?-pv</tt>';

            echo '<hr />';
        }

        if( ! file_exists($this->basePath.DS.'evi_template.php'))
        throw new Exception('EVI template file not found');

        $this->template = implode('', file($this->basePath.DS.'evi_template.php'));

        $JVersion = $this->getJoomlaVersion($this->basePath);

        //        $this->searches = array(
        //        "\$eviVersion = '" => EVI_VERSION
        //        , "\$eviForJoomlaVersion = '" => $JVersion->getShortVersion()
        //        , "\$eviHashTable = '" => $this->buildHashTable($this->basePath)
        //        );

        $this->searches = array(
        "\$eviVersion = '" => EVI_VERSION
        , "\$eviForJoomlaVersion = '" => $JVersion->getShortVersion()
        , "\$eviHashTable = '" => $this->buildCompressedHashTable($this->basePath));

        $fileList = array(
         "/* EVI_CSS */" => 'css.css'
         , "/* EVI_JAVASCRIPT */" => 'js.js'
         , "<!-- EVI_CLASS_EVI -->" => 'evi.php'
         , "<!-- EVI_CLASS_LANG -->" => 'lang.php'
         );
         $fileContents = array();

         foreach($fileList as $tag => $fileName)
         {
             $fileContents[$tag] = implode('', file('evi_template_'.$fileName));
         }//foreach

         foreach($fileContents as $tag => $content)
         {
             $this->searches[$tag] = $content;
         }//foreach

         $this->searches["\$eviStrings = array("] = $this->processLanguages();
         $this->searches["\$eviDefinedLanguages = array("] = $this->processDefinedLanguages();
    }//function

    /**
     *
     */
    public function build()
    {
        echo 'Starting the build process...';

        $fileContents = $this->template;

        foreach($this->searches as $search => $replace)
        {
            $fileContents = str_replace($search, $search.$replace, $fileContents);
        }//foreach

        $fh = fopen($this->basePath.DS.$this->resultFileName, 'w');

        if( ! $fh)
        throw new Exception('can\'t open file');

        fwrite($fh, $fileContents);

        fclose($fh);

        echo 'OK'.BR;

        echo 'File has been writte to: '.BR;
        echo $this->basePath.DS.$this->resultFileName.BR;

        if($this->interface == 'screen')
        {
            echo 'Have a look: <a href="'.$this->resultFileName.'">'.$this->resultFileName.'</a>'.BR;
        }
    }//function

    /**
     *
     */
    public function createPackage()
    {
        if( ! $this->args->pack)
        {
            echo '[!] Packing is not enabled - use [-p] --pack'.BR;
            echo str_repeat('-', 80).BR;

            return;
        }

        echo 'Packing it up...';

        $files = array();

        $files[] = $this->resultFileName;
        $files[] = 'evi_LIESMICH_bitte.txt';
        $files[] = 'evi_README.please.txt';

        $zipName = 'evi_for_joomla_'.$this->getJoomlaVersion()->getShortVersion().'_UNZIP_FIRST.zip';

        $zipCommand = 'zip '.$zipName.' '.implode(' ', $files);

        $result = shell_exec($zipCommand);

        if($this->args->verbose)
        {
            echo ($this->interface == 'screen') ? nl2br($result) : $result;
        }

        echo 'OK'.BR;

        if($this->interface == 'screen')
        {
            echo 'Download: <a href="'.$zipName.'">'.$zipName.'</a>'.BR;
        }
        else
        {
            printf('The file %s has been created', $zipName);
            echo BR;
        }

        echo str_repeat('-', 80).BR;
    }//function

    /**
     *
     */
    public function printInfo()
    {
        $info = '';

        $info .= str_repeat('_', 50).BR;
        $info .= " Short Long\t\tDescription               |".BR;
        $info .= str_repeat('_', 50).'|'.BR;

        foreach($this->shortArgs as $short => $long)
        {
            $info .= sprintf(" [-%s] --%s\t\t%s", $short, $long, $this->argDescs[$short]);
            $info .= BR;
        }//foreach

        $info .= str_repeat('_', 50).'|'.BR;

        if($this->interface == 'screen')
        $info = '<pre>'.$info.'</pre>';

        return $info;
    }//function

    /**
     * Parse command line arguments.
     *
     * @author based on a work by {@link:http://pwfisher.com/nucleus/index.php?itemid=45}
     * The 'second part' - quote: "Just for fun, here is a compacted version:"
     *
     * @param array $argv
     *
     * @return void
     */
    private function parseArgs($argv)
    {
        if( ! $argv)
        {
            // @codingStandardsIgnoreStart - use of superglobals

            //--treat $_GET args as CLI args
            foreach($_GET as $k => $v)
            {
                if($v)
                {
                    $argv[] = $k.'='.$v;
                }
                else
                {
                    $argv[] = $k;
                }
            }//foreach

            // @codingStandardsIgnoreEnd
        }

        if($this->interface == 'cli')
        array_shift($argv);

        $args = new cliArgs;

        foreach($argv as $a)
        {
            if(substr($a, 0, 2) == '--')
            {
                $eq = strpos($a, '=');

                if($eq !== false)
                {
                    $args->{substr($a, 2, $eq - 2)} = substr($a, $eq + 1);
                }
                else
                {
                    $k = substr($a, 2);
                    $args->$k = true;
                }
            }
            else if(substr($a, 0, 1) == '-')
            {
                if(substr($a, 2, 1) == '=')
                {
                    $args->{substr($a, 1, 1)} = substr($a, 3);
                }
                else
                {
                    foreach(str_split(substr($a, 1)) as $k)
                    {
                        $args->$k = true;
                    }//foreach
                }
            }
            else
            {
                $args->$a = true;
            }
        }//foreach

        $this->args = $args;

        //-- add long args definitions
        foreach($this->shortArgs as $short => $long)
        {
            if($this->args->$short)
            $this->args->$long = $this->args->$short;
        }//foreach
    }//function

// @codingStandardsIgnoreStart

    /**
     *
     */
    public function getArgs()
    {
        return $this->args;
    }//function

    /**
     *
     */
    public function getInterface()
    {
        return $this->interface;
    }//function

    /**
     * Get the current Joomla! version
     *
     * @return object JVersion
     */
    private function getJoomlaVersion()
    {
        static $JVersion = null;

        if($JVersion)
        return $JVersion;

        echo 'Do you Joomla! ?...';

        $fileName = $this->basePath.DS.'libraries'.DS.'joomla'.DS.'version.php';

        if( ! file_exists($fileName))
        throw new Exception('Seems you\'re not ! - Unable to determine your Joomla! version :('
        .BR.'Please verify the file: '.$fileName);

        require_once $fileName;

        $JVersion = new JVersion;

        printf('Shure - I\'m on %s - aka %s'
        , $JVersion->getShortVersion()
        , $JVersion->getLongVersion());

        echo BR;

        //--Get the installed Joomla! version
        return $JVersion;
    }//function

    /**
     * Build the md5 hash table
     *
     * @return string
     */
    private function buildHashTable()
    {
        $hashTable = '';

        echo 'So, let\'s build the hash table...';

        $fileList = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->basePath));

        $i = 0;

        $blacks = array('.svn'.DS, 'evi_', 'evi'.DS);

        foreach($fileList as $fileName => $file)
        {
            $sub = substr($fileName, strlen($this->basePath) + 1);

            foreach($blacks as $black)
            {
                if(strpos($sub, $black) === 0)
                continue 2;
            }//foreach

            if( ! is_readable($fileName))
            {
                echo 'Please check permissions on file: '.$fileName.BR;

                continue;
            }

            $contents = file_get_contents($fileName);

            //-- Remove line endings to be OS independant =;)
            $contents = str_replace(array("\n", "\r"), '', $contents);

            //-- Create the MD5 hash
            $hash = md5($contents);

            //-- Build the 'relative path'

            $hashTable .= $hash.' '.$sub.NL;

            $i ++;
        }//foreach

        if( ! $hashTable)
        throw new Exception('Can not build the hash table');

        echo 'OK - hashed '.$i.' files.'.BR;
        echo str_repeat('-', 80).BR;

        return $hashTable;
    }//function

    /**
     * This is an @@@ EXPERIMENTAL @@@ function
     *
     * Build the md5 hash table
     *
     * @return string
     */
    private function buildCompressedHashTable()
    {
        $hashTable = '';

        echo 'So let\'s build the hash table...';

        $fileList = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->basePath));

        $i = 0;

        $blacks = array('.svn/', 'evi', '.', 'administrator/components/com_evimoduleexport');

        $previous = '';

        foreach($fileList as $fileName => $file)
        {
            if(strpos($fileName, DS.'.svn'.DS) !== false)
            continue;

            $fName = substr($fileName, strrpos($fileName, DS) + 1);

            //-- Build the 'relative path'
            $sub = substr($fileName, strlen($this->basePath) + 1);
            $sub = substr($sub, 0, strrpos($sub, DS));

            $compressed = $this->compress($sub);

            if( ! is_readable($fileName))
            {
                echo 'Please check permissions on file: '.$fileName.BR;

                continue;
            }

            foreach($blacks as $black)
            {
                if($sub)
                {
                    if(strpos($sub, $black) === 0)
                    {
                        continue 2;
                    }
                }
                else//
                {
                    if(strpos($fName, $black) === 0)
                    {
                        continue 2;
                    }
                }
            }//foreach

            $contents = file_get_contents($fileName);

            //-- Remove line endings to be OS independant =;)
            $contents = str_replace(array("\n", "\r"), '', $contents);

            //-- Create the MD5 hash
            $hash = md5($contents);

            $hashTable .= $hash.' '.str_replace(DS, '/', $compressed).'@'.$file->getFilename().NL;

            $i ++;
        }//foreach

        if( ! $hashTable)
        throw new Exception('Can not build the hash table');

        echo 'OK - '.$i.'files'.BR;
        echo str_repeat('-', 80).BR;

        return $hashTable;
    }//function

    private function compress($path)
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

                foreach($subParts as $i => $part)
                {
                    if(isset($previousParts[$i])
                    && $part == $previousParts[$i]) //-- Same as previous sub path
                    {
                        $result[] = '-';
                    }
                    else //-- Different sub path
                    {
                        if(count($result) && $result[count($result) - 1] == '-')
                        $result[] = '|'; //-- Add a separator

                        $result[] = $part.DS;
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

    private function decompress($path)
    {
        static $previous = '';

        if( ! $previous) //-- Init
        {
            $previous = $path;

            return $previous;
        }

        $decompressed = $previous;//-- Same as previous path - maximun compression :)

        if($path != '=') //-- Different path - too bad..
        {
            $pos = strpos($path, '|');//-- Separates previous path info from new path

            if($pos)
            {
                $command = substr($path, 0, $pos);

                $c = count(explode('-', $command)) - 1;

                $parts = explode('/', $previous);

                $decompressed = '';

                for($i = 0; $i < $c; $i++)
                {
                    $decompressed .= $parts[$i].'/';
                }//for

                $addPath = substr($path, $pos + 1);

                $decompressed .= $addPath;

                $decompressed = trim($decompressed, '/');

                $previous = $decompressed;

                return $decompressed;
            }

            $decompressed = $path;
        }

        $decompressed = trim($decompressed, '/');

        $previous = $decompressed;

        return $decompressed;
    }//function

    private function processDefinedLanguages()
    {
        $ls = array('en-GB');

        foreach($this->languages as $lang)
        {
            if($lang != 'en-GB')
            {
                $ls[] = $lang;
            }
        }//foreach

        return "'".implode("', '", $ls)."'";
    }//function

    private function processLanguages()
    {
        $fields = array();

        foreach($this->languages as $lang)
        {
            $fileName = $this->basePath.DS.'evi_'.$lang.'.ini';

            if( ! file_exists($fileName))
            throw new Exception('language file not found');

            $lines = file($fileName);

            $field = '';

            foreach($lines as $line)
            {
                $line = trim($line);

                //-- Blank
                if( ! $line)
                continue;

                //-- Comment
                if(strpos($line, '#') === 0)
                continue;

                if(strpos($line, ';') === 0)
                continue;

                $pos = strpos($line, '=');

                //-- Other invalid ?
                if( ! $pos)
                continue;

                $key = trim(substr($line, 0, $pos));
                $value = trim(substr($line, $pos + 1));

                $field .= $key.' = '.$value."\n";
            }//foreach

            $tag = substr($lang, 0, 2);

            $fields[] = "'$lang' => '".$field."'";
        }//foreach

        return implode(",\n", $fields);
    }//function
}//class

/**
 * Helper class to process CLI arguments.
 *
 * @package g11n
 */
class cliArgs
{
    public function __set($k, $v)
    {
        $this->$k = $v;
    }//function

    public function __get($k)
    {
        if(isset($this->$k)) return $this->$k;

        return false;
    }//function
}//class
