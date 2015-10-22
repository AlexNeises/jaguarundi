<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	protected $ci;
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
		$this->ci =& get_instance(); //Initializes instance of database.

		$select = sprintf("SELECT * FROM movies"); //Normal SQL query.
		$query = $this->ci->db->query($select); //Executes query.

		$movies = array(); //Defining array to store results.

		if($query->num_rows() > 0) //Making sure our query returns at least one row.
		{
			foreach($query->result() as $row) //Loops through each row
			{
				$movies[] = $row->title; //Stores each row in an array.
			}
		}

		var_dump($movies); //Dumps the array out to the user.

		$this->load->view('welcome_message');
	}
}
