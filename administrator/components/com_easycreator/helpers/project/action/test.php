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
class EcrProjectActionTest extends EcrProjectAction
{
    protected $type = 'test';

    protected $name = 'A Test';

    public $foo = '';

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

        $html[] = $this->getLabel($cnt, 'foo', jgettext('Foo'));
        $html[] = $this->getInput($cnt, 'foo', $this->foo);
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
        $command = 'myCustomCommand';

        $command = $this->replaceVars($command, $ziper);

        $command = escapeshellcmd($command);

        $ziper->logger->log('Executing: '.$command);

        $output = shell_exec($command.' 2>&1');

        $ziper->logger->log('Script output:'.$output);

        $anErrorOccured = false;

        if($anErrorOccured)
        {
            $this->abort(
                sprintf('%1$s says %2$s :('
                    , $this->name, $this->foo)
                , $ziper);
        }

        $ziper->logger->log('Test script executed succesfully :)');

        return $this;
    }
}
