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
                    "max_actions" => Speakout::campaign_target($campaign->actions),
                ];
                
                Campaign::add_or_update($campaign_data);
            }
        }
    }

    /**
     *  This function was copied from Speakout to give a bogus campaign target as Speakout do not support this feature
     */
    private static function campaign_target($c){
		$n = $c*(5.0/4.0);
		$m = [2.0,2.5,2.0];
		$target = 100.0;
		$i=0;
		while ($n > $target){
			$target = $target * $m[$i%count($m)];
			$i = $i + 1;
		}

		$target = (int)$target;

		return $target;

	}

}