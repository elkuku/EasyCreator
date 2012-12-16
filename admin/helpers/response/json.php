<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 25-Apr-2012
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * JSON response class.
 */
class EcrResponseJson
{
    public $status = 0;

    public $debug = '';

    public $message = '';

    public $data = null;

    /**
     * Constructor.
     *
     * @param null|stdClass $data
     */
    public function __construct(stdClass $data = null)
    {
        $this->data = $data ?: new stdClass;
    }

    /**
     * Convert to string.
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode($this);
    }
}
