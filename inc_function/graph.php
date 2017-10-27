<?php 

if(!function_exists('scoreboard_graph_view')){
	function scoreboard_graph_view(){ ?>
		<div class="wrap" id="scoreBoardFull">
			<h1 class="wp-heading-inline">Score Board Graph</h1>
			<?php global $score;
			
			if(isset($_GET['state']) && $_GET['state'] != '' && $_GET['category'] == ''){
				$alldatas = $score->admin_search($_GET['state']);
			}elseif(isset($_GET['category']) && $_GET['category'] != ''){
				$alldatas = $score->admin_search_cat($_GET['category'], $_GET['state']);
			}else{
				$alldatas = $score->admin_search();	
			}
			
			$allstate = array(
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
								);
			$allcategorys = array(
							"attend" => "Attendance",
							"absent" => "Absent",
							"gpa" => "GPA",
							"infractions" => "Infractions",
							"community_service" => "Community Service",
							"extracurricular" => "Extra Curricular",
							"act" => "ACT",
							"sat" => "SAT",
							"recommendation" => "Letter of Recommendation",
							"job" => "Job/Internship",
							"social_share" => "Social Share",
							"savings_account" => "Checking/Savings Account"
			);
			?>
			<div id="searchForm">
				<form action="<?= admin_url() . 'admin.php'; ?>" method="get" accept-charset="utf-8">
				<input type="hidden" name="page" value="score-board-graph">
					<div class="form-group">
						<label for="state">State:</label>
						<select name="state" id="state">
							<option value="">Select State</option>
							<?php foreach($allstate as $k => $state): ?>
							<option <?= (isset($_GET['state']) && $_GET['state'] == $k)?'selected':'';  ?>  value="<?= $k;  ?>"><?= $state; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="category">Category</label>
						<select name="category">
							<option value="">Select Category</option>
							<?php foreach($allcategorys as $ct => $sCat): ?>
							<option <?= (isset($_GET['category']) && $_GET['category'] == $ct)?'selected':'';  ?> value="<?= $ct;  ?>"><?= $sCat; ?></option>
							<?php endforeach ?>
						</select>
					</div>
					<input style="margin-top:10px;" type="submit" class="button button-primary" value="Go" name="submit"/>
				</form>
			</div>
			<hr style="float: left; width:100%;">
			<h2 class="pertitle">Overall Performance by <?= (isset($_GET['state']) && $_GET['state'] != '')?'School':'State';   ?></h2>
			<div id="graphDiv" style="width: 100%; height: 700px;"></div>
			<hr>
			<?php 
			$show = false;
			if(isset($_GET['submit']) && $_GET['category'] =='' ){
				$show = true;
			}elseif(!isset($_GET['submit'])){
				$show = true;
			}
			if($show): ?>
			<div id="detailsgraph">
				<table style="margin-bottom:20px;" class="widefat fixed details" cellspacing="1">
					<h2 class="text-center">Chart Details</h2>
					<thead>
						<tr>
							<th>Name</th>
							<th>Session Count</th>
							<th>Total Hours</th>
							<th>Average per Session</th>
							<th>Total Point</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($alldatas as $sdata):
						$name = (isset($_GET['state']) && $_GET['state'] != '')?$sdata->school:$sdata->state;
						?>
						<tr>
							<td><?= $name;  ?></td>
							<td><?= $sdata->count;  ?></td>
							<td><?= number_format($sdata->totalhours / 3600, 2, '.', '');  ?></td>
							<td><?= number_format($sdata->average, 2, '.', '');  ?></td>
							<td><?= number_format($sdata->etotal, 2, '.', '');  ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<?php endif; ?>
		</div>
		<script src="<?= PATH;  ?>/js/amcharts.js" type="text/javascript"></script>
        <script src="<?= PATH;  ?>/js/pie.js" type="text/javascript"></script>
        <script>
			var chart = AmCharts.makeChart("graphDiv", {
			  "type": "pie",
			  "startDuration": 0,
			   "theme": "light",
			  "addClassNames": true,
			  "legend":{
			   	"position":"right",
			    "marginRight":100,
			    "autoMargins":false
			  },
			  "innerRadius": "30%",
			  "defs": {
			    "filter": [{
			      "id": "shadow",
			      "width": "300%",
			      "height": "300%",
			      "feOffset": {
			        "result": "offOut",
			        "in": "SourceAlpha",
			        "dx": 0,
			        "dy": 0
			      },
			      "feGaussianBlur": {
			        "result": "blurOut",
			        "in": "offOut",
			        "stdDeviation": 5
			      },
			      "feBlend": {
			        "in": "SourceGraphic",
			        "in2": "blurOut",
			        "mode": "normal"
			      }
			    }]
			  },
			  "dataProvider": [
			  <?php foreach($alldatas as $si):
			  if(isset($_GET['submit']) && $_GET['category'] != ''):
			  	$country = $si->cname;
			  else:
			   	$country = ($_GET['state'])?$si->school:$si->state;	
			  endif;

			  ?>
			  {
			    "country": "<?= $country;  ?>",
			    "litres": <?= number_format($si->etotal, 2, '.', '');  ?>
			  },
			  <?php endforeach; ?>
			  ],
			  "valueField": "litres",
			  "titleField": "country",
			  "export": {
			    "enabled": true
			  }
			});

			chart.addListener("init", handleInit);

			chart.addListener("rollOverSlice", function(e) {
			  handleRollOver(e);
			});

			function handleInit(){
			  chart.legend.addListener("rollOverItem", handleRollOver);
			}

			function handleRollOver(e){
			  var wedge = e.dataItem.wedge.node;
			  wedge.parentNode.appendChild(wedge);
			}
			jQuery(function($){
				$("#searchForm select").chosen();
			});
        </script>
	<?php }
}
?>