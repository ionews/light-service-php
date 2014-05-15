<?php

require_once('LightAction.php');

abstract class LightIteratorAction extends LightAction {

  protected $over;

  protected function __construct($params) {
    parent::__construct($params);

    if (empty($this->over)) {
      $this->fail('Specify the collection which will be iterated over');
    } else {
      array_push($this->expects, $this->over);
    }
  }

  protected function perform() {
    foreach ($this->over as $key => $value) {
      $this->perform_each($key, $value);

      if ($this->failure() || $this->halted()) {
        break;
      }
    }
  }

  abstract protected function perform_each($key, $value);
}
