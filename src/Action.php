<?php
namespace LightServicePHP;

abstract class Action {

  public static function execute($params = array()) {
    $class = get_called_class();
    $instance = new $class($params);

    if (!$instance->expectationsMet()) {
      $instance->fail('Expectations were not met');
      return $instance;
    }

    $instance->before();

    if ($instance->success()) {
      $instance->perform();

      if ($instance->success()) {
        if (!$instance->promisesMet()) {
          $instance->fail('Promises were not met');
        }

        $instance->after();
      }
    } else {
      $instance->rollback();
    }

    return $instance;
  }

  protected $context;
  protected $expects = array();
  protected $promises = array();

  protected function __construct($params) {
    $this->context = Context::build($params);
  }

  abstract protected function perform();
  protected function before() {}
  protected function after() {}
  protected function rollback() {}

  protected function fail($msg = null) {
    return $this->context->fail($msg);
  }

  protected function halt() {
    return $this->context->halt();
  }

  private function expectationsMet() {
    return $this->context->hasKeys($this->expects);
  }

  private function promisesMet() {
    return $this->context->hasKeys($this->promises);
  }

  public function success() {
    return $this->context->success();
  }

  public function failure() {
    return $this->context->failure();
  }

  public function halted() {
    return $this->context->halted();
  }

  public function getContext() {
    return $this->context;
  }

  public function getFailureMessage() {
    return $this->context->getFailureMessage();
  }
}
