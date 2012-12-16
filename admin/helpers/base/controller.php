<?php
/**
 * User: elkuku
 * Date: 10.06.12
 * Time: 10:44
 */

/**
 * EasyCreator base controller class.
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
     * @param array $config
     */
    public function __construct($config = array())
    {
        $this->response = new EcrResponseJson;

        parent::__construct($config);
    }
}
