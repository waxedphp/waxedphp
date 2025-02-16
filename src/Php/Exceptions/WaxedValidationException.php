<?php
namespace Waxedphp\Waxedphp\Php\Exceptions;
/**
 * Define a custom exception class
 */
class WaxedValidationException extends \Exception
{
    private array $messages = [];
    // Redefine the exception so message isn't optional
    public function __construct(array $messages, $code = 0, Throwable $previous = null) {
        // some code
        $this->messages = $messages;
        // make sure everything is assigned properly
        parent::__construct('WaxedValidationException', $code, $previous);
    }

    // custom string representation of object
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: ".json_encode($this->message)."\n";
    }

    public function getMessages() {
      return $this->messages;
    }
}
