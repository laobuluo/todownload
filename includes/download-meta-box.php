<?php
if (!defined('ABSPATH')) {
    exit;
}

function todownload_render_meta_box($post) {
    wp_nonce_field('todownload_meta_box', 'todownload_meta_box_nonce');

    $download_enabled = get_post_meta($post->ID, '_todownload_enabled', true);
    $download_url = get_post_meta($post->ID, '_todownload_url', true);
    $unzip_password = get_post_meta($post->ID, '_todownload_unzip_password', true);
    $shortcode = '[todownload id="' . $post->ID . '"]';
    ?>
    <div class="todownload-meta-box">
        <p>
            <label>
                <input type="checkbox" name="todownload_enabled" value="1" <?php checked($download_enabled, '1'); ?> />
                启用下载框
            </label>
        </p>
        <p>
            <label for="todownload_url">下载地址:</label><br />
            <input type="url" id="todownload_url" name="todownload_url" value="<?php echo esc_url($download_url); ?>" style="width: 100%;" />
        </p>
        <p>
            <label for="todownload_unzip_password">解压密码:</label><br />
            <input type="text" id="todownload_unzip_password" name="todownload_unzip_password" value="<?php echo esc_attr($unzip_password); ?>" style="width: 100%;" />
        </p>
        <p>
            <label for="todownload_shortcode">短代码 (点击复制):</label><br />
            <input type="text" id="todownload_shortcode" value="<?php echo esc_attr($shortcode); ?>" readonly onclick="this.select();" style="width: 100%; background-color: #f0f0f0;" />
        </p>
    </div>
    <?php
}

function todownload_save_meta_box($post_id) {
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

    $download_enabled = isset($_POST['todownload_enabled']) ? '1' : '0';
    update_post_meta($post_id, '_todownload_enabled', $download_enabled);

    if (isset($_POST['todownload_url'])) {
        update_post_meta($post_id, '_todownload_url', esc_url_raw($_POST['todownload_url']));
    }

    if (isset($_POST['todownload_unzip_password'])) {
        update_post_meta($post_id, '_todownload_unzip_password', sanitize_text_field($_POST['todownload_unzip_password']));
    }
} 