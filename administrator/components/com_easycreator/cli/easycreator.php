#!/usr/bin/php
<?php
/**
 * EasyCreator CLI builder.
 *
 * This script must be called from the command line.
 *
 *      --basedir   The Joomla! root directory of your extension.
 *                  EasyCreator must be installed there,
 *
 *      --project   The name of your project e.g. com_mycomponent
 *
 * -v   --verbose   Be verbose.
 *
 * Other build options are taken from the project settings.
 *
 * @todo        to be implemented:
 *         'files'
, 'archiveZip', 'archiveTgz', 'archiveBz2'
, 'create_indexhtml', 'remove_autocode', 'include_ecr_projectfile'
, 'create_md5', 'create_md5_compressed');
 *
 * @package     EasyCreator
 * @subpackage  CLI
 *
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

'cli' == PHP_SAPI || die('This script must be executed from the command line.');

version_compare(PHP_VERSION, '5.3', '>=') || die('This script requires PHP >= 5.3');

define('_JEXEC', 1);

define('ECR_DEBUG', 0);

ini_set('error_reporting', - 1);

// Bootstrap the application.
require getenv('JOOMLA_PLATFORM_PATH').'/libraries/import.php';


/*
 * Some ugly defines..
 */
define('JPATH_BASE', dirname(__DIR__));

//define('JPATH_ADMINISTRATOR', JPATH_BASE.'/administrator');

//define('JPATH_COMPONENT', JPATH_ADMINISTRATOR.'/components/com_easycreator');
define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_BASE);

define('JVERSION', '0.0.0');//:P



/**
     * EasyCreator client builder.
     */
class EasyCreator extends JApplicationCli
{
    /**
     * DoIt
     *
     * @throws Exception
     * @return void
     */
    public function doExecute()
    {
        require JPATH_BASE.'/helpers/loader.php';
        require JPATH_BASE.'/includes/defines.php';

        jimport('joomla.filesystem.folder');

        $this->input->set('ecr_project', 'com_fuuuschubidu');

        $builder = new EcrProjectBuilder;

        $type = 'aaa';//getCmd('tpl_type');
        $name = 'bbb';//getCmd('tpl_name');
        $comName = 'ccc';//getCmd('com_name');

        if( ! $newProject = $builder->build($type, $name, $comName))
        {
            //-- Error
            EcrHtml::message('An error happened while creating your project', 'error');
            JFactory::getApplication()->enqueueMessage(jgettext('An error happened while creating your project'), 'error');
            $builder->printErrors();

            EcrHtml::formEnd();

            return;
        }

        if('test' == JFactory::getApplication()->input->get('ecr_test_mode'))
        {
            //-- Exiting in test mode
            echo '<h2>Exiting in test mode...</h2>';

            echo $builder->printLog();

            $builder->printErrors();

            EcrHtml::formEnd();

            return;
        }

        $ecr_project = JFile::stripExt($newProject->getEcrXmlFileName());

        $uri = 'index.php?option=com_easycreator&controller=stuffer&ecr_project='.$ecr_project;

        $this->setRedirect($uri, jgettext('Your project has been created'));

        echo ECRPATH_DATA;
        $project = EcrProjectHelper::getProject();

        var_dump($project);
    }
}

/**
 * @param $string
 *
 * @return mixed
 */
function jgettext($string)
{
    return $string;
}

/**
 * Dummy.
 */
class JComponentHelper
{
    /**
     * @param $dummy
     *
     * @return JComponentHelper
     */
    public static function getParams($dummy)
    {
        return new JComponentHelper;
    }

    /**
     * @param $whatever
     *
     * @return string
     */
    public function get($whatever)
    {
        return '';
    }
}

/**
 * A.
 */
class JPath
{
    /**
     * @param $input
     *
     * @return mixed
     */
    public static function clean($input)
    {
        return $input;
    }
}

/*
 * Main.
 */

try
{
    //-- Execute the application.
    $application = JApplicationCli::getInstance('EasyCreator');

    JFactory::$application = $application;

    $application->execute();

    exit(0);
}
catch(Exception $e)
{
    //-- An exception has been caught, just echo the message.
    fwrite(STDOUT, $e->getMessage()."\n");

    echo $e->getTraceAsString();

    exit($e->getCode());
}//try
