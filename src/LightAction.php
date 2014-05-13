<?php

require_once('LightContext.php');

abstract class LightAction {

  public static function execute($params = []) {
    $class = get_called_class();
    $instance = new $class($params);
    $instance->setup();

    if ($instance->success()) {
      $instance->perform();
    } else {
      $instance->rollback();
    }

    return $instance;
  }


  // TODO: expects, promises
  protected $context;

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
