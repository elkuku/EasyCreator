<?php
require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class Example extends PHPUnit_Extensions_SeleniumTestCase
{
  protected function setUp()
  {
    $this->setBrowser("*chrome");
    $this->setBrowserUrl("http://storm.kuku/web_easycreator/administrator");
  }

  public function testMyTestCase()
  {
    $this->open("/web_easycreator/administrator/index.php?option=com_easycreator");
    $this->select("id=ecr_project", "label=Neues Projekt");
    $this->waitForPageToLoad("30000");
    $this->click("xpath=(//a[contains(text(),'Hello World')])[4]");
    $this->waitForPageToLoad("30000");
    $this->type("id=com_name", "Test");
    $this->click("link=Weiter");
    $this->waitForPageToLoad("30000");
    $this->click("css=div.ecr_button");
    $this->waitForPageToLoad("30000");
    $this->verifyTextPresent("webapp");
    $this->click("css=span.icon-32-xeyes");
    $this->waitForPageToLoad("30000");
    $this->click("id=btn_runwap");
    $this->waitForPageToLoad("30000");
    $this->click("link=exact:http://storm.kuku/web_easycreator/webapps/test/test.php");
    $this->verifyTextPresent("Hello WWW!");
  }
}
?>
