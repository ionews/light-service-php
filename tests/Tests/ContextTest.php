<?php
namespace Tests;

use LightServicePHP\Context;

/**
 * @author mcbarros
 */
class ContextTest extends \PHPUnit_Framework_TestCase {
    public function testBuildConvertsTheGivenArrayToAContext() {
        $context = Context::build(['test' => 'testValue']);
        $this->assertInstanceOf('LightServicePHP\Context', $context);
        $this->assertArrayHasKey('test', $context);
    }

    public function testChangeContextDoesntAffectOriginalArray() {
        $array = ['test' => 'testValue'];
        $context = Context::build($array);
        $context->test = 'testValue2';
        $this->assertEquals('testValue', $array['test']);
    }

    public function testSuccessTrueByDefault() {
        $context = Context::build([]);
        $this->assertTrue($context->success());
    }

    public function testFailureFalseByDefault() {
        $context = Context::build([]);
        $this->assertFalse($context->failure());
    }

    public function testHaltedFalseByDefault() {
        $context = Context::build([]);
        $this->assertFalse($context->halted());
    }

    public function testFailureMessageEmptyByDefault() {
        $context = Context::build([]);
        $this->assertEmpty($context->getFailureMessage());
    }

    public function testFailChangeSuccessToFalse() {
        $context = Context::build([]);
        $this->assertTrue($context->success());
        $context->fail();
        $this->assertFalse($context->success());
    }

    public function testFailChangeFailureToTrue() {
        $context = Context::build([]);
        $this->assertFalse($context->failure());
        $context->fail();
        $this->assertTrue($context->failure());
    }

    public function testHaltChangeHaltedToTrue() {
        $context = Context::build([]);
        $this->assertFalse($context->halted());
        $context->halt();
        $this->assertTrue($context->halted());
    }

    public function testFailToSetFailureMessage() {
        $context = Context::build([]);
        $this->assertEmpty($context->getFailureMessage());
        $context->fail('Error');
        $this->assertEquals('Error', $context->getFailureMessage());
    }

    public function testHasKeysReturnsTrueForEmptyArray() {
        $context = Context::build(['test' => 0, 'test1' => 1]);
        $this->assertTrue($context->hasKeys([]));
    }

    public function testHasKeys() {
        $context = Context::build(['test' => 0, 'test1' => 1]);
        $this->assertTrue($context->hasKeys(['test']));
        $this->assertTrue($context->hasKeys(['test1']));
        $this->assertTrue($context->hasKeys(['test', 'test1']));
        $this->assertTrue($context->hasKeys(['test1', 'test']));
        $this->assertFalse($context->hasKeys(['test2']));
        $this->assertFalse($context->hasKeys(['test', 'test2']));
    }
}
