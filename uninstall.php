<?php
global $wpdb  ;
// If uninstall/delete not called from WordPress then exit
if( !defined( 'ABSPATH' ) && !defined( 'WP_UNINSTALL_PLUGIN' ) )
exit();
// Delete option from options table
delete_option( 'prowp_options_arr' );
// Delete any other options, custom tables/data, files
$wpdb->query("DROP TABLE IF EXISTS wp_prowp_data") ;
?>