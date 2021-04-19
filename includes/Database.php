<?php

namespace dcms\event\includes;

use dcms\event\helpers\Helper;

class Database{
    private $wpdb;
    private $table_name;
    private $user_meta;
    private $post_event;


    public function __construct(){
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->table_name = $this->wpdb->prefix . 'dcms_event_users';
        $this->user_meta = $this->wpdb->prefix.'usermeta';
        $this->post_event = $this->wpdb->prefix.'posts';

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
                    `joined` tinyint(1) DEFAULT 0,
                    `joined_date` datetime DEFAULT NULL,
                    PRIMARY KEY (`id`)
            )";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    // Select saved users event to export
    public function select_users_event_export($id_post, $only_joined){
        $fields_to_show = Helper::array_to_str_quotes(array_keys(Helper::get_fields_export()));
        return $this->select_users_event($id_post, $fields_to_show, $only_joined);
    }

    // Select saved users in a post event
    public function select_users_event($id_post, $fields_to_show, $only_joined = 0){

        $sql = "SELECT user_id, meta_key, meta_value, joined FROM {$this->table_name} eu
                INNER JOIN {$this->user_meta} um ON eu.id_user = um.user_id
                WHERE id_post = {$id_post} AND meta_key in ( {$fields_to_show} )";

        if ( $only_joined ) $sql .= " AND joined = 1";

        $sql .= " ORDER BY user_id";

        return $this->wpdb->get_results( $sql );
    }

    // Delete users from a post event before insert
    public function remove_before_insert($post_id){
        // Delete all but not users joined
        $sql = "DELETE FROM {$this->table_name}
                WHERE id_post = {$post_id} AND joined != 1";

        return $this->wpdb->query($sql);
    }

    // Delete specific users for an event
    public function remove_users_event($post_id, $ids_user){

        // Remove user event, delete specific users
        $str_ids_user = '"' . implode('","',  $ids_user ) . '"';
        $sql = "DELETE FROM {$this->table_name}
                WHERE id_post = {$post_id} AND id_user IN ($str_ids_user)";
        $res =  $this->wpdb->query($sql);

        // Reset count meta
        if ( $res ){
            foreach ($ids_user as $id_user) {
                $this->update_count_user_meta($id_user, true);
            }
        }

        return $res;
    }

    // Insert users event
    public function save_users_event($ids_user, $post_id){

        // All users joined for that $post_id
        $sql = "SELECT id_user FROM {$this->table_name}
                    WHERE id_post = {$post_id} AND joined = 1";

        $joined =  $this->wpdb->get_results($sql, OBJECT_K); // keys have the id_user


        // Buil SQL insert
        $sql_insert = "INSERT INTO {$this->table_name} (id_user, id_post) VALUES ";
        $sql_values = "";

        foreach ($ids_user as $id_user) {
            $id_user = intval($id_user);
            if ( $id_user > 0 ){
                // Validate, insert only users not joined
                if ( ! array_key_exists( $id_user, $joined ) ){
                    $sql_values .= "( {$id_user} , {$post_id} ),";
                }
            }
        }

        if ( ! empty($sql_values) ){
            $sql = $sql_insert . substr($sql_values, 0, -1);

            return $this->wpdb->query($sql);
        }

        return false;
    }


    // Get all events avaliable for a specific user
    public function get_events_for_user($id_user){

        $sql = "SELECT eu.id_post, eu.joined, eu.joined_date, p.post_title, p.post_content
                FROM {$this->table_name} eu
                INNER JOIN {$this->post_event} p ON p.ID =  eu.id_post
                WHERE eu.id_user = {$id_user} AND  p.post_status = 'publish'";

        return $this->wpdb->get_results( $sql );
    }

    // Save Join/unjoin user to an event
    public function save_join_user_to_event($joined, $id_post, $id_user){
        $sql = "UPDATE {$this->table_name}
                SET joined = {$joined}, joined_date = NOW()
                WHERE id_post = {$id_post} AND id_user = {$id_user}";

        return $this->wpdb->query($sql);
    }

    // Increment/decrement events per user in usermeta
    public function update_count_user_meta($id_user, $is_remove = false){
        // Count elements in event_user table
        $sql = "SELECT COUNT(id)
                FROM {$this->table_name}
                WHERE id_user = {$id_user} AND joined = 1";

        $count = $this->wpdb->get_var($sql);

        update_user_meta($id_user, DCMS_EVENT_COUNT_META, $count);
        // set observation7 meta_user to 1
        if ( ! $is_remove ){
            update_user_meta($id_user, 'observation7', 1);
        } else {
            update_user_meta($id_user, 'observation7', 0);
        }
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


    // User sidebar
    // =============

    // To show user details in the sidebar
    public function show_user_sidebar( $user_id){
        $fields = Helper::get_sidebar_fields_keys();

        $sql = "SELECT * FROM wp_usermeta where user_id = {$user_id} AND meta_key IN ( {$fields} )";
        return $this->wpdb->get_results( $sql );
    }

}
