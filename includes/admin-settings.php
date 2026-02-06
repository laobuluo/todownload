<?php
if (!defined('ABSPATH')) {
    exit;
}

// Ensure we're in admin
if (!is_admin()) {
    return;
}

$options = get_option('todownload_options');
?>

<div class="wrap">
    <h1>ToDownLoad 设置</h1>
    <p class="description">
    在这里配置 ToDownLoad 插件设置。插件介绍 <a href="https://www.laojiang.me/6255.html" target="_blank" target="_blank">查看这里</a>（关注公众号：<span style="color: red;">老蒋朋友圈</span>）
    </p>
    <form method="post" action="options.php">
        <?php
        settings_fields('todownload_options');
        do_settings_sections('todownload-settings');
        ?>
        <table class="form-table" role="presentation">
            <tr valign="top">
                <th scope="row">启用插件</th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text">启用插件</legend>
                        <label for="todownload_enabled">
                            <input type="checkbox" id="todownload_enabled" name="todownload_options[enabled]" value="1" <?php checked(isset($options['enabled']) ? $options['enabled'] : 0); ?> />
                            启用下载功能
                        </label>
                    </fieldset>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="todownload_unzip_text">解压密码文本</label></th>
                <td>
                    <input type="text" id="todownload_unzip_text" name="todownload_options[unzip_text]" value="<?php echo esc_attr(isset($options['unzip_text']) ? $options['unzip_text'] : ''); ?>" class="regular-text" />
                    <p class="description">显示解压密码信息文本</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="todownload_title_color">标题颜色</label></th>
                <td>
                    <input type="color" id="todownload_title_color" name="todownload_options[title_color]" value="<?php echo esc_attr(isset($options['title_color']) ? $options['title_color'] : '#6c757d'); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="todownload_title_size">标题字体大小</label></th>
                <td>
                    <input type="number" id="todownload_title_size" name="todownload_options[title_size]" value="<?php echo esc_attr(isset($options['title_size']) ? $options['title_size'] : '14'); ?>" min="12" max="24" step="1" style="width: 60px;" />
                    <span>px</span>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="todownload_button_text_size">按钮文本字体大小</label></th>
                <td>
                    <input type="number" id="todownload_button_text_size" name="todownload_options[button_text_size]" value="<?php echo esc_attr(isset($options['button_text_size']) ? $options['button_text_size'] : '12'); ?>" min="10" max="20" step="1" style="width: 60px;" />
                    <span>px</span>
                    <p class="description">默认是12px</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="todownload_border_color">边框颜色</label></th>
                <td>
                    <input type="color" id="todownload_border_color" name="todownload_options[border_color]" value="<?php echo esc_attr(isset($options['border_color']) ? $options['border_color'] : '#e9ecef'); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="todownload_background_color">背景颜色</label></th>
                <td>
                    <input type="color" id="todownload_background_color" name="todownload_options[background_color]" value="<?php echo esc_attr(isset($options['background_color']) ? $options['background_color'] : '#f8f9fa'); ?>" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="todownload_button_color">按钮颜色</label></th>
                <td>
                    <input type="color" id="todownload_button_color" name="todownload_options[button_color]" value="<?php echo esc_attr(isset($options['button_color']) ? $options['button_color'] : '#007bff'); ?>" />
                </td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
    <p><img width="150" height="150" src="<?php echo plugins_url('../assets/images/wechat.png', __FILE__); ?>" alt="扫码关注公众号" /></p>
</div> 