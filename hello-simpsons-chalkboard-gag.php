<?php
/*
Plugin Name: Hello Simpsons Chalkboard Gag
Plugin URI: http://wordpress.org/extend/plugins/simpsons-chalkboard-gag
Description: Let Bart Simpson's childish wit lighten your day by randomly adding one of his chalkboard gags to your admin panel.
Author: Dan Rossiter
Version: 1.4
Author URI: http://danrossiter.org
*/

// remote gag repository
define('SIMPSONS_URL', 'http://plugins.svn.wordpress.org/hello-simpsons-chalkboard-gag/trunk/gags.db');

function simpsons_get_gag() {
	$gags = get_option( 'simpsons-gags' );
	
	// And then randomly choose a line
	return $gags ? wptexturize( $gags[ array_rand($gags) ] ) : false;
}
add_shortcode( 'simpsons', 'simpsons_get_gag' );

// echo the chosen gag or error
function simpsons_chalkboard_gag() {
	if( $ret = simpsons_get_gag() ) {
		// nothing here
        } elseif( get_option( 'simpsons-first-run' ) ){
		delete_option( 'simpsons-first-run' );
		$ret = 'Welcome to Hello Simpsons Chalkboard Gag!';
	} elseif( !$ret && $ret = get_option( 'simpsons-error') ) {
		$ret =	"Error: $err. If this persists, please contact the <a ".
			"href='http://wordpress.org/support/plugin/hello-simpsons-chalkboard-gag' ".
			"target='_blank'>plugin author</a>.";

	} else {
		$ret =	"Unknown error. If this persists, please contact the <a ".
			"href='http://wordpress.org/support/plugin/hello-simpsons-chalkboard-gag' ".
			"target='_blank'>plugin author</a>.";
	}

	echo "<p id='simpsons'>$ret</p>";
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
	// Now we set that function up to execute when the admin_notices
	add_action( 'admin_notices', 'simpsons_chalkboard_gag' );
	// Style p#simpsons
	add_action( 'admin_head', 'simpsons_css' );

	// support multisite dash display
	if( is_multisite() ){
		add_action( 'network_admin_notices', 'simpsons_chalkboard_gag' );
		add_action( 'network_admin_head', 'simpsons_css' );
	}
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
		delete_option( 'simpsons-first-run' );
		wp_clear_scheduled_hook( 'simpsons-gag-update' );
		// GOODBYE, CRUEL WORLD!!!
	}
	register_deactivation_hook( __FILE__, 'simpsons_deactivate' );
// END LAST RUN //


// HELPERS //
	function simpsons_store_gags(){
		// get most recent listing of Bartisms from remote site
		$gags = wp_remote_get( SIMPSONS_URL,
			array( 'user-agent' => $_SERVER['HTTP_USER_AGENT'] ) );

		// I PITTY THE FOOL WHO DOESN'T ERROR TRAP!!
		if( is_wp_error( $gags ) ) {
			update_option( 'simpsons-error', $gags->get_error_message() );

		} elseif( $gags['response']['code'] < 200 || $gags['response']['code'] > 299 ) {
			update_option( 'simpsons-error', 
				"{$gags['response']['code']}: {$gags['response']['message']}" );

		} else { // successful get
			$gags = preg_split( '/[\r\n]+/', $gags['body'] );
		}

		// Only fallback to local db if simpsons-gags has never been initialized
		if( isset($error) && !get_option('simpsons-gags') ) {
			$gags = file( plugin_dir_path(__FILE__).'gags.db', 
				FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
		}

		// Parse & store gags
		if( $gags ) {
			if( ( $old = get_option( 'simpsons-gags' ) ) && count( $old ) >= count( $gags ) )
				return; // nothing new
			if( !$old ) update_option( 'simpsons-first-run', true );
			update_option( 'simpsons-gags', $gags );
		}			
	}
// END HELPERS //
?>
