<?php

namespace FluentEmbed;

class PluginInit
{
    function __construct()
    {
        // Creating admin menu and rendering the app 
        add_action('admin_menu', function () {
            add_menu_page(
                'Fluent Clockin',
                'Fluent Clockin',
                'manage_options',
                'fluent_clockin',
                function () {
                    wp_enqueue_script('test_app_js', 'http://localhost:8888/resources/js/app.js', 'jquery', '3.0', false);
                    echo '<div id="fluent_clockin_app"></div>';

                    wp_localize_script('test_app_js', 'clk_ajax', array(
                        'ajaxurl' => admin_url('admin-ajax.php'),
                        'clk_nonce' => wp_create_nonce('vue_clk_nonce')
                    ));
                }
            );
        });

        add_filter('script_loader_tag', [$this,'test_add_module_to_script'], 10, 3);

    
        

        new CustomPost();

    }

    function test_add_module_to_script($tag, $handle, $src)
    {
        $handlers = ['test_app_js'];
        if (in_array($handle, $handlers)) {
            $tag = str_replace(' src', ' type="module" src', $tag);
        }
        return $tag;
    }

}
