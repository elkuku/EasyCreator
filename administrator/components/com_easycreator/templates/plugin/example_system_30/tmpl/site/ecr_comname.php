<?php
##*HEADER*##

/**
 * System Plugin.
 *
 * @package    ECR_COM_NAME
 * @subpackage Plugin
 */
class PlgSystemECR_COM_NAME extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param object $subject The object to observe
	 * @param array  $config  An array that holds the plugin configuration
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
		JLog::add('After framework load and application initialise.'
			, JLog::DEBUG, 'Example - onAfterInitialise');
	}

	/**
	 * Do something onAfterRoute
	 */
	public function onAfterRoute()
	{
		JLog::add('After the framework load, application initialised & route of client request.'
			, JLog::DEBUG, 'Example - onAfterRoute');
	}

	/**
	 * Do something onAfterDispatch
	 */
	public function onAfterDispatch()
	{
		JLog::add('After the framework has dispatched the application.'
			, JLog::DEBUG, 'Example - onAfterDispatch');
	}

	public function onBeforeCompileHead()
	{
		JLog::add('Before the framework creates the head section of the document.'
			, JLog::DEBUG, 'Example - onBeforeCompileHead');
	}

	/**
	 * Do something onAfterRender
	 */
	public function onBeforeRender()
	{
		JLog::add('Before the framework renders the application.'
			, JLog::DEBUG, 'Example - onBeforeRender');
	}

	/**
	 * Do something onAfterRender
	 */
	public function onAfterRender()
	{
		JLog::add('After the framework has rendered the application.'
			, JLog::DEBUG, 'Example - onAfterRender');
	}
}
