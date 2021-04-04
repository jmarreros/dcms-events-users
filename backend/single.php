<?php

namespace dcms\event\backend;

use dcms\event\includes\Database;
use dcms\event\helpers\Helper;

// Custom post type class
class Single{

    public function __construct(){
        add_action('edit_form_advanced', [$this, 'add_filter_area'], 10, 1);
        add_action('save_post_'.DCMS_EVENT_CPT, [$this, 'save_list_filter'], 10, 3);
    }

    // Show filter area and users saved for event
    public function add_filter_area( $post ){
        $screen = get_current_screen();
        $id_post = $post->ID;
        $status_post = $post->post_status;
        $items = [];

        if( $screen->post_type == DCMS_EVENT_CPT ) {

            wp_enqueue_style('admin-event-style');
            wp_enqueue_script('admin-event-script');
            wp_localize_script('admin-event-script','dcms_vars',['ajaxurl'=>admin_url('admin-ajax.php')]);

            if ( $status_post != 'auto-draft' ){
                $db = new Database();
                $items = $db->select_users_event($id_post);
            }

            $fields = Helper::get_filter_fields();
            $data = Helper::transform_columns_arr($items);
            Helper::order_array_column($data); // Order by number
            $count = count($data);

            include_once ('views/single-list-filter.php');
        }
    }

    // Save filter list
    public function save_list_filter( $post_id, $post, $update ){
        $ids_user = [];

        if ( isset($_POST['id_user_event']) && $_POST['id_user_event'] != ''  ){
            $ids_user = explode( ',', $_POST['id_user_event'] );
        }

        if ( is_array($ids_user) && count($ids_user) > 0 ){
            $db = new Database();

            $db->remove_users_event($post_id); // Remove only not joined users
            $res = $db->save_users_event($ids_user, $post_id);

            if ( ! $res ) error_log( 'Error to insert users in event' );
        }

    }
}

