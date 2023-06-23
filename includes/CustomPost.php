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

    //Save video player meta fields
    function save_video_player_meta_fields($post_id)
    {
        // Save video URL
        if (isset($_POST['video_url'])) {
            $video_url = sanitize_text_field($_POST['video_url']);
            update_post_meta($post_id, 'video_url', $video_url);
        }

        // Save autoplay setting
        $autoplay = isset($_POST['autoplay']) ? 'on' : 'off';
        update_post_meta($post_id, 'autoplay', $autoplay);

        // Save audio autoplay setting
        $audio_autoplay = isset($_POST['audio_autoplay']) ? 'on' : 'off';
        update_post_meta($post_id, 'audio_autoplay', $audio_autoplay);

        // Save player height
        if (isset($_POST['player_height'])) {
            $player_height = absint($_POST['player_height']);
            update_post_meta($post_id, 'player_height', $player_height);
        }

        // Save player width
        if (isset($_POST['player_width'])) {
            $player_width = absint($_POST['player_width']);
            update_post_meta($post_id, 'player_width', $player_width);
        }
    }


    //Render video player

    function render_video_player_meta_fields($post)
    {
        // Retrieve saved settings
        $video_url = get_post_meta($post->ID, 'video_url', true);
        $autoplay = get_post_meta($post->ID, 'autoplay', true);
        $audio_autoplay = get_post_meta($post->ID, 'audio_autoplay', true);
        $player_height = get_post_meta($post->ID, 'player_height', true);
        $player_width = get_post_meta($post->ID, 'player_width', true);

        // Output meta fields HTML
?>
        <label for="video_url">Video URL:</label>
        <input type="text" id="video_url" name="video_url" value="<?php echo esc_attr($video_url); ?>"><br><br>

        <label for="autoplay">Autoplay:</label>
        <input type="checkbox" id="autoplay" name="autoplay" <?php checked($autoplay, 'on'); ?>><br><br>

        <label for="audio_autoplay">Audio Autoplay:</label>
        <input type="checkbox" id="audio_autoplay" name="audio_autoplay" <?php checked($audio_autoplay, 'on'); ?>><br><br>

        <label for="player_height">Player Height:</label>
        <input type="number" id="player_height" name="player_height" value="<?php echo esc_attr($player_height); ?>"><br><br>

        <label for="player_width">Player Width:</label>
        <input type="number" id="player_width" name="player_width" value="<?php echo esc_attr($player_width); ?>"><br><br>
<?php
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
    
}
