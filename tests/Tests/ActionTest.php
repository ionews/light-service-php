<?php
namespace Tests;

use LightServicePHP\Action;

class MockAction extends Action {
    public $count = 0;

    protected function perform() { $this->count++; }
}

/**
 * @author mcbarros
 */
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
}
