<?php 

if(!is_user_logged_in()){ // Return login section if user not logged in
	?>
		<p>Please login to access this page...
		<br/><a style="float: left;" class="btn btn-primary" href="<?= get_home_url() . '/login'; ?>" title="Login">Login</a><br/>
		<span class="clearfix"></span>
		</p>
	<?php return false;
	}

	$forms = array(
			array(
				'name' 		=> 'start_time', 
				'type' 		=> 'time', 
				'label' 	=> 'Start Time <small><i>(H:m AM/PM)</i></small>'
				),
			array(
				'name' 		=> 'end_time', 
				'type' 		=> 'time', 
				'label' 	=> 'End Time <small><i>(H:m AM/PM)</i></small>'
			),
			array(
				'name' 		=> 'activity', 
				'type' 		=> 'text',
				'label' 	=> 'Activity'
			),
			array(
				'name' 		=> 'supervisor_name', 
				'type' 		=> 'text',
				'label' 	=> 'Supervisor Name'
			),
			array(
				'name' 		=> 'supervisor_phone', 
				'type' 		=> 'text',
				'label' 	=> 'Supervisor Phone'
			),
	);
?>
<?php 
if(isset($_POST['communitySerSubmit']) && $_POST['communitySerSubmit'] == 'Go'):
	unset($_POST['communitySerSubmit']);
	$timeDif = strtotime($_POST['start_time']) -  strtotime($_POST['end_time']);
	$_POST['hours_worked'] = abs($timeDif);
	$insert = $score->save_service($_POST);
	
endif;

?>
<div class="Message">
	<?= ($insert)?$insert:''; ?>
</div>
<form class="form" action="" id="scroboafd_form" method="POST" accept-charset="utf-8">
	<?php $current_user = wp_get_current_user(); ?>
	<input type="hidden" value="<?= $current_user->id; ?>" name="player_id">
	<?php foreach($forms as $k => $item): ?>
		<div class="form-group">
			<label for="<?= $item['name'];  ?>"><?= $item['label'];  ?></label>
			<?php 
			$postV = (isset($_POST[$item['name']]))?$_POST[$item['name']]:'';
			 switch ($item['type']) {
				case 'time':
					 echo '<input type="time" id="'.$item['name'].'" value="'.$postV.'" name="'.$item['name'].'" class="form-control" />';
					break;
				
				default:
					echo '<input type="text" id="'.$item['name'].'" value="'.$postV.'" name="'.$item['name'].'" class="form-control" />';
					break;
			} ?>
			
		</div>
	<?php endforeach; ?>
	<br>
	<div style="display: block; overflow: hidden; width:100%;">
	<input type="submit" name="communitySerSubmit" style="float: left;" class="btn btn-primary" value="Go" />	
	</div>
	
</form>