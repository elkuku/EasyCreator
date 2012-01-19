<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath
 * @author     Created on 02-Oct-2009
 *
 *#######################################################################################
 2 * PHPUnit 2.x
 * Usage: phpunit [switches] UnitTest [UnitTest.php]
 --coverage-data <file> Write Code Coverage data in raw format to file.
 --coverage-html <file> Write Code Coverage data in HTML format to file.
 --coverage-text <file> Write Code Coverage data in text format to file.

 --testdox-html <file>  Write agile documentation in HTML format to file.
 --testdox-text <file>  Write agile documentation in Text format to file.
 --log-xml <file>       Log test progress in XML format to file.

 --loader <loader>      TestSuiteLoader implementation to use.

 --skeleton             Generate skeleton UnitTest class for Unit in Unit.php.

 --wait                 Waits for a keystroke after each test.

 --help                 Prints this usage information.
 --version              Prints the version and exits.

 #######################################################################################
 PHPUnit 3.4.0

 Usage: phpunit [switches] UnitTest [UnitTest.php]
 phpunit [switches] <directory>

 --log-junit <file>       Log test execution in JUnit XML format to file.
 --log-tap <file>         Log test execution in TAP format to file.
 --log-json <file>        Log test execution in JSON format.

 --coverage-html <dir>    Generate code coverage report in HTML format.
 --coverage-clover <file> Write code coverage data in Clover XML format.
 --coverage-source <dir>  Write code coverage / source data in XML format.

 --story-html <file>      Write Story/BDD results in HTML format to file.
 --story-text <file>      Write Story/BDD results in Text format to file.

 --testdox-html <file>    Write agile documentation in HTML format to file.
 --testdox-text <file>    Write agile documentation in Text format to file.

 --filter <pattern>       Filter which tests to run.
 --group ...              Only runs tests from the specified group(s).
 --exclude-group ...      Exclude tests from the specified group(s).
 --list-groups            List available test groups.

 --loader <loader>        TestSuiteLoader implementation to use.

 --story                  Report test execution progress in Story/BDD format.
 --tap                    Report test execution progress in TAP format.
 --testdox                Report test execution progress in TestDox format.

 --colors                 Use colors in output.
 --stderr                 Write to STDERR instead of STDOUT.
 --stop-on-failure        Stop execution upon first error or failure.
 --verbose                Output more verbose information.
 --wait                   Waits for a keystroke after each test.

 --skeleton-class         Generate Unit class for UnitTest in UnitTest.php.
 --skeleton-test          Generate UnitTest class for Unit in Unit.php.

 --process-isolation      Run each test in a separate PHP process.
 --no-globals-backup      Do not backup and restore $GLOBALS.
 --no-static-backup       Do not backup and restore static attributes.
 --syntax-check           Try to check source files for syntax errors.

 --bootstrap <file>       A "bootstrap" PHP file that is run before the tests.
 --configuration <file>   Read configuration from XML file.
 --no-configuration       Ignore default configuration file (phpunit.xml).
 --include-path <path(s)> Prepend PHP's include_path with given path(s).
 -d key[=value]           Sets a php.ini value.

 --help                   Prints this usage information.
 --version                Prints the version and exits.

 */

/**
 * PHPUnit Helper.
 *
 * @package    EasyCreator
 */
class EcrPearHelperPhpunit extends EcrPearHelperConsole
{
    public $logPath = '';

    /**
     * Run a unit test.
     *
     * @param array $arguments Arguments to pass
     *
     * @return string
     */
    public function test($arguments = array())
    {
        if( ! count($arguments))
        {
            return '';
        }

        $results = $this->cliExec('phpunit', $arguments);

        return $results;
    }//function

    /**
     * Create a unit test skeleton.
     *
     * @param array $arguments Arguments to pass
     *
     * @return string
     */
    public function skeleton($arguments = array())
    {
        $results = $this->cliExec('phpunit', $arguments);

        return $results;
    }//function

    /**
     * Show the formatted log of a unit test.
     *
     * @param string $fileName Full path to XML log file
     *
     * @todo move ?
     *
     * @return string
     */
    public function showFormattedLog($fileName)
    {
        $ret = '';

        if( ! JFile::exists($fileName))
        {
            return 'file not found..';
        }

        if( ! filesize($fileName))
        {
            return 'file size is 0..';
        }

        $xmlObject = EcrProjectHelper::getXML($fileName);

        if( ! $xmlObject instanceof SimpleXMLElement)
        {
            return 'unable to load xml parser..';
        }

        foreach($xmlObject->testsuite as $testSuite)
        {
            $ret .= $this->outputSingleTest($testSuite);

            $ts = $testSuite->testsuite;

            if($ts instanceof SimpleXMLElement
            && isset($ts->testcase))
            {
                foreach($ts as $t)
                {
                    $ret .= $this->outputSingleTest($t);
                }//foreach
            }
        }//foreach

        return $ret;
    }//function

    /**
     * Output a single unit test.
     *
     * @param object $testSuite The testsuite..
     *
     * @todo TODO - MOVE THIS SH** =;)
     *
     * @return string
     */
    private function outputSingleTest($testSuite)
    {
        $ret = '';
        $ret .= '<hr />';
        $ret .= '<h2>'.$testSuite->attributes()->name.'</h2>';

        $ret .= sprintf(
          'Tests: <b style="color: blue;">%d</b>'
          . ' Assertions: <b style="color: blue;">%d</b>'
          . ' Failures: <b style="color: red;">%d</b>'
          . ' Errors: <b style="color: red;">%d</b>'
          . ' Time: <b style="color: green;">%d</b>'

          , $testSuite->attributes()->tests
          , $testSuite->attributes()->assertions
          , $testSuite->attributes()->failures
          , $testSuite->attributes()->errors
          , $testSuite->attributes()->time
          );

          if( ! isset ($testSuite->testcase))
          {
              return $ret;
          }

          $ret .= '<table class="adminlist">';
          $ret .= '  <thead>';
          $ret .= '    <tr>';
          $ret .= '      <th>Stat</th>';
          $ret .= '      <th>Name</th>';
          $ret .= '      <th>Class</th>';
          $ret .= '      <th>Line</th>';
          $ret .= '      <th>Asserts</th>';
          $ret .= '      <th>Time</th>';
          $ret .= '      <th>File</th>';
          $ret .= '    </tr>';
          $ret .= '  </thead>';

          $ret .= '  <tbody>';

          foreach($testSuite->testcase as $testCase)
          {
              $ret .= '<tr valign="top">';

              $stat =(isset($testCase->failure)) ? 'fail' : 'ok';

              $ret .= '<td style="text-align: center;"><span class="img icon-16-check_'.$stat.'"></span></td>';
              $ret .= '<td>'.$testCase->attributes()->name.'</td>';
              $ret .= '<td>'.$testCase->attributes()->class.'</td>';
              $ret .= '<td>'.$testCase->attributes()->line.'</td>';
              $ret .= '<td>'.$testCase->attributes()->assertions.'</td>';
              $ret .= '<td>'.$testCase->attributes()->time.'</td>';
              $ret .= '<td>'.str_replace(JPATH_ROOT.DS, '', $testCase->attributes()->file).'</td>';

              $ret .= '</tr>';

              if(isset($testCase->failure))
              {
                  $ret .= '<tr>';
                  $ret .= '<td colspan="7" style="background-color: #ffb299; color: black;">';
                  $ret .= '<pre>';
                  $ret .= htmlentities(str_replace(JPATH_ROOT.DS, 'JPATH_ROOT'.DS, $testCase->failure));
                  $ret .= '</pre>';
                  $ret .= '</td>';
                  $ret .= '</tr>';
              }
          }//foreach

          $ret .= '</tbody>';

          $ret .= '</table>';

          return $ret;
    }//function
}//class
