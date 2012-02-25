<?php

define( 'MY_IP', '24.22.85.180' );

/* Because of the authorization with Twitter, we want to use
 * the same domain name for every request when logging in. */
if ( 'defollower.com' != $_SERVER[ 'HTTP_HOST' ] ) != "defollower.com")
	header( 'Location: http://defollower.com' );

require_once( 'config.php' );
require_once( 'includes/twitteroauth.php' );

if( $_SERVER['REMOTE_ADDR'] != MY_IP ){
	include 'views/placeholder.php';
	exit;
}else{
	/* Start the session and check if we already have an access token.
	 * If we don't, display the login page. */
	session_start();
	if ( isset( $_SESSION[ 'access_token' ] ) ){
		include 'controllers/display_stale.php';
		exit();
	}
	session_write_close();

	include 'views/login.php';
}