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
?>

Result 1: <?php print_r($result1->getContext()->getArrayCopy()); ?>
<br/>
Result 2: <?php print_r($result2->getContext()->getArrayCopy()); ?>
