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

add_action('admin_menu', function () {
    
    add_menu_page(
        'Fluent Clockin',
        'Fluent Clockin',
        'manage_options',
        'fluent_clockin',
        'fluent_clockin_render'
    );
});

function fluent_clockin_render()
{
    wp_enqueue_script('test_app_js', 'http://localhost:8888/resources/js/app.js', 'jquery', '3.0', false);
    echo '<div id="fluent_clockin_app"></div>';
}


add_filter('script_loader_tag', 'test_add_module_to_script', 10, 3);

function test_add_module_to_script($tag, $handle, $src)
{
    $handlers = ['test_app_js'];
    if (in_array($handle, $handlers)) {
        $tag = str_replace(' src', ' type="module" src', $tag);
    }
    return $tag;
}