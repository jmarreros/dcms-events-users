<?php

namespace dcms\event\includes;

use dcms\event\includes\Database;
use dcms\event\helpers\Helper;

// Class for grouping shortcodes functionality
class Shortcode{

    public function __construct(){
        add_action( 'init', [$this, 'create_shortcodes'] );
    }

    // Function to add shortcodes
    public function create_shortcodes(){
        add_shortcode(DCMS_EVENT_ACCOUNT, [ $this, 'show_user_account' ]);
        add_shortcode(DCMS_EVENT_SIDEBAR, [ $this, 'show_user_sidebar' ]);
        add_shortcode(DCMS_EVENT_LIST, [ $this, 'show_list_events' ]);
    }

    // Function show user account in the front-end
    public function show_user_account(){
        $db = new Database();

        wp_enqueue_style('event-style');
        wp_enqueue_script('event-script');

        wp_localize_script('event-script',
                            'dcms_vars',
                            [ 'ajaxurl'=>admin_url('admin-ajax.php'),
                            'naccount' => wp_create_nonce('ajax-nonce-account')]);

        $id_user = get_current_user_id();
        $data = $db->show_user_details($id_user);
        $text_fields = Helper::get_account_fields();
        $editable_fields = Helper::get_editable_fields();

        include_once DCMS_EVENT_PATH.'views/details-users-account.php';
    }

    // Function to show user sidebar
    public function show_user_sidebar($atts, $content){
        $db = new Database();

        wp_enqueue_style('event-style');
        wp_enqueue_script('event-script');

        $id_user = get_current_user_id();
        $user = $db->show_user_details($id_user);

        $email  = Helper::search_field_in_meta($user, 'email');
        $name   = Helper::search_field_in_meta($user, 'name') . ' ' . Helper::search_field_in_meta($user, 'lastname');
        $number = Helper::search_field_in_meta($user, 'number');
        $content = $content??'';

        include_once DCMS_EVENT_PATH.'views/user-sidebar.php';
    }

    // Function to show liste events in the front-end
    public function show_list_events(){
        $db = new Database();

        wp_enqueue_style('event-style');
        wp_enqueue_script('event-script');

        wp_localize_script('event-script',
                            'dcms_vars',
                            [ 'ajaxurl'=>admin_url('admin-ajax.php'),
                            'nevent' => wp_create_nonce('ajax-nonce-event')]);

        $id_user = get_current_user_id();
        $events = $db->get_events_for_user($id_user);

        include_once DCMS_EVENT_PATH.'views/list-events-user.php';
    }
}