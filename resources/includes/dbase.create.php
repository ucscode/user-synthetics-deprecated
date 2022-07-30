<?php 

(defined("ROOT_PATH") && defined("DB_HOST")) or die("DIRECT ENTRY RESTRICTED"); 

$prefix = trim( DB_PREFIX );

$QUERIES = array(

	"users" => "
		CREATE TABLE IF NOT EXISTS {$prefix}users (
			id int not null primary key auto_increment,
			username varchar(30) unique,
			email varchar(255) not null unique,
			password varchar(100) not null,
			register_time varchar(20) not null,
			status varchar(30) default 'unverified',
			activation_key varchar(60),
			role varchar(60) not null default 'member',
			logintoken varchar(20),
			last_seen varchar(20),
			usercode varchar(12) not null unique,
			remote_addr varchar(20)
		)
	",
	
	"notifications" => "
		CREATE TABLE IF NOT EXISTS {$prefix}notifications (
			id int not null primary key auto_increment,
			receiver int not null,
			period varchar(20) not null,
			message text,
			clicked tinyint not null default 0,
			redirect varchar(700),
			foreign key (receiver) references {$prefix}users(id) ON DELETE CASCADE
		)
	"

);

# -- [ prepare for errors ] --

$errors = array();


# -- [ run all queries ] --

foreach( $QUERIES as $name => $SQL ):
	$result = $ucsqli->query( $SQL );
	if( !$result ):
		$errors[] = "<li class='mb-2'>Unable to create TABLE <strong>`{$prefix}{$name}`</strong></li>";
	endif;
endforeach;


# -- [ Throw Error Exception ] --

if( !empty($errors) ) {
	$stderror = implode("", $errors);
	fatality( "DATABASE ERROR", "<ul>{$stderror}</ul>" );
}