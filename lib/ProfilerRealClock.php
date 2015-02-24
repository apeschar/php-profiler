<?php

class ProfilerRealClock implements ProfilerClock {
    public function now() {
        return microtime(true);
    }
}
