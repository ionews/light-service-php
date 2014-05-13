<?php
require_once('../src/LightOrganizer.php');

class A extends LightAction {
  protected function perform() {
    $this->context->b = $this->context->a + 1;
  }
}

class B extends LightAction {
  protected function perform() {
    $this->context->c = $this->context->a + $this->context->b;
  }
}

class C extends LightOrganizer {
  protected $organize = ['A', 'B'];
}

$result1 = A::execute(['a' => 1]);
$result2 = C::execute(['a' => 2]);

if ($result1->success() && $result2->success()) {
  echo $result1->getContext()->b;
  echo '<br/>';
  echo $result2->getContext()->c;
} else {
  echo 'Oops';
}
