<?php
defined('BASEPATH') OR exit('No direct script access allowed');

define('M3_DAILY_PROGRAM_URL', 
	'https://archivum.mtva.hu/m3/daily-program'
);

class Cron extends CI_Controller {

	public function index()
	{
		show_404();
	}

	public function daily()
	{
		//$this->output->enable_profiler(TRUE);//DEBUG

		$curr_timestamp = intval(date('Ymd'));

		if ($curr_timestamp > $this->cron_timestamp())
		{
			$this->cron_timestamp($curr_timestamp);

			$this->load->model('m3');
			$this->load->helper('curl');

			try
			{
				$res = scrape_url(M3_DAILY_PROGRAM_URL);
				$res = json_decode($res, true);
				$res = $this->m3->parse_programs($res['program']);
				$res = $this->m3->insert_ignore_programs($res);
			}
			catch(Exception $error)
			{
				show_error($error);
			}

			log_message('debug', 'CRON: new items'.$res);

			$this->load->view('cron', array('output'=>$res));
		}
		else $this->load->view('cron', array('output'=>'invalid invocation'));
	}

	private function cron_timestamp($new_value = null)
	{
		if ($new_value)
		{
			return file_put_contents('daily.cron', $new_value);
		}
		else
		{
			return trim(file_get_contents('daily.cron')) ?: 0;
		}
	}
}
