<?php

namespace dcms\event\backend;

// Create Metabox for dates event in backend
class Metabox{

    public function __construct(){
		add_action('add_meta_boxes', [$this, 'create_metabox']);
		add_action('save_post', [$this, 'save_metabox_data']);
    }

	// Create metaboxes for dates, fase 1 and fase 2 dates
	public function create_metabox(){

		add_meta_box( 'dcms_fase_metaboxes',
						__('Fases Evento', 'dcms-events-users'),
						[$this, 'show_metabox'],
						DCMS_EVENT_CPT,
						'side',
						'low');
	}

    // Show metabox
	public function show_metabox( $post ){
		$post_id = $post->ID;
		$enable_fases = get_post_meta($post_id, DCMS_ENABLE_FASES, true );
		$fase1 = get_post_meta($post_id, DCMS_FASE_1, true );
		$fase2 = get_post_meta($post_id, DCMS_FASE_2, true );

        include_once ('views/metabox.php');
	}

	// Save data metabox
	public function save_metabox_data( $post_id ){
		$enable_fases = isset($_POST[DCMS_ENABLE_FASES])?1:0;
		update_post_meta($post_id, DCMS_ENABLE_FASES, $enable_fases );

		if ( $enable_fases ){
			$fase1 =$_POST[DCMS_FASE_1]??null;
			$fase2 =$_POST[DCMS_FASE_2]??null;

			update_post_meta($post_id, DCMS_FASE_1, $fase1);
			update_post_meta($post_id, DCMS_FASE_2, $fase2);
		}
	}


}