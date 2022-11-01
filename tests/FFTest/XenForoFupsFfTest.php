<?php

namespace FFTest;

use XenForoFUPS;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php';

class XenForoFupsFfTest extends TestCase {
    private static $fups;

    public static function setUpBeforeClass(): void {
        $params = array(
            'settings_filename' => __DIR__ . '/../../optionsFile_ffJim.txt',
            'output_dirname'    => __DIR__ . '/../../ffTestOutput',
            'quiet'             => false,
        );

        self::$fups = new XenForoFUPS(false, $params);
    }

    public function testBasicConstruction(): void {
        $this->assertIsObject($this::$fups);
    }

    public function testRun(): void {
        $this::$fups->run();
    }

}
