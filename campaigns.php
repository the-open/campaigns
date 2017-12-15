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

function campaigns_activation_hook() {
    require_once(CAMPAIGNS_BASE_DIR.'lib/database.php');
    setup_database_table();
}

function campaigns_deactivation_hook() {
    require_once(CAMPAIGNS_BASE_DIR.'lib/database.php');
    remove_database_table();
}

?>
