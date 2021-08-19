<?php

namespace dcms\event\backend;

use dcms\event\helpers\Helper;
use dcms\event\includes\Database;

class Report{

    public function __construct(){

    }


    // Get aviable events
    public function get_aviable_events(){
        $db = new Database();
        return $db->get_avaiable_events();
    }

     // Security, verify nonce
    private function validate_nonce(){
        if ( ! wp_verify_nonce( $_POST['nonce'], 'ajax-nonce-account' ) ) {
            $res = [
                'status' => 0,
                'message' => '✋ Error nonce validation!!'
            ];
            echo json_encode($res);
            wp_die();
        }
    }

}



