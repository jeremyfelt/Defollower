<?php
session_start();
require_once('includes/twitteroauth.php');
require_once('config.php');

/* If access tokens are not available redirect to connect page. */
if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
    header('Location: ./clearsessions.php');
}
/* Get user access tokens out of the session. */
$access_token = $_SESSION['access_token'];
$friend_id = $_GET['fid'];

/* Create a TwitterOauth object with consumer/user tokens. */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

$oauth_options = array('user_id' => $friend_id);

$unfollow_result = $connection->post('friendships/destroy', $oauth_options);

var_dump($unfollow_result);

?>
<br><br>
<a href="display.php">Back to list</a>
