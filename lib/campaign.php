<?php

class Campaign {

    static public function getAll() {
        global $wpdb;

        return $wpdb->get_results(
            "SELECT * from " . CAMPAIGNS_TABLE_NAME .
            " ORDER BY id DESC LIMIT 10");
    }

    static public function add($campaign_data) {
        global $wpdb;

        $wpdb->insert(
            CAMPAIGNS_TABLE_NAME,
            $campaign_data,
            [
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%d'
            ]
        );
    }

}

?>