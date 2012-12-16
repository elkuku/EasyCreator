<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 25-Apr-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * GitHub http class.
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
