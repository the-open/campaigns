<?php

/**
 * @package Campaigns
 * @version 0.1
 */

class CSL {

  public static function sync() {
    $csl_url = get_option('csl_url');
    $csl_json_url = $csl_url . '/petitions/featured.json';

    $request = wp_remote_get( $csl_json_url );

    if( is_wp_error($request) ) {
      error_log("ERROR FETCHING JSON FOR CSL");
      return false;
    }

    $body = wp_remote_retrieve_body( $request );
    $featured = json_decode($body);

    $total_pages = (int) $featured->meta->total_pages;

    error_log("TOTAL PAGES ARE " . $total_pages);

    for($i = 1; $i <= $total_pages; $i++) {
      $json_url = $csl_json_url . '?page=' . $i;
      $request = wp_remote_get( $json_url );

      if( is_wp_error($request) ) {
        error_log("ERROR FETCHING JSON FOR CSL");
        return false;
      }

      $body = wp_remote_retrieve_body( $request );
      $campaigns = json_decode($body)->data;

      if(!empty($campaigns)) {
        require_once(CAMPAIGNS_BASE_DIR . 'lib/campaign.php');

        foreach($campaigns as $campaign) {
          $campaign_data = [
            "external_id" => $campaign->slug,
            "created_at" => $campaign->created_at,
            "name" => $campaign->title,
            "description" => $campaign->what,
            "url" => $campaign->url,
            "source" => "CSL",
            "image" => $campaign->image_url,
            "actions" => $campaign->signature_count,
            "max_actions" => $campaign->goal
          ];
          Campaign::add_or_update($campaign_data);
        }
      }
    }
  }

}
