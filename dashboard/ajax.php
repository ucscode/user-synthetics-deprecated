<?php require __DIR__ . "/config.php";

# [enable ajax]
project::enable_ajax_mode( __DIR__ );

# [start dev];
events::exec("ajax_mode:start");

# [get ajax file];
# [ always remember to authenticate user & security whenever necessary ]
require_once ( $universal->ajax->script );

# [end dev];
events::exec("ajax_mode:end");

# [print output];
$helper->jsonify( !!$universal->ajax->status, trim($universal->ajax->message), $universal->ajax->data, TRUE );