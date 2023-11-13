<?php

namespace dcms\event\helpers;

// Custom post type class
class Helper {

	public static function get_abonado_type(): array {
		return [
			'ADULTO'         => 'ADULTO',
			'ADULTO.'        => 'ADULTO.',
			'DISCAPACITADO'  => 'DISCAPACITADO',
			'DISCAPACITADO.' => 'DISCAPACITADO.',
			'JUNIOR'         => 'JUNIOR',
			'JUNIOR.'        => 'JUNIOR.',
			'JUVENIL'        => 'JUVENIL',
			'JUVENIL.'       => 'JUVENIL.',
			'SUB-26'         => 'SUB-26',
			'SUB-26.'        => 'SUB-26.',
			'JUBILADO'       => 'JUBILADO',
			'YOGURÍN'        => 'YOGURÍN',
		];
	}

	public static function get_socio_type(): array {
		return [
			'OPCION A' => 'A',
			'OPCION B' => 'B',
			'OPCION C' => 'C',
			'OPCION D' => 'D',
			'OPCION E' => 'E'
		];
	}

	// Get fields to export
	public static function get_fields_inscribed_export(): array {
		return [
			'identify'     => 'Identificativo', // Login column
			'pin'          => 'PIN', // Password Column
			'number'       => 'Numero',
			'reference'    => 'Referencia',
			'nif'          => 'N.I.F.',
			'name'         => 'Nombre',
			'lastname'     => 'Apellidos',
			'birth'        => 'Fecha Nacimiento',
			'sub_type'     => 'Tipo de Abono',
			'address'      => 'Domicilio Completo',
			'postal_code'  => 'Código Postal',
			'local'        => 'Localidad',
			'email'        => 'E-MAIL',
			'phone'        => 'Teléfono',
			'mobile'       => 'Teléfono Móvil',
			'soc_type'     => 'Tipo de Socio',
			'observation7' => 'Observa 7',
			'observation5' => 'Observa 5',
			'sub_permit'   => 'Permiso Abono',
			'children'     => 'Cant Convivientes',
			'parent'       => 'Inscrito por'
		];
	}

	// Get fields to export
	public static function get_fields_selected_export(): array {
		$arr             = self::get_fields_inscribed_export();
		$arr['selected'] = 'Seleccionado';
		$arr['id_order'] = '# de Pedido';

		return $arr;
	}


	//Style for headers to export
	public static function get_style_header_cells(): array {
		return [
			'font' => [
				'bold' => true,
			],
			'fill' => [
				'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'argb' => 'FFFFFE55',
				],
			],
		];
	}

	// Get fields for the filter event
	public static function get_filter_fields(): array {
		return [
			'number'       => 'Número',
			'name'         => 'Nombre',
			'lastname'     => 'Apellidos',
			'sub_type'     => 'Tipo de Abono',
			'soc_type'     => 'Tipo de Socio',
			'observation7' => 'Observacion7',
			// DCMS_EVENT_COUNT_META => 'Eventos',
		];
	}

	// Get fields for account
	public static function get_account_fields(): array {
		return [
			'identify'    => 'Identificativo',
			'pin'         => 'PIN',
			'reference'   => 'Referencia',
			'nif'         => 'NIF',
			'birth'       => 'Fecha Nacimiento',
			'sub_type'    => 'Tipo de Abono',
			// 'soc_type'  => 'Tipo de socio',
			'address'     => 'Domicilio completo',
			'postal_code' => 'Código Postal',
			'local'       => 'Localidad',
			'email'       => 'E-Mail',
			'phone'       => 'Teléfono',
			'mobile'      => 'Móvil',
		];
	}

	// Editable fields, and type of file
	public static function get_editable_fields(): array {
		return [
			'address'     => 'text',
			'postal_code' => 'text',
			'local'       => 'text',
			'email'       => 'email',
			'phone'       => 'number',
			'mobile'      => 'number'
		];
	}

	// Fields to show in inscribed screen
	public static function get_inscribed_user_fields(): array {
		return [
			'identify'  => 'Identificativo', // Login column
			'pin'       => 'PIN', // Password Column
			'number'    => 'Numero',
			'reference' => 'Referencia',
			'nif'       => 'N.I.F.',
			'name'      => 'Nombre',
			'lastname'  => 'Apellidos',
			'email'     => 'E-MAIL',
			'children'  => 'Cant Convivientes',
			'parent'    => 'Inscrito por'
		];
	}


	// Get fields for sidebar
	public static function get_sidebar_fields_keys(): string {
		$arr = [
			'email'    => 'email',
			'number'   => 'Número',
			'name'     => 'Nombre',
			'lastname' => 'Apellidos'
		];

		return '"' . implode( '","', array_keys( $arr ) ) . '"';
	}


	// Search in array of objects given the meta_key value
	public static function search_field_in_meta( $arr, $search ) {
		$index = array_search( $search, array_column( $arr, 'meta_key' ) );
		if ( $index > - 1 ) {
			return $arr[ $index ]->meta_value;
		}

		return '';
	}


	// Order array by column
	public static function order_array_column( &$arr ) {
		usort( $arr, function ( $a, $b ) {
			return intval( $a['number'] ) > intval( $b['number'] );
		} );
	}

	// Aux function for the sql query
	public static function get_account_fields_keys(): string {
		return '"' . implode( '","', array_keys( self::get_account_fields() ) ) . '"';
	}

	// Aux to convert array to str with quotes
	public static function array_to_str_quotes( $arr ): string {
		return '"' . implode( '","', $arr ) . '"';
	}

	// For validating nonce
	public static function validate_nonce( $nonce_post, $nonce_action ) {
		if ( ! wp_verify_nonce( $nonce_post, $nonce_action ) ) {
			$res = [
				'status'  => 0,
				'message' => '✋ Error nonce validation!!'
			];
			wp_send_json( $res );
		}
	}

	// Custom sporting nonce
	public static function set_sporting_nonce( $id_user, $id_event ): string {
		return md5( $id_user . $id_event . 'sporting_nonce' );
	}

	// Validate custom sporting nonce
	public static function validate_sporting_nonce( $id_user, $id_event, $id_nonce ): bool {
		return md5( $id_user . $id_event . 'sporting_nonce' ) === $id_nonce;
	}


	// Aux - Validate if the rows affected are > 0
	public static function validate_updated( $result ) {
		if ( ! $result ) {
			$res = [
				'status'  => 0,
				'message' => '✋ No se puede actualizar su participación en el evento!'
			];
			echo json_encode( $res );
			wp_die();
		}
	}

	// Aux - Validate if the rows affected are > 0 for adding children
	public static function validate_add_children( $result ) {
		if ( ! $result ) {
			$res = [
				'status'  => 0,
				'message' => '✋ No fue posible agregar algunos convivientes! - Refresca tu navegador'
			];
			echo json_encode( $res );
			wp_die();
		}
	}

	// Aux - Validate a date with today
	public static function is_greater_than_today( $custom_date ) : bool{
		if ( is_null( $custom_date ) ) {
			return true;
		}

		$today        = date( "Y-m-d H:i:s" );
		$compare_date = date( 'Y-m-d H:i:s', strtotime( $custom_date ) );

		return $today < $compare_date;
	}

}