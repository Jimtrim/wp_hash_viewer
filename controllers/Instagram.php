<?php
	require 'vendor/autoload.php';

	use Httpful\Request;

	error_reporting( E_ALL );
	if ( isset($_GET['hashtag']) ){
		$CLIENT_ID = "d81afea83c3f40b5a5485418e2a53aa7";

		$tag	=  $_GET['hashtag'];
		$uri	= "https://api.instagram.com/v1/tags/" . $tag . "/media/recent?client_id=" . $CLIENT_ID;
		if ( isset($_GET['max_tag_id']) ) {
			$uri .= "&max_tag_id=" . $_GET['max_tag_id'];
		}
		$response = Request::get($uri)->send();

		echo("".$response);
	} else {
		echo("{}");
	}


?>

