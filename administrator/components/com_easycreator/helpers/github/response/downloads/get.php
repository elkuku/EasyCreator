<?php
/**
 * Created by JetBrains PhpStorm.
 * User: elkuku
 * Date: 17.04.12
 * Time: 17:08
 * To change this template use File | Settings | File Templates.
 */

/**
 * @link http://developer.github.com/v3/repos/downloads/
 */
class EcrGithubResponseDownloadsGet
{
    public $url; //": "https://api.github.com/repos/octocat/Hello-World/downloads/1",
    public $html_url; //": "https://github.com/repos/octocat/Hello-World/downloads/new_file.jpg",
    public $id; //": 1,
    public $name; //": "new_file.jpg",
    public $description; //": "Description of your download",
    public $size; //": 1024,
    public $download_count; //": 40,
    public $content_type; //": ".jpg"
}
