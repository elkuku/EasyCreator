<?php
/**
 * THIS FILE WILL BE CALLED DIRECTLY !!
 *
 *
 * @description scans the directory /libraries/joomla/* and lists all
 * the found classes and their methods whith path and line numbers.
 *
 * It then scans the directories under /libraries/joomla/ and writes
 * the output to a text file called jmethodlist_JVERSION_.txt
 *
 * This file must be placed in joomla root directory.
 *
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 06-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

error_reporting(E_ALL);

ob_start();

define('DEBUG', 0);
define('DS', DIRECTORY_SEPARATOR);
define('BR', '<br />');
define('NL', "\n");

$J__ROOT = realpath(dirname(__FILE__).DS.'..'.DS.'..'.DS.'..'.DS.'..');

if( ! file_exists($J__ROOT.DS.'configuration.php'))
{
    die('Configuration file NOT FOUND in: '.$J__ROOT.DS.'configuration.php');
}

$basePath = $J__ROOT.DS.'libraries'.DS.'joomla';

define('JPATH_SITE', '');
define('JPATH_ROOT', '');
define('JPATH_ADMINISTRATOR', '');

define('_JEXEC', '');
define('JPATH_PLATFORM', '');
define('JDEBUG', '');

define('JPATH_BASE', '');

define('JPATH_LIBRARIES', $J__ROOT.DS.'libraries');

if( ! file_exists(JPATH_LIBRARIES.DS.'joomla'.DS.'base'.DS.'object.php'))
{
    die('Joomla libs NOT FOUND');
}

if(file_exists(JPATH_LIBRARIES.DS.'joomla'.DS.'version.php'))
{
    include_once JPATH_LIBRARIES.DS.'joomla'.DS.'version.php';
}
else if(file_exists($J__ROOT.DS.'includes'.DS.'version.php'))
{
    include_once $J__ROOT.DS.'includes'.DS.'version.php';
}
else if(file_exists(JPATH_LIBRARIES.'/cms/version/version.php'))
{
    include_once JPATH_LIBRARIES.'/cms/version/version.php';
}
else
{
    echo $J__ROOT.DS.'includes'.DS.'version.php';
    die('No Joomla! version file found :(');
}

require_once JPATH_LIBRARIES.DS.'joomla'.DS.'base'.DS.'object.php';
require_once JPATH_LIBRARIES.DS.'joomla'.DS.'base'.DS.'observer.php';
require_once JPATH_LIBRARIES.DS.'joomla'.DS.'base'.DS.'observable.php';
require_once JPATH_LIBRARIES.DS.'joomla'.DS.'event'.DS.'event.php';
require_once JPATH_LIBRARIES.DS.'joomla'.DS.'document'.DS.'renderer.php';
require_once JPATH_LIBRARIES.DS.'joomla'.DS.'registry'.DS.'registry.php';

require_once JPATH_LIBRARIES.DS.'phpmailer'.DS.'phpmailer.php';

$prevIncluded = array('JObject', 'JObservable', 'JObserver', 'JDocumentRenderer', 'JRequest'
, 'JEvent', 'JRegistry', 'JVersion');

$v = new JVersion;

switch($v->RELEASE)
{
    case '1.5':
        include_once JPATH_LIBRARIES.DS.'pattemplate'.DS.'patTemplate'.DS.'Module.php';
        include_once JPATH_LIBRARIES.DS.'pattemplate'.DS.'patTemplate'.DS.'Function.php';
        include_once JPATH_LIBRARIES.DS.'pattemplate'.DS.'patTemplate'.DS.'Modifier.php';
        include_once JPATH_LIBRARIES.DS.'pattemplate'.DS.'patTemplate.php';

        include_once JPATH_LIBRARIES.DS.'phpgacl'.DS.'gacl.php';
        include_once JPATH_LIBRARIES.DS.'phpgacl'.DS.'gacl_api.php';

        break;

    case '1.6':
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'tablenested.php';

        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'base'.DS.'adapterinstance.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'application'.DS.'component'.DS.'model.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'application'.DS.'component'.DS.'modelform.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'form'.DS.'helper.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'form'.DS.'formfield.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'updater'.DS.'updateadapter.php';

        $prevIncluded = $prevIncluded + array('JTable', 'JTableNested', 'JAdapterInstance'
        , 'JFormField', 'JUpdateAdapter');
        break;

    case '1.7':
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'databasequery.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'tablenested.php';

        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'base'.DS.'adapterinstance.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'application'.DS.'component'.DS.'model.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'application'.DS.'component'.DS.'modelform.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'form'.DS.'helper.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'form'.DS.'formfield.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'updater'.DS.'updateadapter.php';

        $prevIncluded = $prevIncluded + array('JDatabaseQuery', 'JTable', 'JTableNested'
        , 'JAdapterInstance', 'JFormField', 'JUpdateAdapter');
        break;

    case '2.5':
        //include_once JPATH_LIBRARIES.DS.'loader.php';
        //include_once JPATH_LIBRARIES.DS.'cms'.DS.'loader.php';

        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'database.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'query.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'table.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'tablenested.php';

        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'base'.DS.'adapterinstance.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'application'.DS.'component'.DS.'model.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'application'.DS.'component'.DS.'modelform.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'form'.DS.'helper.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'form'.DS.'field.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'updater'.DS.'updateadapter.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'log'.DS.'log.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'log'.DS.'entry.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'utilities'.DS.'date.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'application'.DS.'cli.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'application'.DS.'daemon.php';

        //-- M$ - really :(
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'database'.DS.'sqlsrv.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'database'.DS.'database'.DS.'sqlsrvquery.php';

        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'string'.DS.'string.php';
        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'github'.DS.'object.php';

        include_once JPATH_LIBRARIES.DS.'joomla'.DS.'http'.DS.'http.php';

        //-- juhuuu
//        include_once JPATH_LIBRARIES.DS.'phputf8'.DS.'ucfirst.php';

        $prevIncluded = $prevIncluded + array('JDatabaseQuery', 'JTable', 'JTableNested'
        , 'JAdapterInstance', 'JFormField', 'JUpdateAdapter', 'JLog', 'JLogEntry', 'JDate'
        , 'JApplicationDaemon', 'JApplicationCli', 'JDatabaseSQLSrv', 'JDatabase', 'JDatabaseQuerySQLSrv'
        , 'JString', 'JGithubObject', 'JHttp');

        break;

        default:
        die('Unsupported Joomla! version: '.$v->RELEASE);
        break;
}//switch

/*
 * Post includes...
 */
require_once JPATH_LIBRARIES.DS.'joomla'.DS.'environment'.DS.'request.php';


$folders = EasyFolder::folders($basePath);
array_unshift($folders, '');//add 'base folder'
$folders[] = 'xxxxx';//for custom class list

$JMethods = array();
$classes = array();
$packages = array();

for($i = 0; $i < count($folders); $i++)
{
    if($folders[$i] == 'xxxxx')
    {
        $files = array('xxxxx');
    }
    else
    {
        $p = ($folders[$i]) ? $basePath.DS.$folders[$i] : $basePath;

        if($folders[$i])
        {
            $files = EasyFolder::files($p, 'php', true, true, $basePath);
        }
        else
        {
            $files = EasyFolder::files($p, 'php', false, true, $basePath);
        }
    }

    foreach($files as $file)
    {
        echo (DEBUG == 2)?'<hr />FILE: '.str_replace($basePath.DS, '', $file).BR:'';

        if($file == 'xxxxx')
        {
            //-- Custom class list - files included before
            $foundClasses = $prevIncluded;
        }
        else
        {
            $allClasses = get_declared_classes();

            if( ! $allClasses)
            {
                $allClasses = array();
            }

            if(strpos($file, 'import.php'))
            continue;

            if(strpos($file, 'php50x.php'))
            continue;

            /*
             * @todo J 1.6 BUG
             * 1) Fatal error: Class JFormFieldMediamanager contains 1 abstract method and
             * must therefore be declared abstract or implement the remaining methods
             * (JFormField::getInput) in libraries/joomla/form/fields/mediamanager.php on line 76
             * 2) Warning: require_once() [function.require-once]: Filename cannot be empty
             * in libraries/joomla/form/fields/menu.php on line 18
             * 3) Warning: require_once() [function.require-once]: Filename cannot be empty
             * in libraries/joomla/form/fields/menuitem.php on line 18
             */
            if(strpos($file, 'mediamanager.php'))
            continue;

            if(strpos($file, 'menu.php'))
            continue;

            if(strpos($file, 'menuitem.php'))
            continue;

            //-- Not needed
            if(strpos($file, 'garbagecron.php'))
            continue;

            if(strpos($file, 'stringnormalize.php'))
                continue;

            if(strpos($file, 'utilities'.DS.'string.php'))
                continue;

            /*
             * INCLUDE THE FILE
             */
            include_once $file;
            $foundClasses = array_diff(get_declared_classes(), $allClasses);

            //-- Exeptions from the rules..
            if( ! count($foundClasses))
            {
                $fileName = $file;

                if($file == 'methods.php')
                {
                    $foundClasses = array('JRoute', 'JText');
                }

                if( ! count($foundClasses))
                {
                    echo (DEBUG == 2) ? sprintf('<h3>no classes found in -- %s</h3>'
                    , str_replace($basePath.DS, '', $file)) : '';
                }
            }
        }

        foreach($foundClasses as $c)
        {
            echo (DEBUG == 2)? '<span style="background-color: yellow;">CLASS: '.$c.'</span>'.BR:'';
            $class = new ReflectionClass($c);
            $cl = new stdClass;

            $cl->comment = $class->getDocComment();
            $comment = explode(NL, $cl->comment);
            $searches = array('static', 'subpackage', 'since');
            $subPackage = '';

            foreach($comment as $co)
            {
                foreach($searches as $search)
                {
                    if(strpos($co, '@'.$search))
                    {
                        if($search == 'subpackage')
                        {
                            $p = strpos($co, $search);
                            $subPackage = trim(substr($co, strpos($co, $search) + strlen($search)));

                            if( ! in_array($subPackage, $packages))
                            {
                                $packages[] = $subPackage;
                            }
                        }
                    }
                }//foreach
            }//foreach

            $ms = array();

            $methods = $class->getMethods();

            foreach($methods as $method)
            {
                $rr = $method->getDeclaringClass();

                if($method->getDeclaringClass()->getName() != $c)
                continue;

                if(substr($method->name, 0, 1) == '_'
                || substr($method->name, 0, 2) == '__'
                && $method->name != '_')
                {
                    continue;
                }

                echo (DEBUG == 2)?$method->name.BR:'';

                $m = new stdClass;
                $m->class = $c;
                $m->name = $method->name;
                $s =($file == 'xxxxx') ? $method->getFileName() : $file;
                $m->file = str_replace($basePath.DS, '', $s);
                $m->start = $method->getStartLine();
                $m->end = $method->getEndLine();
                $ms[$method->name] = $m;
                $JMethods[$c][$method->name] = $m;
            }//foreach

            $classes[$c] = new stdClass;
            $classes[$c]->package =($subPackage) ? $subPackage : 'Base';
            $classes[$c]->class = $class;
            $classes[$c]->methods = $ms;
        }//foreach
    }//foreach
}//for

ksort($classes);

$cPath = substr(JPATH_LIBRARIES, 0, strpos(JPATH_LIBRARIES, DS.'administrator'.DS));

$ver = new JVersion;
$ret = '';
$ret .= '<?php'.NL;
$ret .= '/* Class list for Joomla! '.$ver->getShortVersion().' generated on '.date('Y-M-d').' */'.NL;
$ret .= 'function getJoomlaClasses() {'.NL;
$ret .= '$c = array();'.NL;

foreach($classes as $cName => $c)
{
    $p = str_replace($cPath.DS.'libraries'.DS.'joomla'.DS, '', $c->class->getFileName());

    $ms = implode("','", array_keys($c->methods));
    $ret .= '$c'."['$cName']=array('$c->package','$p',array('$ms'));".NL;
}//foreach

$ret .= 'return $c;'.NL;
$ret .= '}';
$ret .= '/* Package list for Joomla! '.$ver->getShortVersion().' generated on '.date('Y-M-d').' */'.NL;
$ret .= 'function getJoomlaPackages() {'.NL;
$ret .= "return array('".implode("','", $packages)."');".NL;
$ret .= '}'.NL;

$fName = 'jclasslist_'.str_replace('.', '_', $ver->getShortVersion()).'.php';

$response = array();
$response['status'] = 1;

if(file_put_contents(dirname(__FILE__).DS.'jclasslists'.DS.$fName, $ret))
{
    echo '*OK*';
    $response['status'] = 0;
}
else
{
    $response['text'] = 'UNABLE TO WRITE THE CLASS LIST FILE !';
}

$response['debug'] = ob_get_contents();
ob_end_clean();

echo json_encode($response);
/*
 * FINISHED..
 */

//######################################
//## helpers..
//######################################
// @codingStandardsIgnoreStart

function jimport() {}
function utf8_ucfirst(){}

/**
 * Enter description here ...
 */
class JLoader
{
    function register() {}

    function import() {}

    function discover() {}
}//class
// @codingStandardsIgnoreEnd

/**
 * Folder operations.
 *
 * @see JFolder
 */
class EasyFolder
{
    /**
     * Utility function to read the files in a folder.
     *
     * @param string       $path The path of the folder to read.
     * @param string       $filter A filter for file names.
     * @param mixed        $recurse True to recursively search into sub-folders, or an
     * integer to specify the maximum depth.
     * @param boolean      $fullpath True to return the full path to the file.
     * @param array|string $stripPath Array with names of files which should not be shown in
     * the result.
     * @param array        $exclude Exclude filter
     *
     * @return array Files in the given folder.
     */
    public static function files($path, $filter = '.', $recurse = false, $fullpath = false
    , $stripPath = '', $exclude = array('.svn', 'CVS'))
    {
        //-- Initialize variables
        $arr = array();

        //-- Is the path a folder?
        if( ! is_dir($path))
        {
            echo 'EasyFolder::files: Path is not a folder: '.$path;

            return false;
        }

        //-- Read the source directory
        $handle = opendir($path);

        while(($file = readdir($handle)) !== false)
        {
            if(($file != '.') && ($file != '..') && ( ! in_array($file, $exclude)))
            {
                $dir = $path.DS.$file;
                $isDir = is_dir($dir);

                if($isDir)
                {
                    if($recurse)
                    {
                        if(is_integer($recurse))
                        {
                            $arr2 = EasyFolder::files($dir, $filter, $recurse - 1, $fullpath);
                        }
                        else
                     {
                            $arr2 = EasyFolder::files($dir, $filter, $recurse, $fullpath);
                        }

                        $arr = array_merge($arr, $arr2);
                    }
                }
                else
              {
                    if(preg_match("/$filter/", $file))
                    {
                        if($fullpath)
                        {
                            $arr[] = $path.DS.$file;
                        }
                        else
                        {
                            $arr[] = $file;
                        }
                    }
                }
            }
        }//while

        closedir($handle);

        asort($arr);

        return $arr;
    }//function

    /**
     * Utility function to read the folders in a folder.
     *
     * @param string $path The path of the folder to read.
     * @param string $filter A filter for folder names.
     * @param mixed $recurse True to recursively search into sub-folders, or an
     * integer to specify the maximum depth.
     * @param boolean $fullpath True to return the full path to the folders.
     * @param array $exclude Array with names of folders which should not be shown in
     * the result.
     *
     * @since 1.5
     * @return array Folders in the given folder.
     */
    public static function folders($path, $filter = '.', $recurse = false
    , $fullpath = false, $exclude = array('.svn', 'CVS'))
    {
        //-- Initialize variables
        $arr = array();

        //-- Is the path a folder?
        if( ! is_dir($path))
        {
            JFactory::getApplication()->enqueueMessage('JFolder::folder: '.jgettext('Path is not a folder').'Path: '.$path, 'error');

            return false;
        }

        //-- Read the source directory
        $handle = opendir($path);

        while(($file = readdir($handle)) !== false)
        {
            if(($file != '.')
            && ($file != '..')
            && ( ! in_array($file, $exclude)))
            {
                $dir = $path.DS.$file;
                $isDir = is_dir($dir);

                if($isDir)
                {
                    //-- Removes filtered directories
                    if(preg_match("/$filter/", $file))
                    {
                        if($fullpath)
                        {
                            $arr[] = $dir;
                        }
                        else
                        {
                            $arr[] = $file;
                        }
                    }

                    if($recurse)
                    {
                        if(is_integer($recurse))
                        {
                            $arr2 = JFolder::folders($dir, $filter, $recurse - 1, $fullpath);
                        }
                        else
                        {
                            $arr2 = JFolder::folders($dir, $filter, $recurse, $fullpath);
                        }

                        $arr = array_merge($arr, $arr2);
                    }
                }
            }
        }//while

        closedir($handle);

        asort($arr);

        return $arr;
    }//function
}//class
