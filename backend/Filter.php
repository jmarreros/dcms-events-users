<?php

namespace dcms\event\backend;

// For filters in single backend cpt
class Filter{
    public function __construct(){
        add_action('wp_ajax_dcms_ajax_filter',[$this, 'filter_data']);
    }

    public function filter_data(){
        error_log(print_r('Filter data',true));

        $data = $_POST['data'];
        echo $data.' + adicional 🚀';
        wp_die();
    }
}