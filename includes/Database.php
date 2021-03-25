<?php

namespace dcms\event\includes;

use dcms\event\helpers\Helper;

class Database{
    private $wpdb;
    private $table_name;
    private $user_meta;

    public function __construct(){
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->table_name = $this->wpdb->prefix . 'dcms_event_users';
        $this->user_meta = $this->wpdb->prefix.'usermeta';
    }

    // To show user details in account
    public function show_user_details( $user_id){
        $fields = Helper::get_account_fields_keys();

        $sql = "SELECT * FROM wp_usermeta where user_id = {$user_id} AND meta_key IN ( {$fields} )";
        return $this->wpdb->get_results( $sql );
    }


    // Update user Account
    // ==================

    // Update email user
    public function update_email_user( $email, $user_id){
        $res = wp_update_user( ['ID' => $user_id, 'user_email' => $email] );

        if ( is_wp_error($res) ) {
            error_log($res->get_error_message());
            return false;
        }

        return $res;
    }

    // Get duplicate email validation
    public function get_duplicate_email( $email, $not_id ){
        $sql = "SELECT user_id FROM $this->user_meta
                WHERE meta_key = 'email' AND meta_value = '$email' AND user_id <> $not_id";

        return $this->wpdb->get_var($sql);
    }

    // Update fields meta user, even email in meta
    public function udpate_fields_meta($fields, $user_id){
        foreach ($fields as $key => $value) {
            update_user_meta($user_id, $key, $value);
        }
        return true;
    }

}