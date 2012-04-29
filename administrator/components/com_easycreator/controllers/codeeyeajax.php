<?php
/**
 * @package    EasyCreator
 * @subpackage Controllers
 * @author     Nikolai Plath
 * @author     Created on 02-Oct-2009
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

jimport('joomla.application.component.controller');

/**
 * EasyCreator Controller.
 *
 * @package    EasyCreator
 * @subpackage Controllers
 */
class EasyCreatorControllerCodeEyeAjax extends JController
{
    protected $testsBase;

    /**
     * Executes a PHPUnit test.
     *
     * @return void
     */
    public function phpunit()
    {
        $response = new stdClass;

        $folder = JRequest::getString('folder');
        $test = JRequest::getString('test');
        $time_stamp = JRequest::getCmd('time_stamp');
        $resultsBase = JRequest::getVar('results_base');

        $arguments = array();

        //-- Joomla! bootstrap
        $arguments[] = '--bootstrap '.JPATH_ROOT.DS.'bootstrap.php';

        if(! JFolder::exists(JPATH_ROOT.DS.$resultsBase))
        {
            JFolder::create(JPATH_ROOT.DS.$resultsBase);
        }

        //-- JUnit XML log file
        $logName = JPATH_ROOT.DS.$resultsBase.DS.$time_stamp.'_'.JFile::getName($test).'.xml';
        $arguments[] = '--log-junit '.$logName;

        //-- @todo: Test Name
        $arguments[] = 'KuKuTest';

        $arguments[] = ($folder) ? JPATH_ROOT.DS.$folder.DS.$test : JPATH_ROOT.DS.$test;

        $phpUnit = new EcrPearHelperPhpunit;

        ob_start();
        $results = $phpUnit->test($arguments);
        $add = ob_get_contents();
        ob_end_clean();

        if(JFile::exists($logName))
        {
            $response->text = $add.BR.$phpUnit->showFormattedLog($logName);
            $response->status = 1;
        }
        else
        {
            $response->text = $add.BR.'ERROR writing: '.$logName;
            $response->status = 0;
        }

        $response->console = htmlentities($results);

        echo json_encode($response);
    }

    //function

    /**
     * Execute a selenium test.
     *
     * @return void
     */
    public function selenium()
    {
        $response = new stdClass;

        $folder = JRequest::getString('folder');
        $test = JRequest::getString('test');
        $time_stamp = JRequest::getCmd('time_stamp');
        $resultsBase = JRequest::getVar('results_base');

        $arguments = array();

        //-- Joomla! bootstrap
        $arguments[] = '--bootstrap '.JPATH_ROOT.'/tests/system/servers/config-def.php';

        //-- C $arguments[] = '--singleWindow';
        if(! JFolder::exists(JPATH_ROOT.DS.$resultsBase))
        {
            //-- C     JFolder::create(JPATH_ROOT.DS.$resultsBase);
        }

        /*
                -- JUnit XML log file
                        $logName = JPATH_ROOT.DS.$resultsBase.DS.$time_stamp.'_'.JFile::getName($test).'.xml';
                        $arguments[] = '--log-junit '.$logName;

                -- @todo: Test Name
                        $arguments[] = 'KuKuTest';
        */

        $arguments[] = ($folder) ? JPATH_ROOT.DS.$folder.DS.$test : JPATH_ROOT.DS.$test;

        $seleniumTest = new EcrPearHelperSelenium;

        ob_start();
        $results = $seleniumTest->test($arguments);
        $add = ob_get_contents();
        ob_end_clean();

        /*
                        if(JFile::exists($logName))
                        {
                            $response['text'] = $add.BR.$phpUnit->showFormattedLog($logName);
                            $response['status'] = 1;
                        }
                        else
                        {
                            $response['text'] = $add.BR.'ERROR writing: '.$logName;
                        }
        */

        $response->console = htmlentities($results);

        echo json_encode($response);
    }

    //function

    /**
     * Create a skeleton for a unit test.
     *
     * @return void
     */
    public function create_skeleton()
    {
        $folder = JRequest::getString('folder');
        $file = JRequest::getString('file');
        $ecr_project = JRequest::getCmd('ecr_project');

        $path = JPATH_ROOT.DS.$folder.DS.$file;

        $response = new stdClass;
        $arguments = array();

        if(! JFile::exists($path))
        {
            $response->text = jgettext('File not found');
            $response->status = 1;
        }
        else
        {
            if(JFile::getExt($path) != 'php')
            {
                $response->text = jgettext('Only PHP files are allowed');
                $response->console = $response->text;
            }
            else
            {
                $classes = get_declared_classes();

                include $path;
                ob_start();
                $includedOutput = ob_get_contents();
                ob_end_clean();

                $foundClasses = array_diff(get_declared_classes(), $classes);

                if(! count($foundClasses))
                {
                    $response->text = jgettext('No classes found');
                    $response->console = $response->text;
                }
                else
                {
                    $class = array_pop($foundClasses);
                    $resultPath = JPATH_ROOT.DS.$folder.DS.$class.'Test.php';

                    //-- Joomla! bootstrap
                    $arguments[] = '--bootstrap '.JPATH_ROOT.DS.'bootstrap.php';
                    $arguments[] = '--skeleton-test '.$class.' '.$path;

                    $phpUnit = new EcrPearHelperPhpunit;

                    ob_start();
                    $results = $phpUnit->skeleton($arguments);
                    $add = ob_get_contents();
                    ob_end_clean();

                    $subFolder = '';

                    if(JFile::exists($resultPath))
                    {
                        $scope = (strpos($resultPath, JPATH_ADMINISTRATOR) === false) ? 'site' : 'admin';

                        $test = str_replace(DS.JFile::getName($resultPath), '', $resultPath);

                        if(strpos($test, JPATH_ADMINISTRATOR) === false)
                        {
                            if($test != JPATH_SITE.DS.'components'.DS.$ecr_project)
                            {
                                //-- Subfolder
                                $subFolder = str_replace(
                                    JPATH_SITE.DS.'components'.DS.$ecr_project.DS
                                    , '', $test);
                            }
                        }
                        else
                        {
                            if($test != JPATH_ADMINISTRATOR.DS.'components'.DS.$ecr_project)
                            {
                                //-- Subfolder
                                $subFolder = str_replace(JPATH_ADMINISTRATOR.DS.'components'.DS.$ecr_project.DS
                                    , '', $test);
                            }
                        }

                        $destFolder = JPATH_ADMINISTRATOR.DS.'components'.DS.$ecr_project.DS.'tests'.DS.$scope;
                        $destFolder .= ($subFolder) ? DS.$subFolder : '';

                        $destFileName = $class.'Test.php';

                        if(! JFolder::exists($destFolder))
                        {
                            JFolder::create($destFolder);
                        }

                        if(JFile::move($resultPath, $destFolder.DS.$destFileName))
                        {
                            $response->status = 1;
                        }
                        else
                        {
                        }
                    }
                    else
                    {
                    }

                    $response->text = $add.$includedOutput;
                    $response->console = htmlentities($results);
                }
            }
        }

        echo json_encode($response);
    }

    //function

    /**
     * Displays the content of the test directory.
     *
     * @return void
     */
    public function draw_test_dir()
    {
        $ecr_project = JRequest::getCmd('ecr_project');
        $this->testsBase = 'administrator'.DS.'components'.DS.$ecr_project.DS.'tests';

        $timeStamp = date('Ymd_his');
        $jsFile = '';
        $jsFile .= " onclick=\"doPHPUnit('[link]', '[file]', '$timeStamp', '[id]');\"";

        $jsFolder = '';

        $fileTree = new EcrFileTree(JPATH_ROOT.DS.$this->testsBase, '', $jsFile, $jsFolder);

        $arguments = array();
        $response = array();

        //-- Joomla! bootstrap
        $arguments[] = '--bootstrap '.JPATH_ROOT.DS.'bootstrap.php';

        // @codingStandardsIgnoreStart - use of superglobals
        $results = print_r($_REQUEST, true);
        // @codingStandardsIgnoreEnd

        $response['text'] = $fileTree->drawFullTree();
        $response['console'] = htmlentities($results);
        $response['status'] = 1;

        echo json_encode($response);
    }

    //function

    /**
     * Executes a 'sniff'.
     *
     * @return void
     */
    public function gitStatus()
    {
        $path = JRequest::getVar('path');
        $file = JRequest::getVar('file');

        $response = new stdClass;

        $args = array();

        ob_start();

        $console = new EcrPearHelperConsole;

        $goToPath = 'cd /home/elkuku/git/testinggg &&';

        $args[] = 'branch';

        $results = $console->cliExec($goToPath.'git', $args);

        $response->text = ob_get_contents();

        ob_end_clean();

        $response->console = $results;

        echo json_encode($response);
    }

    public function runCli()
    {
        $project = EcrProjectHelper::getProject();

        $path = JPath::clean($project->getExtensionPath().'/'.$project->comName.'.php');

        $args = (array)JRequest::getVar('args');

        $response = new stdClass;

        ob_start();

        $console = new EcrPearHelperConsole;

        $results = $console->cliExec('php '.$path, $args);

        $response->text = ob_get_contents();

        ob_end_clean();

        $response->console = $results;

        echo json_encode($response);

    }

    public function phploc()
    {
        $dir = JRequest::getString('dir');

        $response = new stdClass;

        if(! $dir || 'undefined' == $dir)
        {
            $response->status = 1;
            $response->text = 'No directory given';

            echo json_encode($response);

            return;
        }

        ob_start();

        $counter = new EcrPearHelperPhploc;

        $results = $counter->count($dir);

        $response->text = ob_get_contents();

        ob_end_clean();

        $response->console = $results;

        echo json_encode($response);
    }

    //function

    /**
     * Executes a 'sniff'.
     *
     * @return void
     */
    public function load_sniff()
    {
        $path = JRequest::getVar('path');
        $file = JRequest::getVar('file');

        $response = array();

        $response['status'] = 0;
        $response['text'] = '';
        $response['console'] = '';

        if(! $file)
        {
            if(! JFolder::exists(JPATH_ROOT.DS.$path))
            {
                $response['text'] = '<b style="color: red">'.jgettext('Folder not found').'</b>';

                echo json_encode($response);

                return;
            }

            $fullPath = JPATH_ROOT.DS.$path;
        }
        else
        {
            $ext = JFile::getExt($file);

            $sniffExtensions = array('php', 'js');

            if(! in_array($ext, $sniffExtensions))
            {
                $response['text'] = '<b style="color: red">Sniffeable extensions:<br />'
                    .implode(',', $sniffExtensions).'</b>';

                echo json_encode($response);

                return;
            }

            $fullPath = JPATH_ROOT.DS.$path.DS.$file;

            if(! JFile::exists($fullPath))
            {
                $response['text'] = '<b style="color: red">'.jgettext('File not found').'</b>';

                echo json_encode($response);

                return;
            }
        }

        $fullPath = str_replace('/', DS, $fullPath);

        ob_start();

        $sniffer = new EcrPearHelperCodesniffer;

        $standard = JRequest::getVar('sniff_standard');

        if($standard)
            $sniffer->setStandard($standard);

        $format = JRequest::getVar('sniff_format');

        if($format)
            $sniffer->setFormat($format);

        $verbose = JRequest::getCmd('sniff_verbose');
        $sniffer->verboseLevel = ($verbose == 'true') ? '-v' : '';

        $sniffs = JRequest::getVar('sniff_sniffs');

        if($sniffs)
        {
            if(substr($sniffs, strlen($sniffs) - 1) == ',')
            {
                $sniffs = substr($sniffs, 0, strlen($sniffs) - 1);
            }

            $sniffer->sniffs = explode(',', $sniffs);
        }

        $results = $sniffer->sniffFile($fullPath);
        $response['text'] = ob_get_contents();
        ob_end_clean();

        $response['console'] = htmlentities($results);
        $response['status'] = 1;

        if($file
            && 'xml' == $format
        )
        {
            $xml = JFactory::getXML($results, false);

            $errors = array();

            /* @var $error SimpleXMLElement */
            foreach($xml->file->error as $error)
            {
                $line = (int)$error->attributes()->line;

                if(! isset($errors[$line]))
                    $errors[$line] = array();

                $errors[$line][] = htmlentities((string)$error.' ('.(string)$error->attributes()->source.')');
            }
            //foreach

            $warnings = array();

            foreach($xml->file->warning as $error)
            {
                $line = (int)$error->attributes()->line;

                if(! isset($warnings[$line]))
                    $warnings[$line] = array();

                $warnings[$line][] = htmlentities((string)$error.' ('.(string)$error->attributes()->source.')');
            }
            //foreach

            $highlight = '';
            $highlight .= '<pre>';

            $contents = file($fullPath);

            foreach($contents as $i => $line)
            {
                $lNo = $i + 1;

                $msg = '';

                $class = '';

                if(isset($warnings[$lNo]))
                {
                    $class = 'warning';

                    $msg .= implode("\n", $warnings[$lNo]);
                }

                if(isset($errors[$lNo]))
                {
                    $class = 'error';

                    $msg .= implode("\n", $errors[$lNo]);
                }

                $highlight .= '<div class="'.$class.'">';
                $highlight .= str_pad($lNo, 4, ' ', STR_PAD_LEFT).' '.htmlentities($line);

                if($msg)
                    $highlight .= '     <small>'.$msg.'</small>';

                $highlight .= '</div>';
            }
            //foreach

            $highlight .= '</pre>';

            $response['text'] = $highlight.$response['text'];
        }

        echo json_encode($response);
    }

    //function

    /**
     * Runs PHP Copy & Paste detector.
     *
     * @return void
     */
    public function phpcpd()
    {
        $path = JRequest::getVar('path');

        $arguments = array();
        $arguments['min-lines'] = JRequest::getInt('min-lines', 5);
        $arguments['min-tokens'] = JRequest::getInt('min-tokens', 70);

        $response = array();

        if(! $path)
        {
            $response['status'] = 0;
            $response['text'] = jgettext('No path set');
            $response['console'] = '';

            echo json_encode($response);

            return;
        }

        ob_start();
        $phpcpd = new EcrPearHelperPhpcpd;

        $results = $phpcpd->detect($arguments, $path);

        $response['text'] = ob_get_contents();
        ob_end_clean();

        $response['console'] = htmlentities($results);
        $response['status'] = 1;

        echo json_encode($response);
    }

    //function

    /**
     * Runs PHPDocumentor.
     *
     * @return void
     */
    public function phpdoc()
    {
        $response = array();

        $parseDirs = JRequest::getVar('parse_dirs');
        $parseFiles = JRequest::getVar('parse_files');
        $targetDir = JRequest::getVar('target_dir');
        $converter = JRequest::getVar('converter');
        $options = JRequest::getVar('options');

        $phpDoc = new EcrPearHelperPhpdoc;

        if($converter)
        {
            $cs = explode(':', $converter);
            $phpDoc->outputFormat = $cs[0];
            $phpDoc->converter = $cs[1];
            $phpDoc->template = $cs[2];
        }

        $parseDirs = explode(',', $parseDirs);
        $a = array();

        foreach($parseDirs as $n)
        {
            $a[] = JPATH_ROOT.DS.$n;
        }
        //foreach

        $parseDirs = implode(',', $a);

        $parseFiles = explode(',', $parseFiles);
        $a = array();

        foreach($parseFiles as $n)
        {
            $a[] = JPATH_ROOT.DS.$n;
        }
        //foreach

        $parseFiles = implode(',', $a);

        $phpDoc->parseDirs = $parseDirs;
        $phpDoc->parseFiles = $parseFiles;

        $phpDoc->targetDir = JPATH_ROOT.DS.$targetDir;

        ob_start();
        $results = $phpDoc->process($options);
        $add = ob_get_contents();
        ob_end_clean();

        $response['console'] = htmlentities($results);

        if(! JFile::exists(JPATH_ROOT.DS.$targetDir.DS.'index.html'))
        {
            $response['status'] = 0;
            $response['text'] = jgettext('Something went wrong...');
            echo json_encode($response);

            return;
        }

        $response['status'] = 1;
        $response['text'] = '';
        $response['text'] .= '<h1>'.jgettext('Documentation has been created').'</h1>';
        $response['text'] .= '<a class="external" href="'.JURI::root(true)
            .'/'.str_replace(DS, '/', $targetDir).'">'.jgettext('View Documentation').'</a>'.BR;
        $response['text'] .= '<a class="external" href="'.JURI::root(true)
            .'/'.str_replace(DS, '/', $targetDir.'/errors.html').'">'.jgettext('View Errors').'</a>'.BR;
        $response['text'] .= $add;

        echo json_encode($response);
    }

    //function

    /**
     * Checks for installed PEAR packages.
     *
     * @return void
     */
    public function check_environment()
    {
        $response = array();

        $pearConsole = new EcrPearHelperConsole;

        ob_start();
        $pearPackages = $pearConsole->getPackages();
        $notFound = '<strong style="color: red;">'.jgettext('Not found').'</strong>';

        if(! count($pearPackages))
        {
            echo '<h2 style="color: red;">'.jgettext('No PEAR packages found - Please check your Paths').'</h2>';
            echo sprintf(jgettext('For more information see: %s')
                , '<a href="'.ECR_DOCU_LINK.'/EasyCodeEye#Check_your_PATHs">EasyCreator Doku: Check your path</a>');
        }
        else
        {
            ?>

        <div class="infoHeader">Installed PEAR Packages</div>
        <div style="margin-top: 1em;">
        <table class="adminlist">

            <thead>
            <tr>
                <th><?php echo jgettext('Package'); ?></th>
                <th><?php echo jgettext('Version'); ?></th>
                <th><?php echo jgettext('Recommended'); ?></th>
                <th><?php echo jgettext('Info'); ?></th>
            </tr>
            </thead>

            <tbody>
            <tr class="row0">
                <td>PHP_CodeSniffer</td>
                <td><?php echo (array_key_exists('PHP_CodeSniffer', $pearPackages))
                    ? $pearPackages['PHP_CodeSniffer']
                    : $notFound; ?>
                </td>
                <td>1.2.0</td>
                <td><a href="http://pear.php.net/package/PHP_CodeSniffer"
                       class="external">PHP_CodeSniffer</a> tokenises PHP, JavaScript and
                    CSS files and detects violations of a defined set of coding
                    standards.
                </td>
            </tr>

            <tr class="row1">
                <td>phpcpd</td>
                <td><?php echo $pearConsole->testVersion('phpcpd', '1.2.0'); ?></td>
                <td>1.1.1</td>
                <td><a href="http://github.com/sebastianbergmann/phpcpd"
                       class="external">phpcpd</a> is a Copy/Paste Detector (CPD) for PHP
                    code.
                </td>
            </tr>

            <tr class="row0">
                <td>PhpDocumentor</td>
                <td><?php echo (array_key_exists('PhpDocumentor', $pearPackages))
                    ? $pearPackages['PhpDocumentor']
                    : $notFound; ?>
                </td>
                <td>1.4.3</td>
                <td><a href="http://www.phpdoc.org/" class="external">PhpDocumentor</a>
                    is the world standard auto-documentation tool for PHP.
                </td>
            </tr>

            <tr class="row1">
                <td>PhpUnit</td>
                <td><?php  echo $pearConsole->testVersion('phpunit', '3.4.0'); ?></td>
                <td>3.4.0</td>
                <td><a href="http://www.phpunit.de/" class="external">PhpUnit</a>
                    provides both a framework that makes the writing of tests easy as
                    well as the functionality to easily run the tests and analyse their
                    results.
                </td>
            </tr>

            </tbody>

        </table>
</div>
        <p style="margin-top: 1em; padding: 0.5em; background-color: #fff;">
            <?php echo sprintf(jgettext('See also: %s')
            , '<a class="external" href="'.ECR_DOCU_LINK.'/EasyCodeEye">EasyCreator Doku: EasyCodeEye</a>'); ?>
        </p>

        <?php
        }

        $response['text'] = ob_get_contents();
        ob_end_clean();

        ob_start();
        echo '<h3>System</h3>';
        echo 'PHP: '.PHP_VERSION.BR;
        echo 'memory_limit: '.ini_get('memory_limit').BR;
        echo 'max_execution_time: '.ini_get('max_execution_time').BR.BR;
        echo 'PHP include_path: '.BR.get_include_path().BR.BR;
        echo 'System PATH: '.BR.shell_exec('echo $PATH').BR;
        echo '<hr />';
        echo '<h3>All PEAR packages - FYI only.. =;)</h3>';
        print_r($pearPackages);

        $response['console'] = ob_get_contents();
        ob_end_clean();

        $response['status'] = 1;

        echo json_encode($response);
    }

    //function

    /**
     * Display statistics.
     *
     * @return void
     */
    public function get_stats()
    {
        JRequest::setVar('view', 'codeeye');
        JRequest::setVar('layout', 'stats2_table');
        parent::display();

        return;
    }

    //function

    /**
     * Displays a chart.
     *
     * @return void
     */
    public function get_chart()
    {
        error_reporting(E_ALL);

        include JPATH_COMPONENT.DS.'helpers'.DS.'pchart'.DS.'pData.class.php';
        include JPATH_COMPONENT.DS.'helpers'.DS.'pchart'.DS.'pChart.class.php';

        JFactory::getDocument()->setMimeEncoding('image/jpg');

        $data = JRequest::getVar('data', '');
        $labels = JRequest::getVar('labels', '');

        $colorChart = JRequest::getInt('color', 8);

        $colorPath = JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'pchart'.DS.'colours'.DS.'tones-'.$colorChart.'.txt';

        $DataSet = new pData;

        if($data)
        {
            $data = explode(',', $data);
            $labels = explode(',', $labels);

            $DataSet->AddPoint($data, 'Serie1');
            $DataSet->AddPoint($labels, 'Serie2');
        }
        else
        {
            $DataSet->AddPoint(array(10, 2, 3, 5, 3), "Serie1");
            $DataSet->AddPoint(array("Jan", "Feb", "Mar", "Apr", "May"), "Serie2");
        }

        $DataSet->AddAllSeries();
        $DataSet->SetAbsciseLabelSerie("Serie2");

        //-- Initialise the graph
        $chart = new pChart(380, 200);

        if(JFile::exists($colorPath))
        {
            $chart->loadColorPalette($colorPath);
        }

        /*
                              $chart->drawFilledRoundedRectangle(7, 7, 373, 193, 5, 240, 240, 240);
                               $chart->drawRoundedRectangle(5, 5, 375, 195, 5, 230, 230, 230);
        */
        //-- Draw the pie chart
        $chart->setFontProperties(JPATH_COMPONENT.DS.'helpers'.DS.'pchart/Fonts/MankSans.ttf', 10);

        $chart->drawPieGraph($DataSet->GetData()
            , $DataSet->GetDataDescription(), 150, 90, 110, PIE_PERCENTAGE, true, 50, 20, 5);

        $chart->drawPieLegend(290, 15, $DataSet->GetData(), $DataSet->GetDataDescription(), 250, 250, 250);

        //-- Spit it out
        $chart->Stroke();

        return;
    }
    //function
}//class
