<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

define('MINUMUM_INTERVAL_HOURS', 6);
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

		$curr_timestamp = time();
		$diff = $curr_timestamp - $this->cron_timestamp();

		if ($diff >= 3600 * MINUMUM_INTERVAL_HOURS)
		{
			$this->load->model('m3');
			$this->load->helper('curl');

			$raw = '';
			try
			{
				$raw = scrape_url(M3_DAILY_PROGRAM_URL);
				$res = json_decode($raw, true);
				$res = $this->m3->parse_programs($res['program']);
				$res = $this->m3->insert_ignore_programs($res);
			}
			catch(Exception $error)
			{
				@file_put_contents('./backup/daily-'.date('YmdHis').'.json', $raw);
				show_error($error);
			}

			$this->cron_timestamp($curr_timestamp);
			log_message('debug', 'CRON: '.$res.' new items');
			
			$this->load->view('cron', array('output'=>$res));
		}
		else 
		{
			$this->load->view('cron', array('output'=>'diff:'.$diff));
		}
	}

	private function cron_timestamp($new_value = null)
	{
		if ($new_value)
		{
			return file_put_contents('daily-program.cron', $new_value);
		}
		else
		{
			return intval(trim(file_get_contents('daily-program.cron'))) ?: 0;
		}
	}
}
