<?php

class ProfilerMockClock implements ProfilerClock {
    private $now = 0;

    public function __construct($now = 0) {
        $this->now = $now;
    }

    public function tick($seconds = 1) {
        $this->now += $seconds;
    }

    public function now() {
        return $this->now;
    }
}
