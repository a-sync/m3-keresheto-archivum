<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App extends CI_Controller {

	public function index()
	{
		//$this->output->enable_profiler(TRUE);//DEBUG

		$this->load->helper(array('form', 'url'));
		$this->load->model('m3');
		$this->load->library('pagination');

		$limit = 10;
		$offset = intval($this->uri->segment(1)) ? (intval($this->uri->segment(1)) - 1) * $limit : 0;
		$search = trim($this->input->get('kereses', TRUE));

		$programs = $this->m3->get_programs($search, $limit, $offset);

		$this->pagination->initialize(array(
			'base_url' => site_url(''),
			'uri_segment' => 1,
			'num_links' => 4,
			'per_page' => $limit,
			'total_rows' => $programs['total']
		));

		$links = $this->pagination->create_links();

		$this->load->view('head');
		$this->load->view('search', array(
			'search' => $search
		));
		$this->load->view('list', array(
			'items' => $programs['items'], 
			'links' => $links,
			'total' => $programs['total']
		));
		$this->load->view('foot');
	}
}
