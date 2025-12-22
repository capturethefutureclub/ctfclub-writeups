<?php
/**
 * Plugin Name: My Plugin
 * Plugin URI: https://example.com/my-plugin
 * Description: A custom WordPress plugin for the challenge
 * Version: 1.0.0
 * Author: Challenge Author
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: my-plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('MY_PLUGIN_VERSION', '1.0.0');
define('MY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MY_PLUGIN_URL', plugin_dir_url(__FILE__));
ob_start();

function my_auto_login_new_user( $user_id ) {
    if ( defined( 'WP_CLI' ) && WP_CLI ) {
        return;
    }
    // 1. Get the user data
    $user = get_user_by( 'id', $user_id );

    // 2. Set the current user to this new user
    wp_set_current_user( $user_id, $user->user_login );
    wp_set_auth_cookie( $user_id );
    
    // 3. Redirect to home page (or any other URL)
    wp_redirect( home_url() );
    exit;
}
add_action( 'user_register', 'my_auto_login_new_user' );

/**
 * Main plugin class
 */
class My_Plugin {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_filter('body_class', array($this, 'add_body_class'));
        add_action("wp_loaded", array($this, "init"), 9999);
    }

    public function init() {
        if (isset($_GET['settings'])) {
            $this->admin_page();
            exit;
        }
    }
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_style('my-plugin-style', MY_PLUGIN_URL . 'assets/style.css', array(), MY_PLUGIN_VERSION);
        wp_enqueue_script('my-plugin-script', MY_PLUGIN_URL . 'assets/script.js', array('jquery'), MY_PLUGIN_VERSION, true);
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            'My Plugin Settings',
            'My Plugin',
            'manage_options',
            'my-plugin-settings',
            array($this, 'admin_page'),
            'dashicons-admin-generic',
            30
        );
    }

    /**
     * Add body class based on mode
     */
    public function add_body_class($classes) {
        $mode = get_option('my_plugin_dark_mode', 'light');
        if ($mode === 'dark') {
            $classes[] = 'dark-mode';
        }
        return $classes;
    }
    
    /**
     * Admin page callback
     */
    public function admin_page() {
        // Ensure user is admin
        if (!is_admin()) {
            wp_die('Access denied');
        }

        if (isset($_POST['my_plugin_action'])) {
            check_admin_referer("my_plugin_nonce", "my_plugin_nonce");
            
            $mode = sanitize_text_field($_POST['mode']);
            update_option($_POST['my_plugin_action'], $mode);
            echo '<div class="updated"><p>Mode saved.</p></div>';
        } elseif (isset($_POST['my_plugin_action']) && $_POST['my_plugin_action'] === 'reset') {
            delete_option('my_plugin_dark_mode');
            echo '<div class="updated"><p>Mode reset to default.</p></div>';
        }

        $current_mode = get_option('my_plugin_dark_mode', 'light');
        ?>
        <div class="wrap">
            <h1>My Plugin Settings</h1>
            <div class="card">
                <h2>Theme Mode</h2>
                <form method="post" action="">
                    <?php wp_nonce_field('my_plugin_nonce', 'my_plugin_nonce'); ?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Select Mode</th>
                            <td>
                                <select name="mode">
                                    <option value="light" <?php selected($current_mode, 'light'); ?>>Light Mode</option>
                                    <option value="dark" <?php selected($current_mode, 'dark'); ?>>Dark Mode</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <p class="submit">
                        <button type="submit" name="my_plugin_action" value="my_plugin_dark_mode" class="button button-primary">Save Changes</button>
                        <button type="submit" name="my_plugin_action" value="reset" class="button button-secondary">Reset to Default</button>
                    </p>
                </form>
            </div>
        </div>
        <?php
    }
}

// Initialize the plugin
new My_Plugin();

