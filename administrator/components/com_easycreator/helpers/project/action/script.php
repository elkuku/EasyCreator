<?php
/**
 * User: elkuku
 * Date: 22.05.12
 * Time: 19:28
 */

/**
 * Custom action class.
 */
class EcrProjectActionScript extends EcrProjectAction
{
    protected $type = 'script';

    protected $name = 'Script';

    public $script = '';

    public $abort = 0;

    /**
     * Get the input fields
     *
     * @param int $cnt A counter value.
     *
     * @return string
     */
    public function getFields($cnt)
    {
        // TODO: Implement getFields() method.
        $html = array();

        $html[] = '<label class="inline" for="fields_'.$cnt.'_script">'.jgettext('Path').'</label>'
            .'<input class="span4" type="text" name="fields['.$cnt.'][script]" id="fields_'.$cnt.'_script"'
            .' value="'.$this->script.'"><br />';

        $html[] = '<label class="inline" for="">'.jgettext('Abort on failure').'</label> '
            .EcrHtmlSelect::yesno('fields['.$cnt.'][abort]', $this->abort);

        return implode("\n", $html);
    }

    /**
     * Perform the action.
     *
     * @param EcrProjectZiper $ziper
     *
     * @return bool true if successful, false to interrupt the build process
     */
    public function run(EcrProjectZiper $ziper)
    {
        $command = $this->replaceVars($this->script, $ziper);

        $command = escapeshellcmd($command);

        $ziper->logger->log('Executing: '.$command);

        $retVal = 0;

//        $output = shell_exec($command.' 2>&1 | tee -a '.$ziper->logFile);
        //passthru($command.' 2>&1 | tee -a '.$ziper->logFile, $retVal);
        //system($command.' 2>&1 | tee -a '.$ziper->logFile, $retVal);
        //exec($command.' 2>&1 | tee -a '.$ziper->logFile, $output, $retVal);

        system($command.' >> '.$ziper->logFile.' 2>&1', $retVal);

        $ziper->logger->log('Script terminated with exit status: '.$retVal);

        if(0 != $retVal)
        {
            if($this->abort)
            {
                $ziper->addFailure(sprintf('%s: %s finished with exit status %d'
                    , $this->name, $this->script, $retVal));

                $ziper->setInvalid();
            }
        }
    }
}
