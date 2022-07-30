<?php

/*
	Plugin is written in the format:
	-------------------------------
	
	plugins::register( "unique_plugin_name", array() );
	
	The only required fields in the array are "title" and "plugin_file";
	
	If an image.{jpg, png, jpeg or gif} is present in the plugin directory, it will be used as the plugin thumbnail
	
*/

plugins::register("copyright", array(
	"title" => "Copyright plugin 2022 - Footer", // [ required ]
	"plugin_file" => "main-plugin.php", // [ required ]
	"version" => "1.0.0",
	"author" => "Uchenna Ajah adams crescrent the great mobile programmer",
	"author_uri" => "https://ucscode.com",
	"plugin_uri" => "https://github.com/ucscode",
	"description" => "A copyright statement that at the footer of every dashboard page"
));



