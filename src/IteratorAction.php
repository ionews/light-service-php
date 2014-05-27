<?php
namespace LightServicePHP;

abstract class IteratorAction extends Action {

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
        foreach ($this->context->{$this->over} as $key => $value) {
            $this->each($key, $value);

            if ($this->failure() || $this->halted()) {
                break;
            }
        }
    }

    abstract protected function each($key, $value);
}
