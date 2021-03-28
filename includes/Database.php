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

    // User Filters
    // =============

    // Filter usermeta, filter for the poup
    public function filter_query_params( $numbers, $abonado_types, $socio_types ){

        $sql = "";
        $select     = "SELECT user_id FROM {$this->user_meta} WHERE ";
        $select_in  = "SELECT user_id FROM {$this->user_meta} WHERE user_id IN ";

        // number filter
        if ( isset($numbers) && array_sum($numbers) > 0 ){
            $sql = "SELECT user_id FROM {$this->user_meta} WHERE meta_key = 'number' ";

            if ( isset($numbers[0])  && $numbers[0] > 0 ){
                $sql = $sql." AND CAST(meta_value AS UNSIGNED) >= {$numbers[0]}";
            }
            if ( isset($numbers[1]) && $numbers[1] > 0){
                $sql = $sql." AND CAST(meta_value AS UNSIGNED) <= {$numbers[1]}";
            }
        }

        // abonados type
        if ( ! empty($abonado_types) ){
            if ( ! empty ($sql) ){
                $sql = $select_in . "({$sql})
                                    AND meta_key = 'sub_type'
                                    AND meta_value IN ({$abonado_types})";
            } else {
                $sql = $select . "meta_key = 'sub_type'
                                  AND meta_value IN ({$abonado_types})";
            }
        }

        // Socios type
        if ( ! empty($socio_types) ){
            if ( ! empty ($sql) ){
                $sql = $select_in . "({$sql})
                                    AND meta_key = 'soc_type'
                                    AND meta_value IN ({$socio_types})";
            } else {
                $sql = $select . "meta_key = 'soc_type'
                                  AND meta_value IN ({$socio_types})";
            }
        }

        // Final query, only with some fields
        $fields_filter = Helper::array_to_str_quotes(array_keys(Helper::get_filter_fields()));

        if ( empty($sql) ) $sql = '""';
        $sql = "SELECT user_id, meta_key, meta_value FROM {$this->user_meta} WHERE user_id IN ({$sql})
                                AND meta_key IN ({$fields_filter}) AND meta_value<>'' ORDER BY user_id";

        return $this->wpdb->get_results( $sql );
    }


    // User Events
    // ============

    // Init activation create table
    public function create_table(){

        $sql = " CREATE TABLE IF NOT EXISTS {$this->table_name} (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `id_user` bigint(20) unsigned DEFAULT NULL,
                    `id_post` bigint(20) unsigned DEFAULT NULL,
                    `date` datetime DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
            )";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    // Select saved user event to export
    public function select_user_event_export(){
        $fields_to_show = Helper::array_to_str_quotes(array_keys(Helper::get_fields_export()));
        return $this->select_user_event($fields_to_show);
    }

    // Select saved user event
    public function select_user_event($fields_to_show = false){

        if ( ! $fields_to_show ) {
            $fields_to_show = Helper::array_to_str_quotes(array_keys(Helper::get_filter_fields()));
        }

        $sql = "SELECT user_id, meta_key, meta_value FROM {$this->table_name} eu
                INNER JOIN {$this->user_meta} um ON eu.id_user = um.user_id
                WHERE meta_key in ( {$fields_to_show} )
                ORDER BY user_id";

        return $this->wpdb->get_results( $sql );
    }

    // Delete users from event
    public function remove_users_event($post_id){
        $sql = "DELETE FROM {$this->table_name} WHERE id_post = {$post_id}";
        return $this->wpdb->query($sql);
    }

    // Insert users event
    public function save_users_event($ids_user, $post_id){
        $sql_insert = "INSERT INTO {$this->table_name} (id_user, id_post) VALUES ";
        $sql_values = "";

        foreach ($ids_user as $id_user) {
            if ( intval($id_user) > 0 ){
                $sql_values .= "( {$id_user} , {$post_id} ),";
            }
        }

        if ( ! empty($sql_values) ){
            $sql = $sql_insert . substr($sql_values, 0, -1);

            return $this->wpdb->query($sql);
        }

        return false;
    }


    // user Account
    // =============

    // To show user details in account
    public function show_user_details( $user_id){
        $fields = Helper::get_account_fields_keys();

        $sql = "SELECT * FROM wp_usermeta where user_id = {$user_id} AND meta_key IN ( {$fields} )";
        return $this->wpdb->get_results( $sql );
    }

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




// $abonado_types = Helper::array_to_str_quotes($abonado_types);
// $socio_types = Helper::array_to_str_quotes($socio_types);

        // $numbers = [1,100];
        // $abonado_types = ['ADULTO', 'JUBILADO'];
        // $socio_types = ['OPCION A','OPCION C'];
        // $count_events = 1;

                // count events
        // if ( $count_events > 0 ){
        //     $sql = $select_in . "({$sql})
        //                             AND meta_key = '".DCMS_EVENT_COUNT_META."'
        //                             AND CAST(meta_value AS UNSIGNED) >= {$count_events}";
        // }

