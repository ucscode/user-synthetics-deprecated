<?php 

/* The config file contains all necesarry content */

require_once __DIR__ . "/config.php";

$backend = new backend();

$backend->blank(true);

$backend->bodyclass[] = $universal->temp->index_bodyclass ?? null;

$backend->output(function() {
	events::exec("uss:index");
});

