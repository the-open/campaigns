<?php

class Campaign {

    static public function add_or_update($campaign_data) {
        $posts = new WP_Query([
            'post_type' => 'campaign',
            'meta_query' => [
                'relation' => 'AND',
                [
                    'key' => 'external_id',
                    'value' => $campaign_data["external_id"],
                    'compare' => '='
                ],
                [
                    'key' => 'source',
                    'value' => $campaign_data["source"],
                    'compare' => '='
                ]
            ]
        ]);

        $post_content = (empty($campaign_data["description"])) ? "" : $campaign_data["description"];

        $post_args = [
            'post_type' => 'campaign',
            'post_title' => $campaign_data["name"],
            'post_content' => $post_content,
            'post_status' => 'publish',
            'post_date' => $campaign_data["created_at"],
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'meta_input' => [
                'external_id' => $campaign_data["external_id"],
                'url' => $campaign_data["url"],
                'source' => $campaign_data["source"],
                'image' => $campaign_data["image"],
                'actions' => $campaign_data["actions"],
                'max_actions' => $campaign_data["max_actions"]
            ]
        ];

        if ( $posts->have_posts() ) {
            // Avoid overriding admin edits to publish date or status
            unset($post_args['post_date'], $post_args['post_status']);
            $posts->the_post();
            wp_update_post(array_merge([
                'ID' => get_the_ID()
            ], $post_args));
        } else {
            wp_insert_post($post_args);
        }

        wp_reset_postdata();
    }

    static public function create_post_type() {
        register_post_type(
            'campaign',
            [
                'labels' => [
                    'name' => __('Campaigns'),
                    'singular_name' => __('Campaign'),
                    'add_new' => _x('Add New', 'campaign'),
                    'add_new_item' => __('Add New Campaign'),
                    'edit_item' => __('Edit Campaign'),
                    'new_item' => __('New Campaign'),
                    'view_item' => __('View Campaign'),
                    'search_items' => __('Search Campaigns'),
                    'not_found' =>  __('Nothing found'),
                    'not_found_in_trash' => __('Nothing found in Trash')
                ],
                'description' => 'Campaigns',
                'public' => true,
                'show_in_menu' => true,
                'menu_position' => 20,
                'has_archive' => true,
                'supports' => [
                    'title',
                    'editor',
                    'thumbnail',
                    'custom-fields'
                ]
            ]
        );
    }

}

?>