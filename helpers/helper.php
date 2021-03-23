<?php

namespace dcms\event\helpers;

// Custom post type class
class Helper{

    public static function get_abonado_type(){
        $abonado_type = [
            'adulto' => 'Adulto',
            'jubilado' => 'Jubilado',
            'discapacitado' => 'Discapacitado'
        ];
        return $abonado_type;
    }

    public static function get_socio_type(){
        $socio_type = [
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
            'd' => 'D',
            'e' => 'E'
        ];
        return $socio_type;
    }
}