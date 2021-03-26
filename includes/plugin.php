<?php

namespace dcms\event\includes;

use dcms\event\includes\Database;

class Plugin{

    public function __construct(){
        register_activation_hook( DCMS_EVENT_BASE_NAME, [ $this, 'dcms_activation_plugin'] );
        register_deactivation_hook( DCMS_EVENT_BASE_NAME, [ $this, 'dcms_deactivation_plugin'] );
    }

    // Activate plugin - create options and database table
    public function dcms_activation_plugin(){
        // Create table
        $db = new Database();
        $db->create_table();
    }

    // Deactivate plugin
    public function dcms_deactivation_plugin(){

    }

}
