<?php
/*
*
 * @link              https://barrels.ng
 * @since             1.0.0
 * @package           Backgrounder
 *
 * @wordpress-plugin
 * Plugin Name:       Backgrounder
 * Plugin URI:        https://topazdom.com/
 * Description: Allows you to set a background image and CSS properties for the website body, specially developed for BarrelsNG by Topazdom Technologies Limited.
 * Version:           1.0.0
 * Author:            Topazdom Technologies Limited
 * Author URI:        https://topazdom.com/
 * Text Domain:       backgrounder
 * Domain Path:       /languages
*/

// Register the Backgrounder settings page
function backgrounder_register_settings_page()
{
    add_options_page(
        'Backgrounder Settings',
        'Backgrounder',
        'manage_options',
        'backgrounder-settings',
        'backgrounder_render_settings_page'
    );
}
add_action('admin_menu', 'backgrounder_register_settings_page');

// Enqueue scripts and styles for the Backgrounder settings page
function backgrounder_enqueue_admin_scripts($hook)
{
    if ($hook === 'settings_page_backgrounder-settings') {
        wp_enqueue_media();
        wp_enqueue_script('backgrounder-admin-script', plugin_dir_url(__FILE__) . 'js/admin-script.js', array('jquery'), '1.0', true);
    }
}
add_action('admin_enqueue_scripts', 'backgrounder_enqueue_admin_scripts');

// Render the Backgrounder settings page
function backgrounder_render_settings_page()
{
    // Save settings if the form is submitted
    if (isset($_POST['backgrounder_submit'])) {
        if (!current_user_can('manage_options') || !wp_verify_nonce($_POST['backgrounder_nonce'], 'backgrounder_settings')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        // Retrieve and sanitize the submitted settings
        $background_image = sanitize_text_field($_POST['background_image']);
        $background_repeat = sanitize_text_field($_POST['background_repeat']);
        $apply_padding = isset($_POST['apply_padding']) ? 1 : 0;
        $apply_to = sanitize_text_field($_POST['apply_to']);
        $apply_to_category = sanitize_text_field($_POST['apply_to_category']);
        $enabled = isset($_POST['enabled']) ? 1 : 0;


        // Update the plugin options with the new settings
        update_option('backgrounder_background_image', $background_image);
        update_option('backgrounder_background_repeat', $background_repeat);
        update_option('backgrounder_apply_padding', $apply_padding);
        update_option('backgrounder_apply_to', $apply_to);
        update_option('backgrounder_apply_to_category', $apply_to_category);
        update_option('backgrounder_enabled', $enabled);

        echo '<div class="notice notice-success"><p>Settings saved successfully.</p></div>';
    }

    // Retrieve the current settings
    $background_image = get_option('backgrounder_background_image', '');
    $background_repeat = get_option('backgrounder_background_repeat', 'no-repeat');
    $apply_padding = get_option('backgrounder_apply_padding', 0);
    $apply_to = get_option('backgrounder_apply_to', 'all');
    $apply_to_category = get_option('backgrounder_apply_to_category', '');
    $enabled = get_option('backgrounder_enabled', 0);

    // Render the settings page HTML
?>
    <div class="wrap">
        <h1>Backgrounder Settings</h1>
        <form method="post" action="">
            <?php wp_nonce_field('backgrounder_settings', 'backgrounder_nonce'); ?>

            <label for="background_image">Background Image:</label>
            <input type="text" id="background_image" name="background_image" value="<?php echo esc_attr($background_image); ?>">
            <input type="button" class="button button-secondary" value="Select Image" id="backgrounder_upload_image">

            <label for="background_repeat">Background Repeat:</label>
            <select id="background_repeat" name="background_repeat">
                <option value="no-repeat" <?php selected($background_repeat, 'no-repeat'); ?>>No Repeat</option>
                <option value="repeat" <?php selected($background_repeat, 'repeat'); ?>>Repeat</option>
                <option value="repeat-x" <?php selected($background_repeat, 'repeat-x'); ?>>Repeat Horizontally</option>
                <option value="repeat-y" <?php selected($background_repeat, 'repeat-y'); ?>>Repeat Vertically</option>
            </select>

            <!-- <label for="apply_padding">
                <input type="checkbox" id="apply_padding" name="apply_padding" value="1" <?php checked($apply_padding, 1); ?>>
                Apply 10% padding on left and right
            </label> -->

            <p>Apply to:</p>
            <label>
                <input type="radio" name="apply_to" value="all" <?php checked($apply_to, 'all'); ?>>
                All Pages
            </label>
            <br>
            <label>
                <input type="radio" name="apply_to" value="homepage" <?php checked($apply_to, 'homepage'); ?>>
                Homepage Only
            </label>
            <br>
            <label>
                <input type="radio" name="apply_to" value="category" <?php checked($apply_to, 'category'); ?>>
                Specific Category:
                <input type="text" name="apply_to_category" value="<?php echo esc_attr($apply_to_category); ?>">
            </label>
            <br>

            <p>
                <label>
                    <input type="checkbox" name="enabled" value="1" <?php checked($enabled, 1); ?>>
                    Enable Backgrounder
                </label>
            </p>
            <br>
            <p class="submit">
                <input type="submit" name="backgrounder_submit" class="button-primary" value="Save Settings">
            </p>
        </form>
    </div>
<?php
}

// Apply the background image and CSS properties to the website body
function backgrounder_apply_background()
{
    $enabled = get_option('backgrounder_enabled', 0);

    // Check if the plugin is enabled
    if ($enabled) {
        $background_image = get_option('backgrounder_background_image', '');
        $background_repeat = get_option('backgrounder_background_repeat', 'no-repeat');
        $apply_padding = get_option('backgrounder_apply_padding', 0);
        $apply_to = get_option('backgrounder_apply_to', 'all');
        $apply_to_category = get_option('backgrounder_apply_to_category', '');

        // Check if the background image is set
        if (!empty($background_image)) {
            $output = '<style>';
            $output .= 'body {';
            $output .= '    background-image: url("' . esc_url($background_image) . '");';
            $output .= '    background-repeat: ' . esc_attr($background_repeat) . ';';
            $output .= '    background-size: 10%;';
            $output .= '    opacity: 1.0;';
            $output .= '}';

            //apply opacity to bwp-main section
            $output .= '#bwp-main {';
            $output .= '    background: white;';
            $output .= '    margin: 0 10% 0 10%;';
            $output .= '    opacity: 0.955;';
            $output .= '}';

            /* Small Devices, Tablets */
            $output .= '  @media only screen and (max-width : 768px) {';
            $output .= '#bwp-main {';
            $output .= '    background: white;';
            $output .= '    margin: 0 15% 0 15%;';
            $output .= '    opacity: 0.955;';
            $output .= '}';

            $output .= 'body {';
            $output .= '    background-image: url("' . esc_url($background_image) . '");';
            $output .= '    background-repeat: space;';
            $output .= '    background-size: 20%;';
            $output .= '    opacity: 1.0;';
            $output .= '}';
            $output .= '  }';

            //apply opacity to bwp-header section
            $output .= '#bwp-header {';
            $output .= '    background: white;';
            $output .= '    opacity: 0.955;';
            $output .= '}';
            // Apply padding if enabled
            if ($apply_padding) {
                $output .= 'body {';
                $output .= '    padding-left: 10%;';
                $output .= '    padding-right: 10%;';
                $output .= '}';
            }

            // Restrict the background to specific pages or categories
            if ($apply_to !== 'all') {
                if ($apply_to === 'homepage') {
                    $output .= 'body:not(.home) {';
                } elseif ($apply_to === 'category' && !empty($apply_to_category)) {
                    $category_slugs = array_map('sanitize_title_for_query', explode(',', $apply_to_category));
                    $output .= 'body:not(.category-' . implode(', .category-', $category_slugs) . ') {';
                }

                $output .= '    background-image: none;';
                $output .= '}';
            }

            $output .= '</style>';

            echo $output;
        }
    }
}
add_action('wp_head', 'backgrounder_apply_background');

// Handle image upload and return the attachment URL
function backgrounder_handle_upload()
{
    if (!empty($_FILES['backgrounder_image'])) {
        $file = $_FILES['backgrounder_image'];

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error('Upload failed. Please try again.');
        }

        // Check file type
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        $file_type = wp_check_filetype($file['name'], $allowed_types);
        if (!$file_type['ext'] || !$file_type['type']) {
            wp_send_json_error('Invalid file type. Please upload a JPEG, PNG, or GIF image.');
        }

        // Handle the upload
        $upload_dir = wp_upload_dir();
        $filename = wp_unique_filename($upload_dir['path'], $file['name']);
        $new_path = $upload_dir['path'] . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $new_path)) {
            $attachment = array(
                'guid' => $upload_dir['url'] . '/' . $filename,
                'post_mime_type' => $file_type['type'],
                'post_title' => preg_replace('/\.[^.]+$/', '', $file['name']),
                'post_content' => '',
                'post_status' => 'inherit'
            );

            $attachment_id = wp_insert_attachment($attachment, $new_path);
            if (!is_wp_error($attachment_id)) {
                wp_send_json_success(wp_get_attachment_url($attachment_id));
            }
        }
    }

    wp_send_json_error('Upload failed. Please try again.');
}
add_action('wp_ajax_backgrounder_handle_upload', 'backgrounder_handle_upload');
add_action('wp_ajax_nopriv_backgrounder_handle_upload', 'backgrounder_handle_upload');

//Hook for Uninstall the plugin
function backgrounder_uninstall() {
    // Remove plugin options
    delete_option('backgrounder_background_image');
    delete_option('backgrounder_background_repeat');
    delete_option('backgrounder_apply_padding');
    delete_option('backgrounder_apply_to');
    delete_option('backgrounder_apply_to_category');
    delete_option('backgrounder_enabled');
}
register_uninstall_hook(__FILE__, 'backgrounder_uninstall');