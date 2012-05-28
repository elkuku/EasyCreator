<?php

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

class Example extends PHPUnit_Extensions_SeleniumTestCase
{
  protected function setUp()
  {
    $this->setBrowser("*chrome");
    $this->setBrowserUrl("http://test2-2.nik/");
  }

  public function testMyTestCase()
  {
    $this->open("/easysvn_easycreator_16/administrator/index.php?option=com_easycreator");
    $this->select("ecr_project", "label=New project");
    $this->waitForPageToLoad("30000");
    $this->click("//div[@onclick=\"setTemplate('component', 'mvc_10_16'); goWizard(2);\"]");
    $this->waitForPageToLoad("30000");
    $this->type("com_name", "FunctionalTest");
    $this->type("version", "1");
    $this->type("description", "This is a test");
    $this->click("list_postfix");
    $this->type("list_postfix", "ListX");
    $this->type("author", "FunctionalTestName");
    $this->type("authorEmail", "FunctionalTest_e-mail");
    $this->type("authorUrl", "FunctionalTest_url");
    $this->type("license", "FunctionalTest_license");
    $this->type("copyright", "FunctionalTest_copyright");
    $this->click("link=Next");
    $this->waitForPageToLoad("30000");
    $this->click("//form[@id='adminForm']/div[2]/div[2]/div[6]/p");
    $this->waitForPageToLoad("30000");
    try {
        $this->assertTrue($this->isTextPresent("Name FunctionalTest"));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    try {
        $this->assertTrue($this->isTextPresent("Extension name com_functionaltest"));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    try {
        $this->assertEquals("1", $this->getValue("buildvars[version]"));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    try {
        $this->assertEquals("This is a test", $this->getValue("buildvars[description]"));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    try {
        $this->assertTrue($this->isTextPresent("ECR Template mvc_10_16"));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    try {
        $this->assertEquals("FunctionalTestName", $this->getValue("buildvars-author"));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    try {
        $this->assertEquals("FunctionalTest_e-mail", $this->getValue("buildvars-authorEmail"));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    try {
        $this->assertEquals("FunctionalTest_url", $this->getValue("buildvars-authorUrl"));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    try {
        $this->assertEquals("FunctionalTest_license", $this->getValue("buildvars-license"));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    try {
        $this->assertEquals("FunctionalTest_copyright", $this->getValue("buildvars-copyright"));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    try {
        $this->assertEquals("on", $this->getValue("jversion16"));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    try {
        $this->assertEquals("functionaltest", $this->getValue("menu[text]"));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    try {
        $this->assertTrue((bool)preg_match('/^exact:index\.php[\s\S]option=com_functionaltest$/',$this->getValue("menu[link]")));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    try {
        $this->assertEquals("../media/com_functionaltest/images/com_functionaltest-16x16.ico", $this->getValue("img-"));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    try {
        $this->assertEquals("functionaltest", $this->getValue("submenu[1][text]"));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    try {
        $this->assertTrue((bool)preg_match('/^exact:index\.php[\s\S]option=com_functionaltest&view=functionaltestlistx$/',$this->getValue("submenu[1][link]")));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    try {
        $this->assertEquals("categories", $this->getValue("submenu[2][text]"));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    try {
        $this->assertTrue((bool)preg_match('/^exact:index\.php[\s\S]option=com_categories&extension=com_functionaltest$/',$this->getValue("submenu[2][link]")));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
    $this->click("btn_projectdelete");
    $this->waitForPageToLoad("30000");
    $this->click("//td[@onclick=\"submitbutton('delete_project_full')\"]");
    $this->waitForPageToLoad("30000");
    try {
        $this->assertTrue($this->isTextPresent("The project FunctionalTest has been removed"));
    } catch (PHPUnit_Framework_AssertionFailedError $e) {
        array_push($this->verificationErrors, $e->toString());
    }
  }
}
