<?php
if (!defined('ABSPATH')) {
    exit;
}

class ToDownLoad_Frontend {
    private $options;

    public function __construct() {
        $this->options = get_option('todownload_options');
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
    }

    public function enqueue_styles() {
        wp_enqueue_style('todownload-style', plugins_url('assets/css/style.css', dirname(__FILE__)));
        
        $custom_css = $this->get_custom_css();
        wp_add_inline_style('todownload-style', $custom_css);
    }

    private function get_custom_css() {
        $border_color = isset($this->options['border_color']) ? $this->options['border_color'] : '#e9ecef';
        $background_color = isset($this->options['background_color']) ? $this->options['background_color'] : '#f8f9fa';
        $button_color = isset($this->options['button_color']) ? $this->options['button_color'] : '#007bff';
        $title_color = isset($this->options['title_color']) ? $this->options['title_color'] : '#6c757d';
        $title_size = isset($this->options['title_size']) ? intval($this->options['title_size']) : 14;
        $button_text_size = isset($this->options['button_text_size']) ? intval($this->options['button_text_size']) : 12;

        return "
            .todownload-box {
                border-color: {$border_color};
                background-color: {$background_color};
            }
            .todownload-title {
                color: {$title_color};
                font-size: {$title_size}px;
            }
            .todownload-button {
                background-color: {$button_color};
                color: white;
                --todownload-btn-text-size: {$button_text_size}px;
            }
            .todownload-button:hover {
                background-color: " . $this->adjust_brightness($button_color, -20) . ";
                color: white;
            }
        ";
    }

    private function adjust_brightness($hex, $steps) {
        $hex = str_replace('#', '', $hex);
        
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));

        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }

    public function append_download_box($content) {
        if (!is_singular('post')) {
            return $content;
        }

        if (!isset($this->options['enabled']) || !$this->options['enabled']) {
            return $content;
        }

        $post_id = get_the_ID();
        $enabled = get_post_meta($post_id, '_todownload_enabled', true);

        if (!$enabled) {
            return $content;
        }

        if (has_shortcode($content, 'todownload')) {
            return $content;
        }

        return $content . $this->get_download_box_html($post_id);
    }

    public function shortcode_handler($atts) {
        // Get post ID from shortcode attributes or current post
        $post_id = isset($atts['id']) ? intval($atts['id']) : get_the_ID();
        
        // Verify post exists and has download URL
        $download_url = get_post_meta($post_id, '_todownload_url', true);
        if (!$download_url) {
            return '';
        }

        // Generate and return the download box HTML
        return $this->get_download_box_html($post_id);
    }

    public function get_download_box_html($post_id) {
        $title = get_post_meta($post_id, '_todownload_title', true);
        $button_text = get_post_meta($post_id, '_todownload_button_text', true) ?: __('Download', 'todownload');
        $download_url = get_post_meta($post_id, '_todownload_url', true);
        $skip_mode = get_post_meta($post_id, '_todownload_skip_mode', true) ?: 'direct';

        if (!$download_url) {
            return '';
        }

        // Generate download link based on skip mode
        if ($skip_mode === 'direct') {
            // Direct mode: BASE64 encoded URL
            $encoded_url = base64_encode($download_url);
            $download_link = plugins_url('go.php?url=' . $encoded_url, dirname(__FILE__));
        } else {
            // Indirect mode: Using post ID
            $download_link = plugins_url('go.php?id=' . $post_id, dirname(__FILE__));
        }

        ob_start();
        ?>
        <div class="todownload-box">
            <div class="todownload-title">
                <?php echo esc_html($title); ?>
            </div>
            <div class="todownload-buttons">
                <a href="<?php echo esc_url($download_link); ?>" class="todownload-button download-btn" onclick="window.open(this.href); return false;">
                <span style="color: white !important;"><?php echo esc_html($button_text); ?></span> 
                    </a>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

new ToDownLoad_Frontend(); 