<?php
/**
 * User: elkuku
 * Date: 01.06.12
 * Time: 06:37
 */

require_once 'PHPUnit/Autoload.php';

class WebTest extends PHPUnit_Extensions_SeleniumTestCase
{
    protected $captureScreenshotOnFailure = TRUE;
    protected $screenshotPath = '/home/elkuku/test/screeenz';
    protected $screenshotUrl = 'http://localhost/screenshots';

    protected function setUp()
    {
        $this->setBrowser('*firefox');
        $this->setBrowserUrl('http://www.example.com/');
    }

    public function testTitle()
    {
        $this->open('http://www.example.com/');
        $this->assertTitle('Example WWW Page');
    }

    public function xtestRequirements()
    {

    }
}
