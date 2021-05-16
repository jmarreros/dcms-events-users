<?php

namespace dcms\event\includes;

use dcms\event\includes\Database;

// Event class
class Event{
    public function __construct(){
        add_action('wp_ajax_dcms_ajax_update_join',[ $this, 'join_user_event' ]);
    }

    // Update the participation of the user in an event
    public function join_user_event(){
        // Validate nonce
        $this->validate_nonce();

        $id_post = intval($_POST['id_post']);
        $id_user = get_current_user_id();

        //$joined = intval($_POST['joined']);
        $joined = 1; // New condition, only allow joined


        $children = intval($_POST['children']);
        if ( $children > DCMS_MAX_CHILDREN ) $children = 0;

        $db = new Database();
        $result = $db->save_join_user_to_event($children, $id_post, $id_user);

        // Validate if updated rows > 0
        $this->validate_updated($result);

        // Update user meta
        $db->update_count_user_meta($id_user);

        // If all is ok
        $res = [
            'status' => 1,
            'joined' => $joined,
            'message' => "✅ Te has inscrito correctamente al Evento",
        ];

        echo json_encode($res);
        wp_die();
    }


    // Security, verify nonce
    private function validate_nonce(){
        if ( ! wp_verify_nonce( $_POST['nonce'], 'ajax-nonce-event' ) ) {
            $res = [
                'status' => 0,
                'message' => '✋ Error nonce validation!!'
            ];
            echo json_encode($res);
            wp_die();
        }
    }

    // Validate if the rows affected are > 0
    private function validate_updated($result){
        if ( ! $result ) {
            $res = [
                'status' => 0,
                'message' => '✋ No se puede actualizar su participación en el evento!'
            ];
            echo json_encode($res);
            wp_die();
        }
    }
}


// $joined ^= 1; // toggle