<?php
namespace Tests;

use LightServicePHP\Action;

class MockAction extends Action {
    public $count = 0;

    protected function perform() { $this->count++; }
}

class FailMockAction extends Action {
    public $count = 0;

    protected function perform() {
        $this->fail('Error');
        $this->count++;
    }
}

class HaltMockAction extends Action {
    public $count = 0;

    protected function perform() {
        $this->halt();
        $this->count++;
    }
}

class ExpectsMockAction extends Action {
    protected $expects = array('test');
    public $count = 0;

    protected function perform() { $this->count++; }
}

class PromisesMockAction extends Action {
    protected $expects = array('test');
    protected $promises = array('value');
    public $count = 0;

    protected function perform() {
        if ($this->context->test == 1) {
            $this->context->value = 1;
        }
        $this->count++;
    }
}

class ActionTest extends \PHPUnit_Framework_TestCase {
    public function testExecuteReturnsAnActionInstance() {
        $result = MockAction::execute();
        $this->assertInstanceOf('LightServicePHP\Action', $result);
        $this->assertInstanceOf('Tests\MockAction', $result);
    }

    public function testExecuteCallsPerformOnce() {
        $result = MockAction::execute();
        $this->assertEquals(1, $result->count);
    }

    public function testExecuteCreatesAContext() {
        $result = MockAction::execute();
        $this->assertNotNull($result->getContext());
        $this->assertInstanceOf('LightServicePHP\Context', $result->getContext());
    }

    public function testSuccessTrueByDefault() {
        $result = MockAction::execute();
        $this->assertTrue($result->success());
    }

    public function testFailureFalseByDefault() {
        $result = MockAction::execute();
        $this->assertFalse($result->failure());
    }

    public function testHaltedFalseByDefault() {
        $result = MockAction::execute();
        $this->assertFalse($result->halted());
    }

    public function testFailureMessageEmptyByDefault() {
        $result = MockAction::execute();
        $this->assertEmpty($result->getFailureMessage());
    }

    public function testFailChangeSuccessToFalse() {
        $result = FailMockAction::execute();
        $this->assertFalse($result->success());
    }

    public function testFailChangeFailureToTrue() {
        $result = FailMockAction::execute();
        $this->assertTrue($result->failure());
    }

    public function testHaltChangeHaltedToTrue() {
        $result = HaltMockAction::execute();
        $this->assertTrue($result->halted());
    }

    public function testFailToSetFailureMessage() {
        $result = FailMockAction::execute();
        $this->assertEquals('Error', $result->getFailureMessage());
    }

    public function testExecutePassArrayToContext() {
        $result = MockAction::execute(array('test' => 0));
        $this->assertEquals(0, $result->getContext()->test);
    }

    public function testExecuteFailIfExpectationsNotMet() {
        $result = ExpectsMockAction::execute();
        $this->assertTrue($result->failure());
        $this->assertFalse($result->success());
    }

    public function testExecuteSucceedIfExpectationsMet() {
        $result = ExpectsMockAction::execute(array('test' => 0));
        $this->assertFalse($result->failure());
        $this->assertTrue($result->success());
    }

    public function testExecuteDoesntCallPerformIfExpectationsNotMet() {
        $result = ExpectsMockAction::execute();
        $this->assertEquals(0, $result->count);
    }

    public function testExecuteDoesCallPerformIfExpectationsMet() {
        $result = ExpectsMockAction::execute(array('test' => 0));
        $this->assertEquals(1, $result->count);
    }

    public function testExecutePopulateFailureMessageIfExpectationsNotMet() {
        $result = ExpectsMockAction::execute();
        $this->assertEquals('Expectations were not met: ' . $instance->getConcatenatedDiff(), $result->getFailureMessage());
    }

    public function testExecuteEmptyFailureMessageIfExpectationsMet() {
        $result = ExpectsMockAction::execute(array('test' => 0));
        $this->assertEmpty($result->getFailureMessage());
    }

    public function testExecuteFailIfPromisesNotMet() {
        $result = PromisesMockAction::execute(array('test' => 0));
        $this->assertTrue($result->failure());
        $this->assertFalse($result->success());
    }

    public function testExecuteSucceedIfPromisesMet() {
        $result = PromisesMockAction::execute(array('test' => 1));
        $this->assertFalse($result->failure());
        $this->assertTrue($result->success());
    }

    public function testExecutePopulateFailureMessageIfPromisesNotMet() {
        $result = PromisesMockAction::execute(array('test' => 0));
        $this->assertEquals('Promises were not met: ' . $instance->getConcatenatedDiff(), $result->getFailureMessage());
    }

    public function testExecuteEmptyFailureMessageIfPromisesMet() {
        $result = PromisesMockAction::execute(array('test' => 1));
        $this->assertEmpty($result->getFailureMessage());
    }
}
