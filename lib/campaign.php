<?php

class Campaign {

    static public function add_or_update($campaign_data) {
        global $wpdb;
        $acf_post_type = get_option('acf_post_type');
        $post_type = (!empty($acf_post_type) && post_type_exists($acf_post_type)) ? $acf_post_type : 'campaign';
        $post_id = 0;

        $posts = new WP_Query([
            'posts_per_page'    => -1,
            'post_type' => $post_type,
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
            'post_type' => $post_type,
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

        // If using ACF, skip direct editing of custom  fields here and use ACF methods later
        if ( $post_type != 'campaign' ) {
            unset($post_args['meta_input']);
        }

        if ( $posts->have_posts() ) {
            // Avoid overriding admin edits to publish date or status
            unset($post_args['post_date'], $post_args['post_status']);
            $posts->the_post();
            $post_id = get_the_ID();
            wp_update_post(array_merge([
                'ID' => $post_id
            ], $post_args));
        } else {
            $result = wp_insert_post($post_args);
            $post_id = $result;
        }

        // Use ACF methods to update campaign data
        if ( $post_type != 'campaign' ) {
            update_field('external_id', $campaign_data["external_id"], $post_id);
            update_field('url', $campaign_data["url"], $post_id);
            update_field('source', $campaign_data["source"], $post_id);
            update_field('image', $campaign_data["image"], $post_id);
            update_field('actions', $campaign_data["actions"], $post_id);
            update_field('max_actions', $campaign_data["max_actions"], $post_id);
        }

        wp_reset_postdata();
    }

    static public function create_post_type() {
        $acf_post_type = get_option('acf_post_type');
        $post_type = (!empty($acf_post_type) && post_type_exists($acf_post_type)) ? $acf_post_type : 'campaign';
        if ( $post_type == 'campaign' ) {
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
                    'show_in_rest' => true,
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

}

?>