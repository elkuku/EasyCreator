<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elkuku
 * Date: 18.04.12
 * Time: 17:56
 * To change this template use File | Settings | File Templates.
 */

class EcrGithubHttp extends JGithubHttp
{
    /**
     * Constructor.
     *
     * @param   JRegistry       &$options   Client options object.
     * @param   JHttpTransport  $transport  The HTTP transport object.
     */
    public function __construct(JRegistry &$options = null, JHttpTransport $transport = null)
    {
        $options = isset($options) ? $options : new JRegistry;
        $transport =(null == $transport) ? new JHttpTransportCurl($options) : $transport;

        parent::__construct($options, $transport);
    }
}
