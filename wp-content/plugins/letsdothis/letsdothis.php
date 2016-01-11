<?php
/**
 * @package LetsDoThis
 * @version 1.0
 */

/*
Plugin Name: Let's Do This
Description: Custom plugin for the Let's Do This app.
Author: DoSomething.org
Version: 1.0
*/

function letsdothis_allow_origin() {
    header("Access-Control-Allow-Origin: *");
}
add_action( 'init', 'letsdothis_allow_origin' );

remove_post_type_support( 'post', 'editor' );

?>
