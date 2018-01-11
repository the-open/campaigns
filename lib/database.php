<?php

/**
 * @package Campaigns
 * @version 0.1
 */

function setup_database_table() {
    global $wpdb;

    $table_name = CAMPAIGNS_TABLE_NAME;
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        external_id tinytext NOT NULL,
        created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        name tinytext NOT NULL,
        description mediumtext,
        url tinytext NOT NULL,
        source tinytext NOT NULL,
        image tinytext,
        actions mediumint(9),
        max_actions mediumint(9),
        PRIMARY KEY (id)
    ) $charset_collate; ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    update_site_option('campaigns_db_version', '2');
}

function remove_database_table() {
    global $wpdb;

    $wpdb->query("DROP TABLE IF EXISTS " . CAMPAIGNS_TABLE_NAME);

    delete_site_option('campaigns_db_version');
}

?>