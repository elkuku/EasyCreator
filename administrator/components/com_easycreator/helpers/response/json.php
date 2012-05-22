<?php
/**
 * User: elkuku
 * Date: 25.04.12
 * Time: 14:01
 */

/**
 * JSON response class.
 */
class EcrResponseJson
{
    public $status = 0;

    public $debug = '';

    public $message = '';

    public function __toString()
    {
        return json_encode($this);
    }
}
