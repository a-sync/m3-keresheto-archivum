<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App extends CI_Controller {

	public function index()
	{
		$this->output->enable_profiler(TRUE);//DEBUG
		
		$this->load->helper(array('form', 'url'));

		$search = $this->input->get('kereses', TRUE);
		$items = array();

		$this->load->view('head');
		$this->load->view('search', array('search' => $search));
		$this->load->view('list', array('items' => $items));
		$this->load->view('foot');
	}
}
