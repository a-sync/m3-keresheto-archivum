<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App extends CI_Controller {

	public function index()
	{
		//$this->output->enable_profiler(TRUE);//DEBUG

		$this->load->helper(array('form', 'url'));
		$this->load->model('m3');
		$this->load->library('pagination');

		$limit = 100;
		$offset = ($this->uri->segment(1)) ? $this->uri->segment(1) : 0;
		$search = trim($this->input->get('kereses', TRUE));
		$programs = $this->m3->get_programs($search, $limit, $offset);

		$this->pagination->initialize(array(
			'base_url' => site_url(''),
			'total_rows' => $programs['count'],
			'per_page' => $limit,
			'uri_segment' => 1,
			'reuse_query_string' => true,
			'first_link' => '&laquo; ',
			'last_link' => ' &raquo;',
			'num_links' => 20
		));

		$links = $this->pagination->create_links();

		$this->load->view('head');
		$this->load->view('search', array('search' => $search));
		$this->load->view('list', array('items' => $programs['items'], 'links' => $links));
		$this->load->view('foot');
	}
}
