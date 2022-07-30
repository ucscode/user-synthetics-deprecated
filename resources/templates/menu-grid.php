<?php

defined('ROOT_PATH') or die;

global $grid_menu, $helper;

$grid_menu = new menufy();


# --- [ overview ] ---

$grid_menu->add("grid4", array(
	"label" => "Ucscode", 
	"link" => "https://github.com/ucscode",
	"target" => "_blank",
	"icon" => "fab fa-github",
	"title" => ""
));

