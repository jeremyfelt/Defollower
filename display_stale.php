<?php

/* If access tokens are not available redirect to connect page. */
if ( empty( $_SESSION[ 'access_token' ] ) || empty( $_SESSION[ 'access_token' ][ 'oauth_token' ] ) || empty( $_SESSION[ 'access_token' ][ 'oauth_token_secret' ] ) )
	header('Location: ./clearsessions.php');

/* Get user access tokens out of the session. */
$access_token = $_SESSION[ 'access_token' ];
require_once('includes/twitteroauth.php');
require_once('config.php');

/* Create a TwitterOauth object with consumer/user tokens. */
$connection = new TwitterOAuth( CONSUMER_KEY, CONSUMER_SECRET, $access_token[ 'oauth_token' ], $access_token[ 'oauth_token_secret' ] );

$today_date = time();
$default_interval = 2592000;
$screen_name = 'jeremyfelt';

$oauth_options = array('screen_name' => $screen_name,
                       'cursor' => '-1');

$keep_requesting = 1;   /* Keep the loop going while we page through results. */
$x = 0;                 /* Used to separate the friend id array into groups of 100 */
$y = 0;                 /* Used to track how many ids have been added to the array */ 

while ( 1 == $keep_requesting ){

	// friends stream will initially contain a massive list of Twitter user IDs
	$friends_stream = $connection->get( 'friends/ids', $oauth_options );

	if ( 0 == $friends_stream->next_cursor )
		$keep_requesting = 0;
	else
		$oauth_options['cursor'] = $friends_stream->next_cursor;

	foreach( $friends_stream->ids as $friend_id ) {
		if ( 99 <= $y ) {
			/*  Once we reach 100 friend ids in an sub array, move to the next one. */
			$x++;
			$y = 0;
		}
		$friend_id_list[ $x ][] = $friend_id;
		$y++;
	}
}

foreach( $friend_id_list as $friend_id_line ){
	$m = 0; /* Start the list without any commas */
	foreach( $friend_id_line as $fid ){
		if ( 0 == $m ){
			$long_id_list = "$fid";
			$m = 1;
		}else{
            $long_id_list .= ",$fid";
		}
	}

	$keep_requesting = 1;
	$oauth_options['user_id'] = $long_id_list;
	$oauth_options['cursor'] = -1;

	while ( 1 == $keep_requesting ) {
		$status_stream = $connection->get( 'users/lookup', $oauth_options );

		if ( 0 == $status_stream->next_cursor )
			$keep_requesting = 0;
		else
			$oauth_options['cursor'] = $status_stream->next_cursor;

		foreach( $status_stream as $friend_object ) {
			$ob_status_date = $friend_object->status->created_at;
			$ob_status_date_conv = strtotime( $ob_status_date );

			if ( $screen_name == $friend_object->screen_name )
				continue;

			if ( ! $ob_status_date )
				continue;

			if ( 2592000 > time() - $ob_status_date_conv )
				continue;

			if ( ( $today_date - strtotime( $ob_status_date ) ) > $default_interval ) {
				$ob_display_status_date = $ob_status_date;

				$ob_tweet_date = date( 'F m, Y', strtotime( $ob_status_date ) );
				$ob_c_date = date( 'c', strtotime( $ob_status_date ) );
				$unfollow_list .= '<div class="ob_twit">
					<blockquote class="twitter-tweet"><p>' . $friend_object->status->text . '</p>' .
					'&mdash; ' . $friend_object->name . '(@' . $friend_object->screen_name . ') ' .
					'<a href="https://twitter.com/' . $friend_object->screen_name . '/status/' . $friend_object->status->id .
					'" data-datetime="' . $ob_c_date . '">' . $ob_tweet_date . '</a></blockquote></div>';
					//<div class="ob_screen_name">' . $friend_object->screen_name . '</div>
					//<div class="ob_status_date">' . $ob_display_status_date . '</div>
					//<div class="ob_image"><img src="' . $friend_object->profile_image_url . '"></div>
					//<div class="ob_name">' . $friend_object->name . '</div>
					//<div class="ob_description">' . $friend_object->description . '</div>
					//<div class="ob_status">' . $friend_object->status->text . '</div>
					//<div class="unfollow_link"><a href="unfollow.php?fid=' . $friend_object->id . '">Unfollow</a></div></div>';
            }
		}
	}
}
include 'views/display.php';