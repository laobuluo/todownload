<?php
if (!defined('ABSPATH')) {
    require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php');
}

// Check if ID parameter exists (indirect mode)
if (isset($_GET['id'])) {
    $post_id = intval($_GET['id']);
    $download_url = get_post_meta($post_id, '_todownload_url', true);
    
    if (empty($download_url)) {
        wp_die(__('Invalid download URL', 'todownload'));
    }

    // Get download information
    $title = get_post_meta($post_id, '_todownload_title', true);
    $version = get_post_meta($post_id, '_todownload_version', true);
    $file_size = get_post_meta($post_id, '_todownload_file_size', true);
    $last_updated = get_the_modified_date('F j, Y', $post_id);
    $options = get_option('todownload_options');
    $unzip_text = isset($options['unzip_text']) ? $options['unzip_text'] : '';
    $button_text = get_post_meta($post_id, '_todownload_button_text', true);
    
    // BASE64 encode the download URL
    $encoded_url = base64_encode($download_url);
    
    // Output transition page
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo esc_html($title); ?></title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                line-height: 1.6;
                margin: 0;
                padding: 20px;
                background-color: #f8f9fa;
            }
            .download-container {
                max-width: 800px;
                margin: 0 auto;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                padding: 30px;
            }
            .download-title {
                font-size: 24px;
                color: #333;
                margin-bottom: 30px;
                text-align: center;
            }
            .info-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 30px;
            }
            .info-table tr {
                border-bottom: 1px solid #eee;
            }
            .info-table tr:last-child {
                border-bottom: none;
            }
            .info-table td {
                padding: 15px 10px;
            }
            .info-table td:first-child {
                color: #666;
                width: 150px;
            }
            .info-table td:last-child {
                color: #333;
                text-align: right;
            }
            .unzip-notice {
                background-color: #fff3cd;
                border: 1px solid #ffeeba;
                color: #856404;
                padding: 15px;
                border-radius: 4px;
                margin-bottom: 30px;
                text-align: center;
            }
            .download-button {
                display: block;
                width: 200px;
                margin: 0 auto;
                padding: 12px 24px;
                background-color: #007bff;
                color: #fff;
                text-decoration: none;
                text-align: center;
                border-radius: 4px;
                font-weight: 500;
                transition: background-color 0.2s;
            }
            .download-button:hover {
                background-color: #0056b3;
                text-decoration: none;
                color: #fff;
            }
        </style>
    </head>
    <body>
        <div class="download-container">
            <h1 class="download-title"><?php echo esc_html($title); ?></h1>
            
            <table class="info-table">
                <tr>
                    <td>版本</td>
                    <td><?php echo esc_html($version); ?></td>
                </tr>
                <tr>
                    <td>文件大小</td>
                    <td><?php echo esc_html($file_size); ?></td>
                </tr>
                <tr>
                    <td>最后更新</td>
                    <td><?php echo esc_html($last_updated); ?></td>
                </tr>
            </table>

            <?php if ($unzip_text): ?>
            <div class="unzip-notice">
                <?php echo esc_html($unzip_text); ?>
            </div>
            <?php endif; ?>

            <a href="<?php echo esc_url(plugins_url('go.php?url=' . $encoded_url, __FILE__)); ?>" class="download-button" onclick="window.open(this.href); return false;">
            <?php echo esc_html($button_text); ?>
            </a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Direct URL mode or download from transition page
if (!isset($_GET['url']) || empty($_GET['url'])) {
    wp_die(__('Invalid download URL', 'todownload'));
}

// Decode and validate URL
$url = base64_decode($_GET['url']);
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    wp_die(__('Invalid download URL', 'todownload'));
}

// Output transition page for direct URL mode
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>外链跳转提示</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .transition-container {
            max-width: 700px;
            margin: 50px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .transition-title {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
        .url-display {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            word-break: break-all;
        }
        .warning-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
        }
        .continue-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0056b3;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .continue-button:hover {
            background-color: #003d82;
        }
    </style>
</head>
<body>
    <div class="transition-container">
        <h1 class="transition-title">外链跳转提示</h1>
        <p>您即将离开本站，访问以下网址：</p>
        <div class="url-display">
            <?php echo esc_html($url); ?>
        </div>
        <div class="warning-notice">
            注意：该链接将带您离开本站。我们不能保证外部网站内容，注意您的网络安全。
        </div>
        <p>如您仍认为继续访问，请点击下面的按钮：</p>
        <a href="<?php echo esc_url($url); ?>" class="continue-button">继续访问</a>
    </div>
</body>
</html>
<?php exit; ?> 