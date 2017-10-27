<?php 
/*
* Score Board Admin
*/

/** Step 2 (from text above). */
add_action( 'admin_menu', 'scoreboard_menu' );

/** Step 1. */
function scoreboard_menu() {
	add_menu_page( 'Score Board', 'Score Board', 'manage_options', 'score-board', 'scoreboard_list', '', 60 );
	add_submenu_page( 'score-board', 'Graph View', 'Graph View', 'manage_options', 'score-board-graph', 'scoreboard_graph_view' );
}

require_once 'graph.php';

function scoreboard_list(){
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	} 
	global $score;
	/* Delete Process */
	$mssg = '';
	$mssg .= (isset($_GET['delete']))?$score->score_delete($_GET['delete']):'';

	$alls = $score->search_results(array(), 3000);
	?>
	<div class="wrap" id="<?= (!isset($_GET['edit']))?'scoreBoardTable':'scoreBoardFull';  ?>">
	<h1 class="wp-heading-inline">Score Board List</h1>
		<?php if($mssg != ''): ?>
			<div class="messagediv">
				<span><?= $mssg; ?></span>
			</div>
		<?php endif; ?>
		<hr>
	<div id="scoreBoard" class="half">

		<?php if(!isset($_GET['view']) && !isset($_GET['s_edit'])): ?>
		<table class="widefat fixed" cellspacing="1">
			<thead>
				<tr>
					<th>Name</th>
					<th>Counselor</th>
					<th>Counselor Phone</th>
					<th>City</th>
					<th>School</th>
					<th>Total</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($alls as $s => $single):
				$count = $score->count($single->player_id);
				?>
				<tr class="" valign="middle">
					<td class="column-columnname"><?= $single->player_name;  ?></td>
					<td><?= $single->counselor;  ?></td>
					<td><?= $single->counselor_phone;  ?></td>
					<td><?= $single->city;  ?></td>
					<td><?= $single->school;  ?></td>
					<td><?= $single->alltotal;  ?>(<?= $count->total;  ?>)</td>
					<td>
					<a href="<?= admin_url( );  ?>admin.php?page=score-board&view=<?= $single->player_id; ?>" title="View">Details</a>
					&nbsp;|&nbsp;
					<a href="<?= admin_url( );  ?>admin.php?page=score-board&delete=<?= $single->player_id; ?>" title="Delete">Delete</a>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php elseif(isset($_GET['s_edit'])): ?>
			<p>
				<a class="button button-primary" href="<?= get_admin_url() . '?page=score-board'; ?>" title="Back">Back</a>
			</p>
			<?php
				
			 require_once( FILEPATH . 'inc_part/new_form.php' ); ?>
		<?php else: 
		$users = get_userdata( $_GET['view'] );
		$datas = $score->get_single($_GET['view']);
		$services = $score->get_all_service($_GET['view']);
		if(isset($_GET['serviceid'])){
			$dmsg = $score->delete_service($_GET['serviceid']);
			echo '<p style="color:red; font-size:15px; font-waight:bold;" class="message">'.$dmsg.'</p>';
		}
		?>
		<p>
			<a class="button button-primary" href="<?= get_admin_url() . '?page=score-board'; ?>" title="Back">Back</a>
		</p>
		<h3 class="detailsTitle"><?= $users->display_name;  ?> Details</h3>
		<?php foreach($datas as $k => $data): 
			$id = $data->id;
			unset($data->id); 
			unset($data->player_name); 
			unset($data->player_id); ?>
			<table style="margin-bottom:20px;" class="widefat fixed details" cellspacing="1">
				<thead>
					<tr>
						<th colspan="2" style="font-size:20px;"><b><i>#<?= $k+1;  ?></i></b>
						<span class="pull-right button button-primary"><a style="color: #fff;" href="<?= admin_url( );  ?>admin.php?page=score-board&s_edit=<?= $id; ?>" title="Edit Score">Edit</a></span>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($data as $s => $d): ?>
					<tr>
						<td><b><?= ucwords( str_replace('_', ' ', $s));  ?></b></td>
						<td><?php 
							switch($s):
							case 'start_time':
							case 'end_time':
								echo date('h:i A', strtotime($d));
							break;
							case 'date':
								echo date('Y/m/d h:i A', strtotime($d));
							break;
							default:
							echo $d; 
							endswitch;
							
						?></td>
					</tr>
					<?php endforeach; ?>
					<?php $diff = strtotime($data->end_time) - strtotime($data->start_time); ?>
					<tr>
						<td><b>Working Hours</b></td>
						<td><?= date('H:i:s', $diff);  ?></td>
					</tr>
				</tbody>
			</table>
			<hr>
		<?php endforeach; ?>
		<?php if(count($services) > 0): ?>
		<br><br>

		<h3 class="detailsTitle"><?= $users->display_name;  ?> Community Service Details</h3>
			<table style="margin-bottom:20px;" class="widefat fixed details" cellspacing="1">
				<thead>
					<tr>
						<th>Date & Time</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>activity</th>
                        <th>Supervisor</th>
                        <th>Supervisor Phone</th>
                        <th>Total Time</th>
                        <th>Action</th>
					</tr>
				</thead>
				  <tbody>
                    <?php $totalH = 0; foreach($services as $service): ?>
                          <tr>
                             <td> <?= date("d-m-Y h:i A", strtotime($service->date)); ?> </td>
                             <td><?= date('h:i A', strtotime($service->start_time)); ?></td>
                             <td><?= date('h:i A', strtotime($service->end_time)); ?></td>
                             <td><?= $service->activity; ?></td>
                             <td><?= $service->supervisor_name; ?></td>
                             <td><?= $service->supervisor_phone; ?></td>
                             <td><?= gmdate("H:i:s", $service->hours_worked); ?></td>
                             <td><a href="<?= admin_url() . 'admin.php?page=score-board&view='.$_GET['view'].'&serviceid='.$service->id.''; ?>" title="Delete">Delete</a></td>
                             <?php $totalH+= $service->hours_worked; ?>

                          </tr>
                        <?php endforeach; ?>
                 </tbody>
                        <tfoot>
                        <tr>
                        	<th style="text-align: right;" colspan="6"><b>Total Working Time</b></th>
                        	<th style="text-align: left;" colspan="2"><b><?=  number_format($totalH / 3600, 2) . ' H'; ?></b></th>
                        </tr>
                        </tfoot>
                   
			</table>
		<?php endif; //End chack if service amount are grater than 0 ?>
		<?php endif; ?>
		</div>
		<div id="infoirmation" class="half">
			<h3>ShortCode</h3>
			<p><b>For Template Use: </b><br>[score-board]</p>

			<p><b>For PHP Use:</b><br/> &lt;?php echo do_shortcode('[score-board]'); ?&gt;</p>
		</div>
	</div>
<?php }


/* Add Css / JS File  */
add_action( 'admin_enqueue_scripts', 'load_admin_style' );
function load_admin_style(){
		wp_enqueue_style( 'amchart', 'https://www.amcharts.com/lib/3/plugins/export/export.css', array(), '4.0.5', 'screen' );
		wp_enqueue_style( 'choosen-css', PATH . '/css/chosen.css', array(), '5.0.9', 'screen' );
        wp_enqueue_style( 'score-board-css', PATH . '/css/admin/score-board.css', false, '1.0.0' );
        wp_enqueue_script( 'score-board-js', PATH . '/js/scoreboard.js', array('jquery'), '20171508', true );
        wp_enqueue_script( 'scro-board-js', PATH . '/js/chosen.jquery.js', array( 'jquery' ), '20171508' );
        wp_enqueue_script( 'jquery-ui-autocomplete' );
}
?>