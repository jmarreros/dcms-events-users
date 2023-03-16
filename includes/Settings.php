<?php

namespace dcms\event\includes;

/**
 * Class for creating the settings email new users and change seats
 */
class Settings{

    public function __construct(){
        add_action('admin_init', [$this, 'init_configuration']);
    }

    // Register sections and fields
    public function init_configuration(){
        register_setting('dcms_events_options_bd', 'dcms_events_options' );

        $this->fields_email_config();
    }

    // Fields email configuration
    private function fields_email_config(){

		// General
        add_settings_section('dcms_general_section',
                        __('Configuración correos', 'dcms-events-users'),
                                [$this,'dcms_section_cb'],
                                'dcms_events_general_fields' );

        add_settings_field('dcms_sender_email',
                            __('Correo Emisor', 'dcms-events-users'),
                            [$this, 'dcms_section_input_cb'],
                            'dcms_events_general_fields',
                            'dcms_general_section',
                            [
                                'dcms_option' => 'dcms_events_options',
                                'label_for' => 'dcms_sender_email',
                                'required' => true
                            ]
        );

        add_settings_field('dcms_sender_name',
                            __('Nombre emisor', 'dcms-events-users'),
                            [$this, 'dcms_section_input_cb'],
                            'dcms_events_general_fields',
                            'dcms_general_section',
                            [
                              'dcms_option' => 'dcms_events_options',
                              'label_for' => 'dcms_sender_name',
                              'required' => true
                            ]
        );

		// Inscription
	    add_settings_section('dcms_inscription_section',
		    __('Plantilla correo inscripción', 'dcms-events-users'),
		    [$this,'dcms_section_cb'],
		    'dcms_events_inscription_fields' );


	    add_settings_field('dcms_subject_email_inscription',
                            __('Asunto correo', 'dcms-events-users'),
                            [$this, 'dcms_section_input_cb'],
                            'dcms_events_inscription_fields',
                            'dcms_inscription_section',
                            [
                              'dcms_option' => 'dcms_events_options',
                              'label_for' => 'dcms_subject_email_inscription',
                              'required' => true
                            ]
        );

        add_settings_field('dcms_text_email_inscription',
                            __('Texto correo', 'dcms-events-users'),
                            [$this, 'dcms_section_textarea_field'],
                            'dcms_events_inscription_fields',
                            'dcms_inscription_section',
                            [
                             'dcms_option' => 'dcms_events_options',
                             'label_for' => 'dcms_text_email_inscription',
                             'description' => __('Puedes usar las siguientes variables que se pueden reemplazar:
                             %name% (nombre de usuario),
                             %event_title% (título del evento),
                             %event_extracto% (extracto del evento),
                             %convivientes% (lista de convivientes)
                             ', 'dcms-events-users')
                            ]
        );


	    // Selection
	    add_settings_section('dcms_selection_section',
		    __('Plantilla correo selección inscritos', 'dcms-events-users'),
		    [$this,'dcms_section_cb'],
		    'dcms_events_selection_fields' );


	    add_settings_field('dcms_subject_email_selection',
		    __('Asunto correo', 'dcms-events-users'),
		    [$this, 'dcms_section_input_cb'],
		    'dcms_events_selection_fields',
		    'dcms_selection_section',
		    [
			    'dcms_option' => 'dcms_events_options',
			    'label_for' => 'dcms_subject_email_selection',
			    'required' => true
		    ]
	    );

	    add_settings_field('dcms_text_email_selection',
		    __('Texto correo', 'dcms-events-users'),
		    [$this, 'dcms_section_textarea_field'],
		    'dcms_events_selection_fields',
		    'dcms_selection_section',
		    [
			    'dcms_option' => 'dcms_events_options',
			    'label_for' => 'dcms_text_email_selection',
			    'description' => __('Puedes usar las siguientes variables que se pueden reemplazar:
                             %name% (nombre de usuario),
                             %event_title% (título del evento),
                             %event_extracto% (extracto del evento),
                             %convivientes% (lista de convivientes),
                             %params_integration% (Parámetros de integración con WooCommerce, debe ir como parte de la url que tiene el shortcode).
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
        $desc = $args['description'] ?? '';

        $options = get_option( $dcms_option );
        $val = $options[ $id ] ?? '';

        printf("<input id='%s' name='%s[%s]' class='regular-text' type='text' value='%s' %s %s>",
                $id, $dcms_option, $id, $val, $req, $class);

        if ( $desc ) printf("<p class='description'>%s</p> ", $desc);

    }


    public function dcms_section_textarea_field( $args ){
        $dcms_option = $args['dcms_option'];
        $id = $args['label_for'];
        $desc = $args['description'] ?? '';

        $options = get_option( $dcms_option );
        $val = $options[$id];
        printf("<textarea id='%s' name='%s[%s]' rows='5' cols='80' >%s</textarea><p class='description'>%s</p>", $id, $dcms_option, $id, $val, $desc);
	}

}
