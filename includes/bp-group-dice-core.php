<?php
define ( 'BP_GROUP_DICE_IS_INSTALLED', 1 );
define ( 'BP_GROUP_DICE_VERSION', '1.2' );

require( dirname( __FILE__ ) . '/bp-group-dice-functions.php' );

if ( file_exists( dirname( __FILE__ ) . '/lang/' . get_locale() . '.mo' ) )	load_textdomain( 'bp-group-dice', dirname( __FILE__ ) . '/lang/' . get_locale() . '.mo' );

function bp_group_dice_setup_globals() {
	global $bp, $wpdb;
	$bp->dice->id = 'dice';

	$bp->dice->table_name = $wpdb->base_prefix . 'bp_group_dice';
	$bp->dice->format_notification_function = 'bp_group_dice_format_notifications';
	$bp->dice->slug = BP_GROUP_DICE_SLUG;

	$bp->active_components[$bp->dice->slug] = $bp->dice->id;
}
add_action( 'bp_setup_globals', 'bp_group_dice_setup_globals' );

class BP_Group_Dice extends BP_Group_Extension {	

	function bp_group_dice() {
		global $bp;
		
		$this->name = 'Dice';
		$this->slug = 'dice';

		$this->create_step_position = 18;

		$this->enable_nav_item = false;
				
	}	
	
	function create_screen() {
		global $bp;
		
		if ( !bp_is_group_creation_step( $this->slug ) )
			return false;
			
		wp_nonce_field( 'groups_create_save_' . $this->slug );
		?>
		<input type="checkbox" name="bp_group_dice_enabled" id="bp_group_dice_enabled" value="1"  
			<?php 
			if ( groups_get_groupmeta( $bp->groups->current_group->id, 'bp_group_dice_enabled' ) == '1' ) {
				echo 'checked=1';
			}
			?>
		/>
		Enable Group Dice
		<hr>
		<?php
	}

	function create_screen_save() {
		global $bp;
		
		check_admin_referer( 'groups_create_save_' . $this->slug );	
		
		if ( $_POST['bp_group_dice_enabled'] == 1 ) {
			groups_update_groupmeta( $bp->groups->current_group->id, 'bp_group_dice_enabled', 1 );
		}
	}

	function edit_screen() {
		global $bp;
		
		if ( !groups_is_user_admin( $bp->loggedin_user->id, $bp->groups->current_group->id ) ) {
			return false;
		}
		
		if ( !bp_is_group_admin_screen( $this->slug ) )
			return false;
			
		wp_nonce_field( 'groups_edit_save_' . $this->slug );
		?>
		<input type="checkbox" name="bp_group_dice_enabled" id="bp_group_dice_enabled" value="1"  
			<?php 
			if ( groups_get_groupmeta( $bp->groups->current_group->id, 'bp_group_dice_enabled' ) == '1' ) {
				echo 'checked=1';
			}
			?>
		/>
		Enable Group Dice
		<hr>
		<input type="submit" name="save" value="Save" />
		<?php
	}

	function edit_screen_save() {
		global $bp;

		if ( !isset( $_POST['save'] ) )
			return false;

		check_admin_referer( 'groups_edit_save_' . $this->slug );
		
		if ( $_POST['bp_group_dice_enabled'] == 1 ) {
			groups_update_groupmeta( $bp->groups->current_group->id, 'bp_group_dice_enabled', 1 );
			bp_core_add_message( __( 'Group Dice Activated', 'bp-dice' ) );
		} else {
			groups_update_groupmeta( $bp->groups->current_group->id, 'bp_group_dice_enabled', 0 );
			bp_core_add_message( __( 'Group Dice Deactivated', 'bp-dice' ) );
		}
				
		bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $this->slug );

	}

	function display() {
		// Not used
		return false;
	}

	function widget_display() { 
		// Not used
	}
}
bp_register_group_extension( 'BP_Group_Dice' );
?>