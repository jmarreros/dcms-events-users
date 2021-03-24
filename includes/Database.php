<?php

namespace dcms\event\includes;

use dcms\event\helpers\Helper;

class Database{
    private $wpdb;
    private $table_name;

    public function __construct(){
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->table_name = $this->wpdb->prefix . 'dcms_event_users';
    }


    // To show user details in account
    public function show_user_details( $user_id){
        $fields = Helper::get_account_fields_keys();

        $sql = "SELECT * FROM wp_usermeta where user_id = {$user_id} AND meta_key IN ( {$fields} )";
        return $this->wpdb->get_results( $sql );
    }




}