<?php
if (!defined('ABSPATH')) {
    exit;
}

class ToDownLoad_MetaBox {
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('save_post', array($this, 'save_meta_box'));
    }

    public function add_meta_box() {
        $options = get_option('todownload_options');
        if (!isset($options['enabled']) || !$options['enabled']) {
            return;
        }

        add_meta_box(
            'todownload_meta_box',
            __('Download Settings', 'todownload'),
            array($this, 'render_meta_box'),
            'post',
            'normal',
            'high'
        );
    }

    public function render_meta_box($post) {
        wp_nonce_field('todownload_meta_box', 'todownload_meta_box_nonce');

        $title = get_post_meta($post->ID, '_todownload_title', true);
        $button_text = get_post_meta($post->ID, '_todownload_button_text', true);
        $download_url = get_post_meta($post->ID, '_todownload_url', true);
        $file_size = get_post_meta($post->ID, '_todownload_file_size', true);
        $version = get_post_meta($post->ID, '_todownload_version', true);
        $enabled = get_post_meta($post->ID, '_todownload_enabled', true);
        $skip_mode = get_post_meta($post->ID, '_todownload_skip_mode', true);
        
        // Set default skip mode to 'indirect' if not set
        if (empty($skip_mode)) {
            $skip_mode = 'indirect';
        }
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="todownload_enabled"><?php echo esc_html__('启用下载框', 'todownload'); ?></label>
                </th>
                <td>
                    <input type="checkbox" id="todownload_enabled" name="todownload_enabled" value="1" <?php checked($enabled, '1'); ?> />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="todownload_title"><?php echo esc_html__('标题', 'todownload'); ?></label>
                </th>
                <td>
                    <input type="text" id="todownload_title" name="todownload_title" value="<?php echo esc_attr($title); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="todownload_button_text"><?php echo esc_html__('下载按钮文本', 'todownload'); ?></label>
                </th>
                <td>
                    <input type="text" id="todownload_button_text" name="todownload_button_text" value="<?php echo esc_attr($button_text); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="todownload_url"><?php echo esc_html__('下载地址', 'todownload'); ?></label>
                </th>
                <td>
                    <input type="url" id="todownload_url" name="todownload_url" value="<?php echo esc_url($download_url); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="todownload_file_size"><?php echo esc_html__('文件大小', 'todownload'); ?></label>
                </th>
                <td>
                    <input type="text" id="todownload_file_size" name="todownload_file_size" value="<?php echo esc_attr($file_size); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="todownload_version"><?php echo esc_html__('版本', 'todownload'); ?></label>
                </th>
                <td>
                    <input type="text" id="todownload_version" name="todownload_version" value="<?php echo esc_attr($version); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="todownload_skip_mode"><?php echo esc_html__('跳转模式', 'todownload'); ?></label>
                </th>
                <td>
                    <fieldset>
                        <label style="margin-right: 20px;">
                            <input type="radio" name="todownload_skip_mode" value="direct" <?php checked($skip_mode, 'direct'); ?> />
                            <?php echo esc_html__('加密跳转过度页', 'todownload'); ?>
                            <p class="description"><?php echo esc_html__('不显示下载详情，跳转到过度跳转（BASE64编码）', 'todownload'); ?></p>
                        </label>
                        <br><br>
                        <label>
                            <input type="radio" name="todownload_skip_mode" value="indirect" <?php checked($skip_mode, 'indirect'); ?> />
                            <?php echo esc_html__('下载详情页过度', 'todownload'); ?>
                            <p class="description"><?php echo esc_html__('显示下载详情页过渡，在下载前显示文件信息', 'todownload'); ?></p>
                        </label>
                    </fieldset>
                </td>
            </tr>
        </table>
        <p class="description">
            <?php echo esc_html__('你也可以使用短代码：[todownload] 或 [todownload id="ID"]', 'todownload'); ?>
        </p>
        <?php
    }

    public function save_meta_box($post_id) {
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
            '_todownload_enabled' => isset($_POST['todownload_enabled']) ? '1' : '0',
            '_todownload_title' => sanitize_text_field($_POST['todownload_title']),
            '_todownload_button_text' => sanitize_text_field($_POST['todownload_button_text']),
            '_todownload_url' => esc_url_raw($_POST['todownload_url']),
            '_todownload_file_size' => sanitize_text_field($_POST['todownload_file_size']),
            '_todownload_version' => sanitize_text_field($_POST['todownload_version']),
            '_todownload_skip_mode' => isset($_POST['todownload_skip_mode']) ? sanitize_text_field($_POST['todownload_skip_mode']) : 'indirect'
        );

        foreach ($fields as $key => $value) {
            update_post_meta($post_id, $key, $value);
        }
    }
}

new ToDownLoad_MetaBox(); 