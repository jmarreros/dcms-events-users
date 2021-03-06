<?php

namespace dcms\event\includes;

use dcms\event\helpers\Helper;

class Database{
    private $wpdb;
    private $table_name;
    private $user_meta;
    private $post_event;
    private $table_users;
    private $view_users;

    public function __construct(){
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->table_name   = $this->wpdb->prefix.'dcms_event_users';
        $this->view_users   = $this->wpdb->prefix.'dcms_view_users';
        $this->user_meta    = $this->wpdb->prefix.'usermeta';
        $this->post_event   = $this->wpdb->prefix.'posts';
        $this->table_users  = $this->wpdb->prefix.'users';

    }

    // User Filters
    // =============

    // Filter usermeta, filter for the poup
    public function filter_query_params( $numbers, $abonado_types, $socio_types ){
        $sql = "SELECT `user_id`, `number`, `name`, `lastname`, `sub_type`, `soc_type`, `observation7`,
                0 as `joined`, 0 as `children`, 0 as `parent`
                FROM {$this->view_users} WHERE identify <> ''";

        // Number filter
        if ( isset($numbers) && array_sum($numbers) > 0 ){
            if ( isset($numbers[0])  && $numbers[0] > 0 ){
                $sql .= " AND CAST(`number` AS UNSIGNED) >= {$numbers[0]}";
            }
            if ( isset($numbers[1]) && $numbers[1] > 0){
                $sql .= " AND CAST(`number` AS UNSIGNED) <= {$numbers[1]}";
            }
        }

        // abonados type
        if ( ! empty($abonado_types) ){
            $sql .= " AND sub_type IN ({$abonado_types})";
        }

        // socio type
        if ( ! empty($socio_types) ){
            $sql .= " AND soc_type IN ({$socio_types})";
        }

        $sql .= " ORDER BY CAST(`number` AS UNSIGNED)";

        return $this->wpdb->get_results( $sql, OBJECT);
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
                    `children` tinyint unsigned DEFAULT 0,
                    `parent` bigint(20) unsigned DEFAULT NULL,
                    `id_parent` bigint(20) unsigned DEFAULT NULL,
                    PRIMARY KEY (`id`)
            )";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    // Select saved users event to export
    public function select_users_event_export($id_post, $only_joined){

        $fields_to_show = str_replace('"', '`', Helper::array_to_str_quotes(array_keys(Helper::get_fields_export())));

        // return $this->select_users_event($id_post, $fields_to_show, $only_joined);

        $sql = "SELECT `user_id`,{$fields_to_show},`joined`
                FROM wp_dcms_event_users eu
                INNER JOIN wp_dcms_view_users vu ON eu.id_user = vu.user_id
                WHERE id_post = {$id_post}";

        if ( $only_joined ) $sql .= " AND joined = 1";

        $sql .= " ORDER BY CAST(`number` AS UNSIGNED)";

        return $this->wpdb->get_results( $sql , ARRAY_A);
    }

    // Select saved users in a post event
    public function select_users_event($id_post, $only_joined = 0){

        $sql = "SELECT `user_id`, `number`, `name`, `lastname`, `sub_type`, `soc_type`, `observation7`,
                `joined`, `children`, `parent`
                FROM wp_dcms_event_users eu
                INNER JOIN wp_dcms_view_users vu ON eu.id_user = vu.user_id
                WHERE id_post = {$id_post}
                ORDER BY CAST(`number` AS UNSIGNED)";

        return $this->wpdb->get_results( $sql , ARRAY_A);
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

        $str_ids_user = '"' . implode('","',  $ids_user ) . '"';

        // First get the id parent to recount children after delete users event
        $ids_parent = [];
        $ids_parent = $this->get_parents_children($post_id, $str_ids_user);

        // Remove user event, delete specific users
        $sql = "DELETE FROM {$this->table_name}
                WHERE id_post = {$post_id} AND id_user IN ($str_ids_user)";
        $res =  $this->wpdb->query($sql);

        // Reset count meta
        if ( $res ){
            foreach ($ids_user as $id_user) {
                $this->update_count_user_meta($id_user, true);
            }
        }

        // Recount children
        foreach ($ids_parent as $id) {
            if ( $id['id_parent'] ){
                $id_parent = intval($id['id_parent']);
                if ( $id_parent > 0 ){
                    $this->recount_children($id_parent, $post_id);
                }
            }
        }

        return $res;
    }

    // Return sisters quantity for and specific user_id and event
    private function recount_children($id_parent, $post_id){
        // get id_parent
        $sql = "UPDATE {$this->table_name} eu, (
                    SELECT COUNT(id_parent) children
                    FROM {$this->table_name}
                    WHERE id_parent = $id_parent AND id_post = $post_id GROUP BY id_parent
                    ) teu
                SET eu.children = teu.children
                WHERE eu.id_user = $id_parent AND eu.id_post = $post_id";

        $result =  $this->wpdb->query($sql);

        // No hay hijos, por lo tanto actualizamos a 0
        if ( $result == 0){
            $sql ="UPDATE {$this->table_name} SET children = 0, parent = NULL
                    WHERE id_user = $id_parent AND id_post = $post_id";
            $result =  $this->wpdb->query($sql);
        }

        return $result;
    }


    // Get all the parents from as string of ids_users
    private function get_parents_children($post_id, $str_ids_user){

        $sql = "SELECT DISTINCT id_parent FROM {$this->table_name}
                WHERE id_post = {$post_id} AND id_user IN ($str_ids_user)";

        return $this->wpdb->get_results($sql, ARRAY_A);
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

        $sql = "SELECT eu.id_user, eu.id_post, eu.joined, eu.joined_date, eu.children, eu.parent, eu.id_parent, p.post_title, p.post_content
                FROM {$this->table_name} eu
                INNER JOIN {$this->post_event} p ON p.ID =  eu.id_post
                WHERE eu.id_user = {$id_user} AND  p.post_status = 'publish'";

        return $this->wpdb->get_results( $sql );
    }


    // Save Join user to an event, only allow joined
    public function save_join_user_to_event($id_post, $id_user, $parent = 0){

        if ( $parent == 0 ){
            $sql = "UPDATE {$this->table_name}
            SET joined = 1, joined_date = NOW(), parent = NULL
            WHERE id_post = {$id_post} AND id_user = {$id_user}";
        } else {
            $sql = "UPDATE {$this->table_name}
            SET joined = 1, joined_date = NOW(), parent = {$parent}
            WHERE id_post = {$id_post} AND id_user = {$id_user}";
        }

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
        update_user_meta($id_user, 'observation7', $count);

        // set observation7 meta_user to 1
        // if ( ! $is_remove ){
        //     update_user_meta($id_user, 'observation7', 1);
        // } else {
        //     update_user_meta($id_user, 'observation7', 0);
        // }
    }

    // user Account
    // =============

    // To show user details in account
    public function show_user_details( $user_id){
        $fields = Helper::get_account_fields_keys();

        $sql = "SELECT * FROM {$this->user_meta} where user_id = {$user_id} AND meta_key IN ( {$fields} )";
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
        $sql = "SELECT user_id FROM {$this->user_meta}
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

        $sql = "SELECT * FROM {$this->user_meta} where user_id = {$user_id} AND meta_key IN ( {$fields} )";
        return $this->wpdb->get_results( $sql );
    }


    // Children events
    // ================

    // To validate identify and pin, not valid = 0, 2 , valid only 1 row
    public function find_user_identify_pin($identify, $pin){
        $sql = "SELECT user_id, COUNT(user_id) AS count FROM {$this->user_meta}
                WHERE ( meta_key = 'identify' AND meta_value = '{$identify}' )
                        || (meta_key = 'pin' AND meta_value = '{$pin}' )
                GROUP BY user_id having COUNT(user_id)=2";

        return $this->wpdb->get_results( $sql , ARRAY_A);
    }

    // To show user data
    public function get_user_meta($user_id){
        $sql = "SELECT * FROM {$this->user_meta} WHERE user_id = {$user_id} AND meta_key IN ( 'name', 'lastname', 'identify', 'number' )";
        return $this->wpdb->get_results( $sql);
    }

    // Search user in event, return joined =  1 , 0 , null, null is not assignated to the event
    public function search_user_in_event($id_user, $id_post){
        $sql ="SELECT joined FROM {$this->table_name} WHERE id_user = {$id_user} AND id_post = {$id_post}";
        return $this->wpdb->get_var( $sql);
    }

    // Save children
    public function save_children($id_children, $id_post, $parent, $id_user){
        // try update
        $sql = "UPDATE {$this->table_name} SET
                    joined = 1,
                    parent = {$parent},
                    id_parent = {$id_user},
                    joined_date = NOW()
                WHERE id_user = {$id_children} AND id_post = {$id_post}";

        $result = $this->wpdb->query( $sql);

        $this->recount_children($id_user, $id_post);

        return $result;
    }

    // Get children user for the event
    public function get_children_user($id_user, $id_post){
        $sql = "SELECT eu.id_user, v.identify as `identify`, CONCAT(v.`name`, ' ' , v.`lastname`) as `name`
                FROM {$this->table_name} eu
                INNER JOIN {$this->view_users} v ON v.user_id = eu.id_user
                WHERE eu.id_post = {$id_post} AND eu.id_parent = {$id_user} AND eu.joined = 1";

        $result = $this->wpdb->get_results( $sql, ARRAY_A);

        return $result;
    }

    // Remove child from specific event
    public function remove_child_event($id_user, $id_post){
        $id_parent = $this->get_id_parent_child_event($id_post, $id_user);

        $sql = "UPDATE {$this->table_name} SET
            joined = 0,
            children = 0,
            parent = NULL,
            id_parent = NULL
        WHERE id_user = {$id_user} AND id_post = {$id_post}";

        $result = $this->wpdb->query( $sql);

        if ( $result && $id_parent ){
            $this->recount_children($id_parent, $id_post);
        }

        return $result;
    }

    // Get parent child
    private function get_id_parent_child_event($id_post, $id_user){
        $sql = "SELECT id_parent FROM {$this->table_name}
        WHERE id_post = {$id_post} AND id_user = {$id_user}";

        return $this->wpdb->get_var($sql);
    }


    // Report incribed Events
    // =======================

    // Get all aviable events
    public function get_avaiable_events(){
        $sql = "SELECT * FROM {$this->post_event}
                WHERE post_type = 'events_sporting' AND post_status in ('publish' , 'private' )
                ORDER BY post_date DESC";

        return $this->wpdb->get_results($sql);
    }

    // Get all incribed for an specific event

}
