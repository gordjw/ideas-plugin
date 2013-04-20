<?php
/*
Plugin Name: Ideas Plugin
Description: 
Version: 0.1
Author: Gordon Williamson
Author URI: http://gjw.id.au
License: CC-BY 3.0
*/

require_once( 'config/config.php' );
require_once( 'includes/ideas.class.php' );

register_activation_hook( __FILE__,  array( "Ideas", "activate" ) );

$ideas_object = new Ideas();
// END
