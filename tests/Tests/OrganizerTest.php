<?php
namespace Tests;

use LightServicePHP\Action;
use LightServicePHP\Organizer;

class FailMockOrganizer extends Organizer {
}

class Action1 extends Action {
    protected function perform() {
        $this->context->count = 1;
        $this->context->rollback = 0;
    }

    protected function rollback() {
        $this->context->rollback++;
    }
}

class Action2 extends Action {
    protected function perform() {
        $this->context->count++;

        if ($this->context->value == 0) {
            $this->fail();
        } else if ($this->context->value == 1) {
            $this->halt();
        } else if ($this->context->value == 2) {
            throw new \Exception('Error');
        }
    }

    protected function rollback() {
        $this->context->rollback++;
    }
}

class Action3 extends Action {
    protected function perform() {
        $this->context->count++;
    }

    protected function rollback() {
        $this->context->rollback++;
    }
}

class MockOrganizer extends Organizer {
    protected $organize = array('Tests\Action1', 'Tests\Action2', 'Tests\Action3');
}

/**
 * @author mcbarros
 */
class OrganizerTest extends \PHPUnit_Framework_TestCase {
    public function testExecuteFailsIfThereIsntAnOrganizeArray() {
        $result = FailMockOrganizer::execute();
        $this->assertTrue($result->failure());
        $this->assertFalse($result->success());
    }

    public function testExecutePopulateFailureMessageIfThereIsntAnOrganizeArray() {
        $result = FailMockOrganizer::execute();
        $this->assertEquals('Specify the actions to be executed by this organizer', $result->getFailureMessage());
    }

    public function testExecuteCallsEachActionOnce() {
        $result = MockOrganizer::execute(array('value' => 4));
        $this->assertTrue($result->success());
        $this->assertFalse($result->failure());
        $this->assertEquals(3, $result->getContext()->count);
    }

    public function testExecuteCallsEachActionUntilHalted() {
        $result = MockOrganizer::execute(array('value' => 1));
        $this->assertTrue($result->success());
        $this->assertFalse($result->failure());
        $this->assertEquals(2, $result->getContext()->count);
        $this->assertEquals(0, $result->getContext()->rollback);
    }

    public function testExecuteCallsEachActionUntilFail() {
        $result = MockOrganizer::execute(array('value' => 0));
        $this->assertFalse($result->success());
        $this->assertTrue($result->failure());
        $this->assertEquals(2, $result->getContext()->count);
        $this->assertEquals(1, $result->getContext()->rollback);
    }

    public function testExecuteCallsEachActionUntilException() {
        $this->setExpectedException('Exception');
        $result = MockOrganizer::execute(array('value' => 2));
    }
}
