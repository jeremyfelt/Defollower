<?php
/**
 * Route incoming requests
 *
 * If we're already authenticated, then we should see a different view
 * from somebody that has just arrived on the site. If we are authenticated,
 * then we'll need to decide what view to show them.
 */
require_once( 'config.php' );

// If we arrive with any prefix (www/etc) or URL, redirect to SITE_ADDRESS for easier token handling
if ( ! strstr( SITE_ADDRESS, $_SERVER['HTTP_HOST'] ) )
	header( 'Location: ' . SITE_ADDRESS );

// We use sessions, cuz I'm lazy at the moment
session_start();
if ( isset( $_SESSION['access_token'] ) ) {
	include 'generate_stale_data.php';
	session_write_close();
	include 'display.php';
	exit();
}
session_write_close();

include 'header.php';
include 'login.php';
include 'footer.php';