<?php

namespace dcms\event\backend\includes\single;

use dcms\event\helpers\Helper;
use dcms\event\includes\Database;

// For filters in single backend cpt
class Filter{
    public function __construct(){
        add_action('wp_ajax_dcms_ajax_filter',[$this, 'filter_data']);
    }

    public function filter_data():void{

        // Get filters
        $numbers = array_map( 'intval', $_POST['numbers'] );
        $abonado_types = isset($_POST['abonado_types']) ? Helper::array_to_str_quotes($_POST['abonado_types']) : null;
        $socio_types = isset($_POST['socio_types']) ? Helper::array_to_str_quotes($_POST['socio_types']) : null;
		$exclude_observation_person = isset($_POST['exclude_observation_person']) ? Helper::array_to_str_quotes($_POST['exclude_observation_person']) : null;

        $db = new Database();
        $items = $db->filter_query_params($numbers, $abonado_types, $socio_types, $exclude_observation_person);

        // Send data
	    echo json_encode($items);
		wp_die();
    }
}

