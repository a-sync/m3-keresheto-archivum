<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

define('MINUMUM_INTERVAL_DAILY', 3600 * 8);
define('MINUMUM_INTERVAL_WEEKLY', 3600 * 24);

define('M3_DAILY_PROGRAM_URL', 
	'https://nemzetiarchivum.hu/api/m3/v3/daily_program'
);
define('M3_PROGRAM_GUIDE_URL', 
	'https://nemzetiarchivum.hu/api/m3/v3/program_guide?days=30'
);

define('M3_WEEKLY_URL', 
	'https://nemzetiarchivum.hu/m3/get-open'
);

define('M3_ITEM_INFO', 
	'https://nemzetiarchivum.hu/m3/item?id='
);
define('M3_SERIES_INFO', 
	'https://nemzetiarchivum.hu/m3/open?series='
);
define('M3_COLLECTION_INFO', 
	'https://nemzetiarchivum.hu/m3/get-open?collection='
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

			$raw1 = '';
			$new1 = 0;
			try
			{
				$raw1 = scrape_url(M3_DAILY_PROGRAM_URL);
				$res1 = json_decode($raw1, true);
				$res1 = $this->m3->parse_programs($res1['program']);
				$new1 = $this->m3->insert_ignore_programs($res1);
			}
			catch(Exception $error)
			{
				@file_put_contents('./backup/daily-program'.date('YmdHis').'.json', $raw1);
				show_error($error);
			}

			$raw2 = '';
			$new2 = 0;
			try
			{
				$raw2 = scrape_url(M3_PROGRAM_GUIDE_URL);
				$res2 = json_decode($raw2, true);
				$res2 = $this->m3->parse_guides($res2['program_guides']);
				$new2 = $this->m3->insert_ignore_programs($res2);
			}
			catch(Exception $error)
			{
				@file_put_contents('./backup/program-guide'.date('YmdHis').'.json', $raw2);
				show_error($error);
			}

			$this->cron_timestamp($curr_timestamp);
			log_message('debug', 'DAILY CRON: '.$new1.' + '.$new2.' new items');

			$this->load->view('cron', array('output'=>$new1.' + '.$new2));
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
			$coll_list = [];
			try
			{
				$raw = scrape_url(M3_WEEKLY_URL);
				$res = json_decode($raw, true);
				$coll_list = array_column($res, 'id');
				// $coll_list[] = 'most_viewed';
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
			$new = $this->m3->insert_ignore_programs($res);

			$this->cron_timestamp($curr_timestamp, 'weekly-program.cron');
			log_message('debug', 'WEEKLY CRON: '.$new.' new items');

			$this->load->view('cron', array('output'=>$new));
		}
		else 
		{
			$this->load->view('cron', array('output'=>'diff:'.$diff));
		}
	}

	private function _parse_genre_list_items($items) {
		$program_ids = [];
		foreach($items as $item) {
			if ($item['seriesId']) {
				$pages = 1;
				for ($i = 1; $i <= $pages; $i++) {
					try {
						$raw = scrape_url(M3_SERIES_INFO . $item['seriesId'] . '&page=' . $i);

						if ($i === 1) {
							$page_ids = extract_ids($raw, 'open?series='.urlencode($item['seriesId']).'&page=', '"');
							if (count($page_ids) > 0) {
								$pages = max($page_ids);
							}
						}

						$series_ids = extract_ids($raw, '<div class="show-bg" style="background-image: url(https://nemzetiarchivum.hu/images/m3/', ')"></div>');
						$program_ids = array_merge($program_ids, $series_ids);
					} catch (Exception $error) {
						@file_put_contents('./backup/series-'.$item['seriesId'].'-'.$i.'-'.date('YmdHis').'.html', $raw);
						log_message('error', $error);
					}
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

		if ($diff >= MINUMUM_INTERVAL_WEEKLY)
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
			if (substr($id,0,3) === 'M3-') {
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

	public function refresh() {
		if (file_exists('refresh.cron.disabled')) {
			show_error('Disabled...', 403);
		}

		$this->load->model('m3');
		$this->load->helper(array('curl', 'url'));
		$id = intval($this->input->get('id'));

		$res = 0;

		if ($id) {
			$program = $this->m3->get_program_id_by_id($id);

			if ($program && isset($program['program_id'])) {
				$program_id = $program['program_id'];

				try {
					$raw = scrape_url(M3_ITEM_INFO . $program_id);
					if ($raw !== 'false') {
						$res = json_decode($raw, true);
					}
				} catch (Exception $error) {
					log_message('error', $error);
				}

				if ($res) {
					$res = $this->m3->parse_program($res);
					$res['id'] = $id;
					$res = $this->m3->replace_program($res);
				} else {
					$res = 0;
				}
			}

			$prev_id = $id - 1;
			if ($prev_id > 0) {
				// redirect('/cron/refresh?id='.$prev_id.'&res='.intval($res));
				header('Refresh:1;url='.site_url('/cron/refresh?id='.$prev_id.'&res='.intval($res)));
				exit($res);
			} else {
				@touch('refresh.cron.disabled');
			}
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
