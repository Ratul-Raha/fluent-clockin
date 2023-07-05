<?php

namespace FluentClockin;

class CustomPost
{

    function __construct()
    {
        //Register post type
        add_action('init', [$this, 'register_video_player_post_type']);

        add_action('add_meta_boxes', [$this, 'add_video_player_meta_fields']);

        add_action('save_post_video_player', [$this, 'save_video_player_meta_fields']);

        add_action('wp_ajax_add_video_player', [$this, 'add_video_player']);

        add_action('wp_ajax_fetch_video_player', [$this, 'fetch_video_player']);

        add_action('wp_ajax_save_video_player_setting', [$this, 'save_video_player_setting']);

        add_action('wp_ajax_fetch_video_player_setting', [$this, 'fetch_video_player_setting']);

        add_action('wp_ajax_delete_video_player', [$this, 'delete_video_player']);

        update_option('video_player_shortcode', 'video_player_shortcode');

        add_shortcode('video_player', [$this, 'video_player_shortcode']);
    }

    function video_player_shortcode($atts)
    {
        $atts = shortcode_atts(array(
            'id' => '',
        ), $atts);

        $video_url = get_post_meta($atts['id'], 'video_url', true);
        $autoplay = get_post_meta($atts['id'], 'autoplay', true);
        $audio = get_post_meta($atts['id'], 'audio', true);
        $player_size = get_post_meta($atts['id'], 'player_size', true);

        $output = '';

        if ($video_url && $autoplay && $audio && $player_size) {

            $player_width = '';
            switch ($player_size) {
                case 'small':
                    $player_width = '400px';
                    break;
                case 'medium':
                    $player_width = '600px';
                    break;
                default:
                    $player_width = '800px';
                    break;
            }

            $output .= '<div class="video-player" style="width: ' . $player_width . ';">';
            $output .= '<video';

            if ($autoplay) {
                $output .= ' autoplay';
            }

            if ($audio) {
                $output .= ' controls';
            }

            $output .= '>';
            $output .= '<source src="' . $video_url . '" type="video/mp4">';
            $output .= '</video>';
            $output .= '</div>';
        } else {
            $output .= 'Video player settings not found.';
        }
        return  $output;
    }

    function register_video_player_post_type()
    {

        $labels = array(
            'name' => 'Video Players',
            'singular_name' => 'Video Player',
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'show_ui' => true,
            'has_archive' => false,
            'supports' => array('title', 'editor'),
        );

        register_post_type('video_player', $args);
    }


    //Add Meta Fields
    function add_video_player_meta_fields()
    {
        add_meta_box(
            'video_player_settings',
            'Video Player Settings',
            'render_video_player_meta_fields',
            'video_player',
            'normal',
            'high'
        );
    }


    //Add Video Player
    function add_video_player()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'vue_clk_nonce')) {
            if (isset($_POST['title'])) {
                $title = sanitize_text_field($_POST['title']);
                $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';

                $post_id = wp_insert_post([
                    'post_title' => $title,
                    'post_content' => $description,
                    'post_type' => 'video_player',
                    'post_status' => 'publish'
                ]);

                if ($post_id) {
                    wp_send_json_success('Video player added successfully.');
                } else {
                    wp_send_json_error('Failed to add video player. Please try again.');
                }
            } else {
                wp_send_json_error('Please provide a title.');
            }
        } else {
            wp_send_json_error('Invalid request or nonce verification failed.');
        }
    }

    function fetch_video_player()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'vue_clk_nonce')) {
            global $wpdb;

            $post_type = 'video_player';

            $query = $wpdb->prepare("
                SELECT *
                FROM {$wpdb->prefix}posts
                WHERE post_type = %s
            ", $post_type);

            $video_players = $wpdb->get_results($query, ARRAY_A);
            if ($video_players) {

                foreach ($video_players as &$video_player) {
                    $video_player['shortcode'] = '[' . 'video_player' . ' id="' . $video_player['ID'] . '"]';
                }
                wp_send_json_success($video_players);
            } else {
                wp_send_json_error('No video players found');
            }
        } else {
            wp_send_json_error('Invalid request');
        }
    }


    function delete_video_player()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'vue_clk_nonce')) {
            global $wpdb;

            if (isset($_POST['id'])) {
                $video_player_id = absint($_POST['id']);

                // Delete the video player post
                $result = wp_delete_post($video_player_id);

                if ($result !== false) {
                    // Delete the generated shortcode
                    delete_post_meta($video_player_id, 'video_player_shortcode');

                    wp_send_json_success('Video player deleted successfully.');
                } else {
                    wp_send_json_error('Failed to delete video player. Please try again.');
                }
            } else {
                wp_send_json_error('Please provide a video player ID.');
            }
        } else {
            wp_send_json_error('Invalid request or nonce verification failed.');
        }
    }



    function save_video_player_setting()
    {

        if (!$_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['nonce']) && !wp_verify_nonce($_POST['nonce'], 'vue_clk_nonce')) {
            wp_send_json_error('Invalid nonce.');
        }

        $video_player_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        $autoplay = isset($_POST['autoplay']) ? sanitize_text_field($_POST['autoplay']) : '';
        $audio = isset($_POST['audio']) ? sanitize_text_field($_POST['audio']) : '';
        $player_size = isset($_POST['player_size']) ? sanitize_text_field($_POST['player_size']) : '';
        $url = isset($_POST['video_url']) ? esc_url_raw($_POST['video_url']) : '';

        update_post_meta($video_player_id, 'autoplay', $autoplay);
        update_post_meta($video_player_id, 'audio', $audio);
        update_post_meta($video_player_id, 'player_size', $player_size);
        update_post_meta($video_player_id, 'video_url', $url);

        wp_send_json_success('Video player settings saved successfully.');
    }

    function fetch_video_player_setting()
    {
        $nonce = $_POST['nonce'];
        if (!wp_verify_nonce($nonce, 'vue_clk_nonce')) {
            $response = array(
                'success' => false,
                'message' => 'Invalid nonce'
            );
            wp_send_json($response);
        }

        $video_player_id = $_POST['id'];
        $video_player_setting = get_post_meta($video_player_id);

        $response = array(
            'success' => true,
            'data' => $video_player_setting
        );
        wp_send_json($response);
    }
}
