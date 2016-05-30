<?php
/**
 * @package    EasyCreator
 * @author     Nikolai Plath (elkuku) <der.el.kuku@gmail.com>
 * @created    Created on 10-Jun-12
 * @copyright  2008 elkuku
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */
defined('_JEXEC') || die('=;)');

/**
 * EasyCreator base controller class.
 *
 * @since  0
 */
class EcrBaseController extends JControllerLegacy
{
	/**
	 * @var EcrResponseJson
	 */
	protected $response = null;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  Configuration object.
	 */
	public function __construct($config = array())
	{
		$this->response = new EcrResponseJson;

		parent::__construct($config);
	}
}
