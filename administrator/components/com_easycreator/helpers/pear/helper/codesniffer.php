<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author		Nikolai Plath
 * @author		Created on 10.09.2009

 * Usage: phpcs [-nwlsvi] [--report=<report>] [--report-file=<reportfile>]
 [--config-set key value] [--config-delete key] [--config-show]
 [--standard=<standard>] [--sniffs=<sniffs>]
 [--extensions=<extensions>] [--ignore=<patterns>]
 [--generator=<generator>] [--tab-width=<width>] <file> ...
 -n           Do not print warnings
 -w           Print both warnings and errors (on by default)
 -l           Local directory only, no recursion
 -s           Show sniff codes in all reports
 -v[v][v]     Print verbose output
 -i           Show a list of installed coding standards
 --help       Print this help message
 --version    Print version information
 <file>       One or more files and/or directories to check
 <extensions> A comma separated list of file extensions to check
 (only valid if checking a directory)
 <patterns>   A comma separated list of patterns that are used
 to ignore directories and files
 <sniffs>     A comma separated list of sniff codes to limit the check to
 (all sniffs must be part of the specified standard)
 <standard>   The name of the coding standard to use
 <width>      The number of spaces each tab represents
 <generator>  The name of a doc generator to use
 (forces doc generation instead of checking)
 <report>     Print either the "full", "xml", "checkstyle",
 "csv", "emacs", "source" or "summary" report
 (the "full" report is printed by default)
 <reportfile> Write the report to the specified file path
 (report is also written to screen)

 * @author elkuku
 *
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * PHP Code Sniffer helper.
 *
 * @package EasyCreator
 */
class EcrPearHelperCodesniffer extends EcrPearHelperConsole
{
    public $standard = 'PEAR';

    public $sniffFormat = 'full';

    public $verboseLevel = '';

    public $sniffs = array();

    /**
     * Show the config ?.
     *
     * @todo oes not work...
     *
     * @return string
     */
    public function showConfig()
    {
        $cmd = $this->cliBase.'/phpcs --config-show';
        echo $cmd;
        $results = shell_exec($cmd);

        return $results;
    }//function

    /**
     * Set the sniff standard.
     *
     * @param string $standard The standard  e.g. PEAR
     *
     * @todo change when the sniffer supports multiple standards
     *
     * @return void
     */
    public function setStandard($standard)
    {
        $this->standard = $standard;
    }//function

    /**
     * Set the sniff format.
     *
     * @param string $format The sniff format e.g. xml
     *
     * @return void
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }//function

    /**
     * Execute a sniff on a single file.
     *
     * @param string $fullPath Full path to the file to sniff
     *
     * @return string Results
     */
    public function sniffFile($fullPath)
    {
        $args = array();

        if(count($this->sniffs))
        {
            $args[] = '--sniffs='.implode(',', $this->sniffs);
        }

        $args[] = '--report='.$this->format;
        $args[] = '--standard='.$this->standard;

        /*
         * Parse directories
         * clean path
         */
        $args[] = str_replace('/', DS, $fullPath);

        $args[] = $this->verboseLevel;

        /*
        $path = JFactory::getConfig()->get('log_path');

        $fileName = 'ecr_log.php';
        $log = $path.'/'.$fileName;

        $args[] = ' >> '.$log;
*/

        $results = $this->cliExec('phpcs', $args);

        /**
         * @todo save to file
         */

        return $results;
    }//function

    /**
     * Get the PEAR installed coding standards.
     *
     * @return array
     */
    public function getStandards()
    {
        if( ! $this->validEnv)
        {
            return array();
        }

        //-- Expected response: e.g.
        //-- The installed coding standards are PEAR, PHPCS, Squiz, Zend and MySource
        $s = $this->cliExec('phpcs', array('-i'));

        $s = trim($s);
        $s = str_replace('The installed coding standards are ', '', $s);
        $s = str_replace(' and ', ', ', $s);

        return explode(', ', $s);
    }//function
}//class
