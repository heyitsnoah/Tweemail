<?php

include('config.php');

// GRAB LATEST ID
$myFile = "lastid";
$fh = fopen($myFile, 'r');
$theData = fread($fh, filesize($myFile));
fclose($fh);
$lastemail = $theData;

// VARIABLES
$result_url = "http://twitter.com/statuses/user_timeline/".$user.".xml?since_id=".$lastemail;

// GRAB TWEETS
$c = curl_init();
curl_setopt($c, CURLOPT_URL, $result_url);
curl_setopt($c, CURLOPT_GET, 1); 
curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($c);
$status = curl_getinfo($c, CURLINFO_HTTP_CODE); 
curl_close($c);

// CHECK STATUS
if ($status == 200) {
	// PARSE XML
	$xml = new SimpleXmlElement($output);
	if (count($xml) != 0) {
		$newemail = $xml->status->id;
		foreach ($xml->status as $tweet) {
			$new_tweets .= substr($tweet->created_at, 0, -20).": ".$tweet->text."\n\n";	
		}
		$to      = $your_email;
		$subject = 'New Tweets from '.$user;
		$message = $new_tweets;
		$headers = "From: ".$email;
		mail($to, $subject, $message, $headers);
		$fh = fopen($myFile, 'w');
		$stringData = $newemail;
		fwrite($fh, $stringData);
		fclose($fh);
	}
}

?>