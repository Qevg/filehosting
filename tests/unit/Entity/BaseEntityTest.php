<?php

namespace Filehosting\Tests\Unit\Entity;

use Filehosting\Entity\BaseEntity;
use PHPUnit\Framework\TestCase;

/**
 * Class BaseEntityTest
 * @package Filehosting\Tests\Unit\Entity
 */
class BaseEntityTest extends TestCase
{
    /**
     * @var new class extends BaseEntity
     */
    private $baseEntity;

    protected function setUp()
    {
        $this->baseEntity = new class extends BaseEntity {
            private $test;

            /**
             * @return mixed
             */
            public function getTest()
            {
                return $this->test;
            }

            /**
             * @param mixed $test
             */
            public function setTest($test)
            {
                $this->test = $test;
            }
        };
    }

    public function testSuccessfulSetValues()
    {
        $var = array('test' => 'qwe');
        $this->baseEntity->setValues($var);
        $this->assertTrue(method_exists($this->baseEntity, 'getTest'));
        $this->assertEquals($this->baseEntity->getTest(), $var['test']);
    }

    public function testUnsuccessfulSetValues()
    {
        $var = array('id' => '1');
        $this->baseEntity->setValues($var);
        $this->assertFalse(method_exists($this->baseEntity, 'getId'));
    }
}
