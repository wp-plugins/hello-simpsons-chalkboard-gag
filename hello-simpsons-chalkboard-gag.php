<?php
/*
Plugin Name: Hello Simpsons Chalkboard Gag
Plugin URI: http://wordpress.org/extend/plugins/simpsons-chalkboard-gag
Description: Let Bart Simpson's childish wit lighten your day by randomly adding one of his chalkboard gags to your admin panel.
Author: Dan Rossiter
Version: 1.0
Author URI: http://danrossiter.org
*/

// To enable calling of is_plugin_active()
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
// Define remote gag repository
define('SIMPSONS_URL', 'http://pastebin.com/raw.php?i=VdKZ0V4d');

function simpsons_get_gag() {
	// String to array & remove any extra lines
	$gags = preg_split('/[\r\n]+/', get_option( 'simpsons-gags' ) );
	
	// And then randomly choose a line
	return wptexturize( $gags[ array_rand($gags) ] );
}
add_shortcode( 'simpsons', 'simpsons_get_gag' );

// This just echoes the chosen gag
function simpsons_chalkboard_gag() {
	$chosen = simpsons_get_gag();
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
		// Check if Hello Dolly is active
		if( hello_dolly_active() ) update_option( 'simpsons-conflict', true );

		// Handle updating gags in DB
		// TODO: create custom 'weekly' regularity (daily is wasting resources)
		wp_schedule_event( time(), 'daily', 'simpsons-gag-update');
	}
	register_activation_hook( __FILE__, 'simpsons_activate' );
	add_action( 'simpsons-gag-update', 'simpsons_store_gags' );
	
	// Display Hello Dolly warning if active (only once)
	if( get_option( 'simpsons-conflict' ) ){
		add_action( 'admin_footer', 'simpsons_js' );
		delete_option( 'simpsons-conflict' );
	}
	// Return whether Hello Dolly is active
	function hello_dolly_active() {
		return is_plugin_active( 'hello-dolly/hello.php' );
	}

	// Warn user if Hello Dolly is active
	function simpsons_js() {
		echo "
		<script>
			<!--
			var r = window.confirm('It is discouraged to run Hello Simpsons Chalkboard Gag plugin alongside the Hello Dolly ' +
				'plugin. Click OK to go to the Installed Plugins tab where you can deactivate Hello Dolly.');
			if( r ){
				window.location = '". site_url() ."/wp-admin/plugins.php';
			}
			//-->
		</script>
		";
	}
// END FIRST RUN //


// LAST RUN //
	// All good plugins should cleanup after themselves!
	function simpsons_deactivate(){
		delete_option( 'simpsons-gags' );
		wp_clear_scheduled_hook( 'simpsons-gag-update' );
		// GOODBYE CRUEL WORLD!!!
	}
	register_deactivation_hook( __FILE__, 'simpsons_deactivate' );
// END LAST RUN //


// HELPERS //
	function simpsons_has_curl() {
		return in_array( 'curl', get_loaded_extensions() );
	}

	function simpsons_store_gags(){
		// retrive most recent listing of Bartisms from remote
		if( simpsons_has_curl() ) {
			$ch = curl_init( SIMPSONS_URL );
			curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
			$gags = curl_exec($ch);
		}

		// Only fallback to local gags when there isn't a more recent version
		if( !get_option( 'simpsons-gags' ) && (!isset($gags) || !$gags) ) // fallback to local file
			$gags = file_get_contents( plugin_dir_url( __FILE__ ).'gags.db' );
		

		// Store gags
		if( isset($gags) && $gags ) update_option( 'simpsons-gags', $gags );
	}
// END HELPERS
?>
