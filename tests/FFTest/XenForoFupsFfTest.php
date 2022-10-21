<?php

namespace FFTest;

use XenForoFUPS;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../vendor/autoload.php';

class XenForoFupsFfTest extends TestCase {
    private static $fups;

    public static function setUpBeforeClass(): void {
        $params = array(
            'settings_filename' => 'settings.php',
            'output_dirname'    => 'ffTestOutput',
            'quiet'             => false,
        );

        self::$fups = new XenForoFUPS(false, $params);
    }

    public function testBasicConstruction(): void {
        $this->assertIsObject($this::$fups);
    }

}
