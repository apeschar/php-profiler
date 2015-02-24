<?php

require dirname(__FILE__) . '/Profiler.php';
require dirname(__FILE__) . '/ProfilerMockClock.php';

class ProfilerTest extends PHPUnit_Framework_TestCase {
    public function testEmpty() {
        $profiler = new Profiler;
        $this->assertEquals(0, $profiler->getTotalDuration());
    }

    public function testReport() {
        $clock = new ProfilerMockClock(10);
        $profiler = new Profiler($clock);

        $profiler->start('A');
        $clock->tick();

        $profiler->start('B');
        $clock->tick();
        $profiler->stop('B');

        $profiler->start('C');
        $clock->tick();
        $profiler->stop('C');

        $profiler->stop('A');

        $clock->tick();

        $profiler->start('D');
        $clock->tick();
        $profiler->stop('D');

        $this->assertEquals(4, $profiler->getTotalDuration());

        $this->assertEquals('Profiler
-------------------------------------------------------------------------------
A                                                                         3.000
  B                                                                       1.000
  C                                                                       1.000
D                                                                         1.000
-------------------------------------------------------------------------------
Total:                                                                    4.000
', $profiler->getTextReport());
    }

    /**
     * @expectedException ProfilerException
     */
    public function testWrongNameForStop() {
        $profiler = new Profiler;
        $profiler->start('A');
        $profiler->stop('B');
    }

    /**
     * @expectedException ProfilerException
     */
    public function testUnfinished() {
        $profiler = new Profiler;
        $profiler->start('A');
        $profiler->getTextReport();
    }
}
