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

    // Aux function for the sql query
    public static function get_account_fields_keys(){
        return '"' . implode('","', array_keys(self::get_account_fields())) . '"';
    }


}