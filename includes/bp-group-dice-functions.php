<?php

// rolls the dice
function bp_group_dice_roll_the_dice( $dice_type, $dice_number ) {

	$dice_rolls = array();

	for ( $i = 1; $i <= $dice_number; $i++ ) {
		$dice_rolls[] = rand( 1, $dice_type );
	}

	return $dice_rolls;
}

// hooks into new posts on the forum
function bp_group_dice_catch_forum_post( $post_id ) {
	global $bp;
	
	// if dice not enabled for this group skip the rest of the function
	if ( !groups_get_groupmeta( $bp->groups->current_group->id, 'bp_group_dice_enabled' ) ) return false;
	
	// if dice not submitted skip rest of the function
	if ( !isset( $_POST['bp_dice_roll'] ) ) return false;
	
	// if dice type not valid skip the rest of the function
	if ( !is_valid_dice_type( $_POST['dice_type'] ) ) return false;
	
	// if dice number not valid skip the rest of the function
	if ( !is_valid_dice_number( $_POST['dice_number'] ) ) return false;
		
	// okay, let's assume that people want to 'roll the dice'
	$dice_rolls = bp_group_dice_roll_the_dice( $_POST['dice_type'], $_POST['dice_number'] );

	// save the dice type + number rolled and the results
	groups_update_groupmeta( $bp->groups->current_group->id, 'dice_rolled_for_post_' . $post_id, $_POST['dice_type'] . ',' . $_POST['dice_number'] );
	groups_update_groupmeta( $bp->groups->current_group->id, 'dice_results_for_post_' . $post_id, $dice_rolls );
	
	return $post_id;
}
add_action( 'bbp_new_reply_post_extras', 'bp_group_dice_catch_forum_post' );


function bp_group_dice_display_roll_results() {
	global $bp;
	
	$dice_rolled = groups_get_groupmeta( $bp->groups->current_group->id, 'dice_rolled_for_post_' . bbp_get_reply_id() );
	
	if ( !$dice_rolled ) return false;
	
	$dice_details = explode( ',', $dice_rolled );
	$dice_type = $dice_details[0];
	$dice_amount = $dice_details[1];
	$dice_results = maybe_unserialize( groups_get_groupmeta( $bp->groups->current_group->id, 'dice_results_for_post_' . bbp_get_reply_id() ) );
	if ( $dice_results ) {
		$dice_results_formatted = '';
		foreach ( $dice_results as $key => $dice_result ) {
			if ( $key && ( $key < ( count( $dice_results ) ) ) ) {
				$dice_results_formatted .= ', ';
			}
			$dice_results_formatted .= $dice_result;
		}
	}
	echo '
		 <p>Rolled: ' . $dice_amount . 'D' . $dice_type . '...<br/>
			Results: ' . $dice_results_formatted . '
		 ';
	return true;
}
add_action( 'bbp_theme_after_reply_content', 'bp_group_dice_display_roll_results' );

function bp_group_dice_add_rolling_form() {
	global $bp;
	
	// if dice not enabled for this group skip the rest of the function
	if ( !groups_get_groupmeta( $bp->groups->current_group->id, 'bp_group_dice_enabled' ) ) return false;
		
	?>
	<script type="text/javascript">
	function loadDiceRoller() {
		jQuery('#show-roller-button').remove();
		jQuery('#reply_text').remove();
		jQuery('h4:contains("<?php _e( 'Add a reply:', 'buddypress' ) ?>")').remove();
		jQuery('.submit').children('#submit').remove();
		jQuery('.bbp-template-notice').next().remove();
		jQuery('.bbp-template-notice').remove();
		jQuery('#new-post').children().children().first().prepend().html('Dice Roller');
		jQuery('#new-post').children().children().first().after('<input type="hidden" id="bbp_reply_content" name="bbp_reply_content" value="<?php _e( 'Dice Roll', 'bp_group_dice' ) ?>"/>Dice type: <select name="dice_type" id="dice_type"><option value="3">D3</option><option value="4">D4</option><option value="6">D6</option><option value="8">D8</option><option value="10">D10</option><option value="12">D12</option><option value="20">D20</option><option value="100">D100</option></select>&nbsp;&nbsp;Amount: <select name="dice_number" id="dice_number"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select><div class="submit"><input id="submit" type="submit" value="Roll the dice!" name="submit_reply"></div><input type="hidden" name="bp_dice_roll" id="bp_dice_roll" value="1"/>');
	}
	</script>
	
	<input id="show-roller-button" type="button" onclick="loadDiceRoller();return false;" value="Click to Initiate Dice Roll"/> <strong>&nbsp;WARNING: </strong>This will delete any text in the post below!
	<?php
}
add_action( 'bbp_theme_before_reply_form_content', 'bp_group_dice_add_rolling_form' );

function is_valid_dice_type( $dice_type ) {
	return true;
}

function is_valid_dice_number( $dice_number ) {
	return true;
}
?>