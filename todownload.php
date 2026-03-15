<?php
/**
 * Plugin Name: ToDownLoad
 * Plugin URI:  https://www.laojiang.me/7207.html
 * Description: 一个简单的WordPress下载过度插件。公众号：<span style="color: red;">老蒋朋友圈</span>
 * Version: 2.0.0
 * Author: 老蒋和他的小伙伴
 * Author URI: https://www.laojiang.me/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: todownload
 */

if (!defined('ABSPATH')) {
    exit;
}

class ToDownLoad {
    private static $instance = null;
    private $plugin_path;
    private $plugin_url;
    
    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->plugin_path = plugin_dir_path(__FILE__);
        $this->plugin_url = plugin_dir_url(__FILE__);
        
        // Initialize plugin
        add_action('init', array($this, 'init'));
        
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Register settings
        add_action('admin_init', array($this, 'register_settings'));
        
        // Add meta box to post editor
        add_action('add_meta_boxes', array($this, 'add_download_meta_box'));
        
        // Save post meta
        add_action('save_post', array($this, 'save_download_meta'));

        // Include required files
        $this->include_files();

        // Register shortcode and content filter after including files
        add_shortcode('todownload', array($this, 'download_shortcode'));
        add_filter('the_content', array($this, 'append_download_box'));
    }

    private function include_files() {
        // Include meta box class
        if (file_exists($this->plugin_path . 'includes/meta-box.php')) {
            require_once $this->plugin_path . 'includes/meta-box.php';
        }

        // Include frontend display class
        if (file_exists($this->plugin_path . 'includes/frontend-display.php')) {
            require_once $this->plugin_path . 'includes/frontend-display.php';
        }
    }
    
    public function init() {
        // Register scripts and styles
        if (file_exists($this->plugin_path . 'assets/css/style.css')) {
            wp_register_style('todownload-style', $this->plugin_url . 'assets/css/style.css');
        }
    }

    public function add_admin_menu() {
        add_options_page(
            'ToDownLoad 设置',
            'ToDownLoad',
            'manage_options',
            'todownload-settings',
            array($this, 'render_settings_page')
        );
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        if (file_exists($this->plugin_path . 'includes/admin-settings.php')) {
            require $this->plugin_path . 'includes/admin-settings.php';
        }
    }

    public function register_settings() {
        register_setting(
            'todownload_options',
            'todownload_options',
            array(
                'type' => 'array',
                'sanitize_callback' => array($this, 'sanitize_options')
            )
        );

        add_settings_section(
            'todownload_general',
            '基本设置',
            null,
            'todownload-settings'
        );
    }

    public function sanitize_options($input) {
        $new_input = array();
        
        $new_input['enabled'] = isset($input['enabled']) ? 1 : 0;
        $new_input['unzip_text'] = sanitize_text_field($input['unzip_text']);
        $new_input['border_color'] = sanitize_hex_color($input['border_color']);
        $new_input['background_color'] = sanitize_hex_color($input['background_color']);
        $new_input['button_color'] = sanitize_hex_color($input['button_color']);
        $new_input['title_color'] = sanitize_hex_color($input['title_color']);
        $new_input['title_size'] = absint($input['title_size']);
        $new_input['button_text_size'] = absint($input['button_text_size']);

        return $new_input;
    }

    public function add_download_meta_box() {
        add_meta_box(
            'todownload_meta_box',
            '下载设置',
            'todownload_render_meta_box',
            'post',
            'normal',
            'high'
        );
    }

    public function render_meta_box($post) {
        todownload_render_meta_box($post);
    }

    public function save_download_meta($post_id) {
        if (!isset($_POST['todownload_meta_box_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['todownload_meta_box_nonce'], 'todownload_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $fields = array(
            '_todownload_enabled',
            '_todownload_title',
            '_todownload_button_text',
            '_todownload_url',
            '_todownload_file_size',
            '_todownload_version'
        );

        foreach ($fields as $field) {
            if (isset($_POST[str_replace('_', '', $field)])) {
                update_post_meta($post_id, $field, sanitize_text_field($_POST[str_replace('_', '', $field)]));
            }
        }
    }

    public function download_shortcode($atts) {
        // Check if plugin is enabled globally
        $options = get_option('todownload_options');
        if (!isset($options['enabled']) || !$options['enabled']) {
            return '';
        }

        if (file_exists($this->plugin_path . 'includes/frontend-display.php')) {
            include_once $this->plugin_path . 'includes/frontend-display.php';
            $frontend = new ToDownLoad_Frontend();
            return $frontend->shortcode_handler($atts);
        }
        return '';
    }

    public function append_download_box($content) {
        // Don't append if not a single post view
        if (!is_singular('post')) {
            return $content;
        }

        // Check if plugin is enabled globally
        $options = get_option('todownload_options');
        if (!isset($options['enabled']) || !$options['enabled']) {
            return $content;
        }

        $post_id = get_the_ID();
        $enabled = get_post_meta($post_id, '_todownload_enabled', true);

        // If download box is not enabled for this post, return content as is
        if (!$enabled) {
            return $content;
        }

        // Check if content already contains the shortcode
        if (has_shortcode($content, 'todownload')) {
            return $content;
        }

        // If we reach here, append the download box
        if (file_exists($this->plugin_path . 'includes/frontend-display.php')) {
            include_once $this->plugin_path . 'includes/frontend-display.php';
            $frontend = new ToDownLoad_Frontend();
            return $content . $frontend->get_download_box_html($post_id);
        }
        return $content;
    }
    
    public static function activate() {
        // Set default options
        $default_options = array(
            'enabled' => 1,
            'unzip_text' => '解压密码: www.laojiang.me',
            'border_color' => '#e5e5e5',
            'background_color' => '#ffffff',
            'button_color' => '#007bff'
        );
        
        add_option('todownload_options', $default_options);

        // Create necessary directories
        $plugin_path = plugin_dir_path(__FILE__);
        
        // Create includes directory
        if (!file_exists($plugin_path . 'includes')) {
            mkdir($plugin_path . 'includes', 0755, true);
        }
        
        // Create assets directory
        if (!file_exists($plugin_path . 'assets/css')) {
            mkdir($plugin_path . 'assets/css', 0755, true);
        }
    }
    
    public static function deactivate() {
        // Cleanup if necessary
    }
    
    public static function uninstall() {
        // Remove plugin options
        delete_option('todownload_options');
        
        // Remove post meta
        global $wpdb;
        $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'todownload_%'");
    }
}

// Initialize plugin
function todownload_init() {
    $plugin = ToDownLoad::getInstance();
}

// Register activation, deactivation and uninstall hooks
register_activation_hook(__FILE__, array('ToDownLoad', 'activate'));
register_deactivation_hook(__FILE__, array('ToDownLoad', 'deactivate'));
register_uninstall_hook(__FILE__, array('ToDownLoad', 'uninstall'));

// Initialize the plugin
add_action('plugins_loaded', 'todownload_init'); 