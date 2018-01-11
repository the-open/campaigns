<?php

/**
 * @package Campaigns
 * @version 0.1
 */

class Speakout {

    public static function sync() {
        $speakout_url = get_option('speakout_url');
        $speakout_json_url = $speakout_url . 'featured.json';

        $request = wp_remote_get( $speakout_json_url );

        if( is_wp_error($request) ) {
            error_log("ERROR FETCHING JSON FOR SPEAKOUT");
            return false;
        }

        $body = wp_remote_retrieve_body( $request );
        $campaigns = json_decode($body);

        if(!empty($campaigns)) {
            require_once(CAMPAIGNS_BASE_DIR . 'lib/campaign.php');

            $date_time_format = 'Y-m-d\TH:i:s.u\Z';
            usort($campaigns, function($campaign1, $campaign2) {
                return strtotime($campaign1->{'created '}) - strtotime($campaign2->{'created '});
            });
            foreach($campaigns as $campaign) {
                $campaign_data = [
                    "external_id" => $campaign->slug,
                    "created_at" => $campaign->{'created '},
                    "name" => $campaign->name,
                    "description" => $campaign->intro,
                    "url" => $speakout_url . "campaigns/" . $campaign->slug,
                    "source" => "speakout",
                    "image" => $campaign->image,
                    "actions" => $campaign->actions,
                    "max_actions" => 0
                ];

                Campaign::add($campaign_data);
            }
        }
    }

}