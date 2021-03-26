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

    // Get fields for the filter event
    public static function get_filter_fields(){
        return [
            'number'    => 'Número',
            'name'      => 'Nombre',
            'lastname'  => 'Apellidos',
            'sub_type'  => 'Tipo de Abono',
            'soc_type'  => 'Tipo de Socio',
            'dcms-count-event' => 'Eventos'
        ];
    }

    // Get fields for account
    public static function get_account_fields(){
        return [
            'identify'  => 'Identificativo',
            'pin'       => 'PIN',
            'number'    => 'Número',
            'reference' => 'Referencia',
            'nif'       => 'NIF',
            'name'      => 'Nombre',
            'lastname'    => 'Apellidos',
            'birth'     => 'Fecha Nacimiento',
            'sub_type'  => 'Tipo de Abono',
            'address'   => 'Domicilio completo',
            'postal_code'   => 'Código Postal',
            'local'     => 'Localidad',
            'email'     => 'E-Mail',
            'phone'     => 'Teléfono',
            'mobile'    => 'Móvil'
        ];
    }

    // Editable fields, and type of file
    public static function get_editable_fields(){
        return [
            'address'   => 'text',
            'postal_code'   => 'text',
            'local'     => 'text',
            'email'     => 'email',
            'phone'     => 'number',
            'mobile'    => 'number'
        ];
    }

    // Aux function for the sql query
    public static function get_account_fields_keys(){
        return '"' . implode('","', array_keys(self::get_account_fields())) . '"';
    }

    // Aux to convert array to str with quotes
    public static function array_to_str_quotes($arr){
        return '"' . implode('","', $arr) . '"';
    }

}