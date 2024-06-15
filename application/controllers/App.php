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
		$search = trim((string)$this->input->get('kereses', TRUE));
		$select = $this->input->get('nyers') === '' ? '*' : false;

		$programs = $this->m3->get_programs($search, $limit, $offset, $select);

		$this->pagination->initialize(array(
			'base_url' => site_url(''),
			'uri_segment' => 1,
			'num_links' => 4,
			'per_page' => $limit,
			'total_rows' => $programs['total']
		));

		$links = $this->pagination->create_links();

		$this->load->view('head', array(
			'search' => $search
		));
		$this->load->view('search', array(
			'search' => $search
		));
		$this->load->view('list'.($select?'-raw':''), array(
			'items' => $programs['items'], 
			'links' => $links,
			'total' => $programs['total']
		));
		$this->load->view('foot');
	}

	public function playlist()
	{
		$output = '';
		$id = strtoupper(strval($this->input->get('id')));

		if (substr($id, 0, 3 ) === 'M3-' && ctype_alnum(substr($id, 3))) {
			$this->load->helper('curl');

			try {
				$raw = scrape_url('\x68\x74\x74\x70\x73\x3a\x2f\x2f\x6e\x65\x6d\x7a\x65\x74\x69\x61\x72\x63\x68\x69\x76\x75\x6d\x2e\x68\x75\x2f\x6d\x33\x2f\x73\x74\x72\x65\x61\x6d\x3f\x6e\x6f\x5f\x6c\x62\x3d\x31\x26\x74\x61\x72\x67\x65\x74\x3d' . $id);

				$res = json_decode($raw, true);
				$playlist = scrape_url($res['url'], '', '');

				if (count($playlist) > 130) {
					header('Content-Type: application/x-mpegURL');
					$output = $playlist;
				}
			} catch (Exception $error) {
				log_message('error', $error);
			}
		}

		$this->load->view('cron', array('output'=>$output));
	}
}
