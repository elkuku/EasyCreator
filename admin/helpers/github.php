<?php
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 25-Apr-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 *
 */

/**
 * GitHub helper class.
 *
 * @property-read EcrGithubDownloads $downloads
 */
class EcrGithub extends JGithub
{
    /**
     * @var    EcrGithubDownloads  GitHub API object for forks.
     * @since  11.3
     */
    protected $downloads;

    /**
     * Constructor.
     *
     * @param   JRegistry    $options  GitHub options object.
     * @param   JGithubHttp  $client   The HTTP client object.
     *
     * @since   11.3
     */
    public function __construct(JRegistry $options = null, JGithubHttp $client = null)
    {
        $client = isset($client) ? $client : new EcrGithubHttp($this->options);

        parent::__construct($options, $client);
    }

    /**
     * @param string $name
     *
     * @return EcrGithubDownloads|JGithubObject
     */
    public function __get($name)
    {
        if($name == 'downloads')
        {
            if($this->downloads == null)
            {
                $this->downloads = new EcrGithubDownloads($this->options, $this->client);
            }

            return $this->downloads;
        }

        return parent::__get($name);
    }
}
