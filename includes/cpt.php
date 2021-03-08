<?php

namespace dcms\event\includes;

use dcms\event\includes\Database;

// Custom post type class
class Cpt{

    public function __construct(){
        add_action('init', [$this, 'create_cpt_events']);
    }

    // Create cpt: events_sporting
    public function create_cpt_events(){
        $labels = array(
            		'name'                  => _x( 'Events Sporting', 'Post Type General Name', DCMS_EVENT_DOMAIN ),
            		'singular_name'         => _x( 'Event Sporting', 'Post Type Singular Name', DCMS_EVENT_DOMAIN ),
            		'menu_name'             => __( 'Events Sporting', DCMS_EVENT_DOMAIN ),
            		'name_admin_bar'        => __( 'Events Sporting', DCMS_EVENT_DOMAIN ),
            		'archives'              => __( 'Archive Events', DCMS_EVENT_DOMAIN ),
            		'attributes'            => __( 'Atributtes Events', DCMS_EVENT_DOMAIN ),
            		'parent_item_colon'     => __( 'Event parent:', DCMS_EVENT_DOMAIN ),
            		'all_items'             => __( 'All Events', DCMS_EVENT_DOMAIN ),
            		'add_new_item'          => __( 'Add new', DCMS_EVENT_DOMAIN ),
            		'add_new'               => __( 'Add Event', DCMS_EVENT_DOMAIN ),
            		'new_item'              => __( 'New event', DCMS_EVENT_DOMAIN ),
            		'edit_item'             => __( 'Edit event', DCMS_EVENT_DOMAIN ),
            		'update_item'           => __( 'Update event', DCMS_EVENT_DOMAIN ),
            		'view_item'             => __( 'Show event', DCMS_EVENT_DOMAIN ),
            		'view_items'            => __( 'Show events', DCMS_EVENT_DOMAIN ),
            		'search_items'          => __( 'Search event', DCMS_EVENT_DOMAIN ),
            		'not_found'             => __( 'Not found', DCMS_EVENT_DOMAIN ),
            		'not_found_in_trash'    => __( 'Not found in trash', DCMS_EVENT_DOMAIN ),
            		'featured_image'        => __( 'Featured image', DCMS_EVENT_DOMAIN ),
            		'set_featured_image'    => __( 'Add featured image', DCMS_EVENT_DOMAIN ),
            		'remove_featured_image' => __( 'Remove event', DCMS_EVENT_DOMAIN ),
            		'use_featured_image'    => __( 'Use as featured image', DCMS_EVENT_DOMAIN ),
            		'insert_into_item'      => __( 'Add event', DCMS_EVENT_DOMAIN ),
            		'uploaded_to_this_item' => __( 'Update event', DCMS_EVENT_DOMAIN ),
            		'items_list'            => __( 'List events', DCMS_EVENT_DOMAIN ),
            		'items_list_navigation' => __( 'Navigation events', DCMS_EVENT_DOMAIN ),
            		'filter_items_list'     => __( 'Filter events', DCMS_EVENT_DOMAIN ),
            	);
            	$rewrite = array(
            		'slug'                  => 'events',
            		'with_front'            => true,
            		'pages'                 => true,
            		'feeds'                 => true,
            	);
            	$args = array(
            		'label'                 => __( 'Events Sporting', DCMS_EVENT_DOMAIN ),
            		'description'           => __( 'Manage events for Sporting subscritions', DCMS_EVENT_DOMAIN ),
            		'labels'                => $labels,
            		'supports'              => array( 'title', 'author' ),
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
            	register_post_type( 'events_sporting', $args );
    }

}
