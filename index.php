<?php
/**
 * Route incoming requests coming through to defollower.com
 *
 * If we're already authenticated, then we should see a different view
 * from somebody that has just arrived on the site. If we are authenticated,
 * then we'll need to decide what view to show them.
 */

// If we arrive with any prefix (www/etc), redirect to defollower.com for easier token handling
if ( 'defollower.com' != $_SERVER['HTTP_HOST'] )
	header( 'Location: http://defollower.com' );

session_start();
if ( isset( $_SESSION['access_token'] ) ) {
	include 'display_stale.php';
	exit();
}

include 'login.php';