<?php

require_once('LightContext.php');

abstract class LightAction {

  public static function execute($params = []) {
    $class = get_called_class();
    $instance = new $class($params);
    
    if (!$instance->hasExpectations()) {
      $instance->fail('Expectations were not met');
      return $instance;
    }
    
    $instance->setup();

    if ($instance->success()) {
      $instance->perform();

      if ($instance->success() && !$instance->hasPromises()) {
        $instance->fail('Promises were not met');
      }
    } else {
      $instance->rollback();
    }

    return $instance;
  }

  protected $context;
  protected $expects = [];
  protected $promises = [];

  protected function __construct($params) {
    $this->context = LightContext::build($params);
  }

  abstract protected function perform();
  protected function setup() {}
  protected function rollback() {}

  protected function fail($msg = null) {
    return $this->context->fail($msg);
  }

  protected function halt() {
    return $this->context->halt();
  }
  
  private function hasExpectations() {
    return $this->context->hasKeys($this->expects);
  }
  
  private function hasPromises() {
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
}
