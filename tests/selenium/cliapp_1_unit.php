<?php
class Example extends PHPUnit_Extensions_SeleniumTestCase
{
    protected $captureScreenshotOnFailure = TRUE;
    protected $screenshotPath = '/home/elkuku/test/screeenz';
    protected $screenshotUrl = 'http://localhost/screenshots';

    protected function setUp()
  {
    $this->setBrowser("*chrome");
    $this->setBrowserUrl("http://storm.kuku/web_easycreator/administrator");
  }

  public function testMyTestCase()
  {
    $this->open("/web_easycreator/administrator/index.php");
    $this->type("id=mod-login-username", "admin");
    $this->type("id=mod-login-password", "test");
    $this->click("css=input.hidebtn");
    $this->waitForPageToLoad("30000");
    $this->click("link=easycreator");
    $this->waitForPageToLoad("30000");
    $this->select("id=ecr_project", "label=Neues Projekt");
    $this->waitForPageToLoad("30000");
    $this->click("link=Hello World");
    $this->waitForPageToLoad("30000");
    $this->type("id=com_name", "TestOne");
    $this->click("css=i.imgR.icon16-2rightarrow");
    $this->waitForPageToLoad("30000");
    $this->click("css=div.btn > p");
    $this->waitForPageToLoad("30000");
    $this->verifyTextPresent("cliapp");
    $this->verifyTextPresent("TestOne");
    $this->verifyTextPresent("testone");
    $this->click("link=CodeEyes");
    $this->waitForPageToLoad("30000");
    $this->click("css=#btn_runcli > i.img-btn.icon16-icon");
    $this->waitForPageToLoad("30000");
    $this->click("link=Execute");
      //pause(5);
     // $this->pause("5");
    $this->waitForTextPresent("Hello world!");
    $this->click("link=Projekt");
    $this->waitForPageToLoad("30000");
    $this->click("id=btn_projectdelete");
    $this->waitForPageToLoad("30000");
    $this->click("xpath=(//img[@alt='Löschen'])[2]");
    $this->waitForPageToLoad("30000");
    $this->verifyTextPresent("Das Projekt TestOne wurde gelöscht");
    $this->click("css=span.logout > a");
    $this->waitForPageToLoad("30000");
  }
}
?>
