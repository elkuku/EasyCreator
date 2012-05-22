<?php defined('_JEXEC') || die('=;)');
/**
 * @package    EasyCreator
 * @subpackage Helpers
 * @author     Nikolai Plath (elkuku)
 * @author     Created on 25-Apr-2011
 * @license    GNU/GPL, see JROOT/LICENSE.php
 */

/**
 * GitHub response class.
 *
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
