<?php
namespace LightServicePHP;

abstract class Action {

    public static function execute($params = array()) {
        $class = get_called_class();
        $instance = new $class($params);

        if (!$instance->expectationsMet()) {
            $instance->fail('Expectations were not met: ' . $instance->getConcatenatedDiff());
            return $instance;
        }

        $instance->before();

        if ($instance->success()) {
            try {
                $instance->perform();
            } catch(\Exception $ex) {
                $instance->caught($ex);
            }

            if ($instance->success()) {
                if (!$instance->promisesMet()) {
                    $instance->fail('Promises were not met ' . $instance->getConcatenatedDiff());
                    return $instance;
                }

                $instance->after();
            }
        }

        return $instance;
    }

    protected $context;
    protected $expects = array();
    protected $promises = array();
    protected $diff = array();

    protected function __construct($params) {
        $this->context = Context::build($params);
    }

    abstract protected function perform();
    protected function before() {}
    protected function after() {}
    protected function rollback() {}
    protected function caught(\Exception $ex) {
        throw $ex;
    }

    protected function fail($msg = null) {
        return $this->context->fail($msg);
    }

    protected function halt() {
        return $this->context->halt();
    }

    private function expectationsMet() {
        $this->diff = $this->context->diffKeys($this->expects);
        return empty($this->diff);
    }

    private function promisesMet() {
        $this->diff = $this->context->diffKeys($this->promises);
        return empty($this->diff);
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

    public function getDiff() {
        return $this->diff;
    }

    public function getConcatenatedDiff() {
        $str = '';

        foreach ($this->diff as $value) {
            $str .= "$value ";
        }

        return $str;
    }
}
