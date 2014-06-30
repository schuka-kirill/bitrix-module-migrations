<?php
/**
 * @author Maxim Sokolovsky <sokolovsky@worksolutions.ru>
 */

namespace WS\Migrations\Tests;


use WS\Migrations\Tests\Cases\ErrorException;

abstract class AbstractCase {

    static public function className() {
        return get_called_class();
    }

    static private function exportValue($value) {
        return var_export($value, true);
    }

    abstract public function name();

    abstract public function description();

    protected function throwError($message) {
        throw new ErrorException($message);
    }

    private function generateMessage($systemMessage, $userMassage) {
        return $userMassage ? $systemMessage." with message: ".$userMassage : $systemMessage;
    }

    protected function assertTrue($actual, $message = null) {
        if  (!$actual) {
            $this->throwError($this->generateMessage('Value `'.self::exportValue($actual).'` not asserted as true', $message));
        }
    }

    protected function assertFalse($actual, $message = null) {
        if  ($actual) {
            $this->throwError($this->generateMessage('Value `'.self::exportValue($actual).'` not asserted as false', $message));
        }
    }

    protected function assertEquals($actual, $expected, $message = null) {
        if  ($actual != $expected) {
            $this->throwError($this->generateMessage('Value actual:`'.self::exportValue($actual).'` not equals expected:`'.self::exportValue($expected).'`', $message));
        }
    }

    protected function assertNot($actual, $expected, $message = null) {
        if  ($actual == $expected) {
            $this->throwError($this->generateMessage('Value actual:`'.self::exportValue($actual).'` expectation that not equals expected:`'.self::exportValue($expected).'`', $message));
        }
    }
}