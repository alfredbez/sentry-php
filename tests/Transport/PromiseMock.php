<?php

declare(strict_types=1);

namespace Sentry\Tests\Transport;

use Http\Promise\Promise;

final class PromiseMock implements Promise
{
    private $result;

    private $state;

    private $onFullfilledCallbacks = [];

    private $onRejectedCallbacks = [];

    public function __construct($result, $state = self::FULFILLED)
    {
        $this->result = $result;
        $this->state = $state;
    }

    public function then(callable $onFulfilled = null, callable $onRejected = null)
    {
        if (null !== $onFulfilled) {
            $this->onFullfilledCallbacks[] = $onFulfilled;
        }

        if (null !== $onRejected) {
            $this->onRejectedCallbacks[] = $onRejected;
        }

        return $this;
    }

    public function getState()
    {
        return $this->state;
    }

    public function wait($unwrap = true)
    {
        switch ($this->state) {
            case self::FULFILLED:
                foreach ($this->onFullfilledCallbacks as $onFullfilledCallback) {
                    $this->result = $onFullfilledCallback($this->result);
                }

                break;
            case self::REJECTED:
                foreach ($this->onRejectedCallbacks as $onRejectedCallback) {
                    $this->result = $onRejectedCallback($this->result);
                }

                break;
        }

        return $unwrap ? $this->result : null;
    }
}
