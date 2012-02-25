<?php
/* open_connection.php
 * We have the user click a sign in to Twitter button, which
 * sends them over to the Twitter sign in page. */

/* Start session and load library. */
session_start();
require_once( 'includes/twitteroauth.php' );
require_once( 'config.php' );

/* Build TwitterOAuth object with client credentials. */
$connection = new TwitterOAuth( CONSUMER_KEY, CONSUMER_SECRET );
/* Get temporary credentials. */
$request_token = $connection->getRequestToken( OAUTH_CALLBACK );

/* Save temporary credentials to session. */
$_SESSION[ 'oauth_token' ] = $token = $request_token[ 'oauth_token' ];
$_SESSION[ 'oauth_token_secret' ] = $request_token[ 'oauth_token_secret' ];

/* If the last connection failed, don't display authorization link. */
switch ( $connection->http_code ) {
	case 200:
		/* Build authorize URL and redirect user to Twitter. */
		$url = $connection->getAuthorizeURL( $token );
		header( 'Location: ' . $url );
		break;
	default:
		/* Show notification if something went wrong. */
		echo 'Could not connect to Twitter. Refresh the page or try again later.';
}