<?php

/*
 * Plugin Name:       Fluent ClockIn
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Attendance made easy
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Goutom Dash
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       clk
 * Domain Path:       /languages
 */

use FluentClockin\PluginInit;

 if ( ! defined('ABSPATH')) {
    exit;
}
require_once __DIR__ . '/vendor/autoload.php';

define('FLUENTCOLCKIN_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));

class Initial {
    function __construct(){
        new PluginInit();
    }
}
new Initial();










