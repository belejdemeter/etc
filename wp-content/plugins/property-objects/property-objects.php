<?php

/*
Plugin Name: Property Objects
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0
Author: Admin
Author URI: http://URI_Of_The_Plugin_Author
License: MIT
*/

// If this file is called directly, abort.
if (!defined( 'WPINC' )) die;

// Currently plugin version.
define( 'PROPERTY_OBJECT_VERSION', '1.0.0' );


require plugin_dir_path( __FILE__ ) . 'includes/PropertyObjectPlugin.php';
require plugin_dir_path( __FILE__ ) . 'includes/CustomFields.php';
require plugin_dir_path( __FILE__ ) . 'includes/PropertyObjectWidget.php';
require plugin_dir_path( __FILE__ ) . 'includes/PropertyObjectShortcode.php';

$GLOBALS['property_object_query_filters'] = [
    #'house_number' => 'house_number',
    #'location_gps' => 'location_gps',
    'number_of_floors' => 'number_of_floors',
    'building_type' => 'building_type',
    'environmental_friendliness' => 'environmental_friendliness',

    'room_area' => 'room_area',
    'number_of_rooms' => 'number_of_rooms',
    'balcony' => 'balcony',
    'bathroom' => 'bathroom',
];

$GLOBALS['property_object_query_ordering_params'] = [
    'date' => __('Date'),
    'environmental_friendliness' => __('Environmental friendliness'),
];


/**
 * Begins execution of the plugin.
 */
function run_property_object_plugin() {
    $plugin = new \Property\PropertyObjectPlugin();
    $plugin->run();
    // flush_rewrite_rules();
}


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-property_object-activator.php
 */
function activate_property_object_plugin() {
    // ...
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-property_object-deactivator.php
 */
function deactivate_property_object_plugin() {
    // ...
}

run_property_object_plugin();
register_activation_hook( __FILE__, 'activate_property_object_plugin' );
register_deactivation_hook( __FILE__, 'deactivate_property_object_plugin' );
