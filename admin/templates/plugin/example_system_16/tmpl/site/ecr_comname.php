<?php
##*HEADER*##

jimport('joomla.plugin.plugin');

/**
 * System Plugin.
 *
 * @package    ECR_COM_NAME
 * @subpackage Plugin
 */
class plgSystemECR_COM_NAME extends JPlugin
{
    /**
     * Constructor
     *
     * @param object $subject The object to observe
     * @param array $config  An array that holds the plugin configuration
     */
    public function __construct(& $subject, $config)
    {
        $doSomething = 'here';

        parent::__construct($subject, $config);
    }

    /**
     * Do something onAfterInitialise
     */
    public function onAfterInitialise()
    {
        $this->_log(
            'onAfterInitialise',
            'After framework load and application initialise.'
            );
    }

    /**
     * Do something onAfterRoute
     */
    public function onAfterRoute()
    {
        $this->_log(
            'onAfterRoute',
            'After the framework load, application initialised & route of client request.'
            );
    }

    /**
     * Do something onAfterDispatch
     */
    public function onAfterDispatch()
    {
        $this->_log(
            'onAfterDispatch',
            'After the framework has dispatched the application.'
            );
    }

    public function onBeforeCompileHead()
    {
        $this->_log(
            'onBeforeCompileHead',
            'Before the framework creates the head section of the document.'
            );
    }

    /**
     * Do something onAfterRender
     */
    public function onBeforeRender()
    {
        $this->_log(
            'onBeforeRender',
            'Before the framework renders the application.'
            );
    }

    /**
     * Do something onAfterRender
     */
    public function onAfterRender()
    {
        $this->_log(
            'onAfterRender',
            'After the framework has rendered the application.'
            );
    }

    /**
     * Log events.
     *
     * @param string $event The event to be logged.
     * @param string $comment A comment about the event.
     */
    private function _log ($status, $comment)
    {
        jimport('joomla.error.log');

        JLog::getInstance('plugin_system_example_log.php')
        ->addEntry(array('event' => $event, 'comment' => $comment));
    }
}
