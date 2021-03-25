<?php

namespace dcms\event\includes;

use dcms\event\helpers\Helper;
use dcms\event\includes\Database;

class Account{

    public function __construct(){
        add_action('wp_ajax_dcms_ajax_save_account',[ $this, 'save_account_details' ]);
    }

    public function save_account_details(){
         // Validate nonce
         $this->validate_nonce();

         // Get current user data from database
         $current_user = wp_get_current_user();
         $id_user = $current_user->ID;
         $email_user = $current_user->user_email;

         // Pass Post data to $fields var
         $fields = [];
         $editable_fields = Helper::get_editable_fields();
         foreach ($editable_fields as $key => $value) {
            $fields[$key] = sanitize_text_field($_POST[$key]);
         }

         // Update Email
         $email = strtolower($fields['email']);
         $db = new Database();
         if ( $email !=  $email_user ){
            $this->validate_email($email);
            $res = $db->update_email_user( $email, $id_user );
            $this->validate_update_email($res);
         }

         // Update fields user meta
         $db->udpate_fields_meta( $fields, $id_user );

         // update pint sent, to block changes in email when import xls
         if ( defined('DCMS_PIN_SENT') ){
            update_user_meta($id_user, DCMS_PIN_SENT, true);
         }

         // If all is ok
         $res = [
             'status' => 1,
             'message' => "✅ Los datos se guardaron correctamente",
         ];
         echo json_encode($res);
         wp_die();
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

    // Validate email
    private function validate_email($email){
        if ( !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $res = [
                'status' => 0,
                'message' => '✉️ Error ingresa un correo válido'
            ];
            echo json_encode($res);
            wp_die();
        }
    }

    // Validate update email
    private function validate_update_email($bol){
        if ( ! $bol ) {
            $res = [
                'status' => 0,
                'message' => '✉️ Error al actualizar el correo, posiblemente el correo ya se esta usando'
            ];
            echo json_encode($res);
            wp_die();
        }
    }

}



