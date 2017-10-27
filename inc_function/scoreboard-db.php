<?php 
/*
* Score Board Database Functionality
*/

if(!class_exists('SCORE')){
	class SCORE{
		function __construct(){
			global $wpdb;
			$this->db 		= $wpdb;
			$table 			= $this->db->prefix . 'scoreboard';
			$servtable 		= $this->db->prefix . 'comm_service';
			$this->table 	= $table; 
			$this->srtable 	= $servtable;

			//DB for scoreboard

			if($this->db->get_var("SHOW TABLES LIKE '$this->table'") != $this->table) {
		     //table not in database. Create new table
		     $charset_collate = $this->db->get_charset_collate();
		     $sql = "CREATE TABLE $this->table (
		          id mediumint(10) NOT NULL AUTO_INCREMENT,
		          player_name varchar(400) NOT NULL,
		          attend varchar(100) NOT NULL,
		          absent varchar(100) NOT NULL,
		          gpa tinyint(30) NOT NULL,
		          infractions tinyint(30) NOT NULL,
		          community_service tinyint(5) NOT NULL DEFAULT 0,
		          extracurricular tinyint(30) NOT NULL,
		          act tinyint(30) NOT NULL,
		          sat tinyint(30) NOT NULL,
		          recommendation tinyint(30) NOT NULL,
		          job tinyint(30) NOT NULL,
		          social_share tinyint(30) NOT NULL,
		          savings_account tinyint(30) NULL,
		          counselor varchar(200) NOT NULL,
		          counselor_phone varchar(200) NOT NULL,
		          counselor_email varchar(200) NOT NULL,
		          school varchar(300) NOT NULL,
		          state varchar(150) NOT NULL,
		          city varchar(200) NULL,
		          attendance tinyint(10) NOT NULL,
		          player_id tinyint(10) NOT NULL,
		          date timestamp NOT NULL,
		          UNIQUE KEY id (id)
		     ) $charset_collate;";
		     require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		     dbDelta( $sql );
			}


			//DB for Service Table

			if($this->db->get_var("SHOW TABLES LIKE '$this->srtable'") != $this->srtable) {
		     //table not in database. Create new table
		     $charset_collate = $this->db->get_charset_collate();
		     $sql = "CREATE TABLE $this->srtable (
		          id mediumint(10) NOT NULL AUTO_INCREMENT,
		          player_id tinyint(10) NOT NULL,
		          start_time time NOT NULL,
		          end_time time NOT NULL,
		          activity varchar(500) NOT NULL,
		          supervisor_name varchar(200) NOT NULL,
		          supervisor_phone varchar(200) NOT NULL,
		          hours_worked varchar(100) NOT NULL,
		          date timestamp NOT NULL,
		          UNIQUE KEY id (id)
		     ) $charset_collate;";
		     require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		     dbDelta( $sql );
			}

		} 


		public function save_service($post){
			$insert = $this->db->insert( 
				$this->srtable, 
				$post,
				array( 
					'%d',
					'%s', 
					'%s', 
					'%s', 
					'%s', 
					'%s',
					'%s'
				) 
			);
			if($insert){
				return '<p class="success">Success</p>';	
			}else{
				return '<p class="error">Insert Failed</p>';
			}
			
		} // End Save_service

		public function delete_service($id){
			$delete = $this->db->delete( $this->srtable, array( 'id' => $id ) );
			if($delete){
				return 'Delete Successfully';
			}else{
				return 'Delete Failed';
			}
		}



		public function get_all_service($id){
			$args = 'SELECT * FROM '.$this->srtable.' WHERE `player_id`="'.$id.'"';
    		$query = $this->db->get_results($args, OBJECT);
    		return $query;
		}





		public function save_score($post){
			$insert = $this->db->insert( 
				$this->table, 
				$post,
				array( 
					'%s',
					'%d', 
					'%d', 
					'%d', 
					'%d', 
					'%d', 
					'%d',
					'%d', 
					'%d', 
					'%d', 
					'%d', 
					'%d', 
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d' 
				) 
			);
			if($insert){
				return '<p class="success">Success</p>';	
			}else{
				return '<p class="error">Insert Failed</p>';
			}
			
		} // End Save_score

		public function update_score($post){
				$id = $post['id'];
				$update = $this->db->update( 
				$this->table, 
				$post,
				array( 
					'id' => $id
				) 
			);
			if($update){
				return 'Update Success';	
			}else{
				return 'Update failed';
			}
			
		}


		public function score_delete($id){
			$delete = $this->db->delete( $this->table, array( 'player_id' => $id ) );
			if($delete){
				return 'Delete Successfully';
			}else{
				return 'Delete Failed';
			}
		}

		public function item_array($clumn){
        	$qry =  $this->db->get_results( 'SELECT `'.$clumn.'` FROM '.$this->table.' WHERE `'.$clumn.'` != "" GROUP BY `'.$clumn.'`', OBJECT );
        	$results = array();
        	foreach($qry as $q) array_push($results, '"'.$q->$clumn.'"');
        	$results = implode(',', $results);
        	return $results;
    	} 

		public function option_array($clumn){
        	$qry =  $this->db->get_results( 'SELECT `'.$clumn.'` FROM '.$this->table.' WHERE `'.$clumn.'` != "" GROUP BY `'.$clumn.'`', OBJECT );
        	$arrays = json_decode(json_encode($qry), true);
        	$results = array('' => 'Select ' . $clumn);
        	foreach($arrays as $q) $results[$q[$clumn]] = $q[$clumn];
        	return $results;
    	}    

    	public function get_single($player_id){
    		$args = 'SELECT * FROM '.$this->table.' WHERE `player_id`="'.$player_id.'"';
    		$query = $this->db->get_results($args, OBJECT);
    		return $query;
    	}

    	public function admin_search_cat($cat, $state=''){
    		$tableE = ($cat == 'community_service')?'s':'t';
    		$cat = ($cat == 'community_service')?'hours_worked':$cat;
    		if($cat == 'hours_worked'){
    			$args = 'SELECT sum('.$tableE.'.`'.$cat.'` / 3600) as etotal, ';
    		}else{
    			$args = 'SELECT sum('.$tableE.'.`'.$cat.'`) as etotal, ';	
    		}
    		

    		$args .=($state != '')?'t.`school` ':'t.`state` ';
    		$args .='as `cname` ';
    		
    		if($cat == 'hours_worked'){
    			$args .= 'FROM '.$this->table.' t LEFT JOIN '.$this->srtable.' s ON s.`player_id` = t.`player_id` '; 
    		}else{
    			$args .= 'FROM '.$this->table.' t '; 
    		}
    		$args .=($state != '')?'WHERE t.`state` = "'.$state.'" ':'';
    		$args .=($state != '')?'GROUP BY t.`school`':'GROUP BY t.`state` ';
    		$query = $this->db->get_results($args, OBJECT);
    		return $query;
    	}


    	public function admin_search($state = ''){
    		$args = 'SELECT t.`id`, t.`state`, t.`city`, t.`school`, count(DISTINCT t.`id`) as `count`, COALESCE(sum(s.`hours_worked`), 0) as `totalhours`,  
    		sum(DISTINCT t.`attendance` + t.`gpa` + t.`infractions` + t.`community_service` + t.`extracurricular` + t.`act` + t.`sat` + t.`recommendation` + t.`job` + t.`social_share` + t.`savings_account`) as `alltotal`, 
    		sum(DISTINCT t.`attendance` + t.`gpa` + t.`infractions` + t.`community_service` + t.`extracurricular` + t.`act` + t.`sat` + t.`recommendation` + t.`job` + t.`social_share` + t.`savings_account` + (s.`hours_worked` / 3600)) / count(*) as `average`, 
    		sum(DISTINCT t.`attendance` + t.`gpa` + t.`infractions` + t.`community_service` + t.`extracurricular` + t.`act` + t.`sat` + t.`recommendation` + t.`job` + t.`social_share` + t.`savings_account` + (s.`hours_worked` / 3600)) as `etotal`   
    		FROM '.$this->table.' t LEFT JOIN '.$this->srtable.' s ON s.`player_id` = t.`player_id` ';
    		$args .=($state != '')?'WHERE t.`state` = "'.$state.' " ':'';
    		$args .='AND s.`hours_worked` IS NOT NULL ';
    		$args .=($state == '')?'GROUP BY t.`state` ':'GROUP BY t.`school` ';
    		$args .='ORDER BY `etotal` DESC';
    		$query = $this->db->get_results($args, OBJECT);
    		return $query;
    	}

    	public function search_results($get = array(), $limit = 25){
    		$args 	= 'SELECT `id`, `player_name`, `counselor`, `counselor_phone`, `player_id`, `school`, `city`, 
    		sum(`attendance` + `gpa` + `infractions` + `community_service` + `extracurricular` + `act` + `sat` + `recommendation` + `job` + `social_share` + `savings_account`) as `alltotal` FROM '.$this->table.' WHERE ';
    		$args  .= '`player_name` != "" ';
    		$args  .= (isset($get['player_name']) && $get['player_name'])?'AND `player_name` LIKE "%'.$get['player_name'].'%" ':'';
    		$args  .= (isset($get['school']) && $get['school'])?'AND `school` LIKE "%'.$get['school'].'%" ':'';
    		$args  .= (isset($get['city']) && $get['city'])?'AND `city` LIKE "%'.$get['city'].'%" ':'';
    		
    		$args  .= 'GROUP BY `player_id` ';
    		$args  .= 'ORDER BY `alltotal` DESC ';
    		$args  .= 'LIMIT 0, '.$limit.'';

    				
    		$query = $this->db->get_results($args, OBJECT);
    		return $query;
    	}


    	public function count($player){
    		$args = 'SELECT Count(*) as `total`,
    				sum(`attendance`) as `attendance`,  
    				sum(`gpa`) as `gpa`,  
    				sum(`infractions`) as `infractions`, 
    				sum(`community_service`) as `community_service`,  
    				sum(`extracurricular`) as `extracurricular`,  
    				sum(`act`) as `act`,  
    				sum(`sat`) as `sat`,  
    				sum(`recommendation`) as `recommendation`,  
    				sum(`job`) as `job`,  
    				sum(`social_share`) as `social_share`,  
    				sum(`savings_account`) as `savings_account` 
    				FROM '.$this->table.' 
    				WHERE `player_id`="'.$player.'"';
    		$query = $this->db->get_row($args, OBJECT);
    		return $query;
    	}


		public function count_service($id){
    		$args = 'SELECT Count(*) as `total`,
    				sum(`hours_worked`) as `total_second`  
    				FROM '.$this->srtable.' WHERE `player_id`="'.$id.'"';
    		$query = $this->db->get_row($args, OBJECT);
    		return $query;
    	}


    	public function each_details($player, $item){
    		$args = 'SELECT `'.$item.'` FROM '.$this->table.' WHERE `player_id`="'.$player.'"';
    		$query = $this->db->get_results($args, OBJECT);
    		return $query;
    	}

    	public function player_details($player){
    		$args = 'SELECT * FROM '.$this->table.' WHERE `player_name`="'.$player.'"';
    		$query = $this->db->get_results($args, OBJECT);
    		return $query;
    	}

    	public function get_single_player($id){
    		$args = "SELECT * FROM ".$this->table." WHERE `id`='".$id."'";
    		$query = $this->db->get_row($args, OBJECT);
    		return $query;
    	}

    	public function get_top_date($player){
    		$args = "SELECT * FROM ".$this->table." WHERE `player_name`='".$player."' ORDER BY date ASC";
    		$query = $this->db->get_row($args, OBJECT);
    		if($query){
    			return $query->date;	
    		}else{
    			return 'error';
    		}
    	}
	} // End Class
}
