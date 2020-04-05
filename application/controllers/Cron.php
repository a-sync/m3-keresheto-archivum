<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

define('MINUMUM_INTERVAL_DAILY', 3600 * 8);
define('MINUMUM_INTERVAL_WEEKLY', 3600 * 24);

define('M3_DAILY_PROGRAM_URL', 
	'https://archivum.mtva.hu/m3/daily-program'
);

define('M3_WEEKLY_URL', 
	'https://archivum.mtva.hu/m3/open'
);
define('M3_WEEKLY_GENRE_LIST', 
	'https://archivum.mtva.hu/m3/get-open?genre='
);

define('M3_ITEM_INFO', 
	'https://archivum.mtva.hu/m3/item?id='
);
define('M3_SERIES_INFO', 
	'https://archivum.mtva.hu/m3/open?series='
);
define('M3_COLLECTION_INFO', 
	'https://archivum.mtva.hu/m3/get-open?collection='
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

		if ($diff >= MINUMUM_INTERVAL_DAILY)
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

	public function weekly()
	{
		//$this->output->enable_profiler(TRUE);//DEBUG
		set_time_limit(300);

		$curr_timestamp = time();
		$diff = $curr_timestamp - $this->cron_timestamp(null, 'weekly-program.cron');

		if ($diff >= MINUMUM_INTERVAL_WEEKLY)
		{
			$this->load->model('m3');
			$this->load->helper('curl');
			$this->load->helper('parser');

			$raw = '';
			try
			{
				$raw = scrape_url(M3_WEEKLY_URL);
				$coll_list = extract_ids($raw, ' data-collection="', '">');
			}
			catch(Exception $error)
			{
				@file_put_contents('./backup/weekly-'.date('YmdHis').'.html', $raw);
				show_error($error);
			}

			$coll_list_items = [];
			foreach($coll_list as $i => $coll) {
				try {
					$raw = scrape_url(M3_COLLECTION_INFO . $coll);
					$res = json_decode($raw, true);
					$coll_list_items = array_merge($coll_list_items, $res['docs']);
				} catch (Exception $error) {
					@file_put_contents('./backup/coll-'.$coll.'-'.date('YmdHis').'.json', $raw);
					log_message('error', $error);
				}
			}

			$res = $this->_parse_genre_list_items($coll_list_items);
			$res = $this->m3->parse_programs($res);
			$res = $this->m3->insert_ignore_programs($res);

			$this->cron_timestamp($curr_timestamp, 'weekly-program.cron');
			log_message('debug', 'CRON: '.$res.' new items');

			$this->load->view('cron', array('output'=>$res));
		}
		else 
		{
			$this->load->view('cron', array('output'=>'diff:'.$diff));
		}
	}

	private function _parse_genre_list_items($items) {
		$program_ids = [];
		foreach($items as $item) {
			if ($item['isSeries']) {
				try {
					$raw = scrape_url(M3_SERIES_INFO . $item['seriesId']);
					$series_ids = extract_ids($raw, '<div class="show-bg" style="background-image: url(https://archivum.mtva.hu/images/m3/', ')"></div>');
					$program_ids = array_merge($program_ids, $series_ids);
				} catch (Exception $error) {
					@file_put_contents('./backup/series-'.$item['seriesId'].'-'.date('YmdHis').'.html', $raw);
					log_message('error', $error);
				}
			} else {
				$program_ids[] = $item['id'];
			}
		}

		$new_items = [];
		if (count($program_ids)) {
			$missing_ids = $this->m3->return_missing_program_ids($program_ids);
			foreach($missing_ids as $id) {
				try {
					$raw = scrape_url(M3_ITEM_INFO . $id);
					$res = json_decode($raw, true);
					$new_items[] = $res;
				} catch (Exception $error) {
					@file_put_contents('./backup/item-'.$id.'-'.date('YmdHis').'.json', $raw);
					log_message('error', $error);
				}
			}
		}

		return $new_items;
	}

	public function backup() {
		//$this->output->enable_profiler(TRUE);//DEBUG
		set_time_limit(300);

		$curr_timestamp = time();
		$diff = $curr_timestamp - $this->cron_timestamp(null, 'backup.cron');

		if ($diff >= MINUMUM_INTERVAL_DAILY)
		{
			$this->load->dbutil();
			$this->load->helper('file');

			$backup = $this->dbutil->backup(array('add_drop'=>false));
			$res = write_file('./public/m3-db.gz', $backup);

			if ($res) {
				$this->cron_timestamp($curr_timestamp, 'backup.cron');
				$res = get_file_info('./public/m3-db.gz');
				$res = print_r($res, true);
				log_message('debug', 'BACKUP: '.$res);
			} else {
				log_message('error', 'BACKUP FAILED');
			}

			$this->load->view('cron', array('output'=>$res));
		}
		else 
		{
			$this->load->view('cron', array('output'=>'diff:'.$diff));
		}
	}

	public function csv() {
		//$this->output->enable_profiler(TRUE);//DEBUG
		set_time_limit(300);

		$curr_timestamp = time();
		$diff = $curr_timestamp - $this->cron_timestamp(null, 'csv.cron');

		if ($diff >= MINUMUM_INTERVAL_DAILY)
		{
			$this->load->dbutil();
			$this->load->helper('file');
			$this->load->model('m3');

			$csv = $this->dbutil->csv_from_result($this->m3->return_programs_csv_query(), ';');
			$res = write_file('./public/m3-db.csv.gz', gzencode($csv));

			if ($res) {
				$this->cron_timestamp($curr_timestamp, 'csv.cron');
				$res = get_file_info('./public/m3-db.csv.gz');
				$res = print_r($res, true);
				log_message('debug', 'CSV: '.$res);
			} else {
				log_message('error', 'CSV FAILED');
			}

			$this->load->view('cron', array('output'=>$res));
		}
		else 
		{
			$this->load->view('cron', array('output'=>'diff:'.$diff));
		}
	}
	
	public function add() {
		$this->load->model('m3');
		$this->load->helper('curl');
		$ids = explode(',', $this->input->get('id'));

		$program_ids = [];
		foreach($ids as $id) {
			if (substr($id,0,3) === 'M3-' || substr($id,0,6) === 'RADIO-') {
				$program_ids[] = $id;
			}
		}

		$new_items = [];
		if (count($program_ids)) {
			$missing_ids = $this->m3->return_missing_program_ids($program_ids);
			foreach($missing_ids as $id) {
				try {
					$raw = scrape_url(M3_ITEM_INFO . $id);
					if ($raw !== 'false') {
						$res = json_decode($raw, true);
						$new_items[] = $res;
					}
				} catch (Exception $error) {
					log_message('error', $error);
				}
			}
		}

		$res = 0;
		if (count($new_items)) {
			$res = $this->m3->parse_programs($new_items);
			$res = $this->m3->insert_ignore_programs($res);
		}

		$this->load->view('cron', array('output'=>$res));
	}

	private function cron_timestamp($new_value = null, $name = 'daily-program.cron')
	{
		if ($new_value)
		{
			return file_put_contents($name, $new_value);
		}
		else
		{
			return intval(trim(file_get_contents($name))) ?: 0;
		}
	}
}
