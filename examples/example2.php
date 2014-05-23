<?php
require_once('../src/LightOrganizer.php');

class A extends LightAction {
  protected $expects = ['a'];
  protected $promises = ['b'];

  protected function perform() {
    $this->context->b = $this->context->a + 1;
  }
}

class B extends LightAction {
  protected $expects = ['a', 'b'];
  protected $promises = ['c'];

  protected function perform() {
    $this->context->c = $this->context->a + $this->context->b;
  }
}

class C extends LightOrganizer {
  protected $organize = ['A', 'B'];
}

class D extends LightAction {
  protected $expects = ['c'];
  protected $promises = ['d'];

  protected function perform() {
    $this->context->d = $this->context->c * 2;
  }
}

class E extends LightOrganizer {
  protected $organize = ['C', 'D'];
}

$result1 = A::execute(['a' => 1]);
$result2 = C::execute(['a' => 2]);
$result3 = D::execute(['a' => 2]); // Fails: expectations
$result4 = D::execute(['c' => 2]);
$result5 = E::execute(['a' => 2]);

echo 'Result1: ' . $result1->success();
echo 'Result2: ' . $result2->success();
echo 'Result3: ' . $result3->success();
echo 'Result4: ' . $result4->success();
echo 'Result5: ' . $result5->success();
