<?php

/**
 * @package Campaigns
 * @version 0.1
 */

class Because {

    public static function sync() {
        $because_url = get_option('because_url');
        $because_json_url = $because_url . '/campaigns/v1/campaigns?per_page=100';
        $page = 0;

        $request = wp_remote_get( $because_json_url . '&page=' . $page, array(
            'headers' => array( 
                'x-request-id' => 'af7c1fe6-d669-414e-b066-e9733f0de7a8', // dummy value to keep the API happy
            ) 
        ));


        if( is_wp_error($request) ) {
            error_log("ERROR FETCHING JSON FOR BECAUSE");
            return false;
        }

        $body = wp_remote_retrieve_body( $request );
        $campaigns = json_decode($body)->campaigns;

        while(!empty($campaigns)) {
            require_once(CAMPAIGNS_BASE_DIR . 'lib/campaign.php');
            foreach($campaigns as $campaign) {
                $max_actions = (is_null($campaign->maxActions) || $campaign->maxActions === "0") ? Because::campaign_target($campaign->actions) : $campaign->maxActions;
                $campaign_data = [
                    "external_id" => $campaign->id,
                    "created_at" => $campaign->createdAt,
                    "name" => $campaign->title,
                    "description" => $campaign->description,
                    "url" => $campaign->url,
                    "source" => "because",
                    "image" => $campaign->imageUrl,
                    "actions" => $campaign->actions,
                    "max_actions" => $max_actions,
                ];
                
                Campaign::add_or_update($campaign_data);
            }

            $page++;

            $request = wp_remote_get( $because_json_url . '&page=' . $page, array(
            'headers' => array( 
                'x-request-id' => 'af7c1fe6-d669-414e-b066-e9733f0de7a8', // dummy value to keep the API happy
            ) 
            ));


            if( is_wp_error($request) ) {
                error_log("ERROR FETCHING JSON FOR BECAUSE");
                return false;
            }

            $body = wp_remote_retrieve_body( $request );
            $campaigns = json_decode($body)->campaigns;
        }
    }

    /**
     *  This function was copied from Speakout to give a bogus campaign target as Because does not support this feature yet
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