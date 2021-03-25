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
    }

    // Function show user account
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
}