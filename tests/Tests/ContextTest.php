<?php
namespace Tests;

use LightServicePHP\Context;

/**
 * @author mcbarros
 */
class ContextTest extends \PHPUnit_Framework_TestCase {
    public function testBuildConvertsTheGivenArrayToAContext() {
        $context = Context::build(array('test' => 'testValue'));
        $this->assertInstanceOf('LightServicePHP\Context', $context);
        $this->assertArrayHasKey('test', $context);
    }

    public function testChangeContextDoesntAffectOriginalArray() {
        $array = array('test' => 'testValue');
        $context = Context::build($array);
        $context->test = 'testValue2';
        $this->assertEquals('testValue', $array['test']);
    }

    public function testSuccessTrueByDefault() {
        $context = Context::build(array());
        $this->assertTrue($context->success());
    }

    public function testFailureFalseByDefault() {
        $context = Context::build(array());
        $this->assertFalse($context->failure());
    }

    public function testHaltedFalseByDefault() {
        $context = Context::build(array());
        $this->assertFalse($context->halted());
    }

    public function testFailureMessageEmptyByDefault() {
        $context = Context::build(array());
        $this->assertEmpty($context->getFailureMessage());
    }

    public function testFailChangeSuccessToFalse() {
        $context = Context::build(array());
        $this->assertTrue($context->success());
        $context->fail();
        $this->assertFalse($context->success());
    }

    public function testFailChangeFailureToTrue() {
        $context = Context::build(array());
        $this->assertFalse($context->failure());
        $context->fail();
        $this->assertTrue($context->failure());
    }

    public function testHaltChangeHaltedToTrue() {
        $context = Context::build(array());
        $this->assertFalse($context->halted());
        $context->halt();
        $this->assertTrue($context->halted());
    }

    public function testFailToSetFailureMessage() {
        $context = Context::build(array());
        $this->assertEmpty($context->getFailureMessage());
        $context->fail('Error');
        $this->assertEquals('Error', $context->getFailureMessage());
    }

    public function testHasKeysReturnsTrueForEmptyArray() {
        $context = Context::build(array('test' => 0, 'test1' => 1));
        $this->assertTrue($context->hasKeys(array()));
    }

    public function testHasKeys() {
        $context = Context::build(array('test' => 0, 'test1' => 1));
        $this->assertTrue($context->hasKeys(array('test')));
        $this->assertTrue($context->hasKeys(array('test1')));
        $this->assertTrue($context->hasKeys(array('test', 'test1')));
        $this->assertTrue($context->hasKeys(array('test1', 'test')));
        $this->assertFalse($context->hasKeys(array('test2')));
        $this->assertFalse($context->hasKeys(array('test', 'test2')));
    }
}
