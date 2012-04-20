<?php

require_once 'Testing/Selenium.php';

class Example extends PHPUnit_Framework_TestCase
{
  protected function setUp()
  {
    $this = new Testing_Selenium("*chrome", "http://storm.kuku/web_easycreator/administrator")
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
    try {
        $this->assertTrue($this->isTextPresent("webapp"));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    $this->click("css=span.icon-32-xeyes");
    $this->waitForPageToLoad("30000");
    $this->click("id=btn_runwap");
    $this->waitForPageToLoad("30000");
    $this->click("link=exact:http://storm.kuku/web_easycreator/webapps/test/test.php");
    try {
        $this->assertTrue($this->isTextPresent("Hello WWW!"));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
  }
}
?>