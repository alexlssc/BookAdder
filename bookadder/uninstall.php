<?php

if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ){
  die;
}

// Get table's name
$table_name = $wpdb->prefix . 'books';


// Clear database of plugin
global $wpdb;
$sql = "DROP TABLE IF EXISTS $table_name";
$wpdb->query($sql);
delete_option("my_plugin_db_version");
