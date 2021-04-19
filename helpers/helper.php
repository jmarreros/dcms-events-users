<?php

namespace dcms\event\helpers;

// Custom post type class
class Helper{

    public static function get_abonado_type(){
        $abonado_type = [
            'ADULTO'        => 'ADULTO',
            'ADULTO.'       => 'ADULTO.',
            'DISCAPACITADO' => 'DISCAPACITADO',
            'DISCAPACITADO.'=> 'DISCAPACITADO.',
            'JUNIOR'        => 'JUNIOR',
            'JUNIOR.'       => 'JUNIOR.',
            'JUVENIL'       => 'JUVENIL',
            'JUVENIL.'      => 'JUVENIL.',
            'SUB-26'        => 'SUB-26',
            'SUB-26.'       => 'SUB-26.',
            'JUBILADO'      => 'JUBILADO',
            'YOGURÍN'       => 'YOGURÍN',
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

    // Get fields to export
    public static function get_fields_export(){
        return [
            'identify'  => 'Identificativo', // Login column
            'pin'       => 'PIN', // Password Column
            'number'    => 'Numero',
            'reference' => 'Referencia',
            'nif'       => 'N.I.F.',
            'name'      => 'Nombre',
            'lastname'    => 'Apellidos',
            'birth'     => 'Fecha Nacimiento',
            'sub_type'  => 'Tipo de Abono',
            'address'   => 'Domicilio Completo',
            'postal_code'   => 'Código Postal',
            'local'     => 'Localidad',
            'email'     => 'E-MAIL',
            'phone'     => 'Teléfono',
            'mobile'    => 'Teléfono Móvil',
            'soc_type'  => 'Tipo de Socio',
            'observation7'   => 'Observa 7',
            'observation5'   => 'Observa 5',
            'sub_permit'=> 'Permiso Abono'
        ];
    }

    //Style for headers to export
    public static function get_style_header_cells(){
        return [
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFFFFE55',
                ],
            ],
        ];
    }

    // Get fields for the filter event
    public static function get_filter_fields(){
        return [
            'number'    => 'Número',
            'name'      => 'Nombre',
            'lastname'  => 'Apellidos',
            'sub_type'  => 'Tipo de Abono',
            'soc_type'  => 'Tipo de Socio',
            'observation7' => 'Observacion7',
            // DCMS_EVENT_COUNT_META => 'Eventos',
        ];
    }

    // Get fields for account
    public static function get_account_fields(){
        return [
            'identify'  => 'Identificativo',
            'pin'       => 'PIN',
            'reference' => 'Referencia',
            'nif'       => 'NIF',
            'birth'     => 'Fecha Nacimiento',
            'sub_type'  => 'Tipo de Abono',
            'soc_type'  => 'Tipo de socio',
            'address'   => 'Domicilio completo',
            'postal_code'   => 'Código Postal',
            'local'     => 'Localidad',
            'email'     => 'E-Mail',
            'phone'     => 'Teléfono',
            'mobile'    => 'Móvil',
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


    // Get fields for sidebar
    public static function get_sidebar_fields_keys(){
        $arr = [
                'email'     => 'email',
                'number'    => 'Número',
                'name'      => 'Nombre',
                'lastname'    => 'Apellidos'
            ];

        return '"' . implode('","', array_keys( $arr )) . '"';
    }


    // Search in array of objects given the meta_key value
    public static function search_field_in_meta($arr, $search){
        $index = array_search($search, array_column($arr, 'meta_key'));
        return $arr[$index]->meta_value;
    }


    // Transform results cols to rows in arrays from $items object
    public static function transform_columns_arr($items){
         $id = 0;
         $arr = [];
         $result = [];
         foreach ($items as $item) {

             if ( $item->user_id != $id && $id != 0 ){
                 $result[] = $arr;
                 $arr = [];
             }

             if ( ! $arr ) {
                 $arr['user_id']= $item->user_id;
                 $arr['joined'] = $item->joined??0;
             }

             $arr[$item->meta_key] = $item->meta_value;

             $id = $item->user_id;
         }
         if ( $arr ) $result[] = $arr;

         return $result;
    }

    // Order array by column
    public static function order_array_column(&$arr){
        usort($arr, function($a, $b){
                    return intval($a['number']) > intval($b['number']);
                });
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