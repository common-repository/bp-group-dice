<?php
/*
Plugin Name: BuddyPress Group Dice
Plugin URI: http://linktart.co.uk
Description: Dice rolling in forums for RP/etc groups
Version: 1.2
Revision Date: May 17, 2013
Requires at least: WordPress 3.5.1, BuddyPress 1.7.2
Tested up to: WordPress 3.5.1, BuddyPress 1.7.2
License: AGPL
Author: David Cartwright
Author URI: http://linktart.co.uk
Network: true
*/

/* Only load the component if BuddyPress is loaded and initialized. */
function bp_group_dice_init() {
	require( dirname( __FILE__ ) . '/includes/bp-group-dice-core.php' );
}
add_action( 'bp_init', 'bp_group_dice_init' );
?>