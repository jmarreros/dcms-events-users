<?php

namespace dcms\event\includes;

use dcms\event\includes\Database;

// Custom post type class
class Cpt{

    public function __construct(){
        add_action('init', [$this, 'create_cpt_events']);

		// Deshabilitamos Gutenberg
		add_filter('use_block_editor_for_post_type', '__return_false', 5);
    }

    // Create cpt: events_sporting
    public function create_cpt_events(){
        $labels = array(
            		'name'                  => _x( 'Events Sporting', 'Post Type General Name', 'dcms-events-users' ),
            		'singular_name'         => _x( 'Event Sporting', 'Post Type Singular Name', 'dcms-events-users' ),
            		'menu_name'             => __( 'Events Sporting', 'dcms-events-users' ),
            		'name_admin_bar'        => __( 'Events Sporting', 'dcms-events-users' ),
            		'archives'              => __( 'Archive Events', 'dcms-events-users' ),
            		'attributes'            => __( 'Atributtes Events', 'dcms-events-users' ),
            		'parent_item_colon'     => __( 'Event parent:', 'dcms-events-users' ),
            		'all_items'             => __( 'All Events', 'dcms-events-users' ),
            		'add_new_item'          => __( 'Add new', 'dcms-events-users' ),
            		'add_new'               => __( 'Add Event', 'dcms-events-users' ),
            		'new_item'              => __( 'New event', 'dcms-events-users' ),
            		'edit_item'             => __( 'Edit event', 'dcms-events-users' ),
            		'update_item'           => __( 'Update event', 'dcms-events-users' ),
            		'view_item'             => __( 'Show event', 'dcms-events-users' ),
            		'view_items'            => __( 'Show events', 'dcms-events-users' ),
            		'search_items'          => __( 'Search event', 'dcms-events-users' ),
            		'not_found'             => __( 'Not found', 'dcms-events-users' ),
            		'not_found_in_trash'    => __( 'Not found in trash', 'dcms-events-users' ),
            		'featured_image'        => __( 'Featured image', 'dcms-events-users' ),
            		'set_featured_image'    => __( 'Add featured image', 'dcms-events-users' ),
            		'remove_featured_image' => __( 'Remove event', 'dcms-events-users' ),
            		'use_featured_image'    => __( 'Use as featured image', 'dcms-events-users' ),
            		'insert_into_item'      => __( 'Add event', 'dcms-events-users' ),
            		'uploaded_to_this_item' => __( 'Update event', 'dcms-events-users' ),
            		'items_list'            => __( 'List events', 'dcms-events-users' ),
            		'items_list_navigation' => __( 'Navigation events', 'dcms-events-users' ),
            		'filter_items_list'     => __( 'Filter events', 'dcms-events-users' ),
            	);
            	$rewrite = array(
            		'slug'                  => 'events',
            		'with_front'            => true,
            		'pages'                 => true,
            		'feeds'                 => true,
            	);
            	$args = array(
            		'label'                 => __( 'Events Sporting', 'dcms-events-users' ),
            		'description'           => __( 'Manage events for Sporting subscritions', 'dcms-events-users' ),
            		'labels'                => $labels,
            		'supports'              => array( 'title', 'author', 'editor' ),
            		'taxonomies'            => array(),
            		'hierarchical'          => false,
            		'public'                => true,
            		'show_ui'               => true,
            		'show_in_menu'          => true,
            		'menu_position'         => 5,
            		'menu_icon'             => 'dashicons-format-aside',
            		'show_in_admin_bar'     => true,
            		'show_in_nav_menus'     => true,
            		'can_export'            => true,
            		'has_archive'           => true,
            		'exclude_from_search'   => false,
            		'publicly_queryable'    => true,
            		'rewrite'               => $rewrite,
            		'capability_type'       => 'page',
            		'show_in_rest'          => true,
            	);
            	register_post_type( DCMS_EVENT_CPT, $args );
    }

}
