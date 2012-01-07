<?php
/**
 * @package
 * @subpackage
 * @author		Nikolai Plath {@link http://www.nik-it.de}
 * @author		Created on 21.10.2009
 */

class EasyProjectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var EasyProject
     */
    private $project;

    protected function setUp()
    {
        defined('ECR_DEBUG') or define ('ECR_DEBUG', 0);
        defined('JPATH_COMPONENT') or define ('JPATH_COMPONENT', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easycreator');
        require_once JPATH_COMPONENT.DS.'helpers'.DS.'projecthelper.php';
        require_once JPATH_COMPONENT.DS.'helpers'.DS.'html.php';
        $this->project = EasyProjectHelper::newProject('empty');
    }//function

    public function testEmptyProjectIsEmpty()
    {
        $this->assertEquals($this->project->type, '');
        $this->assertEquals($this->project->name, '');
        $this->assertEquals($this->project->comName, '');
    }//function

    public function testCannnotDeleteEmptyProject()
    {
        $ret = $this->project->remove();
        $this->assertEquals($ret, false);
    }

    public function testSetName()
    {
        $this->project->name = 'foo';
        $this->assertEquals($this->project->name, 'foo');
    }//function

    public function testAddSubstitute()
    {
        $this->project->addSubstitute('foo', 'bar');
        $this->assertEquals($this->project->getSubstitute('foo'), 'bar');
    }//function
}//class
