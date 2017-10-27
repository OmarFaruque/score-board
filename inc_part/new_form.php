<?php 
/*
* Score Board New
*/
?>
				<?php 
				if(!is_user_logged_in()){
					?>
					<p>Please login to access this page...
						<br/><a style="float: left;" class="btn btn-primary" href="<?= get_home_url() . '/login'; ?>" title="Login">Login</a><br/>
					<span class="clearfix"></span>
					</p>

					<?php return false;
				}
						if(isset($_POST['submit'])){
							unset($_POST['submit']);
							$allposts = $_POST;
							/* Check if have day access */
							$dbdate = $score->get_top_date($_POST['player_name']);
							if($dbdate != 'error'):
							$timeDif = strtotime(date('Y-m-d H:i:s')) -  strtotime($dbdate);
							$dayleft = ceil(abs($timeDif) / 86400);
							if($dayleft > 50){
								echo "You have't right permission to access this page.<br/>";
								echo '<a style="mergin-top:10px;" class="btn btn-primary" href="'.get_the_permalink( $post->ID, false ).'" title="">Back</a>';
								return false;
							}
							endif;
							
							$_POST['attendance'] 		= attendance_convert($_POST['attend'], $_POST['absent']);
							$_POST['gpa'] 				= gpa_convert($_POST['gpa']);
							$_POST['community_service'] = 0;

							if(isset($_POST['id']) && $_POST['id'] != ''){
								$mess .= $score->update_score($_POST);
							}else{
								$mess .= $score->save_score($_POST);	
							}
						}
						
					?>
				<form class="form" action="" id="scroboafd_form" method="POST" accept-charset="utf-8">
				<?php 
					global $score;
					$values = (isset($_GET['s_edit']) && $_GET['s_edit'] != '')?$score->get_single_player($_GET['s_edit']):array();


						$timeDif = 0;
						if(count($values) > 0){
							$dbdate = $score->get_top_date($values->player_name);
							$timeDif .= strtotime(date('Y-m-d H:i:s')) -  strtotime($dbdate);
							$values->gpa = 	gpa_revert($values->gpa);
							$values->community_service = community_service_revert($values->community_service);
						}
						
					
						$dayleft = ceil(abs($timeDif) / 86400);

						if(isset($_GET['s_edit']) && $dayleft > 5 ){
							echo "you have't permission to access this page";
							return false;
						}

					$fields = array(
						array(
							'name' 		=> 'attend', 
							'type' 		=> 'number', 
							'label' 	=> 'Attendance <small><i>(Present day)</i></small>',
							'min' 		=> 0,
							'required' 	=> 1,
							'step' 		=> 1,
							'value' 	=> (count($values) > 0)?$values->attend:''
						),
						array(
							'name' 		=> 'absent', 
							'type' 		=> 'number', 
							'label' 	=> 'Absent <small><i>(Absent day)</i></small>',
							'min' 		=> 0,
							'required' 	=> 1,
							'step' 		=> 1,
							'value' 	=> (count($values) > 0)?$values->absent:''
						),
						array(
							'name' 		=> 'gpa', 
							'type' 		=> 'number', 
							'label' 	=> 'GPA', 
							'min' 		=> 1.0,
							'max' 		=> 6.0,
							'step' 		=> .01,
							'required' 	=> 1,
							'value' 	=> (count($values) > 0)?$values->gpa:''
						),
						array(
							'name' 		=> 'infractions', 
							'type' 		=> 'select', 
							'label' 	=> 'Infractions',
							'options' 	=> array(
								'' 	=> 'No Infraction',
								-3 	=> 'Detention',
								-5	=> 'Suspension',
								-10 => 'Expulsion',
							),
							'value' 	=> (count($values) > 0)?$values->infractions:''
						),
						/*array(
							'name' 		=> 'community_service', 
							'type' 		=> 'number', 
							'label' 	=> 'Community Service', 
							'min' 		=> 1.0,
							'max' 		=> 12.5,
							'step' 		=> .5,
							'value' 	=> (count($values) > 0)?$values->community_service:''
						),*/
						array(
							'name' 		=> 'extracurricular', 
							'type' 		=> 'select', 
							'label' 	=> 'Extracurricular Activities <small><i>(School Club, Team)</i></small>',
							'options' 	=> array(
								5 	=> 'Yes',
								0	=> 'No'
							),
							'value' 	=> (count($values) > 0)?$values->extracurricular:''
						),
						array(
							'name' 		=> 'act', 
							'type' 		=> 'select', 
							'label' 	=> 'ACT',
							'options' 	=> array(
								1 	=> 'Yes',
								0	=> 'No'
							),
							'value' 	=> (count($values) > 0)?$values->act:''
						),
						array(
							'name' 		=> 'sat', 
							'type' 		=> 'select', 
							'label' 	=> 'SAT',
							'options' 	=> array(
								1 	=> 'Yes',
								0	=> 'No'
							),
							'value' 	=> (count($values) > 0)?$values->sat:''
						),
						array(
							'name' 		=> 'recommendation', 
							'type' 		=> 'select', 
							'label' 	=> 'Letter of Recommendation',
							'options' 	=> array(
								3 	=> 'Yes',
								0	=> 'No'
							),
							'value' 	=> (count($values) > 0)?$values->recommendation:''
						),
						array(
							'name' 		=> 'job', 
							'type' 		=> 'select', 
							'label' 	=> 'Job/Internship',
							'options' 	=> array(
								10 	=> 'Yes',
								0	=> 'No'
							),
							'value' 	=> (count($values) > 0)?$values->job:''
						),
						array(
							'name' 		=> 'social_share', 
							'type' 		=> 'select', 
							'label' 	=> 'Share w/ GC via IG or Twitter Tag',
							'options' 	=> array(
								5 	=> 'Yes',
								0	=> 'No'
							),
							'value' 	=> (count($values) > 0)?$values->social_share:''
						),
						array(
							'name' 		=> 'savings_account', 
							'type' 		=> 'select', 
							'label' 	=> 'Checking/Savings Account',
							'options' 	=> array(
								3 	=> 'Yes',
								0	=> 'No'
							),
							'value' 	=> (count($values) > 0)?$values->savings_account:''
						),
						 
						/*array(
							'name' 		=> 'supervisor', 
							'type' 		=> 'text', 
							'label' 	=> 'Supervisor Name',
							'required' 	=> 1,
							'value' 	=> (count($values) > 0)?$values->supervisor:''
						),

						array(
							'name' 		=> 'supervisor_phone', 
							'type' 		=> 'text', 
							'label' 	=> 'Supervisor Phone/Mobile',
							'required' 	=> 1,
							'value' 	=> (count($values) > 0)?$values->supervisor_phone:''
						),
						array(
							'name' 		=> 'supervisor_email', 
							'type' 		=> 'email', 
							'label' 	=> 'Supervisor Email',
							'required' 	=> 1,
							'value' 	=> (count($values) > 0)?$values->supervisor_email:''
						),*/
						array(
							'name' 		=> 'counselor', 
							'type' 		=> 'text', 
							'label' 	=> 'Counselor Name',
							'required' 	=> 1,
							'value' 	=> (count($values) > 0)?$values->counselor:''
						),

						array(
							'name' 		=> 'counselor_phone', 
							'type' 		=> 'text', 
							'label' 	=> 'Counselor Phone/Mobile',
							'required' 	=> 1,
							'value' 	=> (count($values) > 0)?$values->counselor_phone:''
						),
						array(
							'name' 		=> 'counselor_email', 
							'type' 		=> 'email', 
							'label' 	=> 'Counselor Email',
							'required' 	=> 1,
							'value' 	=> (count($values) > 0)?$values->counselor_email:''
						),
						array(
							'name' 		=> 'school', 
							'type' 		=> 'text', 
							'label' 	=> 'School Name',
							'required' 	=> 1,
							'value' 	=> (count($values) > 0)?$values->school:''
						),
						array(
							'name' 		=> 'state', 
							'type' 		=> 'select',
							'label' 	=> 'State',
							'value' 	=> (count($values) > 0)?$values->country:'',
							'options' 	=> array(
										 "Alaska" => "Alaska",
						                  "Alabama" => "Alabama",
						                  "Arkansas" => "Arkansas",
						                  "American Samoa" => "American Samoa",
						                  "Arizona" => "Arizona",
						                  "California" => "California",
						                  "Colorado" => "Colorado",
						                  "Connecticut" => "Connecticut",
						                  "District of Columbia" => "District of Columbia",
						                  "Delaware" => "Delaware",
						                  "Florida" => "Florida",
						                  "Georgia" => "Georgia",
						                  "Guam" => "Guam",
						                  "Hawaii" => "Hawaii",
						                  "Iowa" => "Iowa",
						                  "Idaho" => "Idaho",
						                  "Illinois" => "Illinois",
						                  "Indiana" => "Indiana",
						                  "Kansas" => "Kansas",
						                  "Kentucky" => "Kentucky",
						                  "Louisiana" => "Louisiana",
						                  "Massachusetts" => "Massachusetts",
						                  "Maryland" => "Maryland",
						                  "Maine"=>"Maine",
						                  "Michigan"=>"Michigan",
						                  "Minnesota"=>"Minnesota",
						                  "Missouri"=>"Missouri",
						                  "Mississippi"=>"Mississippi",
						                  "Montana"=>"Montana",
						                  "North Carolina"=>"North Carolina",
						                  "North Dakota"=>"North Dakota",
						                  "Nebraska"=>"Nebraska",
						                  "New Hampshire"=>"New Hampshire",
						                  "New Jersey"=>"New Jersey",
						                  "New Mexico"=>"New Mexico",
						                  "Nevada"=>"Nevada",
						                  "New York"=>"New York",
						                  "Ohio"=>"Ohio",
						                  "Oklahoma"=>"Oklahoma",
						                  "Oregon"=>"Oregon",
						                  "Pennsylvania"=>"Pennsylvania",
						                  "Puerto Rico"=>"Puerto Rico",
						                  "Rhode Island"=>"Rhode Island",
						                  "South Carolina"=>"South Carolina",
						                  "South Dakota"=>"South Dakota",
						                  "Tennessee"=>"Tennessee",
						                  "Texas"=>"Texas",
						                  "Utah"=>"Utah",
						                  "Virginia"=>"Virginia",
						                  "Virgin Islands"=>"Virgin Islands",
						                  "Vermont"=>"Vermont",
						                  "Washington"=>"Washington",
						                  "Wisconsin"=>"Wisconsin",
						                  "West Virginia"=>"West Virginia",
						                  "Wyoming"=>"Wyoming"
								)
						),
						array(
							'name' 		=> 'city', 
							'type' 		=> 'text', 
							'label' 	=> 'City',
							'value' 	=> (count($values) > 0)?$values->city:''
						)
						
					);
				?>
				<?php if($mess != ''): ?>
  					<div class="message bg-info">
  						<p><?= $mess; ?></p>
  					</div>
  				<?php endif; ?>
				<p class="lead">Complete Form...</p>
				<?php $current_user = wp_get_current_user(); ?>
				<input type="hidden" value="<?= (!isset($_GET['s_edit']))?$current_user->display_name:$values->player_name;  ?>" name="player_name">
				<input type="hidden" value="<?= (!isset($_GET['s_edit']))?$current_user->id:$values->player_id;  ?>" name="player_id">
				<?php foreach($fields as $fld): ?>
				<div class="form-group">
					<?php switch($fld['type']): 
						case 'number':
					?>
					<label for="<?= $fld['name'];  ?>"><?= $fld['label'];  ?></label>
					<input <?= (isset($fld['required']))?'required':'';  ?>  <?= (isset($fld['step']))?'step="'.$fld['step'].'"':'';  ?> <?= ($fld['max'] )?'min="'.$fld['min'].'" max="'.$fld['max'].'"':'';  ?> type="<?= $fld['type'];  ?>" name="<?= $fld['name'];  ?>" id="<?= $fld['name'];  ?>" class="form-control" value="<?= (isset($_POST[$fld['name']]))?$allposts[$fld['name']]:$fld['value']; ?>" />
					<?php break; case 'select': ?>
					<label for="<?= $fld['name'];  ?>"><?= $fld['label'];  ?></label>
					<select  <?= (isset($fld['required']))?'required':'';  ?> name="<?= $fld['name'];  ?>" id="<?= $fld['name'];  ?>">
						<?php foreach($fld['options'] as $op => $optn): ?>
							<option <?php if(isset($_POST[$fld['name']]) && $_POST[$fld['name']] == $op ) { echo 'selected'; }elseif(count($values) > 0 && $values->$fld['name'] == $op){echo 'selected';} ?> value="<?= $op;  ?>"><?= $optn;  ?></option>
						<?php endforeach; ?>
					</select>
					<?php break; case 'text': case 'email': case 'time': ?>
					<label for="<?= $fld['name'];  ?>"><?= $fld['label'];  ?></label>
					<input <?= (isset($fld['required']))?'required':'';  ?>   type="<?= $fld['type'];  ?>" name="<?= $fld['name'];  ?>" id="<?= $fld['name'];  ?>" class="form-control" value="<?= (isset($_POST[$fld['name']]))?$_POST[$fld['name']]:$fld['value']; ?>" />
					<?php break; endswitch; ?>
				</div>
				<?php endforeach; ?>
				<br><br>
				<p class="lead">
					<?php if(isset($_GET['s_edit']) && $_GET['s_edit'] != ''): ?>
						<input type="hidden" name="id" value="<?= $_GET['s_edit']; ?>">
						<input type="submit" name="submit" class="btn button button-primary btn-primary btn-lg" value="Update" role="button" />
					<?php else: ?>
						<input type="submit" name="submit" class="btn btn-primary btn-lg" value="Submit" role="button" />
					<?php endif; ?>
				</p>
				</form>


