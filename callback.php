<?php
/*  callback.php
    Handles the authentication callback after the user is
    processed through the Twitter oAuth servers.
*/

session_start();
require_once('includes/twitteroauth.php');
require_once('config.php');

/* If the oauth_token is old redirect to the connect page. */
if ( isset( $_REQUEST['oauth_token'] ) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token'] ) {
  $_SESSION['oauth_status'] = 'oldtoken';
  header('Location: ./clearsessions.php');
}

/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
$connection = new TwitterOAuth( CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret'] );

/* Request access tokens from twitter */
$access_token = $connection->getAccessToken( $_REQUEST['oauth_verifier'] );

/*  These two variables need to be stored. */
$access_token['oauth_token'];
$access_token['oauth_token_secret'];

/* Remove no longer needed request tokens */
unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);

/* If HTTP response is 200 continue otherwise send to connect page to retry */
if (200 == $connection->http_code) {
  /* The user has been verified and the access tokens can be saved for future use */
  $_SESSION['status'] = 'verified';
  header('Location: ./index.php');
} else {
  /* Save HTTP status for error dialog on connnect page.*/
  header('Location: ./clearsessions.php');
}
