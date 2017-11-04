<?php
/*
* Support Function
*/
 
function output_post_list($name) {
    global $wpdb;

    $custom_post_type = $name; // define your custom post type slug here

    // A sql query to return all post titles
    $results = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type = %s and post_status = 'publish'", $custom_post_type ), ARRAY_A );

    // Return null if we found no results
    if ( ! $results )
        return;

    // HTML for our select printing post titles as loop
    $output = array();

    foreach( $results as $index => $post ) array_push($output, '"'. $post['post_title'] . '"');   

    // get the html
    return implode(',', $output);
}


function search_from_db(){ ?>
    
    <?php
     global $score, $post;
     if(isset($_GET['search'])){
        unset($_GET['search']);
      }
        $result = $score->search_results($_GET); 

        $current_user = wp_get_current_user();
        $allservices = $score->get_all_service($current_user->id);

     ?>
    <div class="singleboard">
        <?php 
          $cnt = 0; if(is_array(@$result) ): foreach($result as $r => $item): 
          $count = $score->count($item->player_id);
          $user = get_userdata( $item->player_id );
          
        


        ?>
        <div class="card">
            <div class="card__header">
                <h4 class="sp-table-caption"><?= $user->display_name;  ?></h4>
            </div>
            <div class="card__content">
                <div class="rangTag">
                  <div class="ribbon">
                    <span>Rank#<?= $cnt+1; ?></span>
                  </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p class="text-left"><b>School: </b><?= $item->school;  ?></p>
                    </div>
                    <div class="col-md-6">
                       <p class=""><b>City: </b><?= $item->city;  ?></p>
                    </div>
                  </div>
                      <div id="chartdiv_<?= $cnt; ?>" style="width: 100%; height: 280px;"></div>
                      <div class="details">
                        <div class="row">
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <ul class="list-inline">
                            <?php if(is_user_logged_in() && $user->ID != $item->player_id): ?>
                              <li><a href="<?= get_the_permalink( $post->ID, false );  ?>?edit=<?= $item->player_name;  ?>" title="">Edit</a></li>
                            <?php endif; ?>
                            </ul>
                          </div>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                            <a class="btn text-right details pull-right" href="javascript:void(0)" title="Details">Details</a>
                          </div>
                        </div>
                      
                        <div class="details_Content" style="display:none;">
                          <table class="table table-inverse table-bordered table-hover">

                            <caption><h3 class="text-center">Details</h3></caption>
                            <thead>
                              <tr>
                                <th>Categories</th>
                                <th>Weighted %</th>

                                <?php for($s = 1 ; $s <= $count->total; $s++): ?>
                                <th>Qtr<?= $s; ?></th>
                                <?php endfor; ?>
                              </tr>
                            </thead>
                            <tbody>
                             <?php $cn = 1; 
                              foreach($count as $l => $cart_l): if($l != 'total' && $l != 'community_service'): 
                             $alls = $score->each_details($item->player_id, $l);
                             ?>  
                              <tr>
                                 <td><?= ucfirst( str_replace('_', ' ', $l) );  ?></td>  
                                 <td><?=  sprintf("%.2f%%", (int)$cart_l / $count->total );  ?></td>
                                 <?php foreach($alls as $ac => $al): ?>
                                    <td><?= $al->$l;  ?></td>
                                 <?php endforeach; ?>
                              </tr>
                              <?php endif; $cn++; endforeach; ?>
                            </tbody>
                          </table>
                          <br>
                          <?php 
                          $current_user = wp_get_current_user();          
                          if(is_user_logged_in() && $current_user->ID == $item->player_id): ?>
                           <table style="margin-top:15px;" class="table table-inverse table-bordered table-hover">
                              <caption><h3 class="text-center">Service Details</h3></caption>
                              <thead>
                                <tr>
                                  <th>Date & Time</th>
                                  <th>Start Time</th>
                                  <th>End Time</th>
                                  <th>activity</th>
                                  <th>Supervisor</th>
                                  <th>Supervisor Phone</th>
                                  <th>Total Time</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php $totalH = 0; foreach($allservices as $service): ?>
                                  <tr>
                                    <td> <?= date("d-m-Y h:i A", strtotime($service->date)); ?> </td>
                                    <td><?= date('h:i A', strtotime($service->start_time)); ?></td>
                                    <td><?= date('h:i A', strtotime($service->end_time)); ?></td>
                                    <td><?= $service->activity; ?></td>
                                    <td><?= $service->supervisor_name; ?></td>
                                    <td><?= $service->supervisor_phone; ?></td>
                                    <td><?= gmdate("H:i:s", $service->hours_worked); ?></td>
                                     <?php $totalH+= $service->hours_worked; ?>
                                  </tr>
                                <?php endforeach; ?>
                              </tbody>
                               <tfoot>
                                  <tr>
                                    <th style="text-align: right;" colspan="6"><b>Total Working Time</b></th>
                                    <th><b><?=  number_format($totalH / 3600, 2); ?></b></th>
                                  </tr>
                                </tfoot>
                           </table>
                         <?php endif; //show if user login in and can see only own data ?>
                        </div>
                        <div class="note" style="display: none;">
                          <small><i>Note: A few of the categories are listed on the chart if you turn your mobile device horizontal. To see all categories, log into your account on a desktop or laptop.</i></small>
</div>
         
                        
                      </div>
                </div>
            </div>
        <script src="<?= PATH;  ?>/js/amcharts.js" type="text/javascript"></script>
        <script src="<?= PATH;  ?>/js/pie.js" type="text/javascript"></script>
        <script src="https://www.amcharts.com/lib/3/serial.js" type="text/javascript"></script>
        <script>
          var width  = (screen.width > 0)?screen.width:window.innerWidth;
          
          if(width > 568){
          var chart = AmCharts.makeChart( "chartdiv_<?= $cnt; ?>", {
            "type": "pie",
            "theme": "light",

            "dataProvider": [ 
          <?php 
            foreach($count as $k => $cart_i): if($k != 'total'): 
              switch($k):
              case 'community_service':
              $countS = $score->count_service($item->player_id);
              $serviceHours = (int)$countS->total_second / 3600;
              $hoursAve = $serviceHours / $count->total;
              $hours = community_service_convert($hoursAve);
              $main = (int)$cart_i / $count->total;
              $hours = $hours + $main;
            ?>    
            {
              "title": "<?= ucfirst( str_replace('_', ' ', $k) );  ?>",
              "value": <?= $hours;  ?>
            }, 
          <?php 
            break;
              case 'savings_account':
            ?>    
            {
              "title": "Bank Account",
              "value": <?= (int)$cart_i / $count->total;  ?>
            }, 
          <?php 
            break;
            case 'act':
            ?>    
            {
              "title": "ACT",
              "value": <?= (int)$cart_i / $count->total;  ?>
            }, 
          <?php 
            break;
            case 'sat':
            ?>    
            {
              "title": "SAT",
              "value": <?= (int)$cart_i / $count->total;  ?>
            }, 
          <?php 
            break;
            case 'gpa':
            ?>    
            {
              "title": "GPA",
              "value": <?= (int)$cart_i / $count->total;  ?>
            }, 
          <?php 
            break;
            default:
            ?>    
            {
              "title": "<?= ucfirst( str_replace('_', ' ', $k) );  ?>",
              "value": <?= (int)$cart_i / $count->total;  ?>
            }, 
          <?php   
            endswitch; endif; endforeach; 
          ?>
           ],
            "valueField": "value",
            "titleField": "title",
            "outlineAlpha": 0.4,
            "depth3D": 15,
            "balloonText": "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>",
            "angle": 30,
            "export": {
              "enabled": false
            }
          } );
        }else{
         jQuery(".note").show();
          var chart = AmCharts.makeChart( "chartdiv_<?= $cnt; ?>", {
            "type": "serial",
            "theme": "light",
            "startDuration": 2,
            "dataProvider": [ 
          <?php 
            $colors = array('19F921', '28D6F9', '0F41A2', '26069D', '6E0FA3', 'B0DE09', '04D215', '0D8ECF', '0D52D1', '2A0CD0', '8A0CCF', 'CD0D74', '754DEB', 'DDDDDD', '333333');
            $cnt = 0;
          /*  $arrayItems = array(
              'total' => 1,
              'attendance' => 1,
              'gpa' => 35,
              'infractions' => 3,
              'community_service' => 0,
              'extracurricular' => 5,
              'act' => 1,
              'sat' => 1,
             /* 'recommendation' => 3,
              'job' => 10,*/
             /* 'social_share' => 5,
              'savings_account' => 3
            );
            $count = array_diff_key($count, $arrayItems);*/

            unset($count->infractions);
            unset($count->extracurricular);
            unset($count->sat);
            unset($count->act);
            unset($count->recommendation);
            unset($count->savings_account);
            
            
            



            foreach($count as $k => $cart_i): if($k != 'total'): 
              switch($k):
              case 'community_service':
              $countS = $score->count_service($item->player_id);
              $serviceHours = (int)$countS->total_second / 3600;
              $hoursAve = $serviceHours / $count->total;
              $hours = community_service_convert($hoursAve);
              $main = (int)$cart_i / $count->total;
              $hours = $hours + $main;
            ?>    
            {
              "title": "Serv.",
              "value": <?= $hours;  ?>,
              "color": "#<?= $colors[$cnt];  ?>"
            }, 
          <?php 
            break;
            case 'savings_account':
            ?>    
            {
              "title": "Bank Account",
              "value": <?= (int)$cart_i / $count->total;  ?>,
              "color": "#<?= $colors[$cnt];  ?>"
            }, 
          <?php 
            break;
            case 'act':
            ?>    
            {
              "title": "ACT",
              "value": <?= (int)$cart_i / $count->total;  ?>,
              "color": "#<?= $colors[$cnt];  ?>"
            }, 
          <?php 
            break;
            case 'sat':
            ?>    
            {
              "title": "SAT",
              "value": <?= (int)$cart_i / $count->total;  ?>,
              "color": "#<?= $colors[$cnt];  ?>"
            }, 
          <?php 
            break;
            case 'gpa':
            ?>    
            {
              "title": "GPA",
              "value": <?= (int)$cart_i / $count->total;  ?>,
              "color": "#<?= $colors[$cnt];  ?>"
            }, 
          <?php 
            break;
            case 'attendance':
            ?>
            {
              "title": "Attn.",
              "value": <?= (int)$cart_i / $count->total;  ?>,
              "color": "#<?= $colors[$cnt];  ?>"
            }, 
            <?php
            break;
            case 'social_share':
            ?>
            {
              "title": "SS",
              "value": <?= (int)$cart_i / $count->total;  ?>,
              "color": "#<?= $colors[$cnt];  ?>"
            }, 
            <?php
            break;

            default:
            ?>    
            {
              "title": "<?= ucfirst( str_replace('_', ' ', $k) );  ?>",
              "value": <?= (int)$cart_i / $count->total;  ?>,
              "color": "#<?= $colors[$cnt];  ?>"
            }, 
          <?php   
            endswitch; $cnt++; endif; endforeach; 
          ?>
           ],
            "valueAxes": [{
        "position": "left",
        "axisAlpha":0,
        "gridAlpha":0
    }],
    "graphs": [{
        "balloonText": "[[category]]: <b>[[value]]</b>",
        "colorField": "color",
        "fillAlphas": 0.85,
        "lineAlpha": 0.1,
        "type": "column",
        "topRadius":1,
        "valueField": "value"
    }],
    "depth3D": 40,
  "angle": 30,
    "chartCursor": {
        "categoryBalloonEnabled": false,
        "cursorAlpha": 0,
        "zoomable": false
    },
    "categoryField": "title",
    "categoryAxis": {
        "gridPosition": "start",
        "axisAlpha":0,
        "gridAlpha":0

    },
    "export": {
      "enabled": true
     }
    },0 );
    } <!-- End check width -->
        </script>
        <?php $cnt++; endforeach; endif; ?>
    </div>
    <!-- Chart code -->

<?php }
add_action( 'searchresult', 'search_from_db', 5 );


/* Edit Page */
if(!function_exists('edit_page')){
  function edit_page(){ 
  global $score, $post;
  if(!is_user_logged_in()){
    echo '<script>window.location.replace('.get_the_permalink( $post->ID, false ).')</script>';
    return false;
  }
  
  ?>
    <div class="card">
            <div class="card__header">
                <h4 class="sp-table-caption"><?= $_GET['edit'];  ?></h4>
            </div>
            <div class="card__content">
              <?php 
                $players = $score->player_details($_GET['edit']);
              ?>
              <table class="table table-inverse table-responsive table-hover">
                <thead>
                  <tr>
                    <th>Supervisor</th>
                    <th>Attendance</th>
                    <th>GPA</th>
                    <th>CS</th>
                    <th>Job</th>
                    <th>Total</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($players as $p):
                  $total = $p->attendance + $p->gpa + $p->infractions + $p->community_service + $p->extracurricular + $p->act + $p->sat + $p->recommendation + $p->job + $p->social_share + $p->savings_account;
                  ?>
                  <tr>
                    <td><?= $p->supervisor;  ?></td>
                    <td><?= $p->attendance;  ?></td>
                    <td><?= $p->gpa;  ?></td>
                    <td><?= $p->community_service;  ?></td>
                    <td><?= $p->job;  ?></td>
                    <td><?= $total;  ?></td>
                    <td>
                      <a href="<?= get_the_permalink( $post->ID, false );  ?>?sb=update&s_edit=<?= $p->id;  ?>" title="Edit">Edit</a>
                      |
                      <a href="<?= get_the_permalink( $post->ID, false );  ?>?delete=<?= $p->id;  ?>" title="Edit">Delete</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
                </tbody>
              </table>
            </div>
    </div>
  <?php }
  add_action( 'editresult', 'edit_page', 5 );
}



/* Converter */
function attendance_convert($attend, $absent){
    $days = $absent / $attend;
    $days = $days * 100;
    $days = 100 - $days;

    if($days <= 89 && $days >= 80 ){
      $post = 5;
    }elseif( $days <= 100 && $days >= 90 ){
      $post = 10;
    }else{
      $post = 1;
    }
    return $post;
}



// GPA 
function gpa_convert($post){
  $gpa = $post;
    if($gpa >= 4.1){
      $post = 35;
    }elseif($gpa == 4.0){
      $post = 30; 
    }elseif($gpa >= 3.5 && $gpa <= 3.9 ){
      $post = 25;
    }elseif($gpa <= 3.49 && $gpa >= 3.2 ){
      $post = 20;
    }elseif($gpa <= 3.19 && $gpa >= 3.0){
      $post = 15;
    }elseif($gpa <= 2.90 && $gpa >= 2.50){
      $post = 10;
    }elseif($gpa <= 2.49 && $gpa >= 2.0){
      $post = 5;
    }
    return $post;
}

function gpa_revert($post){
  $gpa = $post;
    if($gpa >= 31){
      $post = 4.1;
    }elseif($gpa == 30){
      $post = 4.0; 
    }elseif($gpa >= 25 && $gpa <= 29 ){
      $post = 3.7;
    }elseif($gpa <= 24 && $gpa >= 20 ){
      $post = 3.3;
    }elseif($gpa <= 19 && $gpa >= 15){
      $post = 3.1;
    }elseif($gpa <= 14 && $gpa >= 10){
      $post = 2.6;
    }elseif($gpa <= 10 && $gpa >= 5){
      $post = 2.2;
    }
    return $post;
}

function community_service_convert($post){
    $community = $post;
    if($community > 12){
      $post = 30;
    }elseif($community <= 12 && $community >= 8.0 ){
      $post = 20;
    }elseif($community <= 7.9 && $community >= 4.0 ){
      $post = 15;
    }elseif($community <= 4.9 && $community >= 1.0 ){
      $post = 10;
    }else{
      $post = 5;
    }
    return $post;
}

function community_service_revert($post){
    $community = $post;
    if($community > 20){
      $post = 12.5;
    }elseif($community <= 20 && $community >= 15 ){
      $post = 10;
    }elseif($community <= 14 && $community >= 11 ){
      $post = 6.0;
    }elseif($community <= 10 && $community >= 5 ){
      $post = 3.0;
    }
    return $post;
}
?>
