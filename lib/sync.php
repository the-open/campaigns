<?php

/**
 * @package Campaigns
 * @version 0.1
 */


function setup_sync() {
    if (! wp_next_scheduled ('campaigns_sync_event')) {
        wp_schedule_event(time(), 'hourly', 'campaigns_sync_event');
    }
}

function remove_sync() {
    if ( wp_next_scheduled('campaigns_sync_event')) {
        wp_clear_scheduled_hook('campaigns_sync_event');
    }
}

function sync_campaigns() {
    if( get_option('speakout_url') ) {
        error_log('Syncing campaigns with Speakout');

        require_once(CAMPAIGNS_BASE_DIR . 'lib/adapters/speakout.php');
        Speakout::sync();
    }
}

?>