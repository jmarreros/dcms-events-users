<?php

namespace dcms\event\includes;

class Database{
    private $wpdb;
    private $table_name;

    public function __construct(){
        global $wpdb;

        $this->wpdb = $wpdb;
        $this->table_name = $this->wpdb->prefix . 'dcms_event_users';
    }


}