<?php

if ( ! file_exists('alfred.bundler.php') ) 
  exec( 'curl -sL "https://raw.githubusercontent.com/shawnrice/alfred-bundler/aries/wrappers/alfred.bundler.php" > "alfred.bundler.php"' );

// Require the bundler.
require_once('alfred.bundler.php');


// Load and use David Ferguson's Workflows.php class
$files = __load( "Workflows" );
$w = new Workflows;

require('functions.php');


 // get info on current song
$command_output = exec("./track_info.sh 2>&1");

if (substr_count($command_output, '⇾') > 0) {
    $results = explode('⇾', $command_output);
 
	//
	// Read settings from DB
	//
	$getSettings = 'select alfred_playlist_uri,alfred_playlist_name,theme from settings';
	$dbfile = $w->data() . '/settings.db';
	exec("sqlite3 -separator '	' \"$dbfile\" \"$getSettings\" 2>&1", $settings, $returnValue);
	
	if ($returnValue != 0) {
	    displayNotification("Error: Alfred Playlist is not set");
	    return;
	}
    

	foreach ($settings as $setting):
	
	    $setting = explode("	", $setting);
	
	    $alfred_playlist_uri = $setting[0];
	    $alfred_playlist_name = $setting[1];
	    $theme = $setting[2];
	endforeach;

    exec("osascript -e 'tell application \"Spotify\" to open location \"spotify:app:miniplayer:addtoalfredplaylist:$results[4]:$alfred_playlist_uri\"'");
    exec("osascript -e 'tell application \"Spotify\" to open location \"$alfred_playlist_uri\"'"); 
    
    displayNotificationWithArtwork('' . $results[2] . ' by ' . $results[1] . ' was added to ' . $alfred_playlist_name,getTrackOrAlbumArtwork($w,$theme,$results[4],true));                   
}
else {
	displayNotification("Error: No track is playing");
}

?>