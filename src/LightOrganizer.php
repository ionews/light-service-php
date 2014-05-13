<?php

require_once('LightAction.php');

abstract class LightOrganizer extends LightAction {

  protected $organize;
  protected $performed = [];

  protected function perform() {
    if (empty($this->organize)) {
      $this->fail('Specify the actions to be executed by this organizer');
    } else {
      foreach ($this->organize as $action) {
        try {
          $instance = $action::execute($this->context);
        } catch(Exception $ex) {
          $this->rollback();
          throw $ex;
        }

        if ($this->failure()) {
          $this->rollback();
          break;
        }

        $this->performed[] = $instance;

        if ($this->halted()) {
          break;
        }
      }
    }
  }

  protected function rollback() {
    $index = count($this->performed);

    while($index) {
      $this->performed[--$index]->rollback();
    }
  }
}
