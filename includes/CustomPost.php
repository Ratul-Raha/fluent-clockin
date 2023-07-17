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

    public function clk_video_player_shortcode($atts)
    {
        $atts = shortcode_atts(array(
            'id' => '',
        ), $atts);

        $settings = get_post_meta(sanitize_text_field($atts['id']), 'video_player_settings', true);

        $output = '';

        if ($settings && isset($settings['video_url'], $settings['autoplay'], $settings['audio'], $settings['player_size'], $settings['controls'])) {
            $video_url = $settings['video_url'];
            $autoplay = $settings['autoplay'];
            $audio = $settings['audio'];
            $player_size = $settings['player_size'];
            $controls = $settings['controls'];

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

                // Extract video ID from URL
                if (preg_match('/youtu\.be\/([A-Za-z0-9_-]{11})/', $video_url, $matches)) {
                    $video_id = $matches[1];
                } else if (preg_match('/\?v=([^&]+)/', $video_url, $matches)) {
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
                    </iframe>
                </div>';

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


    public function clk_register_video_player_post_type()
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
    public function clk_add_video_player()
    {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $nonce && wp_verify_nonce($nonce, 'vue_clk_nonce')) {
            if (isset($_POST['title']) && trim($_POST['title']) !== '') {
                $title = sanitize_text_field($_POST['title']);
                $description = isset($_POST['description']) ? sanitize_textarea_field($_POST['description']) : '';

                if (strlen($title) > 20 && strlen($description) > 100) {
                    wp_send_json_error('Max Length: Title(20), Description(100)');
                }

                if (strlen($title) > 20) {
                    wp_send_json_error('Title maxlength: 20, please check!');
                    return;
                }

                if (strlen($description) > 100) {
                    wp_send_json_error('Description maxlength: 100, please check!');
                    return;
                }

                $post_id = wp_insert_post([
                    'post_title' => $title,
                    'post_content' => $description,
                    'post_type' => 'video_player',
                    'post_status' => 'publish'
                ]);
                if ($post_id) {
                    $response_data = array(
                        'success' => true,
                        'message' => "Successfully saved!",
                        'id' => $post_id,
                        'title' => $title,
                        'description' => $description,
                    );
                    wp_send_json($response_data);
                } else {
                    wp_send_json_error('Failed to add video player. Please try again.');
                }
            } else {
                wp_send_json_error('Please provide a valid title. hjklahdAHSDJ KAHSDKJHASJK Hsdkfhsd klfsdhjfjksdh jsdfhsdjkfh kjsdhfsjfh khsdjfsdh');
            }
        } else {
            wp_send_json_error('Invalid request or nonce verification failed.');
        }
    }


    public function clk_fetch_video_player()
    {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $nonce && wp_verify_nonce($nonce, 'vue_clk_nonce')) {
            global $wpdb;

            $post_type = 'video_player';
            $query = $wpdb->prepare("
            SELECT p.ID, p.post_title, p.post_content
            FROM {$wpdb->prefix}posts AS p
            WHERE p.post_type = %s
        ", $post_type);

            $video_players = $wpdb->get_results($query, ARRAY_A);

            if ($video_players) {
                wp_send_json_success($video_players);
            } else {
                wp_send_json_error("No data");
            }
        } else {
            wp_send_json_error('Invalid request');
        }
    }


    public function clk_delete_video_player()
    {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $nonce && wp_verify_nonce($nonce, 'vue_clk_nonce')) {
            global $wpdb;

            if (isset($_POST['id'])) {
                $id = sanitize_text_field($_POST['id']);
                $video_player_id = absint($id);
                $result = wp_delete_post($video_player_id);

                if ($result !== false) {
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

    public function clk_save_video_player_setting()
    {
        $nonce = sanitize_text_field($_POST['nonce']);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$nonce || !wp_verify_nonce($_POST['nonce'], 'vue_clk_nonce')) {
            wp_send_json_error('Invalid nonce.');
        }

        $video_player_id = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : '';

        $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
        $description = isset($_POST['description']) ? sanitize_text_field($_POST['description']) : '';

        if (empty($title) || empty($_POST['autoplay']) || empty($_POST['audio']) || empty($_POST['controls']) || empty($_POST['player_size']) || empty($_POST['video_url'])) {
            wp_send_json_error('All fields are required except Description.');
        }

        if (strlen($title) > 20 && strlen($description) > 100) {
            wp_send_json_error('Max Length: Title(20), Description(100)');
        }
        
        if (strlen($title) > 20) {
            wp_send_json_error('Title maxlength: 20, please check!');
            return;
        }

        if (strlen($description) > 100) {
            wp_send_json_error('Description maxlength: 100, please check!');
            return;
        }

        $post_data = array(
            'ID' => $video_player_id,
            'post_title' => $title,
            'post_content' => $description,
        );
        wp_update_post($post_data);

        $settings = array(
            'autoplay' => sanitize_text_field($_POST['autoplay']),
            'audio' => sanitize_text_field($_POST['audio']),
            'controls' => sanitize_text_field($_POST['controls']),
            'player_size' => sanitize_text_field($_POST['player_size']),
            'video_url' => sanitize_url($_POST['video_url']),
        );

        update_post_meta($video_player_id, 'video_player_settings', $settings);

        $shortcode = '[video_player id="' . $video_player_id . '"]';
        $updated_video_player = array(
            'id' => $video_player_id,
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

    public function clk_fetch_video_player_setting()
    {
        if (!isset($_POST['nonce']) || !isset($_POST['id'])) {
            $response = array(
                'success' => false,
                'message' => 'Invalid request'
            );
            wp_send_json($response);
        }

        $nonce = sanitize_text_field($_POST['nonce']);
        $video_player_id = sanitize_text_field($_POST['id']);

        if (!wp_verify_nonce($nonce, 'vue_clk_nonce')) {
            $response = array(
                'success' => false,
                'message' => 'Invalid nonce'
            );
            wp_send_json($response);
        }

        $settings = get_post_meta($video_player_id, 'video_player_settings', true);

        $title = get_the_title($video_player_id);
        $description = get_post_field('post_content', $video_player_id);

        if (!$settings) {
            $response = array(
                'success' => true,
                'title' => $title,
                'description' => $description,
                'player_size' => "small"
            );
        } else {
            $response = array(
                'success' => true,
                'title' => $title,
                'description' => $description,
                'audio' => isset($settings['audio']) ? $settings['audio'] : false,
                'autoplay' => isset($settings['autoplay']) ? $settings['autoplay'] : false,
                'controls' => isset($settings['controls']) ? $settings['controls'] : true,
                'player_size' => isset($settings['player_size']) ? $settings['player_size'] : "small",
                'video_url' => isset($settings['video_url']) ? $settings['video_url'] : '',
            );
        }
        wp_send_json($response);
    }
}
