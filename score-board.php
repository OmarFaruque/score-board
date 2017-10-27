<?php
/*
Plugin Name: Score Board
Plugin URI: http://larasoftbd.com/
Description: Score board for Contest, Game, Local Game, Scool Game etc.
Author: ronymaha
Author URI: http://larasoftbd.com/
Text Domain: score-board
Version: 1.0.1
*/

include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 
include_once('inc_function/scoreboard-db.php');
include_once('inc_function/admin.php');
include_once('inc_function/support-function.php'); 
define('PATH', plugins_url() . '/score-board');
define('FILEPATH', plugin_dir_path(__FILE__));
$score = new SCORE;

add_action('wp_enqueue_scripts', 'scroe_board_script');
function scroe_board_script(){
	wp_enqueue_script( 'scro-board-js', PATH . '/js/chosen.jquery.js', array( 'jquery' ), '20171508' );
	wp_enqueue_script( 'charts-light-js', PATH . '/js/scoreboard.js', array( 'jquery' ), '20171708' );
	

	wp_enqueue_style( 'amchart', 'https://www.amcharts.com/lib/3/plugins/export/export.css', array(), '4.0.5', 'screen' );
	wp_enqueue_style( 'choosen-css', PATH . '/css/chosen.css', array(), '5.0.9', 'screen' );
	wp_enqueue_style( 'score-board-css', PATH . '/css/scoreboad.css', array(), '9.5.9', 'screen' );
	wp_enqueue_script( 'jquery-ui-autocomplete' );
}

function score_board_input_form(){
	/* Form Save Process */
	global $score, $post;
	$allposts = '';
	if(isset($_GET['delete']) && $_GET['delete'] != ''){
		$mess .= $score->score_delete($_GET['delete']);
	}

	/* End Form Save Process */
	?>
		
			<div class="pls_login ">
			<div class="jumbotron">

  				<h1 class="display-3 mb0">Score Board</h1>
  				<div id="boardmenu">

  					<ul class="list-inline" id="bmenu">
  						<li><a class="btn btn-primary" href="<?= get_the_permalink( $post->ID, false );  ?>" title="">Score Board</a></li>
  						<li><a class="btn btn-primary" href="<?= get_the_permalink( $post->ID, false );  ?>?sb=new" title="">Add New</a></li>
  						<li><a class="btn btn-primary" href="<?= get_the_permalink( $post->ID, false );  ?>?sb=csh" title="">Add Community Service</a></li>
  					</ul>
  				</div>
				<hr class="my-4">
				<!-- Score Board Search Form -->
				<?php if(!isset($_GET['sb'])): ?>
				<form id="scoreboardSearch" action="" method="get">
					<div class="row">
						<div class="col-md-3 col-sm-3 col-xs-6">
							<div class="form-group">
								<input type="text" value="<?= @$_GET['player_name'];  ?>" placeholder="Player Name" name="player_name" id="player_name" class="form-control" />
							</div>
						</div>

						<div class="col-md-3 col-sm-3 col-xs-6">
							<div class="form-group">
							<select data-placeholder="Select Schoool" name="school" class="form-control" id="school">
								<?php $s=0; foreach($score->option_array('school') as $school): ?>
									<option <?= (@$_GET['school'] == $school)?'selected':''; ?> value="<?= ($s != 0)?$school:'';  ?>"><?= $school;  ?></option>
								<?php $s+=1; endforeach; ?>
							</select>
							</div>
						</div>

						<div class="col-md-3 col-sm-3 col-xs-6">
							<div class="form-group">
							<select data-placeholder="Select State" name="state" class="form-control" id="state">
							
								<?php $c = 0; foreach($score->option_array('state') as $state): ?>
									<option <?= (@$_GET['state'] == $state)?'selected':''; ?> value="<?= ($c != 0)?$state:'';  ?>"><?= $state;  ?></option>
								<?php $c+=1; endforeach; ?>
							</select>
							</div>
						</div>
						<div class="col-md-3 col-sm-3 col-xs-6">
							<div class="form-group">
								<input type="submit" name="search" class="btn btn-block btn-lg btn-primary" value="Search">
							</div>
						</div>
					</div>
				</form>
				<?php endif;  ?>
				<div id="searchResults">
					<?php if(isset($_GET['edit'])): ?>
					<?php do_action( 'editresult' ); ?>		
					<?php elseif(!isset($_GET['sb'])): ?>
					<?php do_action( 'searchresult' ); ?>		
					<?php endif; ?>
				</div>
				<!-- New Score -->
				<?php if(isset($_GET['sb']) && $_GET['sb'] == 'csh'): ?>
				<?php include( 'inc_part/new_community_form.php' ); ?>
				<?php elseif(isset($_GET['sb']) || isset($_GET['s_edit']) ): ?>
				<?php include( 'inc_part/new_form.php' ); ?>
				<?php endif; ?>
  			</div>
  						    	<!--availableTags -->

  			<script>
  				jQuery(function($){
  					$("#scroboafd_form select, #scoreboardSearch select").chosen();
  					 var availableTags = [<?= output_post_list('sp_player');  ?>];
  					 var superviers = [<?= $score->item_array('supervisor');  ?>];
  					 var school = [<?= $score->item_array('school');  ?>];
  					 var city = [<?= $score->item_array('city');  ?>];
  					$( "#player_name" ).autocomplete({
			      		source: availableTags
			    	});
			    	$( "input#supervisor" ).autocomplete({
			      		source: superviers
			    	});
			    	$( "input#school" ).autocomplete({
			      		source: school
			    	});
			    	$( "input#city" ).autocomplete({
			      		source: city
			    	});

  				})
  			</script>
		</div>
	<?php
}
add_shortcode( 'score-board', 'score_board_input_form' );

