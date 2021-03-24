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

        $id_user = 315;
        $data = $db->show_user_details($id_user);
        $text_fields = Helper::get_account_fields();

        include_once DCMS_EVENT_PATH.'views/details-users-account.php';
    }
}