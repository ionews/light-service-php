<?php
namespace Tests;

use LightServicePHP\IteratorAction;

class FailMockIteratorAction extends IteratorAction {
    public $count = 0;

    protected function each($key, $value) { $this->count++; }
}

class MockIteratorAction extends IteratorAction {
    protected $over = 'collection';
    public $count = 0;
    public $array = array();

    protected function each($key, $value) {
        $this->count++;
        $this->array[$key] = $value;
    }
}

class HaltMockIteratorAction extends IteratorAction {
    protected $over = 'collection';
    public $count = 0;
    public $array = array();

    protected function each($key, $value) {
        $this->count++;
        $this->array[$key] = $value;
        $this->halt();
    }
}

class IteratorActionTest extends \PHPUnit_Framework_TestCase {
    public function testExecuteFailsIfThereIsntAnOverCollection() {
        $result = FailMockIteratorAction::execute();
        $this->assertTrue($result->failure());
        $this->assertFalse($result->success());
    }

    public function testExecutePopulateFailureMessageIfThereIsntAnOverCollection() {
        $result = FailMockIteratorAction::execute();
        $this->assertEquals('Specify the collection which will be iterated over', $result->getFailureMessage());
    }

    public function testExecuteFailsIfOverCollectionNotInContext() {
        $result = MockIteratorAction::execute();
        $this->assertTrue($result->failure());
        $this->assertFalse($result->success());
    }

    public function testExecutePopulateFailureMessageIfOverCollectionNotInContext() {
        $result = MockIteratorAction::execute();
        $this->assertEquals('Expectations were not met: ' . $result->getConcatenatedDiff(), $result->getFailureMessage());
    }

    public function testExecuteCallsEachForEveryElementInArray() {
        $collection = array(1, 2, 3);
        $result = MockIteratorAction::execute(array('collection' => $collection));
        $this->assertEquals(3, $result->count);
        $this->assertEquals($collection, $result->array);
    }

    public function testExecuteStopIfHalted() {
        $collection = array(1, 2, 3);
        $result = HaltMockIteratorAction::execute(array('collection' => $collection));
        $this->assertEquals(1, $result->count);
        $this->assertEquals(array(1), $result->array);
    }
}
