<?php
/* open_connection.php
 * We have the user click a sign in to Twitter button, which
 * sends them over to the Twitter sign in page. */

/* Start session and load library. */
session_start();
require_once( 'includes/twitteroauth.php' );
require_once( 'config.php' );

// Build a new TwitterOAuth object with client credentials from our config
$connection = new TwitterOAuth( CONSUMER_KEY, CONSUMER_SECRET );
$request_token = $connection->getRequestToken( OAUTH_CALLBACK );

// Save temporary credentials to the session, though I'd rather use cookies or something for this
// @todo ditch the session stuff
$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

if ( 200 == $connection->http_code ) {
	header( 'Location: ' . $connection->getAuthorizeURL( $token ) );
	die();
}

include 'views/header_preauth.php';
// Something didn't work right.
echo 'Could not connect to Twitter. Refresh the page or try again later';
include 'views/footer_preauth.php';