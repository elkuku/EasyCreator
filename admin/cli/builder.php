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

$options = getopt('', array('basedir:'));

count($options) || die('Please specify a build path with the --basedir option');

define('THE_BUILD_PATH', $options['basedir']);

require THE_BUILD_PATH.'/libraries/import.php';

//-- @todo deprecated JRequest..
jimport('joomla.environment.request');
jimport('joomla.application.component.helper');

jimport('joomla.environment.uri');

//-- @todo deprecated JError..
JError::$legacy = false;

/**
 * EasyCreator client builder.
 */
class EcrCliBuilder extends JApplicationCli
{
    /**
     * DoIt
     *
     * @throws Exception
     * @return void
     */
    public function doExecute()
    {
        define('JPATH_BASE', THE_BUILD_PATH);
        define('JPATH_SITE', JPATH_BASE);
        define('JPATH_CACHE', JPATH_BASE.'/cache');
        define('JPATH_ADMINISTRATOR', JPATH_BASE.'/administrator');

        define('JPATH_COMPONENT', JPATH_ADMINISTRATOR.'/components/com_easycreator');
        define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_COMPONENT);

        define('JPATH_INSTALLATION', '');

        require JPATH_BASE.'/configuration.php';
        require JPATH_BASE.'/libraries/cms/version/version.php';

        $jversion = new JVersion;

        define('JVERSION', $jversion->getShortVersion());

        //-- Global constants
        require JPATH_COMPONENT.'/includes/defines.php';

        //-- Global functions
        require JPATH_COMPONENT.'/includes/loader.php';

        $buildOpts = array();

        if($this->input->get('v') || $this->input->get('verbose'))
            $buildOpts[] = 'logging';

        if($this->input->get('list'))
        {
            $this->printList();

            return;
        }

        $projectName = $this->input->get('project');

        if('' == $projectName)
            throw new Exception('Please specify a project with the option --project');

        $project = EcrProjectHelper::getProject($projectName);

        foreach($project->buildOpts as $opt => $v)
        {
            //-- @todo this is ugly..
            if('1' == $v)
                $buildOpts[] = $opt;
        }

        $ziper = new EcrProjectZiper;

        $preset = new EcrProjectModelBuildpreset;

        $ziper->create($project, $preset, $buildOpts);

        $this->out('Finished =;)');
    }

    private function printList()
    {
        $this->out('*** Project List ***');

        $list = EcrProjectHelper::getProjectList();

        foreach($list as $type => $items)
        {
            $this->out('===========================');
            $this->out($type);
            $this->out('===========================');

            foreach($items as $item)
            {
                $this->out($item->name.' ('.$item->comName.')');
            }
        }
    }
}

/**
 * Dummy function.
 *
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
class JUri
{
    /**
     * Dummy.
     *
     * @static
     * @return string
     */
    public static function root()
    {
        return '';
    }
}

try
{
    //-- Execute the application.
    JApplicationCli::getInstance('EcrCliBuilder')->execute();

    exit(0);
}
catch(Exception $e)
{
    //-- An exception has been caught, just echo the message.
    fwrite(STDOUT, $e->getMessage()."\n");

    exit($e->getCode());
}//try
