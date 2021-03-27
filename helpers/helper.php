<?php

namespace dcms\event\helpers;

// Custom post type class
class Helper{

    public static function get_abonado_type(){
        $abonado_type = [
            'ADULTO' => 'Adulto',
            'JUBILADO' => 'Jubilado',
            'DISCAPACITADO' => 'Discapacitado'
        ];
        return $abonado_type;
    }

    public static function get_socio_type(){
        $socio_type = [
            'OPCION A' => 'A',
            'OPCION B' => 'B',
            'OPCION C' => 'C',
            'OPCION D' => 'D',
            'OPCION E' => 'E'
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
            DCMS_EVENT_COUNT_META => 'Eventos'
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