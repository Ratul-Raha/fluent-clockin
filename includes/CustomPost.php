<?php

namespace FluentEmbed;

class CustomPost
{

    function __construct()
    {
        //Register post type
        add_action('init', [$this, 'clk_register_video_player_post_type']);
        add_action('wp_ajax_clk_add_video_player', [$this, 'clk_add_video_player']);
        add_action('wp_ajax_clk_fetch_video_player', [$this, 'clk_fetch_video_player']);
        add_action('wp_ajax_clk_save_video_player_setting', [$this, 'clk_save_video_player_setting']);
        add_action('wp_ajax_clk_fetch_video_player_setting', [$this, 'clk_fetch_video_player_setting']);
        add_action('wp_ajax_clk_delete_video_player', [$this, 'clk_delete_video_player']);
        add_shortcode('video_player', [$this, 'clk_video_player_shortcode']);
    }

    function clk_video_player_shortcode($atts)
    {
        $atts = shortcode_atts(array(
            'id' => '',
        ), $atts);

        $video_url = get_post_meta($atts['id'], 'video_url', true);
        $autoplay = get_post_meta($atts['id'], 'autoplay', true);
        $audio = get_post_meta($atts['id'], 'audio', true);
        $player_size = get_post_meta($atts['id'], 'player_size', true);
        $controls = get_post_meta($atts['id'], 'controls', true);

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

            if (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false) {

                $video_id = '';
                if (preg_match('/\?v=([^&]+)/', $video_url, $matches)) {
                    $video_id = $matches[1];
                }

                $query_params = array();
                if ($autoplay === 'yes') {
                    $query_params[] = 'autoplay=1';
                } else if ($autoplay === 'no') {
                    $query_params[] = 'autoplay=0&modestbranding=1&rel=0&cc_load_policy=0';
                }
                if ($controls === 'off') {
                    $query_params[] = 'controls=0';
                }

                if ($audio === 'off') {
                    $query_params[] = 'mute=1';
                }
                $aspect_ratio = 0.5625;
                $player_height = round(intval($player_width) * $aspect_ratio);
                $embedded_url = 'https://www.youtube.com/embed/' . $video_id;
                if (!empty($query_params)) {
                    $embedded_url .= '?' . implode('&', $query_params);
                }
                $output = '<div class="video-player">
                <iframe width="' . $player_width . '" height="' . $player_height . '"
            src="' . $embedded_url . '">
            </iframe></div>';
                return $output;
            }

            $output .= '<div class="video-player">';
            $output .= '<video src="' . $video_url . '"';

            if ($autoplay === 'yes') {
                $output .= ' autoplay';
            }

            if ($audio === 'off') {
                $output .= ' muted';
            }

            if ($controls === 'on') {
                $output .= ' controls';
            }

            $output .= ' style="width: ' . $player_width . '" >';
            $output .= '</video>';
            $output .= '</div>';
        } else {
            return $output .= 'Video player settings not found.';
        }

        return  $output;
    }


    function clk_register_video_player_post_type()
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

    //Add Video Player
    function clk_add_video_player()
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

    function clk_fetch_video_player()
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
                    $setting_exist = get_post_meta($video_player['ID']);
                    if ($setting_exist) {
                        $video_player['shortcode'] = '[' . 'video_player' . ' id="' . $video_player['ID'] . '"]';
                    } else {
                        $video_player['shortcode'] = "Add setting first";
                    }
                }
                wp_send_json_success($video_players);
            } else {
                wp_send_json_error('No video players found');
            }
        } else {
            wp_send_json_error('Invalid request');
        }
    }
    function clk_delete_video_player()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'vue_clk_nonce')) {
            global $wpdb;

            if (isset($_POST['id'])) {
                $video_player_id = absint($_POST['id']);
                $result = wp_delete_post($video_player_id);

                if ($result !== false) {
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

    function clk_save_video_player_setting()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'vue_clk_nonce')) {
            wp_send_json_error('Invalid nonce.');
        }

        $video_player_id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
        $description = isset($_POST['description']) ? sanitize_text_field($_POST['description']) : '';

        if (empty($title) || empty($_POST['autoplay']) || empty($_POST['audio']) || empty($_POST['controls']) || empty($_POST['player_size']) || empty($_POST['video_url'])) {
            wp_send_json_error('All fields are required except Description.');
        }

        $post_data = array(
            'ID' => $video_player_id,
            'post_title' => $title,
            'post_content' => $description,
        );
        wp_update_post($post_data);

        $autoplay = sanitize_text_field($_POST['autoplay']);
        $audio = sanitize_text_field($_POST['audio']);
        $controls = sanitize_text_field($_POST['controls']);
        $player_size = sanitize_text_field($_POST['player_size']);
        $url = esc_url_raw($_POST['video_url']);

        update_post_meta($video_player_id, 'autoplay', $autoplay);
        update_post_meta($video_player_id, 'audio', $audio);
        update_post_meta($video_player_id, 'player_size', $player_size);
        update_post_meta($video_player_id, 'video_url', $url);
        update_post_meta($video_player_id, 'controls', $controls);

        $post_meta_data = array(
            'autoplay' => $autoplay,
            'audio' => $audio,
            'controls' => $controls,
            'player_size' => $player_size,
            'video_url' => $url,
        );
        $has_all_post_meta = !in_array('', $post_meta_data, true);

        $shortcode = $has_all_post_meta ? '[video_player id="' . $video_player_id . '"]' : 'Add setting first';

        $updated_video_player = array(
            'id' => $video_player_id,
            'autoplay' => $autoplay,
            'audio' => $audio,
            'controls' => $controls,
            'player_size' => $player_size,
            'video_url' => $url,
            'post_title' => $title,
            'post_content' => $description,
            'shortcode' => $shortcode, 
        );

        $response_data = array(
            'success' => true,
            'data' => $updated_video_player,
        );
        wp_send_json($response_data);
    }

    function clk_fetch_video_player_setting()
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
        $audio = get_post_meta($video_player_id, 'audio', true);
        $autoplay = get_post_meta($video_player_id, 'autoplay', true);
        $video_url = get_post_meta($video_player_id, 'video_url', true);
        $player_size = get_post_meta($video_player_id, 'player_size', true);
        $controls = get_post_meta($video_player_id, 'controls', true);
        $title = get_the_title($video_player_id);
        $description = get_post_field('post_content', $video_player_id);

        $response = array(
            'success' => true,
            'title' => $title,
            'description' => $description,
            'audio' => $audio,
            'autoplay' => $autoplay,
            'controls' => $controls,
            'player_size' => $player_size,
            'video_url' => $video_url,
        );
        wp_send_json($response);
    }
}
