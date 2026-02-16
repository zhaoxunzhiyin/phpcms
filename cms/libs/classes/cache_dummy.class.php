<?php

class cache_dummy {

    public function __construct() {
    }

    public function initialize() {
    }

    public function get(string $key) {
        return null;
    }

    public function save(string $key, $value, int $ttl = 60) {
        return true;
    }

    public function delete(string $key) {
        return true;
    }

    public function clean() {
        return true;
    }

    public function isSupported(): bool {
        return true;
    }
}
