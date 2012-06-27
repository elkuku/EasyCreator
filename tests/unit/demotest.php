<?php

/**
 * Test Test Class.
 */
class DemoTest extends PHPUnit_Framework_TestCase
{
    public function testAuthor()
    {
        $author = 'Nikolai Plath';

        $this->assertSame($author, 'Nikolai Plath');
    }

    public function testArrayKey()
    {
        $data = array(
            'nickname' => 'elkuku',
            'forename' => 'Nikolai',
            'surname' => 'Plath'
        );

        $this->assertArrayHasKey('nickname', $data);
        $this->assertArrayHasKey('forename', $data);
        $this->assertArrayHasKey('surname', $data);
    }

    public function testObjectAttribute()
    {
        $object = new stdClass;

        $object->nickname = 'elkuku';
        $object->forename = 'Nikolai';
        $object->surname = 'Plath';

        $this->assertObjectHasAttribute('nickname', $object);
        $this->assertObjectHasAttribute('forename', $object);
        $this->assertObjectHasAttribute('surname', $object);
    }
}
