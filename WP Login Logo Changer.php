<?php
/*
<?php
/*
Plugin Name: Custom Login Logo by Taukir
Description: Elevate your WordPress login page with Custom Login Logo by Taukir. This powerful plugin allows you to effortlessly customize the login logo and URL, creating a branded and seamless experience for your users. Visit <a href="https://github.com/taukir007" target="_blank">GitHub</a> for more information. Connect with the author at <a href="https://facebook.com/imph3n1x" target="_blank">Facebook</a>.
Version: 1.0
Author: Taukir Ahmed
Author URI: https://github.com/taukir007
*/

// Register the settings page
function custom_login_logo_settings_menu() {
    add_options_page(
        'Custom Login Logo Settings',
        'Login Logo Settings',
        'manage_options',
        'custom_login_logo_settings',
        'custom_login_logo_settings_page'
    );
}
add_action('admin_menu', 'custom_login_logo_settings_menu');

// Display the settings page
function custom_login_logo_settings_page() {
    ?>
    <div class="wrap">
        <h2>Custom Login Logo Settings by Taukir</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('custom_login_logo_settings');
            do_settings_sections('custom_login_logo_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register and initialize settings
function custom_login_logo_settings_init() {
    register_setting(
        'custom_login_logo_settings',
        'custom_login_logo_options',
        'custom_login_logo_sanitize_options'
    );

    add_settings_section(
        'custom_login_logo_section',
        'Logo Settings',
        'custom_login_logo_section_callback',
        'custom_login_logo_settings'
    );

    add_settings_field(
        'custom_login_logo_image',
        'Select Logo',
        'custom_login_logo_image_callback',
        'custom_login_logo_settings',
        'custom_login_logo_section'
    );

    add_settings_field(
        'custom_login_logo_url',
        'Website URL',
        'custom_login_logo_url_callback',
        'custom_login_logo_settings',
        'custom_login_logo_section'
    );
}
add_action('admin_init', 'custom_login_logo_settings_init');

// Sanitize options
function custom_login_logo_sanitize_options($input) {
    $sanitized_input = array();

    if (isset($input['custom_login_logo_image'])) {
        $sanitized_input['custom_login_logo_image'] = esc_url_raw($input['custom_login_logo_image']);
    }

    if (isset($input['custom_login_logo_url'])) {
        $sanitized_input['custom_login_logo_url'] = esc_url_raw($input['custom_login_logo_url']);
    }

    return $sanitized_input;
}

// Section callback
function custom_login_logo_section_callback() {
    echo '<p>Customize the login logo and URL below:</p>';
}

// Enqueue media scripts
function enqueue_media_scripts($hook) {
    if ('settings_page_custom_login_logo_settings' === $hook) {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'enqueue_media_scripts');

// ...

// Image callback with WordPress Media Uploader
function custom_login_logo_image_callback() {
    $options = get_option('custom_login_logo_options');
    $logo_url = isset($options['custom_login_logo_image']) ? $options['custom_login_logo_image'] : '';
    ?>
    <div>
        <input type="text" name="custom_login_logo_options[custom_login_logo_image]" id="custom_login_logo_image" value="<?php echo esc_url($logo_url); ?>" style="width: 100%;">
        <input type="button" id="upload_logo_button" class="button" value="Select Logo">
    </div>
    <script>
        jQuery(document).ready(function ($) {
            $('#upload_logo_button').on('click', function (e) {
                e.preventDefault();

                if (wp.media) {
                    var customUploader = wp.media({
                        title: 'Choose Logo',
                        button: {
                            text: 'Choose Logo'
                        },
                        multiple: false
                    });

                    customUploader.on('select', function () {
                        var attachment = customUploader.state().get('selection').first().toJSON();
                        $('#custom_login_logo_image').val(attachment.url);
                    });

                    customUploader.open();
                }
            });
        });
    </script>
    <p class="description">Select the login logo (PNG, SVG, or JPG).</p>
    <?php
}


// URL callback
function custom_login_logo_url_callback() {
    $options = get_option('custom_login_logo_options');
    $url = isset($options['custom_login_logo_url']) ? $options['custom_login_logo_url'] : '';
    ?>
    <input type="url" name="custom_login_logo_options[custom_login_logo_url]" value="<?php echo esc_url($url); ?>">
    <p class="description">Enter the website URL.</p>
    <?php
}

// Apply custom logo and URL to login page
function apply_custom_login_logo() {
    $options = get_option('custom_login_logo_options');

    if (!empty($options['custom_login_logo_image'])) {
        echo '<style type="text/css">
            .login h1 a {
                background-image: url(' . esc_url($options['custom_login_logo_image']) . ');
                background-position: center center;
                background-size: contain;
                width: 100%;
            }
        </style>';
    }

    if (!empty($options['custom_login_logo_url'])) {
        remove_filter('login_headerurl', 'login_url'); // Remove previous filter
        add_filter('login_headerurl', function () use ($options) {
            return esc_url($options['custom_login_logo_url']);
        });
    }
}
add_action('login_head', 'apply_custom_login_logo');
