<?php

	namespace dcms\event\includes;

use dcms\event\helpers\Helper;

class Database {
	private $wpdb;
	private string $event_users;
	private string $user_meta;
	private string $post_event;
	private string $view_users;
	private string $post_product;
	private string $post_meta;

	public function __construct() {
		global $wpdb;

		$this->wpdb         = $wpdb;
		$this->event_users  = $this->wpdb->prefix . 'dcms_event_users';
		$this->view_users   = $this->wpdb->prefix . 'dcms_view_users';
		$this->user_meta    = $this->wpdb->prefix . 'usermeta';
		$this->post_event   = $this->wpdb->prefix . 'posts';
		$this->post_product = $this->wpdb->prefix . 'posts';
		$this->post_meta    = $this->wpdb->prefix . 'postmeta';
	}

	// User Filters
	// =============

	// Filter usermeta, filter for the poup
	public function filter_query_params( $numbers, $abonado_types, $socio_types ) {
		$sql = "SELECT `user_id`, `number`, `name`, `lastname`, `sub_type`, `soc_type`, `observation7`,
                0 as `joined`, 0 as `children`, 0 as `parent`
                FROM {$this->view_users} WHERE identify <> ''";

		// Number filter
		if ( isset( $numbers ) && array_sum( $numbers ) > 0 ) {
			if ( isset( $numbers[0] ) && $numbers[0] > 0 ) {
				$sql .= " AND CAST(`number` AS UNSIGNED) >= {$numbers[0]}";
			}
			if ( isset( $numbers[1] ) && $numbers[1] > 0 ) {
				$sql .= " AND CAST(`number` AS UNSIGNED) <= {$numbers[1]}";
			}
		}

		// abonados type
		if ( ! empty( $abonado_types ) ) {
			$sql .= " AND sub_type IN ({$abonado_types})";
		}

		// socio type
		if ( ! empty( $socio_types ) ) {
			$sql .= " AND soc_type IN ({$socio_types})";
		}

		$sql .= " ORDER BY CAST(`number` AS UNSIGNED)";

		return $this->wpdb->get_results( $sql, OBJECT );
	}


	// User Events
	// ============

	// Init activation create table
	public function create_table() {

		$sql = " CREATE TABLE IF NOT EXISTS {$this->event_users} (
                    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                    `id_user` bigint(20) unsigned DEFAULT NULL,
                    `id_post` bigint(20) unsigned DEFAULT NULL,
                    `date` datetime DEFAULT CURRENT_TIMESTAMP,
                    `maximum_date` datetime DEFAULT NULL,
                    `joined` tinyint(1) DEFAULT 0,
                    `joined_date` datetime DEFAULT NULL,
                    `children` tinyint unsigned DEFAULT 0,
                    `parent` bigint(20) unsigned DEFAULT NULL,
                    `id_parent` bigint(20) unsigned DEFAULT NULL,
                    `selected` tinyint(1) DEFAULT 0,
                    `id_order` bigint(20) unsigned DEFAULT NULL,
                    PRIMARY KEY (`id`)
            )";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	// Getting maximum date for user group
	public function group_users_by_created_date( $id_post ) {
		$sql = "SELECT DISTINCT DATE_FORMAT(`date`, '%Y-%m-%d') group_date, maximum_date  
				FROM $this->event_users WHERE id_post = $id_post
				ORDER BY group_date";

		return $this->wpdb->get_results( $sql );
	}

	public function update_maximum_date_per_group( $post_id, $groups_maximum_date ): void {
		foreach ( $groups_maximum_date as $group_id => $date ) {
			$date = is_null( $date ) ? 'NULL' : "'$date'";

			$sql = "UPDATE $this->event_users SET maximum_date = $date
					WHERE id_post = $post_id AND DATE_FORMAT(date, '%Y-%m-%d') = '$group_id'";

			$this->wpdb->query( $sql );
		}
	}

	// Select saved users event to export
	public function select_users_event_export( $id_post, $only_joined, $only_selected = false ) {
		$fields_to_show = str_replace( '"', '`', Helper::array_to_str_quotes( array_keys( Helper::get_fields_inscribed_export() ) ) );

		$sql = "SELECT vu.`user_id`,{$fields_to_show},eu.`joined`,eu.`selected`,eu.`id_order`, DATE_FORMAT(eu.`maximum_date`, '%Y-%m-%d') AS maximum_date  
                FROM $this->event_users eu
                INNER JOIN $this->view_users vu ON eu.id_user = vu.user_id
                WHERE id_post = {$id_post}";

		if ( $only_joined ) {
			$sql .= " AND joined = 1";
		}

		if ( $only_selected ) {
			$sql .= " AND selected = 1";
		}

		$sql .= " ORDER BY CAST(`number` AS UNSIGNED)";

		return $this->wpdb->get_results( $sql, ARRAY_A );
	}

	// Select inscribed users in an event
	public function select_inscribed_users_event( $id_post ) {
		return $this->select_users_event_export( $id_post, true );
	}

	// Select selected users inscribed in an event
	public function select_selected_users_event( $id_post ): array {
		return $this->select_users_event_export( $id_post, true, true );
	}

	// Select saved users in a post event
	public function select_users_event( $id_post ) {
		$sql = "SELECT `user_id`, `number`, `name`, `lastname`, `sub_type`, `soc_type`, `observation7`,
                `joined`, `children`, `parent`
                FROM $this->event_users eu
                INNER JOIN $this->view_users vu ON eu.id_user = vu.user_id
                WHERE id_post = {$id_post}
                ORDER BY CAST(`number` AS UNSIGNED)";

		return $this->wpdb->get_results( $sql, ARRAY_A );
	}

	// Delete users from a post event before insert
	public function remove_before_insert( $post_id ) {
		// Delete all but not users joined
		$sql = "DELETE FROM {$this->event_users}
                WHERE id_post = {$post_id} AND joined != 1";

		return $this->wpdb->query( $sql );
	}

	// Delete specific users for an event
	public function remove_users_event( $post_id, $ids_user ) {

		$str_ids_user = '"' . implode( '","', $ids_user ) . '"';

		// First get the id parent to recount children after delete users event
		$ids_parent = $this->get_parents_children( $post_id, $str_ids_user );

		// Remove user event, delete specific users
		$sql = "DELETE FROM {$this->event_users}
                WHERE id_post = {$post_id} AND id_user IN ($str_ids_user)";
		$res = $this->wpdb->query( $sql );

		// Reset count meta
		if ( $res ) {
			foreach ( $ids_user as $id_user ) {
				$this->update_count_user_meta( $id_user, true );
			}
		}

		// Recount children
		foreach ( $ids_parent as $id ) {
			if ( $id['id_parent'] ) {
				$id_parent = intval( $id['id_parent'] );
				if ( $id_parent > 0 ) {
					$this->recount_children( $id_parent, $post_id );
				}
			}
		}

		return $res;
	}

	// Return sisters quantity for and specific user_id and event
	private function recount_children( $id_parent, $post_id ) {
		// get id_parent
		$sql = "UPDATE {$this->event_users} eu, (
                    SELECT COUNT(id_parent) children
                    FROM {$this->event_users}
                    WHERE id_parent = $id_parent AND id_post = $post_id GROUP BY id_parent
                    ) teu
                SET eu.children = teu.children
                WHERE eu.id_user = $id_parent AND eu.id_post = $post_id";

		$result = $this->wpdb->query( $sql );

		// No hay hijos, por lo tanto actualizamos a 0
		if ( $result == 0 ) {
			$sql    = "UPDATE {$this->event_users} SET children = 0, parent = NULL
                    WHERE id_user = $id_parent AND id_post = $post_id";
			$result = $this->wpdb->query( $sql );
		}

		return $result;
	}


	// Get all the parents from as string of ids_users
	private function get_parents_children( $post_id, $str_ids_user ) {

		$sql = "SELECT DISTINCT id_parent FROM {$this->event_users}
                WHERE id_post = {$post_id} AND id_user IN ($str_ids_user)";

		return $this->wpdb->get_results( $sql, ARRAY_A );
	}

	// Insert users event
	public function save_users_event( $ids_user, $post_id ) {

		// All users joined for that $post_id
		$sql = "SELECT id_user FROM {$this->event_users}
                    WHERE id_post = {$post_id} AND joined = 1";

		$joined = $this->wpdb->get_results( $sql, OBJECT_K ); // keys have the id_user


		// Buil SQL insert
		$sql_insert = "INSERT INTO {$this->event_users} (id_user, id_post) VALUES ";
		$sql_values = "";

		foreach ( $ids_user as $id_user ) {
			$id_user = intval( $id_user );
			if ( $id_user > 0 ) {
				// Validate, insert only users not joined
				if ( ! array_key_exists( $id_user, $joined ) ) {
					$sql_values .= "( {$id_user} , {$post_id} ),";
				}
			}
		}

		if ( ! empty( $sql_values ) ) {
			$sql = $sql_insert . substr( $sql_values, 0, - 1 );

			return $this->wpdb->query( $sql );
		}

		return false;
	}


	// Get all events avaliable for a specific user
	public function get_events_for_user( $id_user ) {

		$sql = "SELECT eu.id_user, eu.id_post, eu.joined, eu.joined_date, eu.children, eu.parent, eu.id_parent, eu.maximum_date, eu.id_order, p.post_title, p.post_content
                FROM {$this->event_users} eu
                INNER JOIN {$this->post_event} p ON p.ID =  eu.id_post
                WHERE eu.id_user = {$id_user} AND  p.post_status = 'publish'";

		return $this->wpdb->get_results( $sql );
	}


	// Save Join user to an event, only allow joined
	public function save_join_user_to_event( $id_post, $id_user, $parent = 0 ) {

		if ( $parent == 0 ) {
			$sql = "UPDATE {$this->event_users}
            SET joined = 1, joined_date = NOW(), parent = NULL
            WHERE id_post = {$id_post} AND id_user = {$id_user}";
		} else {
			$sql = "UPDATE {$this->event_users}
            SET joined = 1, joined_date = NOW(), parent = {$parent}
            WHERE id_post = {$id_post} AND id_user = {$id_user}";
		}

		return $this->wpdb->query( $sql );
	}

	// Increment/decrement events per user in usermeta
	public function update_count_user_meta( $id_user, $is_remove = false ) {
		// Count elements in event_user table
		$sql = "SELECT COUNT(id)
                FROM {$this->event_users}
                WHERE id_user = {$id_user} AND joined = 1";

		$count = $this->wpdb->get_var( $sql );

		update_user_meta( $id_user, DCMS_EVENT_COUNT_META, $count );
		update_user_meta( $id_user, 'observation7', $count );

		// set observation7 meta_user to 1
		// if ( ! $is_remove ){
		//     update_user_meta($id_user, 'observation7', 1);
		// } else {
		//     update_user_meta($id_user, 'observation7', 0);
		// }
	}

	// user Account
	// =============

	// To show user details in account
	public function show_user_details( $user_id ) {
		$fields = Helper::get_account_fields_keys();

		$sql = "SELECT * FROM {$this->user_meta} where user_id = {$user_id} AND meta_key IN ( {$fields} )";

		return $this->wpdb->get_results( $sql );
	}

	// Update email user
	public function update_email_user( $email, $user_id ) {
		$res = wp_update_user( [ 'ID' => $user_id, 'user_email' => $email ] );

		if ( is_wp_error( $res ) ) {
			error_log( $res->get_error_message() );

			return false;
		}

		return $res;
	}

	// Get duplicate email validation
	public function get_duplicate_email( $email, $not_id ) {
		$sql = "SELECT user_id FROM {$this->user_meta}
                WHERE meta_key = 'email' AND meta_value = '$email' AND user_id <> $not_id";

		return $this->wpdb->get_var( $sql );
	}

	// Update fields meta user, even email in meta
	public function udpate_fields_meta( $fields, $user_id ) {
		foreach ( $fields as $key => $value ) {
			update_user_meta( $user_id, $key, $value );
		}

		return true;
	}


	// User sidebar
	// =============

	// To show user details in the sidebar
	public function show_user_sidebar( $user_id ) {
		$fields = Helper::get_sidebar_fields_keys();

		$sql = "SELECT * FROM {$this->user_meta} where user_id = {$user_id} AND meta_key IN ( {$fields} )";

		return $this->wpdb->get_results( $sql );
	}


	// Children events
	// ================

	// To validate identify and pin, not valid = 0, 2 , valid only 1 row
	public function find_user_identify_pin( $identify, $pin ) {
		$sql = "SELECT user_id, COUNT(user_id) AS count FROM {$this->user_meta}
                WHERE ( meta_key = 'identify' AND meta_value = '{$identify}' )
                        || (meta_key = 'pin' AND meta_value = '{$pin}' )
                GROUP BY user_id having COUNT(user_id)=2";

		return $this->wpdb->get_results( $sql, ARRAY_A );
	}

	// To show user data
	public function get_user_meta( $user_id ) {
		$sql = "SELECT * FROM {$this->user_meta} WHERE user_id = {$user_id} AND meta_key IN ( 'name', 'lastname', 'identify', 'number' )";

		return $this->wpdb->get_results( $sql );
	}

	// Search user in event, return joined =  1 , 0 , null, null is not assignated to the event
	public function search_user_in_event( $id_user, $id_post ) {
		$sql = "SELECT joined FROM {$this->event_users} WHERE id_user = {$id_user} AND id_post = {$id_post}";

		return $this->wpdb->get_var( $sql );
	}

	// Save children
	public function save_children( $id_children, $id_post, $parent, $id_user ) {
		// try update
		$sql = "UPDATE {$this->event_users} SET
                    joined = 1,
                    parent = {$parent},
                    id_parent = {$id_user},
                    joined_date = NOW()
                WHERE id_user = {$id_children} AND id_post = {$id_post}";

		$result = $this->wpdb->query( $sql );

		if ( $result ) {
			$this->recount_children( $id_user, $id_post );
		}

		return $result;
	}

	// Get children user for the event
	public function get_children_user( $id_user, $id_post ) {
		$sql = "SELECT eu.id_user, eu.joined, eu.selected, v.identify as `identify`, CONCAT(v.`name`, ' ' , v.`lastname`) as `name`
                FROM {$this->event_users} eu
                INNER JOIN {$this->view_users} v ON v.user_id = eu.id_user
                WHERE eu.id_post = {$id_post} AND eu.id_parent = {$id_user} AND eu.joined = 1";

		return $this->wpdb->get_results( $sql, ARRAY_A );
	}

	// Remove child from specific event
	public function remove_child_event( $id_user, $id_post ) {
		$id_parent = $this->get_id_parent_child_event( $id_post, $id_user );

		$sql = "UPDATE {$this->event_users} SET
            joined = 0,
            children = 0,
            parent = NULL,
            id_parent = NULL
        WHERE id_user = {$id_user} AND id_post = {$id_post}";

		$result = $this->wpdb->query( $sql );

		if ( $result && $id_parent ) {
			$this->recount_children( $id_parent, $id_post );
		}

		return $result;
	}

	// Get parent child
	private function get_id_parent_child_event( $id_post, $id_user ) {
		$sql = "SELECT id_parent FROM {$this->event_users}
        WHERE id_post = {$id_post} AND id_user = {$id_user}";

		return $this->wpdb->get_var( $sql );
	}


	// Inscribed Events
	// ==================

	// Get all available events
	public function get_avaiable_events() {
		$sql = "SELECT * FROM {$this->post_event}
                WHERE post_type = 'events_sporting' AND post_status in ('publish' , 'private' )
                ORDER BY post_date DESC";

		return $this->wpdb->get_results( $sql );
	}

	// Filter selected users inscribed event with identifies numbers
	public function filter_users_event_selected_identifies( $id_event, $identifies ) {
		$sql = "SELECT id, id_user, name, lastname, email, children, id_parent 
				FROM $this->event_users eu
				INNER JOIN (
					SELECT user_id FROM $this->user_meta
					WHERE meta_key = 'identify' AND meta_value IN ( " . join( ',', $identifies ) . " ) 
				) um ON eu.id_user = um.user_id
				INNER JOIN $this->view_users u ON eu.id_user = u.user_id
				WHERE eu.id_post = $id_event AND joined = 1 AND selected = 0";

		return $this->wpdb->get_results( $sql );
	}

	// Selected user for the event, $id is the identify table
	public function update_selected_event_user_by_id( $id ): int {
		$sql = "UPDATE $this->event_users SET selected = 1 WHERE id = $id";

		return $this->wpdb->query( $sql );
	}

	// Selected user for the event with user_id and event_id, also select children if exist
	public function update_selected_event_user( $event_id, $user_id ): int {
		$sql = "UPDATE $this->event_users 
				SET selected = 1 
             	WHERE id_post = $event_id AND (id_user = $user_id OR id_parent = $user_id)";

		return $this->wpdb->query( $sql );
	}

	// Get data selected user event
	public function get_selected_event_user( $id_user, $id_event ): array {
		$sql = "SELECT * FROM $this->event_users 
         		WHERE id_user = $id_user AND id_post = $id_event";

		return $this->wpdb->get_row( $sql, ARRAY_A ) ?? [];
	}

	// Get all active products to metabox event
	public function get_list_products(): array {
		$args = [
			'limit'   => - 1,
			'status'  => 'publish'
		];

		$products = wc_get_products( $args );

		$results = [];
		foreach ( $products as $product ) {
			$result['id']    = $product->get_id();
			$result['name']  = $product->get_name();
			$result['price'] = number_format( (float) $product->get_regular_price(), 2 );

			if ( $product->get_type() === 'variable' ) {
				$product_price_min = $product->get_variation_regular_price( 'min' );
				$product_price_max = $product->get_variation_regular_price( 'max' );
				$result['price']   = $product_price_min . '-' . $product_price_max;
			}
			$results[] = $result;
		}

		return $results;
	}

	// Deselecting children for the event
	public function deselect_children_event( $id_parent, $children_deselected, $id_event ) {
		// Set select = 1 for all children
		$sql = "UPDATE $this->event_users SET selected = 1
			 	WHERE id_parent = $id_parent AND id_post = $id_event";

		$this->wpdb->query( $sql );

		// Update select = 0 for children deselected
		if ( ! empty( $children_deselected ) ) {
			$sql = "UPDATE $this->event_users SET selected = 0 
             		WHERE id_user IN ( " . join( ',', $children_deselected ) . ") 
             		AND id_post = $id_event";

			$this->wpdb->query( $sql );
		}

	}

	// Get id_event associate with the product
	public function get_event_id_product( $id_product ): int {
		$sql = "SELECT pm.post_id FROM $this->post_meta pm
               INNER JOIN $this->post_event  p ON pm.post_id = p.ID
				WHERE pm.meta_key = '" . DCMS_EVENT_PRODUCT_ID . "' 
				AND pm.meta_value = $id_product
				AND p.post_status = 'publish'
				ORDER BY post_id DESC LIMIT 1";

		return intval( $this->wpdb->get_var( $sql ) );
	}

	// Update user event order
	public function update_event_user_order( $user_id, $event_id, $order_id ) {
		$sql = "UPDATE $this->event_users SET id_order = $order_id
				WHERE id_user = $user_id AND id_post = $event_id";

		return $this->wpdb->query( $sql );
	}

//	public function get_order_id( $user_id, $event_id ): int {
//		$sql = "SELECT id_order FROM $this->event_users
//				WHERE id_user = $user_id AND id_post = $event_id";
//
//		return intval( $this->wpdb->get_var( $sql ) );
//	}

	public function get_totals_group_user_type($ids):array{
		if ( empty($ids)) {
			return [];
		}

		// Get totals by sub_type and exclude observation_person = 'SOCIO DE HONOR'
		$sql = "SELECT meta_value soc_type, COUNT(meta_value) qty_type
				FROM $this->user_meta 
				WHERE
				    user_id IN (" . implode(',', $ids) . ") AND
					meta_key = 'sub_type' AND
					user_id NOT IN (
						SELECT user_id FROM $this->user_meta  
						WHERE 
							user_id IN (" . implode(',', $ids) . ") AND 
							meta_key = 'observation_person' AND 
							meta_value = 'SOCIO DE HONOR'
					)
					GROUP BY meta_value
					HAVING soc_type <> 'JUNIOR'";

		$items = $this->wpdb->get_results( $sql, ARRAY_A );

		// Format results
		$results = [];
		foreach ( $items as $item ) {
			$results[$item['soc_type']] = $item['qty_type'];
		}

		return $results;
	}

	public function get_variations_product($id_product):array{
		$product = wc_get_product($id_product);

		if ( $product->get_type() !== 'variable') {
			return [];
		}

		$variations = $product->get_available_variations();

		$results = [];
		foreach($variations as $variation){
			$name = $variation['attributes']['attribute_pa_tipo-socio'];
			$results[$name] = $variation['variation_id'];
		}

		return $results;
	}

	// User list whose send sepa file
	public function get_users_with_sepa() {
		$sql = "SELECT user_id, 
					group_concat( case when (meta_key = 'identify') then meta_value end ) AS `identify`,
					group_concat( case when (meta_key = 'pin') then meta_value end ) AS `pin`,
					group_concat( case when (meta_key = 'first_name') then meta_value end ) AS `first_name`,
					group_concat( case when (meta_key = 'lastname') then meta_value end ) AS `last_name`,
					group_concat( case when (meta_key = 'sepa_file') then meta_value end ) AS `sepa_file`,
					group_concat( case when (meta_key = 'sepa_locked') then meta_value end ) AS `sepa_locked`,
					group_concat( case when (meta_key = 'sepa_file') then CAST(SUBSTRING(meta_value,1, 10) AS UNSIGNED) end ) AS `unix_time`
				FROM $this->user_meta WHERE meta_key in ('identify','pin','first_name', 'lastname', 'sepa_file', 'sepa_locked') group by user_id 
				HAVING sepa_file IS NOT NULL
				ORDER BY `unix_time`";

		return $this->wpdb->get_results( $sql, ARRAY_A );
	}
}
