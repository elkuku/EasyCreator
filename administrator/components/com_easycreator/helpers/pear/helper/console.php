<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author		Nikolai Plath
 * @author		Created on 29-Sep-2009
 */

/**
 * Helper to access PEAR console scripts.
 *
 * @package    EasyCreator
 */
class EcrPearHelperConsole extends EcrLogconsole
{
    public $cliBase = '';

    public $validEnv = false;

    public $packages = array();

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->validEnv = $this->checkEnv();
    }

    /**
     * Checks if the environment has PEAR..
     *
     * @return boolean true on success
     */
    public function checkEnv()
    {
        if($this->validEnv)
            return true;

        //-- Check if PEAR is accessible
        @include_once 'System.php';

        if( ! class_exists('System'))
        {
            JFactory::getApplication()->enqueueMessage('PEAR not found', 'error');

            return false;
        }

        return true;
    }//function

    /**
     * Read the installed PEAR packages.
     *
     * @return array Array with package name as index and version as value.
     */
    public function getPackages()
    {
        $pList = $this->cliExec('pear', array('list'));

        $pList = explode(NL, $pList);

        $packages = array();

        $started = false;

        foreach($pList as $package)
        {
            if( ! $started)
            {
                if(strpos($package, 'PACKAGE') === 0)
                {
                    $started = true;
                }

                continue;
            }

            $name = '';
            $version = '';

            $as = explode(' ', $package);

            if(count($as) < 3)
            {
                continue;
            }

            foreach($as as $a)
            {
                if( ! $a)
                    continue;

                if( ! $name)
                {
                    $name = $a;
                }
                else if( ! $version)
                {
                    $version = $a;
                }
            }//foreach

            $packages[$name] = $version;
        }//foreach

        return $packages;
    }//function

    /**
     * Get the version number of a specific package.
     *
     * @param string $package Package name.
     *
     * @return mixed string Version number | boolean false on error
     */
    public function getVersion($package)
    {
        $v = $this->cliExec($package, array('--version'));

        return ($v) ? $v : false;
    }//function

    /**
     * Test a package agains a specific version.
     *
     * @param string $package Package name
     * @param string $version Version to test
     *
     * @return string
     */
    public function testVersion($package, $version)
    {
        $v = $this->cliExec($package, array('--version'));

        if( ! $v)
        {
            return '<strong style="color: red">'.jgettext('Not found').'</strong>';
        }

        return $v;
    }//function

    /**
     * Executes a Shell command.
     *
     * @param string $command The command to execute
     * @param array $arguments Arguments to pass
     *
     * @uses shell_exec() !!
     *
     * @return string Console output
     */
    public function cliExec($command, $arguments = array())
    {
        $args = implode(' ', $arguments);
        $cmd = $command.' '.$args.' 2>&1';

        (ECR_DEBUG) ? BR.EcrDebugger::dEcho($cmd) : '';

        $results = shell_exec($cmd);

        return $results;
    }//function

    /**
     * Executes a Shell command.
     *
     * @param string $command The command to execute
     * @param array $arguments Arguments to pass
     *
     * @uses shell_exec() !!
     *
     * @return string Console output
     */
    public function cliSystem($command, $arguments = array())
    {
        $args = implode(' ', $arguments);
        $cmd = $this->cliBase.$command.' '.$args;

        (ECR_DEBUG) ? EcrDebugger::dEcho($cmd) : '';
        echo '<pre class="console">';
        $lastLine = system($cmd);
        echo '</pre>';

        return $lastLine;
    }//function
}//class
