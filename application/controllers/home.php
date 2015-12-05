<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
	public $config;
	protected $ci;
	private $info, $data;

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		// $str = "Noel sees Leon.";
		// $stripped_str = strtolower(preg_replace('/\PL/u', '', $str));
		// $reversed_stripped_str = strrev($stripped_str);
		// if($stripped_str === $reversed_stripped_str)
		// {
		// 	var_dump('true');
		// }
		$this->data['index'] = true;
		$this->load->view('welcome_message', $this->data);
	}

	public function connect()
	{
		if($post_data = $this->input->post())
		{
			// $efg = "";
			// $data = explode('.', $_SERVER['SERVER_NAME']);
			// if(!empty($data[0]))
			// {
			// 	$efg = $data[0];
			// }
			// $dsn = 'mysql:dbname=' . $post_data['database'] . ';host=' . $post_data['hostname'] . ';port=' . $post_data['port'];
			// $user = $post_data['username'];
			// $password = $post_data['password'];
			// try
			// {
			// 	$this->dbh = new PDO($dsn, $user, $password);
			// }
			// catch(PDOException $e)
			// {
			// 	echo 'Connection failed: ' . $e->getMessage();
			// }
			// $this->dbh->debugDumpParams();

			$config['default']['dsn']			= '';
			$config['default']['hostname']	 	= $post_data['hostname'];
			$config['default']['port']	 		= $post_data['port'];
			$config['default']['username']		= $post_data['username'];
			$config['default']['password']		= $post_data['password'];
			$config['default']['database']		= $post_data['database'];
			$config['default']['dbdriver']		= "mysqli";
			$config['default']['dbprefix']		= "";
			$config['default']['pconnect']		= FALSE;
			$config['default']['db_debug']		= (ENVIRONMENT !== 'production');
			$config['default']['cache_on']		= FALSE;
			$config['default']['cachedir']		= '';
			$config['default']['char_set']		= 'utf8';
			$config['default']['dbcollat']		= 'utf8_general_ci';
			$config['default']['swap_pre']		= '';
			$config['default']['encrypt']		= FALSE;
			$config['default']['compress']		= FALSE;
			$config['default']['stricton']		= FALSE;
			$config['default']['failover']		= array();
			$config['default']['save_queries']	= TRUE;
			$default_schema = $this->load->database($config['default'], TRUE);

			$config['info_schema']['dsn']			= '';
			$config['info_schema']['hostname']	 	= $post_data['hostname'];
			$config['info_schema']['port']	 		= $post_data['port'];
			$config['info_schema']['username']		= $post_data['username'];
			$config['info_schema']['password']		= $post_data['password'];
			$config['info_schema']['database']		= 'information_schema';
			$config['info_schema']['dbdriver']		= "mysqli";
			$config['info_schema']['dbprefix']		= "";
			$config['info_schema']['pconnect']		= FALSE;
			$config['info_schema']['db_debug']		= (ENVIRONMENT !== 'production');
			$config['info_schema']['cache_on']		= FALSE;
			$config['info_schema']['cachedir']		= '';
			$config['info_schema']['char_set']		= 'utf8';
			$config['info_schema']['dbcollat']		= 'utf8_general_ci';
			$config['info_schema']['swap_pre']		= '';
			$config['info_schema']['encrypt']		= FALSE;
			$config['info_schema']['compress']		= FALSE;
			$config['info_schema']['stricton']		= FALSE;
			$config['info_schema']['failover']		= array();
			$config['info_schema']['save_queries']	= TRUE;
			$info_schema = $this->load->database($config['info_schema'], TRUE);
			
			$this->session->set_userdata($config);
			$this->new_search();
		}
	}

	public function new_search()
	{
		$info_schema = $this->session->userdata('info_schema');
		$default = $this->session->userdata['default'];

		$db_default = $this->load->database($default, TRUE);
		$db_info = $this->load->database($info_schema, TRUE);
		$this->data['new_search'] = true;

		$this->load->view('welcome_message', $this->data);
	}

	public function search()
	{
		if($post_data = $this->input->post())
		{
			$table = $post_data['table'];
			$word = $post_data['word'];
			$info_schema = $this->session->userdata('info_schema');
			$default = $this->session->userdata['default'];

			$db_default = $this->load->database($default, TRUE);
			$db_info = $this->load->database($info_schema, TRUE);

			$columns = sprintf("SELECT `column_name` FROM `COLUMNS` WHERE `table_name` = '%s' AND `table_schema` LIKE '" . $default['database'] . "' AND DATA_TYPE LIKE 'varchar'", $table);
			$column_query = $db_info->query($columns);
			$column_results = array();
			$select_results = array();
			if($column_query->num_rows() > 0)
			{
				foreach($column_query->result() as $row)
				{
					$select = sprintf("SELECT * FROM %s WHERE lower(%s) LIKE '%% %s' OR lower(%s) LIKE '%s %%' OR lower(%s) LIKE '%s' OR lower(%s) LIKE '%% %s %%'", $table, $row->column_name, $word, $row->column_name, $word, $row->column_name, $word, $row->column_name, $word);
					$select_query = $db_default->query($select);
					if($select_query->num_rows() > 0)
					{
						foreach($select_query->result() as $new_row)
						{
							$select_results[] = $new_row;
						}
					}
				}
			}

			$new_search = sprintf("SELECT `table_name` FROM `key_column_usage` WHERE `table_schema` LIKE '" . $default['database'] . "' AND `REFERENCED_TABLE_NAME` LIKE '%s'", $table);
			$new_search_query = $db_info->query($new_search);
			$new_search_results = array();
			if($new_search_query->num_rows() > 0)
			{
				foreach($new_search_query->result() as $row)
				{
					$columns = sprintf("SELECT `column_name` FROM `COLUMNS` WHERE `table_name` = '%s' AND `table_schema` LIKE '" . $default['database'] . "' AND DATA_TYPE LIKE 'varchar'", $row->table_name);
					$column_query = $db_info->query($columns);
					$column_results = array();
					if($column_query->num_rows() > 0)
					{
						foreach($column_query->result() as $newer_row)
						{
							$select = sprintf("SELECT * FROM %s WHERE lower(%s) LIKE '%% %s' OR lower(%s) LIKE '%s %%' OR lower(%s) LIKE '%s' OR lower(%s) LIKE '%% %s %%'", $row->table_name, $newer_row->column_name, $word, $newer_row->column_name, $word, $newer_row->column_name, $word, $newer_row->column_name, $word);
							$select_query = $db_default->query($select);
							if($select_query->num_rows() > 0)
							{
								foreach($select_query->result() as $new_row)
								{
									$select_results[] = $new_row;
								}
							}
						}
					}
				}
			}
			$this->data['search'] = true;
			$this->data['results'] = $select_results;
			$this->load->view('welcome_message', $this->data);
		}
		// if($post_data = $this->input->post())
		// {
		// 	$table = $post_data['table'];
		// 	$word = $post_data['word'];
		// 	$info_schema = $this->session->userdata('info_schema');
		// 	$default = $this->session->userdata['default'];

		// 	$db_default = $this->load->database($default, TRUE);
		// 	$db_info = $this->load->database($info_schema, TRUE);

		// 	$columns = sprintf("SELECT `column_name` FROM `COLUMNS` WHERE `table_name` = '%s' AND `table_schema` = '" . $default['database'] . "' AND DATA_TYPE = 'varchar'", $table);
		// 	$column_query = $db_info->query($columns);
		// 	$column_results = array();
		// 	$select_results = array();
			// if($column_query->num_rows() > 0)
			// {
			// 	foreach($column_query->result() as $row)
			// 	{
			// 		$this->print_r2($row);
			// 		// $select = sprintf("SELECT * FROM %s WHERE lower(%s) LIKE '%% %s' OR lower(%s) LIKE '%s %%' OR lower(%s) LIKE '%s' OR lower(%s) LIKE '%% %s %%'", $table, $row->column_name, $word, $row->column_name, $word, $row->column_name, $word, $row->column_name, $word);
			// 		// $select_query = $db_default->query($select);
			// 		// if($select_query->num_rows() > 0)
			// 		// {
			// 		// 	foreach($select_query->result() as $new_row)
			// 		// 	{
			// 		// 		$select_results[] = $new_row;
			// 		// 	}
			// 		// }

					
			// 	}
			// }
			// $new_search = sprintf("SELECT `table_name`, `referenced_table_name`, `referenced_column_name` FROM `key_column_usage` WHERE `table_schema` = '" . $default['database'] . "' AND `REFERENCED_TABLE_NAME` = '%s'", $table);
			// $new_search_query = $db_info->query($new_search);
			// $new_search_results = array();
			// if($new_search_query->num_rows() > 0)
			// {
			// 	foreach($new_search_query->result() as $row2)
			// 	{
			// 		$this->print_r2($row2);
			// 		$new_columns = sprintf("SELECT `column_name` FROM `columns` WHERE `table_name` = '" . $row2->table_name . "' AND `data_type` = 'varchar'");
			// 		$new_columns_query = $db_info->query($new_columns);
			// 		$new_columns_results = array();
			// 		if($new_columns_query->num_rows() > 0)
			// 		{
			// 			foreach($new_columns_query->result() as $row3)
			// 			{
			// 				$this->print_r2($row3);
			// 				$query2 = sprintf("SELECT * FROM `" . $row2->table_name . "` WHERE lower(`" . $row3->column_name . "`) = '" . $word . "'");
			// 				$query2_query = $db_default->query($query2);
			// 				$query2_results = array();
			// 				if($query2_query->num_rows() > 0)
			// 				{
			// 					foreach($query2_query->result() as $row4)
			// 					{
			// 						$this->print_r2($row4);
			// 					}
			// 				}

			// 			}
			// 		}
			// 				// $columns = sprintf("SELECT `column_name` FROM `COLUMNS` WHERE `table_name` = '%s' AND `table_schema` LIKE '" . $default['database'] . "' AND DATA_TYPE LIKE 'varchar'", $row->table_name);
			// 				// $column_query = $db_info->query($columns);
			// 				// $column_results = array();
			// 				// if($column_query->num_rows() > 0)
			// 				// {
			// 				// 	foreach($column_query->result() as $newer_row)
			// 				// 	{
			// 				// 		var_dump($newer_row);
			// 				// 		$select = sprintf("SELECT * FROM %s WHERE lower(%s) LIKE '%% %s' OR lower(%s) LIKE '%s %%' OR lower(%s) LIKE '%s' OR lower(%s) LIKE '%% %s %%'", $row->table_name, $newer_row->column_name, $word, $newer_row->column_name, $word, $newer_row->column_name, $word, $newer_row->column_name, $word);
			// 				// 		$select_query = $db_default->query($select);
			// 				// 		if($select_query->num_rows() > 0)
			// 				// 		{
			// 				// 			foreach($select_query->result() as $new_row)
			// 				// 			{
			// 				// 				$select_results[] = $new_row;
			// 				// 			}
			// 				// 		}
			// 				// 	}
			// 				// }
			// 	}
			// }

		// 	$this->print_r2($select_results);
		// 	print '<br/><br/>';
		// 	print '<a href="'.site_url('home/new_search').'">New Search...</a>';
		// 	print '<br/><br/>';
		// 	print '<a href="'.site_url('home').'">Connect to another database...</a>';
		// }
	}

	private function print_r2($val)
	{
		echo '<pre>';
		print_r($val);
		echo '</pre>';
	}
}
