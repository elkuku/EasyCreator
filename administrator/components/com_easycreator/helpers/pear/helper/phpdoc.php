<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author		Nikolai Plath
 * @author		Created on 30-Sep-2009
 */

/**
 * PHPDocumentor Helper.
 *
 * @package    EasyCreator
 */
class EcrPearHelperPhpdoc extends EcrPearHelperConsole
{
    /**
     * title of generated documentation, default is 'Generated Documentation'
     *
     * @var string TITLE
     */
    public $title = "Your Project Documentation";

    /**
     * name to use for the default package. If not specified, uses 'default'
     *
     * @var string PACKAGES
     */
    public $packages = "yourProject";

    /**
     * name of a directory(s) to parse directory1,directory2
     *
     * @var string parseDirs
     */
    public $parseDirs = '';

    /**
     * name of a file(s) to parse file1,file2
     *
     * @var string parseFfiles
     */
    public $parseFiles = '';

    /**
     * where documentation will be put
     *
     * @var string targetdir Relative to JPATH_ROOT.
     */
    public $targetDir = 'documentation';

    /**
     * what outputformat to use (html/pdf)
     *
     * @var string OUTPUTFORMAT
     */
    public $outputFormat = 'HTML';

    /**
     * converter to be used
     *
     * @var string CONVERTER
     */
    public $converter = 'Smarty';

    /**
     * template to use
     *
     * @var string TEMPLATE
     */
    public $template = 'default';

    /**
     * parse elements marked as private
     *
     * @var bool (on/off) PRIVATE
     */
    public $private = 'off';

    /**
     * Available converters.
     *
     * @var array
     */
    public $converters = array(
    "HTML:frames:default"           => 'HTML:frames:default',
    "HTML:frames:earthli"           => 'HTML:frames:earthli',
    "HTML:frames:l0l33t"            => 'HTML:frames:l0l33t',
    "HTML:frames:phpdoc.de"         => 'HTML:frames:phpdoc.de',
    "HTML:frames:phphtmllib"        => 'HTML:frames:phphtmllib',
    "HTML:frames:phpedit"           => 'HTML:frames:phpedit',
    "HTML:frames:DOM/default"       => 'HTML:frames:DOM/default',
    "HTML:frames:DOM/earthli"       => 'HTML:frames:DOM/earthli',
    "HTML:frames:DOM/l0l33t"        => 'HTML:frames:DOM/l0l33t',
    "HTML:frames:DOM/phpdoc.de"     => 'HTML:frames:DOM/phpdoc.de',
    "HTML:frames:DOM/phphtmllib"    => 'HTML:frames:DOM/phphtmllib',
    "HTML:Smarty:default"           => 'HTML:Smarty:default',
    "HTML:Smarty:HandS"             => 'HTML:Smarty:HandS',
    "HTML:Smarty:PHP"               => 'HTML:Smarty:PHP',
    "PDF:default:default"           => 'PDF:default:default',
    "CHM:default:default"           => 'CHM:default:default',
    "XML:DocBook/peardoc2:default"  => 'XML:DocBook/peardoc2:default'
);

    // make documentation
    //"$PATH_PHPDOC" -d "$PATH_PROJECT" -t "$PATH_DOCS" -ti "$TITLE" -dn $PACKAGES
    // -o $OUTPUTFORMAT:$CONVERTER:$TEMPLATE -pp $PRIVATE

/**
 * Generate the documentation.
 *
 * @param array $options Options to pass
 *
 * @return string
 */
    public function process($options = array())
    {
        $args = array();

        if(count($options))
        {
            foreach($options as $o)
            {
                $args[] = '-'.$o.' on';
            }//foreach
        }

        /*
         * Parse directories, clean path.
         */
        $args[] = '-d '.str_replace('/', DS, $this->parseDirs);

        /*
         * Parse files, clean path.
         */
        $args[] = '-f '.str_replace('/', DS, $this->parseFiles);

        /*
         * Target directory, clean path.
         */
        $args[] = '-t '.str_replace('/', DS, $this->targetDir);

        /*
         * Output format
         */
        $args[] = '-o '.$this->outputFormat.':'.$this->converter.':'.$this->template;

        $results = $this->cliExec('phpdoc', $args);

        return $results;
    }//function
}//class
