<?php

/*
Plugin Name: Editable Table
Plugin URI: https://www.editabletable.com
Description: Add Simple and Fast Sql Editable DataBase Tables to Wordpress Website.
Author: Claudio Garini
Version: 0.1.4
Tested up to: 4.9.4
Author URI: https://www.editabletable.com
*/



class edtb {
    private $pdo;
    private $config = array(
        'id'=>0,
        'database'=>array(
		
			
			'host'=>DB_HOST,
            'user'=>DB_USER,
            'pass'=>DB_PASSWORD,
            'schema'=>DB_NAME
			            ),			
        );

	    public function __construct () {
      
	    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8',$this->config['database']['host'],$this->config['database']['schema']);
        $this->pdo = new PDO($dsn,$this->config['database']['user'],$this->config['database']['pass']);
        
        add_action('wp_head',array($this,'load_assets'));
        add_action('wp_ajax_edtb_fetch',array($this,'fetch'));
        add_action('wp_ajax_nopriv_edtb_fetch',array($this,'fetch'));
        add_action('wp_ajax_edtb_update',array($this,'update'));
        add_action('wp_ajax_nopriv_edtb_update',array($this,'update'));
        add_action('wp_ajax_edtb_insert',array($this,'insert'));
        add_action('wp_ajax_nopriv_edtb_insert',array($this,'insert'));
        add_action('wp_ajax_edtb_delete',array($this,'delete'));
        add_action('wp_ajax_nopriv_edtb_delete',array($this,'delete'));
		
		add_action( 'init','create_post_typea' ); 
        
      
        add_action ('admin_menu',array($this,'setup_admin_pages'));
        add_action ('admin_init',array($this,'register_edtb_posttype'));
        add_action('wp_ajax_save_new_edtb',array($this,'save_edtb'));
        add_action('wp_ajax_save_setting_edtb',array($this,'save_settingedtb'));
        add_action('wp_ajax_atualiza_post_content',array($this,'atualiza_content'));
        add_shortcode('edtbplugin',array($this,'display_frontend'));
  
  }

    public function display_frontend ($atts) {
        $idconfig= null;
        if (isset($atts['id'])) {
            $id = $atts['id'];
            
            $post = get_post($id);
            $idconfig = json_decode($post->post_content,true);

		    $this->config['database']['host'] = $idconfig['DB_HOST'];
            $this->config['database']['user'] = $idconfig['DB_USER'];
            $this->config['database']['pass'] = $idconfig['DB_PASSWORD'];
            $this->config['database']['schema'] = $idconfig['DB_NAME'];
			$this->config['edtble'] = $idconfig['editable'];
			$this->config['table'] = $idconfig['tablename'];$this->config['Pagination'] = $idconfig['Pagination'];
            
            $this->config['columns'] = array();
            $this->config['columns_front'] = array();
            $this->config['columns_size'] = array();

            foreach($idconfig['columns'] as $c) {
                $this->config['columns'][] = $c['name'];
                $this->config['columns_front'][] = $c['label'];
                $this->config['columns_size'][] = $c['size'];
            }
            
        }
         
        ob_start();
        include(dirname(__FILE__).'/views/frontend.php');
        $view = ob_get_clean();
        return $view;
		
    }
	
    public function load_assets () {
		$path = plugin_dir_url(__FILE__);
		wp_enqueue_style('bootstrap.min.css', $path .'assets/bootstrap.min.css');
        wp_enqueue_style('bootstrap-datepicker.css', $path .'assets/bootstrap-datepicker.css');
	   	wp_enqueue_script('jquery.dataTables.min.js', $path .'assets/jquery.dataTables.min.js');
	    wp_enqueue_script('dataTables.bootstrap.min.js', $path .'assets/dataTables.bootstrap.min.js');
        wp_enqueue_script('bootstrap-datepicker.js', $path .'assets/bootstrap-datepicker.js');
    }
	 
    
    public function fetch() {
        if (isset($_GET['edtb']) && $_GET['edtb'] != 0) {
            $id = sanitize_key($_GET['edtb']);
            $post = get_post($id);
            $idconfig = json_decode($post->post_content,true);
		    $this->config['database']['host'] = $idconfig['DB_HOST'];
            $this->config['database']['user'] = $idconfig['DB_USER'];
            $this->config['database']['pass'] = $idconfig['DB_PASSWORD'];
            $this->config['database']['schema'] = $idconfig['DB_NAME'];
			$this->config['edtble'] = $idconfig['editable'];
			$this->config['table'] = $idconfig['tablename'];$this->config['Pagination'] = $idconfig['Pagination'];
            
            $this->config['columns'] = array();
            $this->config['columns_front'] = array();
            $this->config['columns_size'] = array();
            
            foreach($idconfig['columns'] as $c) {
                $this->config['columns'][] = $c['name'];
                $this->config['columns_front'][] = $c['label'];
                $this->config['columns_size'][] = $c['size'];
            }
        }
        try {
            $query = sprintf('SELECT * FROM %s',$this->config['table']);
            if (!empty($_POST['search']['value'])) {
            
			  $qstring = '';
                foreach($this->config['searchable'] as $idx=>$search_column) {
                    $qstring .= sprintf('%s%s = "%s"',($idx==0?'':'OR '),$search_column,sanitize_text_field ($_POST['search']['value']));
                }

			   
                $query .= $qstring;
             } else {

			    $query .= '';
            }
            
			
			
			if(isset($_POST["order"])) {
                $query .= ' ORDER BY '.$this->config['columns'][sanitize_text_field ($_POST['order']['0']['column'])].' '.sanitize_text_field ($_POST['order']['0']['dir']);
            } else {
                $query .= ' ORDER BY '.$this->config['columns'][0].' ASC';
            }
            $query1 = ''; 
            if(isset($_POST['length']) && $_POST["length"] != -1) {
                $query1 = ' LIMIT ' . sanitize_key($_POST['start']) . ', ' . sanitize_key($_POST['length']);
            }
            
            $stmt = $this->pdo->query($query.$query1);
            $data = array();
			
			$table152=$this->config['table'];
			global $wpdb;
			$existing_columns = $wpdb->get_col("DESC {$table152}", 0);	
			$idxkey= $existing_columns[0];
           
		   while($row = $stmt->fetch()) {
                $content = array();
                foreach($this->config['columns'] as $column) {
                              
			   $content[] = sprintf('<div class="update" data-id="%s" data-column="%s" contenteditable>%s</div>',$row[$idxkey],$column,$row[$column]);  //$row['id']
               if ($this->config['edtble']=="Not"){
             $content =  str_replace('contenteditable', ' ' ,$content );      				   
			 }               
			   }
                $data[] = $content;            
            }
            	 
            $query_count = str_replace('*','count("' .$idxkey. '")',$query);
            $fiter_count = $this->pdo->query($query_count)->fetchColumn();
            
            $all_count = $this->pdo->query('SELECT COUNT("' .$idxkey. '") AS totalCount FROM '.$this->config['table'].' ')->fetchColumn();
			
             echo json_encode(array(
             "draw"    => intval($_POST["draw"])?intval($_POST["draw"]):1,
             "recordsTotal"  =>  $all_count,
             "recordsFiltered" => $filter_count,
             "data"    => $data,
            ));
        } catch (Exception $e) {
            echo $e->getMessage();    
        }
        wp_die();
    }
    
    public function update () {
        if (isset($_GET['edtb']) && $_GET['edtb'] != 0) {
            $id = sanitize_key($_GET['edtb']);
            
            $post = get_post($id);
            $idconfig = json_decode($post->post_content,true);

		    $this->config['database']['host'] = $idconfig['DB_HOST'];
            $this->config['database']['user'] = $idconfig['DB_USER'];
            $this->config['database']['pass'] = $idconfig['DB_PASSWORD'];
            $this->config['database']['schema'] = $idconfig['DB_NAME'];
			$this->config['table'] = $idconfig['tablename'];$this->config['Pagination'] = $idconfig['Pagination'];
 
            $this->config['columns'] = array();
            $this->config['columns_front'] = array();
            $this->config['columns_size'] = array();
            
            foreach($idconfig['columns'] as $c) {
                $this->config['columns'][] = $c['name'];
                $this->config['columns_front'][] = $c['label'];
                $this->config['columns_size'][] = $c['size'];
            }
        }
        if (!empty($_POST['column'])) {
		
		$table152=$this->config['table'];
		global $wpdb;
        $existing_columns = $wpdb->get_col("DESC {$table152}", 0);	
		$idxkey= $existing_columns[0];
		
        	$column = sanitize_text_field( $_POST['column'] );			
            $query = sprintf('UPDATE %s SET %s=? WHERE ' .$idxkey. '=?',$this->config['table'],$column);
            $stmt=$this->pdo->prepare($query);
            $value = sanitize_text_field( $_POST['value'] );
            $id = sanitize_key( $_POST['id'] );
            $stmt->execute(array($value,$id));
            if (!$stmt) {
                print_r($this->pdo->errorInfo());
            } else {
                //echo 'Data Updated!';
            }
        }
        wp_die();
    }
    
    public function insert () {
        if (isset($_GET['edtb']) && $_GET['edtb'] != 0) {
            $id = sanitize_key($_GET['edtb']);
            
            $post = get_post($id);
            $idconfig = json_decode($post->post_content,true);

		    $this->config['database']['host'] = $idconfig['DB_HOST'];
            $this->config['database']['user'] = $idconfig['DB_USER'];
            $this->config['database']['pass'] = $idconfig['DB_PASSWORD'];
            $this->config['database']['schema'] = $idconfig['DB_NAME'];
			$this->config['table'] = $idconfig['tablename'];$this->config['Pagination'] = $idconfig['Pagination'];
            
            $this->config['columns'] = array();
            $this->config['columns_front'] = array();
             $this->config['columns_size'] = array();
             
            foreach($idconfig['columns'] as $c) {
                $this->config['columns'][] = $c['name'];
                $this->config['columns_front'][] = $c['label'];
                $this->config['columns_size'][] = $c['size'];
            }
        }
        if (isset($_POST[$this->config['columns'][0]])) {
            $columns = $this->config['columns'];
            $values = array();
            $placeholders = array();
           
		   foreach($this->config['columns'] as $column) {
                $values[] = sanitize_text_field($_POST[$column]);
                $placeholders[] = '?';
                }
	   
            $query = sprintf('INSERT INTO %s (%s) VALUES (%s)',$this->config['table'],implode(',',$columns),implode(',',$placeholders));    
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($values);
            if (!$stmt) {
                print_r($this->pdo->errorInfo());
            } else {
               // echo 'Data Inserted!';    
            }   
        }
        wp_die();
    }
    
    public function delete () {
        if (isset($_GET['edtb']) && $_GET['edtb'] != 0) {
            $id = sanitize_key($_GET['edtb']);
            
            $post = get_post($id);
            $idconfig = json_decode($post->post_content,true);

		    $this->config['database']['host'] = $idconfig['DB_HOST'];
            $this->config['database']['user'] = $idconfig['DB_USER'];
            $this->config['database']['pass'] = $idconfig['DB_PASSWORD'];
            $this->config['database']['schema'] = $idconfig['DB_NAME'];
			$this->config['table'] = $idconfig['tablename'];$this->config['Pagination'] = $idconfig['Pagination'];
            
            $this->config['columns'] = array();
            $this->config['columns_front'] = array();
            $this->config['columns_size'] = array();

            foreach($idconfig['columns'] as $c) {
                $this->config['columns'][] = $c['name'];
                $this->config['columns_front'][] = $c['label'];
                $this->config['columns_size'][] = $c['size'];
            }
        }
        if (!empty($_POST['id'])) {

        $table152=$this->config['table'];
		global $wpdb;
        $existing_columns = $wpdb->get_col("DESC {$table152}", 0);	
		$idxkey= $existing_columns[0];
		
		$stmt = $this->pdo->prepare(sprintf('DELETE FROM %s WHERE ' .$idxkey. '=?',$this->config['table']));   
            $id = sanitize_key( $_POST['id'] );
            $stmt->execute(array($id));
            if (!$stmt) {
                print_r($this->pdo->errorInfo());
            } else {
               // echo 'Data Deleted!';
            }
        }
        wp_die();
    }
    

    public function register_edtb_posttype () {
        register_post_type ('edtb',array(
            'labels'=>array(
                'name'=>'EDTB',
                'singular_name'=>'EDTB'
                ),
            'public'=>false
            ));    
    }
    
    public function setup_admin_pages () {


        add_menu_page('EDTB','EditibleTable','manage_options','edtb',array($this,'display_add_form'));
    }
    

    public function display_add_form () {
        include(dirname(__FILE__).'/views/admin-add.php');
    }
  
    public function save_edtb () {
        $errors = array();
		   if (empty(DB_HOST) || empty(DB_USER) || empty(DB_NAME) || empty($_POST['tablename'])) {
		    
            $errors[] = 'Database configuration is incomplete.';
        }
        
		$pass = empty(DB_PASSWORD)?'':DB_PASSWORD;
        if (!$mysqli = new mysqli(DB_HOST,DB_USER,$pass,DB_NAME)) {	
            $errors[] = 'Unable to connect to database.';  
        }
        
        if (!count($_POST['columns']) || !isset($_POST['columns'][0]['name'])) {
            $errors[] = 'Please add at least 1 column.';
        }
        $title ='EDTB-'.strtotime('now');

        
        if (!count($errors)) {
            $post = wp_insert_post(array(
                'post_title'=>'EDTB-'.strtotime('now'),
                'post_content'=>json_encode($_POST, JSON_UNESCAPED_UNICODE),  //utf8
                'post_status'=>'publish',
                'post_type'=>'edtb'
                ));

            if (!$post) {
                
                echo json_encode(array('success'=>false,'errors'=>array('Unable to save EDTB, please try again later.')));
            } else {
                $scode = '[edtbplugin id='.$post.']';
                echo json_encode(array('success'=>true,'post_id'=>$post,'title'=>$title,'scode'=>$scode))   ;
            }
        } else {
			
            echo json_encode(array('success'=>false,'errors'=>$errors));   
        }
        wp_die();
    }			  public function atualiza_content () {						  $my_post = array(				'ID'           => ($_POST['id']),				 'post_content' => $_POST['value'],				 			  );			  wp_update_post( $my_post );  			wp_redirect( admin_url().'?page=edtb');			exit;				  }
}
			
$edtb = new edtb();


		function create_post_typea(){

		if($_REQUEST['action']=='deleteadmla'){
            $idsias = sanitize_key($_REQUEST['idsias']);
			wp_trash_post( $idsias );
			wp_redirect( admin_url().'?page=edtb');
			exit;
		}
	}