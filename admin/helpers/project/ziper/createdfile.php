<?php
/**
 * User: elkuku
 * Date: 23.06.12
 * Time: 23:34
 */

/**
 * Class to describe a created file.
 */
class EcrProjectZiperCreatedfile
{
    public $path = '';

    public $name = '';

    public $downloadUrl = '';

    public $alternateDownload = '';

    /**
     * Constructor.
     *
     * @param string $path Full path to file.
     * @param string $url
     */
    public function __construct($path, $url)
    {
        $this->path = $path;

        $this->name = basename($path);

        $this->downloadUrl = $url;
    }
}
