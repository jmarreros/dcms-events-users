<?php

namespace dcms\event\includes;

/**
 * Class for creating the settings email new users and change seats
 */
class Settings{

    public function __construct(){
        add_action('admin_init', [$this, 'init_configuration']);
    }

    // Register seccions and fields
    public function init_configuration(){
        register_setting('dcms_events_options_bd', 'dcms_events_options' );

        $this->fields_new_user();
    }

    // New user fields
    private function fields_new_user(){

        add_settings_section('dcms_email_section',
                        __('Texto por defecto en correo', 'dcms-events-users'),
                                [$this,'dcms_section_cb'],
                                'dcms_events_sfields' );

        add_settings_field('dcms_sender_email',
                            __('Correo Emisor', 'dcms-events-users'),
                            [$this, 'dcms_section_input_cb'],
                            'dcms_events_sfields',
                            'dcms_email_section',
                            [
                                'dcms_option' => 'dcms_events_options',
                                'label_for' => 'dcms_sender_email',
                                'required' => true
                            ]
        );

        add_settings_field('dcms_sender_name',
                            __('Nombre emisor', 'dcms-events-users'),
                            [$this, 'dcms_section_input_cb'],
                            'dcms_events_sfields',
                            'dcms_email_section',
                            [
                              'dcms_option' => 'dcms_events_options',
                              'label_for' => 'dcms_sender_name',
                              'required' => true
                            ]
        );

        add_settings_field('dcms_subject_email',
                            __('Asunto correo', 'dcms-events-users'),
                            [$this, 'dcms_section_input_cb'],
                            'dcms_events_sfields',
                            'dcms_email_section',
                            [
                              'dcms_option' => 'dcms_events_options',
                              'label_for' => 'dcms_subject_email',
                              'required' => true
                            ]
        );

        add_settings_field('dcms_text_email',
                            __('Texto correo', 'dcms-events-users'),
                            [$this, 'dcms_section_textarea_field'],
                            'dcms_events_sfields',
                            'dcms_email_section',
                            [
                             'dcms_option' => 'dcms_events_options',
                             'label_for' => 'dcms_text_email',
                             'description' => __('Puedes usar las siguientes variables que se pueden reemplazar:
                             %name% (nombre de usuario),
                             %event_title% (título del evento),
                             %event_extracto% (extracto del evento),
                             %convivientes% (lista de convivientes)
                             ', 'dcms-events-users')
                            ]
        );
    }

    // Métodos auxiliares genéricos

    // Callback section
    public function dcms_section_cb(){
		echo '<hr/>';
	}

    // Callback input field callback
    public function dcms_section_input_cb($args){
        $dcms_option = $args['dcms_option'];
        $id = $args['label_for'];
        $req = isset($args['required']) ? 'required' : '';
        $class = isset($args['class']) ? "class='".$args['class']."'" : '';
        $desc = isset($args['description']) ? $args['description'] : '';

        $options = get_option( $dcms_option );
        $val = isset( $options[$id] ) ? $options[$id] : '';

        printf("<input id='%s' name='%s[%s]' class='regular-text' type='text' value='%s' %s %s>",
                $id, $dcms_option, $id, $val, $req, $class);

        if ( $desc ) printf("<p class='description'>%s</p> ", $desc);

    }


    public function dcms_section_textarea_field( $args ){
        $dcms_option = $args['dcms_option'];
        $id = $args['label_for'];
        $desc = isset($args['description']) ? $args['description'] : '';

        $options = get_option( $dcms_option );
        $val = $options[$id];
        printf("<textarea id='%s' name='%s[%s]' rows='5' cols='80' >%s</textarea><p class='description'>%s</p>", $id, $dcms_option, $id, $val, $desc);
	}

}
