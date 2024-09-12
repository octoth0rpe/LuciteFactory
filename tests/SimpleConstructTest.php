<?php

declare(strict_types=1);
use Lucite\Container\Container;
use Lucite\Factory\Factory;
use PHPUnit\Framework\TestCase;

class TestClass
{
    public string $testkey1 = '';
    public string $testkey2 = '';
    public string $testkey3 = '';
    public function setTestKey1(string $testval): void
    {
        $this->testkey1 = $testval;
    }
    public function setTestKey2(string $testval): void
    {
        $this->testkey2 = $testval;
    }
    public function setTestKey3(string $testval): void
    {
        $this->testkey3 = $testval;
    }
}

class SimpleConstructTest extends TestCase
{
    public function testWithoutSetters(): void
    {
        $container = new Container();
        $factory = new Factory($container);
        $obj = $factory->assemble(TestClass::class);
        $this->assertInstanceOf(TestClass::class, $obj);
    }

    public function testSingleSetterViaRegisterSetter(): void
    {
        $container = new Container();
        $container->add('testkey1', 'testval');

        $factory = new Factory($container);
        $factory->registerSetter('setTestKey1', 'testkey1');

        $obj = $factory->assemble(TestClass::class);
        $this->assertEquals($obj->testkey1, 'testval');
        $this->assertEquals($obj->testkey2, '');
        $this->assertEquals($obj->testkey3, '');
    }

    public function testMultipleSetterViaChaining(): void
    {
        $container = new Container();
        $container
            ->add('testkey1', 'testval1')
            ->add('testkey2', 'testval2');

        $factory = new Factory($container);
        $factory
            ->registerSetter('setTestKey1', 'testkey1')
            ->registerSetter('setTestKey2', 'testkey2');

        $obj = $factory->assemble(TestClass::class);
        $this->assertEquals($obj->testkey1, 'testval1');
        $this->assertEquals($obj->testkey2, 'testval2');
        $this->assertEquals($obj->testkey3, '');
    }

    public function testMultipleSetterViaInitialSetterMap(): void
    {
        $container = new Container();
        $container
            ->add('testkey1', 'testval1')
            ->add('testkey2', 'testval2')
            ->add('testkey3', 'testval3');

        $factory = new Factory($container, [
            'setTestKey1' => 'testkey1',
            'setTestKey3' => 'testkey3',
        ]);

        $obj = $factory->assemble(TestClass::class);
        $this->assertEquals($obj->testkey1, 'testval1');
        $this->assertEquals($obj->testkey2, '');
        $this->assertEquals($obj->testkey3, 'testval3');
    }
}
