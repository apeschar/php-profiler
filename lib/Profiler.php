<?php

require_once dirname(__FILE__) . '/ProfilerException.php';
require_once dirname(__FILE__) . '/ProfilerClock.php';
require_once dirname(__FILE__) . '/ProfilerRealClock.php';
require_once dirname(__FILE__) . '/ProfilerMetric.php';

class Profiler {
    private $clock;
    private $metrics = array();
    private $current;

    public function __construct($clock = null) {
        if($clock === null)
            $clock = new ProfilerRealClock;
        $this->clock = $clock;
    }

    public function start($name) {
        $metric = new ProfilerMetric($name, $this->clock->now());
        if($this->current)
            $this->current->addChild($metric);
        else
            $this->metrics[] = $metric;
        $this->current = $metric;
        return $metric;
    }

    public function stop($name) {
        if(!$this->current)
            throw new ProfilerException(sprintf('Profiler->stop: No metric is currently running but you tried to stop "%s".',
                                                $name));
        if($this->current->getName() != $name)
            throw new ProfilerException(sprintf('Profiler->stop: Current metric is "%s" but you tried to stop "%s".',
                                                $this->current->getName(), $name));
        $this->current->stop($this->clock->now());
        $this->current = $this->current->getParent();
    }

    public function getTextReport() {
        if($this->current)
            throw new ProfilerException(sprintf('Profiler->getTextReport: Metric "%s" is still running.',
                                                $this->current->getName()));

        $report = "Profiler\n";
        $report .= str_repeat('-', 79) . "\n";
        $report .= $this->getTextReportLines($this->metrics, 0);
        $report .= str_repeat('-', 79) . "\n";
        $report .= str_pad('Total:', 70) . sprintf('% 9.3f', $this->getTotalDuration()) . "\n";

        return $report;
    }

    private function getTextReportLines(array $metrics, $indent) {
        $prefix = str_repeat('  ', $indent);
        $report = '';
        foreach($metrics as $metric) {
            $lines = explode("\n", wordwrap($metric->getName(), 70 - strlen($prefix), "\n", true));
            foreach($lines as &$line)
                $line = $prefix . $line;
            $line = str_pad($line, 70);
            $line .= sprintf('% 9.3f', $metric->getDuration());
            $report .= implode("\n", $lines) . "\n";
            $report .= $this->getTextReportLines($metric->getChildren(), $indent + 1);
        }
        return $report;
    }

    public function getTotalDuration() {
        if($this->current)
            throw new ProfilerException(sprintf('Profiler->getTotalDuration: Metric "%s" is still running.',
                                                $this->current->getName()));

        $total = 0.0;
        foreach($this->metrics as $metric)
            $total += $metric->getDuration();
        return $total;
    }
}
