<?php

namespace dcms\event\includes;

use dcms\event\includes\Database;
use dcms\event\helpers\Helper;

// Event class
class Event{
    public function __construct(){
        // Add user event
        add_action('wp_ajax_dcms_ajax_update_join',[ $this, 'join_user_event' ]);

        //Children
        add_action('wp_ajax_dcms_ajax_validate_children',[ $this, 'validate_identify_children' ]);
        add_action('wp_ajax_dcms_ajax_add_children',[ $this, 'add_children_event' ]);
        add_action('wp_ajax_dcms_ajax_remove_child',[ $this, 'remove_child_event' ]);

    }

    // Update the participation of the Individual user in an event
    public function join_user_event(){
        // Validate nonce
        $this->validate_nonce('ajax-nonce-event');

        // Event data
        $id_post = intval($_POST['id_post']);
        $event_title = get_the_title($id_post);
        $event_excerpt = get_the_excerpt($id_post);

        //User data
        $obj_user = wp_get_current_user();
        $id_user = $obj_user->ID;
        $name = $obj_user->display_name;
        $email = $obj_user->user_email;

        $identify_user = 0; // no necesary identify parent for individual inscription

        $joined = 1; // New condition, only allow joined

        $db = new Database();
        $result = $db->save_join_user_to_event($id_post, $id_user, $identify_user);

        // Validate if updated rows > 0
        $this->validate_updated($result);

        //Send email user
        $this->send_email_join_event($name, $email, $event_title, $event_excerpt);

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


    // Verify Identify and PIN
    public function validate_identify_children(){

        // Validate nonce
        $this->validate_nonce('ajax-nonce-event-children');

        $id_post    = intval($_POST['id_post']);
        $identify   = $_POST['identify']??null;
        $pin        = $_POST['pin']??null;
        $id_user    = get_current_user_id();


        // Validate identify and pin
        $db = new Database();
        $result = $db->find_user_identify_pin($identify, $pin);
        if ( ! count($result) || $result[0]['count'] != 2 ){
            $res = [
                'status' => 0,
                'message' => "Identificador o PIN no válido",
            ];

            echo json_encode($res);
            wp_die();
        }

        // Id user children
        $children_id = $result[0]['user_id']??0;

        // Validate if $identify user is yet in the event or if has been assigned
        $message = '';
        $joined = $db->search_user_in_event($children_id, $id_post);
        if ( $joined == 1 ) $message = "El conviviente ya participa en el evento, seleccione otro";
        if ( is_null($joined) ) $message = "El conviviente no ha sido asignado a este evento, seleccione otro";

        if ( $message != '' ){
            $res = [
                'status' => 0,
                'message' => $message ,
            ];

            echo json_encode($res);
            wp_die();
        }

        // Return values
        $children_meta = $db->get_user_meta($children_id);

        $children_name      = Helper::search_field_in_meta($children_meta, 'name');
        $children_lastname  = Helper::search_field_in_meta($children_meta, 'lastname');
        $children_identify  = Helper::search_field_in_meta($children_meta, 'identify');

        // If all is ok
        $res = [
            'status' => 1,
            'name' => $children_name . ' ' . $children_lastname,
            'id_user' => $children_id,
            'identify' => $children_identify,
            'message' => "Conviviente encontrado"
        ];

        echo json_encode($res);
        wp_die();
    }

    // Remove specif child from event
    public function remove_child_event(){
        // Validate nonce
        $this->validate_nonce('ajax-nonce-event-children');

        $id_user    = intval($_POST['id_user']);
        $id_post    = intval($_POST['id_event']);

        // Validate identify and pin
        $db = new Database();

        $result = $db->remove_child_event($id_user, $id_post);

         // If all is ok
         $res = [
            'status' => 1,
            'message' => "Se eliminó correctamente"
        ];

        echo json_encode($res);
        wp_die();
    }

    // Add children
    public function add_children_event(){
        // Validate nonce
        $this->validate_nonce('ajax-nonce-event-children');

        // Event data
        $id_post = intval($_POST['id_post']);
        $event_title = get_the_title($id_post);
        $event_excerpt = get_the_excerpt($id_post);

        //User data
        $obj_user = wp_get_current_user();
        $id_user = $obj_user->ID;
        $name = $obj_user->display_name;
        $email = $obj_user->user_email;
        $identify_user = get_user_meta($id_user, 'identify', true);

        // Children data
        $ids_children = $_POST['children_data'];
        $count_children = count($ids_children);
        $children_data = [];

        $result = 0;
        $db = new Database();
        if ( $ids_children && $count_children <= DCMS_MAX_CHILDREN ){

            foreach($ids_children as $id_child ){
                $result = $db->save_children($id_child, $id_post, $id_user, $identify_user);

                //Children data
                $user_data = get_user_meta($id_child);
                $child_name = $user_data['name'][0]. ' ' . $user_data['lastname'][0];
                $child_identify = $user_data['identify'][0];
                $children_data[$child_identify] = $child_name;
            }

            // Update user inscription
            $db->save_join_user_to_event($id_post, $id_user, $identify_user);

            //Send email user
            $this->send_email_join_event($name, $email, $event_title, $event_excerpt, $children_data);
        }

        $this->validate_add_children($result);

        // If all is ok
        $res = [
            'status' => 1,
            'message' => "➜ Los convivientes se agregaron correctamente"
        ];

        echo json_encode($res);
        wp_die();

    }

    // Send mail join event
    private function send_email_join_event( $name, $email, $event_title, $event_excerpt ='', $convivientes = []){
        $options = get_option( 'dcms_events_options' );

        add_filter( 'wp_mail_from', function(){
            $options = get_option( 'dcms_events_options' );
            return $options['dcms_sender_email'];
        });
        add_filter( 'wp_mail_from_name', function(){
            $options = get_option( 'dcms_events_options' );
            return $options['dcms_sender_name'];
        });

        $headers = ['Content-Type: text/html; charset=UTF-8'];
        $subject = $options['dcms_subject_email'];
        $body    = $options['dcms_text_email'];
        $body = str_replace( '%name%', $name, $body );
        $body = str_replace( '%event_title%', $event_title, $body );
        $body = str_replace( '%event_extracto%', $event_excerpt, $body );

        $str = '';
        if ($convivientes){
            $str = "Convivientes: <br>";
            $str .= "<ul>";
            foreach ($convivientes as $key => $value){
                $str .= "<li> ID: " . $key . " - " . $value . "</li>";
            }
            $str .= "</ul>";
        }
        $body = str_replace( '%convivientes%', $str, $body );


        return wp_mail( $email, $subject, $body, $headers );
    }

    // Aux - Security, verify nonce
    private function validate_nonce( $nonce_name ){
        if ( ! wp_verify_nonce( $_POST['nonce'], $nonce_name ) ) {
            $res = [
                'status' => 0,
                'message' => '✋ Error nonce validation!!'
            ];
            echo json_encode($res);
            wp_die();
        }
    }

    // Aux - Validate if the rows affected are > 0
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

    // Aux - Validate if the rows affected are > 0 for adding children
    private function validate_add_children($result){
        if ( ! $result ) {
            $res = [
                'status' => 0,
                'message' => '✋ No fue posible agregar convivientes!'
            ];
            echo json_encode($res);
            wp_die();
        }
    }
}


// $joined ^= 1; // toggle

//$joined = intval($_POST['joined']);

// $children = intval($_POST['children']);
// if ( $children > DCMS_MAX_CHILDREN ) $children = 0;

//$identify_user  = get_user_meta($id_user, 'identify', true);
