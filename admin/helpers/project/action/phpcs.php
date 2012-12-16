<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 22-May-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * Custom action class.
 *
 * @see http://pear.php.net/manual/en/package.php.php-codesniffer.advanced-usage.php
 */
class EcrProjectActionPhpcs extends EcrProjectAction
{
    protected $name = 'PHP Code Sniffer';

    public $standard = '';

    public $extensions = '';

    public $arguments = '';

    public $ignore = '';

    public $warningThreshold = 0;

    public $errorThreshold = 0;

    /**
     * Get the input fields
     *
     * @param int $cnt A counter value.
     *
     * @return string
     */
    public function getFields($cnt)
    {
        $html = array();

        $html[] = '<label class="inline2" for="fields_'.$cnt.'_standard">'.jgettext('Coding Standard').'</label>';
        $html[] = '<input type="text" value="'.$this->standard.'"'
            .' name="fields['.$cnt.'][standard]" id="fields_'.$cnt.'_standard">'
            .'<br />';

        $html[] = '<label class="inline2" for="fields_'.$cnt.'_extensions">'.jgettext('Extensions').'</label>';
        $html[] = '<input type="text" value="'.$this->extensions.'"'
            .' name="fields['.$cnt.'][extensions]" id="fields_'.$cnt.'_extensions">'
            .'<br />';

        $html[] = '<label class="inline2" for="fields_'.$cnt.'_arguments">'.jgettext('Arguments').'</label>';
        $html[] = '<input type="text" value="'.$this->arguments.'"'
            .' name="fields['.$cnt.'][arguments]" id="fields_'.$cnt.'_arguments">'
            .'<br />';

        $html[] = '<label class="inline2" for="fields_'.$cnt.'_ignore">'.jgettext('Ignore').'</label>';
        $html[] = '<input type="text" value="'.$this->ignore.'"'
            .' name="fields['.$cnt.'][ignore]" id="fields_'.$cnt.'_ignore">'
            .'<br />';

        $html[] = '<label class="inline2" for="fields_'.$cnt.'_wThreshold">'.jgettext('Warning Threshold').'</label>';
        $html[] = '<input type="text" class="span1" value="'.$this->warningThreshold.'"'
            .' name="fields['.$cnt.'][warningThreshold]" id="fields_'.$cnt.'_wThreshold">'
            .'<br />';

        $html[] = '<label class="inline2" for="fields_'.$cnt.'_eThreshold">'.jgettext('Error Threshold').'</label>';
        $html[] = '<input type="text" class="span1" value="'.$this->errorThreshold.'"'
            .' name="fields['.$cnt.'][errorThreshold]" id="fields_'.$cnt.'_eThreshold">';

        return implode("\n", $html);
    }

    /**
     * Perform the action.
     *
     * @param EcrProjectZiper $ziper
     *
     * @return EcrProjectAction
     */
    public function run(EcrProjectZiper $ziper)
    {
        $logger = $ziper->logger;
        $project = $ziper->project;

        $logger->log('Executing CodeSniffer');

        $files = implode(' ', $project->copies);

        $ignore =($this->ignore) ? '--ignore='.$this->ignore : '';
        $extensions =($this->extensions) ? '--extensions='.$this->extensions : '';

        $parts = array(
            'phpcs'
            , '-p'
        , '--report=summary'
        , '--standard='.$this->standard
        , $ignore
        , $this->arguments
        , $files
        );

        $cmd = implode(' ', $parts);

        $cmd = escapeshellcmd($cmd);

        $logger->log($cmd);

        $output = shell_exec($cmd.' 2>&1 | tee -a '.$ziper->logFile);

        $pattern = "/A TOTAL OF (\d+) ERROR\(S\) AND (\d+) WARNING\(S\) WERE FOUND IN (\d+) FILE\(S\)/";

        preg_match_all($pattern, $output, $matches);

        if($matches && isset($matches[3]))
        {
            $errors = (isset($matches[1][0])) ? $matches[1][0] : 'n/a';
            $warnings = (isset($matches[2][0])) ? $matches[2][0] : 'n/a';
            $filesProcessed = (isset($matches[3][0])) ? $matches[3][0] : 'n/a';

            $logger->log('PHP CodeSniffer results:');

            $logger->log(sprintf('Files processed: %s', $filesProcessed));
            $logger->log(sprintf('Errors:   %s', $errors));
            $logger->log(sprintf('Warnings: %s', $warnings));

            if(0 != $this->warningThreshold)
            {
                if($warnings >= $this->warningThreshold)
                {
                    $ziper->addFailure(sprintf('%s: The warning threshold of %d has been exceeded (%d warnings)'
                        , $this->name, $this->warningThreshold, $warnings));

                    $ziper->setInvalid();
                }
            }

            if(0 != $this->errorThreshold)
            {
                if($errors >= $this->errorThreshold)
                {
                    $ziper->addFailure(sprintf('%s: The error threshold of %d has been exceeded (%d errors)'
                        , $this->name, $this->errorThreshold, $errors));

                    $ziper->setInvalid();
                }
            }
        }

        return $this;
    }
}
