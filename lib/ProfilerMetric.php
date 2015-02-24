<?php

class ProfilerMetric {
    private $name;
    private $start_time;
    private $end_time;
    private $parent;
    private $children = array();

    public function __construct($name, $start_time) {
        $this->name = $name;
        $this->start_time = $start_time;
    }

    public function getName() {
        return $this->name;
    }

    public function stop($end_time) {
        $this->end_time = $end_time;
    }

    public function getDuration() {
        return $this->end_time - $this->start_time;
    }

    public function getChildren() {
        return $this->children;
    }

    public function getParent() {
        return $this->parent;
    }

    public function addChild(ProfilerMetric $metric) {
        $metric->setParent($this);
        $this->children[] = $metric;
    }

    public function setParent(ProfilerMetric $metric) {
        $this->parent = $metric;
    }
}
