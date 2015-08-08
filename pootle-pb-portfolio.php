<?php
/*
Plugin Name: pootle page builder portfolios
Plugin URI: http://pootlepress.com/
Description: Boilerplate for fast track Pootle Page Builder Addon Development
Author: Shramee
Version: 0.1
Author URI: http://shramee.com/
*/

/** Plugin admin class */
require 'inc/class-admin.php';
/** Plugin public class */
require 'inc/class-public.php';
/**
 * Including Main Plugin class
 */
require_once 'class-pootle-pb-portfolio.php';
/** Instantiating main plugin class */
Pootle_PB_Portfolios::instance( __FILE__ );