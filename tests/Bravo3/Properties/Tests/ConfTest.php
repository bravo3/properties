<?php
namespace Bravo3\Properties\Tests;

use Bravo3\Properties\Conf;

class ConfTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @small
     */
    public function testSolid()
    {
        Conf::init(__DIR__.'/Resources');
        $conf = Conf::getInstance();

        $this->assertEquals('hello world', $conf['some.property']);
    }

    /**
     * @small
     */
    public function testStatic()
    {
        Conf::init(__DIR__.'/Resources');
        $this->assertEquals('hello world', Conf::get('some.property'));
    }

    /**
     * @small
     */
    public function testDefault()
    {
        Conf::init(__DIR__.'/Resources');
        $this->assertEquals('default value', Conf::get('some.other.property', 'default value'));
    }

    /**
     * @small
     */
    public function testAlternative()
    {
        Conf::init(__DIR__.'/Resources', 'alternative.yml');
        $this->assertEquals('bar', Conf::get('foo'));
    }

    /**
     * @small
     */
    public function testDelim()
    {
        Conf::init(__DIR__.'/Resources', 'properties.yml', '_');
        $this->assertEquals('hello world', Conf::get('some_property'));
    }

    /**
     * @small
     * @expectedException \Bravo3\Properties\Exception\PropertyNotFoundException
     */
    public function testMissingProperty()
    {
        Conf::init(__DIR__.'/Resources');
        $this->assertNull(Conf::get('invalidproperty'));
        Conf::getInstance()->offsetGet('invalidproperty');
    }

    /**
     * @small
     * @expectedException \Bravo3\Properties\Exception\PropertyNotFoundException
     */
    public function testMissingPropertyArray()
    {
        Conf::init(__DIR__.'/Resources');
        $this->assertNull(Conf::get('invalid.property'));
        Conf::getInstance()->offsetGet('invalid.property');
    }

    /**
     * @small
     * @expectedException \Bravo3\Properties\Exception\UnreadableConfigException
     */
    public function testMissingPropertiesFile()
    {
        Conf::init(__DIR__.'/Resources', 'missing.yml');
    }

}
 