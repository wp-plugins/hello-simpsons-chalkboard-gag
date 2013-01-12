<?php
/*
Plugin Name: Hello Simpsons Chalkboard Gag
Plugin URI: http://wordpress.org/extend/plugins/simpsons-chalkboard-gag
Description: Let Bart Simpson's childish wit lighten your day by randomly adding one of his chalkboard gags to your admin panel.
Author: Dan Rossiter
Version: 1.2
Author URI: http://danrossiter.org
*/

// To enable calling of is_plugin_active()
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
// Define remote gag repository
define('SIMPSONS_URL', 'http://pastebin.com/raw.php?i=VdKZ0V4d');

function simpsons_get_gag() {
	// traps any issues with get_option
	// specifically, fixes issue on activation
	if( !$gags = get_option( 'simpsons-gags' )) return;
	
	// And then randomly choose a line
	return wptexturize( $gags[ array_rand($gags) ] );
}
add_shortcode( 'simpsons', 'simpsons_get_gag' );

// This just echoes the chosen gag
function simpsons_chalkboard_gag() {
        if( $err = get_option( 'simpsons-error') ) {
		$chosen = "Error: $err. If this persists, please contact the <a ".
			"href='http://wordpress.org/support/plugin/hello-simpsons-chalkboard-gag' ".
			"target='_blank'>plugin author</a>.";
	} else { $chosen = simpsons_get_gag(); }

	echo "<p id='simpsons'>$chosen</p>";
}

// We need some CSS to position the paragraph
function simpsons_css() {
	// This makes sure that the positioning is also good for right-to-left languages
	$x = is_rtl() ? 'left' : 'right';

	echo "
	<style type='text/css'>
	p#simpsons {
		float: $x;
		padding-$x: 15px;
		padding-top: 5px;		
		margin: 0;
		font-size: 11px;
	}
	</style>";
}


// Display Chalkboard Gag //
	// Now we set that function up to execute when the admin_notices action is called
	add_action( 'admin_notices', 'simpsons_chalkboard_gag' );
	// Style p#simpsons
	add_action( 'admin_head', 'simpsons_css' );
// End Display Chalkboard Gag //


// FIRST RUN //
	function simpsons_activate(){
		// Handle updating gags in DB
		// TODO: create custom 'weekly' regularity (daily is wasting resources)
		wp_schedule_event( time(), 'daily', 'simpsons-gag-update');
	}
	register_activation_hook( __FILE__, 'simpsons_activate' );
	add_action( 'simpsons-gag-update', 'simpsons_store_gags' );
// END FIRST RUN //


// LAST RUN //
	// All good plugins should cleanup after themselves!
	function simpsons_deactivate(){
		delete_option( 'simpsons-gags' );
		delete_option( 'simpsons-error' );
		wp_clear_scheduled_hook( 'simpsons-gag-update' );
		// GOODBYE, CRUEL WORLD!!!
	}
	register_deactivation_hook( __FILE__, 'simpsons_deactivate' );
// END LAST RUN //


// HELPERS //
	function simpsons_store_gags(){
		// retrive most recent listing of Bartisms from remote
		$gags = wp_remote_get( SIMPSONS_URL,
			array( 'user-agent' => $_SERVER['HTTP_USER_AGENT'] ) );

		// I PITTY THE FOOL WHO DOESN'T ERROR TRAP!!
		if( is_wp_error( $gags ) ) {
			update_option( 'simpsons-error', $gags->get_error_message() );
			unset($gags); 

		} elseif( $gags['response']['code'] < 200 || $gags['response']['code'] > 299 ) {
			update_option( 'simpsons-error',
				"{$gags['response']['code']}: {$gags['response']['message']}" );
			unset($gags);

		} else { $gags = $gags['body']; }

		// Only fallback to local db if simpsons-gags has never been initialized
		if( !get_option('simpsons-gags') && !isset($gags) || !$gags ) {
			$gags = file_get_contents( plugin_dir_path(__FILE__).'gags.db' );
		}

		// Parse & store gags
		if( isset($gags) && $gags ){
			$gags = preg_split('/[\r\n]+/', $gags );

			update_option( 'simpsons-gags', $gags ); 
			delete_option( 'simpsons-error' );
		}
	}
// END HELPERS
?>
