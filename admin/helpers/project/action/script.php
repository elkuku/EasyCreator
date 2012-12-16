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
 */
class EcrProjectActionScript extends EcrProjectAction
{
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
        $html = array();

        $html[] = $this->getLabel($cnt, 'script', jgettext('Path'));
        $html[] = $this->getInput($cnt, 'script', $this->script);
        $html[] = '<br />';
        $html[] = $this->getLabel($cnt, '', jgettext('Abort on failure'));
        $html[] = EcrHtmlSelect::yesno('fields['.$cnt.'][abort]', $this->abort);

        return implode("\n", $html);
    }

    /**
     * Perform the action.
     *
     * @param EcrProjectZiper $ziper
     *
     * @return \EcrProjectAction
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

        //$this->abort('ERROR: Script terminated with exit status: '.$retVal, $ziper);

        if(0 != $retVal)
        {
            $this->abort(
                sprintf('%1$s: %2$s finished with exit status %3$d'
                    , $this->name, $this->script, $retVal)
                , $ziper);
        }
        else
        {
            $ziper->logger->log('Script terminated with exit status 0');
        }

        /*
        if(0 == $retVal)
        {
        }
        else
        {
            $ziper->logger->log('Script terminated with exit status: '.$retVal);

            if($this->abort)
            {
                $ziper->addFailure(sprintf('%s: %s finished with exit status %d'
                    , $this->name, $this->script, $retVal));

                $ziper->setInvalid();
            }
        }
        */

        return $this;
    }
}
