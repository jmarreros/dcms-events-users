<?php

namespace dcms\event\backend;

use dcms\event\includes\Database;
use dcms\event\helpers\Helper;

// For filters in single backend cpt
class Filter{
    public function __construct(){
        add_action('wp_ajax_dcms_ajax_filter',[$this, 'filter_data']);
    }

    public function filter_data(){

        $numbers = [1,100];
        $abonado_types = ['ADULTO', 'JUBILADO'];
        $socio_types = ['OPCION A','OPCION C'];
        $count_events = 1;

        $db = new Database();

        $abonado_types = Helper::array_to_str_quotes($abonado_types);
        $socio_types = Helper::array_to_str_quotes($socio_types);

        $result = $db->filter_query_params($numbers, $abonado_types, $socio_types);

        error_log(print_r($result, true));

        $data = $_POST['data'];
        echo $data.' + adicional 🚀';
        wp_die();
    }
}