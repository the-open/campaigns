<?php
/**
 * @package Campaigns
 * @version 0.1
 */

/*
  Plugin Name: Campaigns
  Description: Pull and lists petitions from CSL and Speakout
  Author: Hiemanshu Sharma
  Version: 0.1
  Text Domain: campaigns
*/

if ( ! defined('CAMPAIGNS_BASE_DIR') ) {
    define('CAMPAIGNS_BASE_DIR', trailingslashit(plugin_dir_path(__FILE__)));
}

register_activation_hook(__FILE__, 'campaigns_activation_hook');
register_deactivation_hook(__FILE__, 'campaigns_deactivation_hook');

add_action('admin_menu', 'campaigns_admin_menu');
add_action('campaigns_sync_event', function() {
    require_once(CAMPAIGNS_BASE_DIR . 'lib/sync.php');
    sync_campaigns();
});

add_action('init', function() {
    require_once(CAMPAIGNS_BASE_DIR . 'lib/campaign.php');
    Campaign::create_post_type();
});

function campaigns_activation_hook() {
    require_once(CAMPAIGNS_BASE_DIR . 'lib/sync.php');
    setup_sync();
}

function campaigns_deactivation_hook() {
    require_once(CAMPAIGNS_BASE_DIR . 'lib/sync.php');
    remove_sync();
}

function campaigns_admin_menu() {
    add_menu_page('Campaigns Plugin Settings', 'Campaigns Settings', 'administrator', __FILE__, 'campaigns_settings_page');

    add_action('admin_init', 'campaigns_admin_init');
}

function campaigns_admin_init() {
    register_setting('campaigns-settings', 'speakout_url');
    register_setting('campaigns-settings', 'csl_url');
    register_setting('campaigns-settings', 'acf_post_type');
}

function campaigns_settings_page() {
?>
    <div class="wrap">
      <h1>Campaigns Settings Page</h1>
      <form method="post" action="options.php">
        <?php settings_fields( 'campaigns-settings' ); ?>
        <?php do_settings_sections( 'campaigns-settings' ); ?>

        <table class="form-table">
            <tr valign="top">
            <th scope="row">Speakout URL</th>
            <td><input type='text' name='speakout_url' value="<?php echo esc_attr(get_option('speakout_url')) ?>" style="width: 60%;"></td>
            </tr>
        </table>

        <table class="form-table">
            <tr valign="top">
            <th scope="row">CSL URL</th>
            <td><input type='text' name='csl_url' value="<?php echo esc_attr(get_option('csl_url')) ?>" style="width: 60%;"></td>
            </tr>
        </table>

        <table class="form-table">
            <tr valign="top">
            <th scope="row">ACF Post Type: (Leave blank to not use ACF)</th>
            <td><input type='text' name='acf_post_type' value="<?php echo esc_attr(get_option('acf_post_type')) ?>" style="width: 60%;"></td>
            </tr>
        </table>
        <p>Note: if using ACF, ensure your chosen post type includes fields named external_id,url,source,image,actions and max_actions.</p>

        <?php submit_button(); ?>
      </form>
    </div>
<?php
}
?>
