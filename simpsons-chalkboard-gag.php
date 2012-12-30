<?php
/*
Plugin Name: Simpsons Chalkboard Gag
Plugin URI: http://wordpress.org/extend/plugins/simpsons-chalkboard-gag
Description: Let Bart Simpson's childish wit lighten your day by randomly adding one of his chalkboard gags to your admin panel.
Author: Dan Rossiter
Version: 0.5
Author URI: http://danrossiter.org
*/

// To enable calling of is_plugin_active()
//include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

define("SIMPSONS_URL", "http://pastebin.com/raw.php?i=VdKZ0V4d");
	
function simpsons_get_gag() {
	// retrive most recent listing of Bartisms
	if( simpsons_has_curl() ) {
		$ch = curl_init( SIMPSONS_URL );
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , true);
		$gags = curl_exec($ch);
		
		// String to array & remove any extra lines
		$gags = preg_split('/[\r\n]+/', $gags );
	}
	if( !isset($gags) || !$gags ){ // fallback to local file
		$gags = file( plugin_dir_url( __FILE__ )."gags.db", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
	}
	
	// And then randomly choose a line
	return wptexturize( $gags[ array_rand($gags) ] );
}
add_shortcode( 'simpsons', 'simpsons_get_gag' );

// This just echoes & positions the chosen gag
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

// Now we set that function up to execute when the admin_notices action is called
add_action( 'admin_notices', 'simpsons_chalkboard_gag' );
// Style p#simpsons
add_action( 'admin_head', 'simpsons_css' );

function simpsons_has_curl() {
	return in_array( 'curl', get_loaded_extensions() );
}


// TODO...

// Handle deactivating of hello-dolly or simpsons-chalkboard-gag
/*if( is_admin() && array_key_exists('deactivate', $_GET) ) { ?>
	<script>
		// popup will close once complete
		<!--
		settimeout('self.close()',3000);
		//-->
	</script>
	
	<?php 
	simpsons_deactivate( $_GET['deactivate'] );
}
function simpsons_deactivate( $plugin ) {
	if( !strcmp($plugin, "hello-dolly" ) ) {
		$plugin_path = plugin_dir_url( __FILE__ )."../hello-dolly/hello.php";
	} else if ( !strcmp($plugin, "simpsons-chalkboard-gag") ) {
		$plugin_path = plugin_dir_url( __FILE__ )."simpsons-chalkboard-gag.php";
	} else { // invalid $plugin value
		return;
	}
	
	deactivate_plugins( $plugin_path );
}

// Return whether Hello Dolly is active
function hello_dolly_active() {
	$dolly_path = plugin_dir_url( __FILE__ )."../hello-dolly/hello.php";
	return is_plugin_active( $dolly_path );
}
// Error to be thrown if Hello Dolly is active
function simpsons_js() {
	echo "
	<script>
		var r = confirm('The Simpsons Chalkboard Gag plugin is not compatible with the Hello Dolly
			plugin. If you would like to deactivate Hello Dolly, click OK. Click CANCEL to deactivate
			the Simpsons Chalkboard Gag plugin.');
		
		var plugin_path = ".plugin_dir_url( __FILE__ )."simpsons-chalkboard-gag.php;
		if( r ) {
			window.open( plugin_path + '?deactivate=hello-dolly' );
		} else {
			window.open( plugin_path + '?deactivate=simpsons-chalkboard-gag' );
		}
	</script>
	";
}*/

?>