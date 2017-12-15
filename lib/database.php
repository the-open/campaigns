<?php

/**
 * @package Campaigns
 * @version 0.1
 */

function setup_database_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'campaigns';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        external_id int NOT NULL,
        created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        name tinytext NOT NULL,
        description mediumtext,
        url tinytext NOT NULL,
        source tinytext NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate; ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    update_site_option('campaigns_db_version', '1');
}

function remove_database_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'campaigns';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");

    delete_site_option('campaigns_db_version');
}

?>