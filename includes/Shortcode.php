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
        $id_user = get_current_user_id();

        if ( $id_user ){
            $db = new Database();

            wp_enqueue_style('event-style');
            wp_enqueue_script('event-script');

            wp_localize_script('event-script',
                                'dcms_uaccount',
                                [ 'ajaxurl'=>admin_url('admin-ajax.php'),
                                'naccount' => wp_create_nonce('ajax-nonce-account')]);

            $data = $db->show_user_details($id_user);
            $text_fields = Helper::get_account_fields();
            $editable_fields = Helper::get_editable_fields();

            ob_start();
                include_once DCMS_EVENT_PATH.'views/details-users-account.php';
                $html_code = ob_get_contents();
            ob_end_clean();

            return $html_code;
        }
    }

    // Function to show user sidebar
    public function show_user_sidebar($atts, $content){
        $id_user = get_current_user_id();

        if ( $id_user ){
            $db = new Database();

            wp_enqueue_style('event-style');
            wp_enqueue_script('event-script');

            $user = $db->show_user_sidebar($id_user);

            if ( $user ):
                $email  = Helper::search_field_in_meta($user, 'email');
                $name   = Helper::search_field_in_meta($user, 'name') . ' ' . Helper::search_field_in_meta($user, 'lastname');
                $number = Helper::search_field_in_meta($user, 'number');
            endif;

            $content = $content??'';
            $email   = $email??'';
            $name    = $name??'';
            $number  = $number??'';

            ob_start();
                include_once DCMS_EVENT_PATH.'views/user-sidebar.php';
                $html_code = ob_get_contents();
            ob_end_clean();

            return $html_code;
        }
    }

    // Function to show liste events in the front-end
    public function show_list_events(){
        $id_user = get_current_user_id();

        if ( $id_user ){
            $db = new Database();

            wp_enqueue_style('event-style');
            wp_enqueue_script('event-script');

            // Ajax event
            wp_localize_script('event-script',
                                'dcms_uevents',
                                [ 'ajaxurl'=>admin_url('admin-ajax.php'),
                                'join' => __('Unirse al Evento', 'dcms-events-users'),
                                'nojoin' => __('Inscrito al Evento', 'dcms-events-users'),
                                'nevent' => wp_create_nonce('ajax-nonce-event')]);

            // Ajax children
            wp_localize_script('event-script',
                                'dcms_echildren',
                                [ 'ajaxurl'=>admin_url('admin-ajax.php'),
                                'nchildren' => wp_create_nonce('ajax-nonce-event-children')]);


            $events = $db->get_events_for_user($id_user);

            ob_start();
                include_once DCMS_EVENT_PATH.'views/list-events-user.php';
                $html_code = ob_get_contents();
            ob_end_clean();

            return $html_code;
        }
    }
}