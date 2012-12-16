<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 07-Mar-2008
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

//-- No direct access
defined('_JEXEC') || die('=;)');

/**
 * Enter description here ...
 *
 */
class EcrProjectMatrix extends JFolder
{
    public $totalFiles = 0;

    public $totalLines = 0;

    public $totalSize = 0;

    public $projectData = array();

    public $charts = array();

    public $chartCode = '';

    public $series = array();

    //--For pcharts
    public $fileCount = array();

    //--Private vars

    private $fileExtensions = array('php', 'js', 'html', 'css', 'xml', 'ini', 'sql', 'languages');

    private $imgExtensions = '.png$|.jpg$|.jpeg$|.ico$|.svg$|.gif$';

    private $codeRatioTypes = array(

    'php' => array(
        'singleComment' => array('//', '#')
    ,   'multiComment' => array(array('/*', '*/'))
    )

    , 'js' => array(
        'singleComment' => array('//')
    ,   'multiComment' => array(array('/*', '*/'))
    )

    , 'css' => array(
        'singleComment' => array()
    ,   'multiComment' => array(array('/*', '*/'))
    )

    , 'html' => array(
        'singleComment' => array()
    ,   'multiComment' => array(array('<!--', '-->'))
    )

    , 'xml' => array(
        'singleComment' => array()
    ,   'multiComment' => array(array('<!--', '-->'))
    )

    );

    /**
     * @var EcrProjectBase
     */
    private $project;

    /**
     * Constructor.
     *
     * @param EcrProjectBase $project The project
     * @param string $type Matrix type e.g. highcharts
     */
    public function __construct(EcrProjectBase $project, $type = 'highcharts')
    {
        $this->project = $project;

        $this->setUp();
        $this->scan();
        $this->calcPercentages();

        switch($type)
        {
            case 'pcharts':
                // #               $this->generatePChartCode();
                break;

            default:
                $this->generateChartCode();
                break;
        }//switch
    }//function

    /**
     * Get known project extensions.
     *
     * @return array
     */
    public function getProjectExtensions()
    {
        return $this->fileExtensions;
    }//function

    /**
     * Get known code ratio types.
     *
     * @return array
     */
    public function getCodeRatioTypes()
    {
        return array_keys($this->codeRatioTypes);
    }//function

    /**
     * Setup - Prevent notices.
     *
     * @return \EcrProjectMatrix
     */
    private function setUp()
    {
        foreach($this->fileExtensions as $ext)
        {
            $this->projectData[$ext]['files'] = 0;
            $this->projectData[$ext]['lines'] = 0;
            $this->projectData[$ext]['size'] = 0;

            $this->projectData[$ext]['ratioCode'] = 0;
            $this->projectData[$ext]['ratioBlanks'] = 0;
            $this->projectData[$ext]['ratioComments'] = 0;
        }//foreach

        $this->projectData['images']['files'] = 0;
        $this->projectData['images']['size'] = 0;

        return $this;
    }//function

    /**
     * Scans the project.
     *
     * @return void
     */
    private function scan()
    {
        foreach($this->project->copies as $copy)
        {
            if(is_dir($copy))
            {
                //-- Process known extensions
                foreach($this->fileExtensions as $ext)
                {
                    $data = $this->getCodeLines($copy, $ext);
                    $this->addData($data, $ext);
                }//foreach

                //-- Process images
                $files = JFolder::files($copy, $this->imgExtensions, true, true);

                $this->projectData['images']['files'] += count($files);

                foreach($files as $file)
                {
                    $this->projectData['images']['size'] += filesize($file);
                }//foreach

                $this->totalFiles += $this->projectData['images']['files'];
                $this->totalSize += $this->projectData['images']['size'];
            }
            else
            {
                //-- It's a file
                //... @todo ?
                //-- Process known extensions
                foreach($this->fileExtensions as $ext)
                {
                    $data = $this->getCodeLines($copy, $ext);
                    $this->addData($data, $ext);
                }//foreach
            }
        }//foreach

        /*
         * Language files
         */
        $langPaths = $this->project->getLanguagePaths();

        $languages = EcrLanguageHelper::discoverLanguages($this->project);

        foreach($languages as $tag => $scopes)
        {
            foreach($scopes as $scope)
            {
                $fileName = $langPaths[$scope].DS.'language'.DS.$tag.DS.$tag
                    .'.'.$this->project->getLanguageFileName($scope);

                $data = $this->getCodeLines($fileName, 'ini');

                $this->addData($data, 'languages');
            }//foreach
        }//foreach

        return $this;
    }//function

    /**
     * Add data to the matrix.
     *
     * @param array $data Data about the project
     * @param string $ext Extension type e.g. php or js
     *
     * @return void
     */
    private function addData($data, $ext)
    {
        $this->projectData[$ext]['files'] += $data['files'];
        $this->projectData[$ext]['lines'] += $data['lines'];
        $this->projectData[$ext]['size'] += $data['size'];

        $this->projectData[$ext]['ratioCode'] += $data['ratioCode'];
        $this->projectData[$ext]['ratioComments'] += $data['ratioComments'];
        $this->projectData[$ext]['ratioBlanks'] += $data['ratioBlanks'];

        $this->totalFiles += $data['files'];
        $this->totalLines += $data['lines'];
        $this->totalSize += $data['size'];
    }//function

    /**
     * Gets the number of code lines of all the files of a given path by extension.
     *
     * @param string $path Path to file
     * @param string $ext Extension
     *
     * @return array indexed array
     */
    private function getCodeLines($path, $ext)
    {
        if(JFile::exists($path))
        {
            $files = array($path);
        }
        else if(JFolder::exists($path))
        {
            $files = $this->files($path, '.'.$ext.'$', true, true);
        }
        else
        {
            $files = array();
        }

        $lines = 0;
        $cnt_files = 0;
        $cnt_size = 0;

        $ratioCode = 0;
        $ratioBlanks = 0;
        $ratioComments = 0;

  //      $ratio = array();

        foreach($files as $fileName)
        {
            if($ext != JFile::getExt($fileName))
                continue;

            $buffer = explode("\n", JFile::read($fileName));

            if(array_key_exists($ext, $this->codeRatioTypes))
            {
                $ratio = $this->getCodeRatio($buffer, $ext);
                $ratioCode += $ratio['code'];
                $ratioBlanks += $ratio['blanks'];
                $ratioComments += $ratio['comments'];
            }

            $cnt_size += filesize($fileName);
            $lines += count($buffer);
            $cnt_files ++;
        }//foreach

        $project_data = array(
        'files' => $cnt_files
        , 'lines' => $lines
        , 'size' => $cnt_size
        , 'ratioCode' => $ratioCode
        , 'ratioBlanks' => $ratioBlanks
        , 'ratioComments' => $ratioComments
        );

        return $project_data;
    }//function

    /**
     * Gets the comment to code ratio of a given file.
     *
     * @param array $buffer File contents
     * @param string $ext Extension e.g. php or js
     *
     * @return array indexed array with [code], [blanks] and [comments] as key, line counts as values
     */
    private function getCodeRatio($buffer, $ext)
    {
        $lines = array();

        $lines['code'] = 0;
        $lines['blanks'] = 0;
        $lines['comments'] = 0;

        $mlStarted = false;

        foreach($buffer as $line)
        {
            $line = trim($line);

            if( ! strlen($line))
            {
                //-- Blank
                $lines['blanks'] ++;
                continue;
            }

            foreach($this->codeRatioTypes[$ext]['multiComment'] as $multi)
            {
                if(strpos($line, $multi[0]) === 0)
                {
                    //-- Multi line comment START found
                    if(strpos($line, $multi[1]) !== false)
                    {
                        //-- Multi line comment END found on the same line
                        $lines['comments'] ++;
                        $mlStarted = false;
                        continue 2;
                    }

                    $lines['comments'] ++;
                    $mlStarted = true;
                    continue 2;
                }

                if(strpos($line, $multi[1]) !== false)
                {
                    //-- Multi line comment END found
                    $lines['comments'] ++;
                    $mlStarted = false;
                    continue 2;
                }
            }//foreach

            if($mlStarted)
            {
                //-- We are multi line commenting
                $lines['comments'] ++;
                continue;
            }

            foreach($this->codeRatioTypes[$ext]['singleComment'] as $single)
            {
                if(strpos($line, $single) === 0)
                {
                    //-- Single line comment found
                    $lines['comments'] ++;
                    continue 2;
                }
            }//foreach

            //-- It's code =;)
            $lines['code'] ++;
        }//foreach

        return $lines;
    }//function

    /**
     * Calculates percentages.
     *
     * @return void
     */
    private function calcPercentages()
    {
        //-- Code files
        foreach($this->fileExtensions as $ext)
        {
            $w = $this->totalFiles - $this->projectData[$ext]['files'];
            $this->projectData[$ext]['perc_files'] = number_format(100 - ($w * 100) / $this->totalFiles, 2);

            $w = $this->totalLines - $this->projectData[$ext]['lines'];
            $this->projectData[$ext]['perc_lines'] = number_format(100 - ($w * 100) / $this->totalLines, 2);

            $w = $this->totalSize - $this->projectData[$ext]['size'];
            $this->projectData[$ext]['perc_size'] = number_format(100 - ($w * 100) / $this->totalSize, 2);
        }//foreach

        //-- Images
        $w = $this->totalFiles - $this->projectData['images']['files'];
        $this->projectData['images']['perc_files'] = number_format(100 - ($w * 100) / $this->totalFiles, 2);

        $w = $this->totalSize - $this->projectData['images']['size'];
        $this->projectData['images']['perc_size'] = number_format(100 - ($w * 100) / $this->totalSize, 2);

        //-- Calc ratios
        $this->series = array();

        $charts = array(
    'filecount' => jgettext('Files')
        , 'linecount' => jgettext('Code lines')
        , 'sizecount' => jgettext('Size')
        );

        foreach(array_keys($this->codeRatioTypes) as $ext)
        {
            $lines = $this->projectData[$ext]['lines'];

            if( ! $lines)
            continue;

            $w = $lines - $this->projectData[$ext]['ratioCode'];
            $this->projectData[$ext]['perc_ratio_code'] = number_format(100 - ($w * 100) / $lines, 2);

            $w = $lines - $this->projectData[$ext]['ratioComments'];
            $this->projectData[$ext]['perc_ratio_comments'] = number_format(100 - ($w * 100) / $lines, 2);

            $w = $lines - $this->projectData[$ext]['ratioBlanks'];
            $this->projectData[$ext]['perc_ratio_blanks'] = number_format(100 - ($w * 100) / $lines, 2);

            $this->series['ratio_'.$ext]['code'] = $this->projectData[$ext]['perc_ratio_code'];
            $this->series['ratio_'.$ext]['comments'] = $this->projectData[$ext]['perc_ratio_comments'];
            $this->series['ratio_'.$ext]['blanks'] = $this->projectData[$ext]['perc_ratio_blanks'];

            $charts['ratio_'.$ext] = sprintf(jgettext('Code analysis %s'), $ext);
        }//foreach

        foreach($this->fileExtensions as $ext)
        {
            $this->series['filecount'][$ext] = $this->projectData[$ext]['perc_files'];
            $this->series['linecount'][$ext] = $this->projectData[$ext]['perc_lines'];
            $this->series['sizecount'][$ext] = $this->projectData[$ext]['perc_size'];
        }//foreach

        $this->series['filecount']['images'] = $this->projectData['images']['perc_files'];
        $this->series['sizecount']['images'] = $this->projectData['images']['perc_size'];
    }//function

    /**
     * Generate javscript code for highcharts.
     *
     * @return void
     */
    private function generateChartCode()
    {
        $charts = array(
          'filecount' => jgettext('Files')
        , 'linecount' => jgettext('Code lines')
        , 'sizecount' => jgettext('Size')
        );

        $chartCode = '';

        $chartCode .= "
<script type=\"text/javascript\">";

        foreach($charts as $chart => $title)
        {
            $chartCode .= 'var '.$chart.';'.NL;
        }//foreach

        $chartCode .= "window.addEvent('domready', function() {".NL;

        foreach($charts as $chart => $title)
        {
            if( ! isset($this->series[$chart]))
            continue;

            $x = strpos($chart, 'ratio_');

            $options = array();
            $options['legend'] = true;

            $chartCode .= $this->getPieChart($chart, $chart, $title, $this->series[$chart], $options);
        }//foreach

        $charts = array();

        $series = array();

        $categories = array();

        foreach(array_keys($this->codeRatioTypes) as $ext)
        {
            if( ! $this->projectData[$ext]['lines'])
            continue;

            $categories[] = $ext;

            $series[jgettext('code')][] = $this->series['ratio_'.$ext]['code'];
            $series[jgettext('blanks')][] = $this->series['ratio_'.$ext]['blanks'];
            $series[jgettext('comments')][] = $this->series['ratio_'.$ext]['comments'];
        }//foreach

        $chartCode .= 'var ratio;'.NL;
        $title = jgettext('Comment to Code ratio');

        $chartCode .= $this->getBarChart('fooo', 'ratio', $title, $categories, $series);

        $chartCode .= NL.'});'.NL.'</script>'.NL;

        $this->chartCode = $chartCode;
    }//function

    /**
     * Generate javscript code for highcharts.
     *
     * @return void
     */
    private function generatePChartCode()
    {
        return;
        $charts = array(
          'filecount' => jgettext('Files')
        , 'linecount' => jgettext('Code lines')
        , 'sizecount' => jgettext('Size')
        );

        $chartCode = '';

        foreach($charts as $chart => $title)
        {
            $chartCode .= 'var '.$chart.';'.NL;
        }//foreach

        $chartCode .= "window.addEvent('domready', function() {".NL;

        foreach($charts as $chart => $title)
        {
            if( ! isset($this->series[$chart]))
            continue;

            $x = strpos($chart, 'ratio_');

            $options = array();
            $options['legend'] = true;

            $chartCode .= $this->getPieChart($chart, $chart, $title, $this->series[$chart], $options);
        }//foreach

        $charts = array();

        $series = array();

        $categories = array();

        foreach(array_keys($this->codeRatioTypes) as $ext)
        {
            if( ! $this->projectData[$ext]['lines'])
            continue;

            $categories[] = $ext;

            $series[jgettext('code')][] = $this->series['ratio_'.$ext]['code'];
            $series[jgettext('blanks')][] = $this->series['ratio_'.$ext]['blanks'];
            $series[jgettext('comments')][] = $this->series['ratio_'.$ext]['comments'];
        }//foreach

        $chartCode .= 'var ratio;'.NL;
        $title = jgettext('Comment to Code ratio');

        $chartCode .= $this->getBarChart('fooo', 'ratio', $title, $categories, $series);

        $chartCode .= NL.'});'.NL.'</script>'.NL;

        $this->chartCode = $chartCode;
    }//function

    /**
     * Generate javscript code for highcharts.
     *
     * @param string $name Chart name
     * @param string $renderTo Html element id
     * @param string $title Chart title
     * @param array $series Chart series
     * @param array $options Additional options
     *
     * @return string
     */
    private function getPieChart($name, $renderTo, $title, $series, $options = array())
    {
        $started = false;
        $serie = '';
        $height = 230;
        $width = 250;

        foreach($series as $k => $v)
        {
            $x = (int)$v;

            if($started)
            {
                $serie .= ', ';
            }

            $started = true;

            $serie .= "['".jgettext($k)."', ".$v."]".NL;
        }//foreach

        if(array_key_exists('legend', $options))
        {
            if($options['legend'] == true)
            {
                $legendEnabled = 'true';
                $width += 70;
                $marginRight = 0;
            }
        }
        else
        {
            $legendEnabled = 'false';
            $marginRight = 0;
        }

        $js = "
    var $name = new Highcharts.Chart({
        chart: {
            renderTo: '$renderTo',
            margin: [20, $marginRight, 0, 120],
            width: $width,
            height: $height
        },
        title: {
            text: '$title'
        },
        plotArea: {
            shadow: null,
            borderWidth: null,
            backgroundColor: null
        },
        tooltip: {
            formatter: function() {
                return '<b>'+ this.point.name +'</b>: '+ this.y +' %';
            }
        },
        plotOptions: {
            pie: {
                size: '90%',
                allowPointSelect: false,
                dataLabels: {
                    enabled: true,
                    formatter: function() {
                        if (this.y > 5) return this.point.name;
                    },
                    color: 'white',
                    style: {
                        font: '14px Trebuchet MS, Verdana, sans-serif'
                    }
                }
            }
        },
        legend: {
            enabled: $legendEnabled,
            layout: 'vertical',
            style: {
                        left: '0px',
                        bottom: '10px',
                        right: 'auto',
                        top: 'auto'
                    },
            backgroundColor: '#eee',
            borderColor: '#ccc',
            borderWidth: 1,
            shadow: true
    },
            series: [{
            type: 'pie',
            name: '$title',
            data: [
            $serie
                ]
            }]
        });
";

        return $js;
    }//function

    /**
     * Get a bar chart.
     *
     * @param string $name Chart name
     * @param string $renderTo HTML id
     * @param string $title Chart title
     * @param array $categories Categories
     * @param array $series Chart series
     *
     * @return return_type
     */
    private function getBarChart($name, $renderTo, $title, $categories, $series)
    {
        $categoryString = "['".implode("', '", $categories)."']";

        $sEs = array();

        foreach($series as $name => $values)
        {
            $s = '';
            $s .= "    name: '$name',".NL;
            $s .= '    data: ['.implode(', ', $values).']'.NL;
            $sEs[] = $s;
        }//foreach

        $seriesString = '[{'.NL.implode(NL.'}, {'.NL, $sEs).NL.'}]';

        $js = "
    var chart = new Highcharts.Chart({
        chart: {
            renderTo: '$renderTo',
            defaultSeriesType: 'bar'
        },
        title: {
            text: '$title'
        },
        xAxis: {
            categories: $categoryString
        },
        yAxis: {
            min: 0,
//            max: 100,
            title: {
                text: '%'
            }
        },
        legend: {
//            style: {
//                left: 'auto',
//                bottom: 'auto',
//                right: '0px',
//                top: '35px'
//            },
            backgroundColor: '#eee',
            borderColor: '#ccc',
            borderWidth: 1,
//            shadow: true
        },
        tooltip: {
            formatter: function() {
//                return '<b>'+ this.x +'</b><br/>'+
//                     this.series.name +': '+ this.y +'';
                return this.x +' '+this.series.name+' : '+ this.y +'';
                }
        },
        plotOptions: {
            bar: {
                stacking: 'normal'
            }
        },
            series: $seriesString
    });";

        return $js;
    }//function
}//class
