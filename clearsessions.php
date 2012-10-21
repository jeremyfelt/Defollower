<?php
/* Load and clear sessions */
session_start();
session_destroy();
/* Redirect to page with the connect to Twitter option. */
header( 'Location: http://defollower.com' );
