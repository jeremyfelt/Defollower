<?php

/* If access tokens are not available redirect to connect page. */
if ( empty( $_SESSION[ 'access_token' ] ) || empty( $_SESSION[ 'access_token' ][ 'oauth_token' ] ) || empty( $_SESSION[ 'access_token' ][ 'oauth_token_secret' ] ) )
	header('Location: ./clearsessions.php');

/* Get user access tokens out of the session. */
$access_token = $_SESSION[ 'access_token' ];

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
	$friends_stream = $connection->get( 'friends/ids', $oauth_options );
	$next_cursor = $friends_stream->next_cursor;

	if ( 0 == $next_cursor )
		$keep_requesting = 0;
	else
		$oauth_options[ 'cursor' ] = $next_cursor;

	foreach( $friends_stream->ids as $friend_id ){
		if ( 99 <= $y ){
			/*  Once we reach 100 friend ids in an sub array, move to the next one. */
			$x++;
			$y=0;
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
	$oauth_options[ 'user_id' ] = $long_id_list;
	$oauth_options[ 'cursor' ] = -1;

	while ( 1 == $keep_requesting ){
		$status_stream = $connection->get( 'users/lookup', $oauth_options );
		$next_cursor = $status_stream->next_cursor;

		if ( 0 == $next_cursor )
			$keep_requesting = 0;
		else
			$oauth_options[ 'cursor' ] = $next_cursor;

		$abc = 1;
		foreach( $status_stream as $friend_object ){
			if ( $abc = 1 ){
				$abc = 2;
				continue;
			}
			$ob_id = $friend_object->id;
			$ob_screen_name = $friend_object->screen_name;
			$ob_name = $friend_object->name;
			$ob_image = $friend_object->profile_image_url;
			$ob_description = $friend_object->description;
			$ob_status = $friend_object->status->text;
			$ob_status_date = $friend_object->status->created_at;

			$unfollow_side_list .= $ob_id . ' ' . $ob_screen_name . ' ' . $ob_status_date . '<br>';

			if ( ( $today_date - strtotime( $ob_status_date ) ) > $default_interval ){
				$ob_display_status_date = $ob_status_date;

				$unfollow_list .= '<div class="ob_twit">
					<div class="ob_screen_name">' . $ob_screen_name . '</div>
					<div class="ob_status_date">' . $ob_display_status_date . '</div>
					<div class="ob_image"><img src="' . $ob_image . '"></div>
					<div class="ob_name">' . $ob_name . '</div>
					<div class="ob_description">' . $ob_description . '</div>
					<div class="ob_status">' . $ob_status . '</div>
					<div class="unfollow_link"><a href="unfollow.php?fid=' . $ob_id . '">Unfollow</a></div></div>';
            }
		}
	}
}

include VIEWS_DIR . 'display.php';