<?php defined('BASEPATH') OR exit('*');

class M3 extends CI_Model
{

	public function __construct()
	{
		$this->load->database();
	}

	public function parse_programs($data)
	{
		$programs = array();

		if ($data)
		{
			foreach ($data as $program)
			{
				try
				{
					$programs[] = $this->parse_program($program);
				}
				catch(Exception $error)
				{
					log_message('error', 'Unable to parse daily program item: '.json_encode($program));
				}
			}
		}

		return $programs;
	}

	public function parse_guides($data)
	{
		$programs = array();

		if ($data)
		{
			foreach ($data as $daily_guides)
			{
				foreach ($daily_guides as $program)
				{
					try
					{
						$programs[] = $this->parse_program($program);
					}
					catch(Exception $error)
					{
						log_message('error', 'Unable to parse program guide item: '.json_encode($program));
					}
				}
			}
		}

		return $programs;
	}

	public function parse_program($d)
	{
		$release_ts = 0;
		$release_dts_fields = ['start_playable_dts', 'start_startTime_dts'];
		foreach ($release_dts_fields as $key) {
			if (isset($d[$key]) && is_array($d[$key]) && count($d[$key]) > 0) {
				foreach ($d[$key] as $dts) {
					$t = strtotime($dts);  
					if ($release_ts === 0 || $t < $release_ts) {
						$release_ts = $t;
					}
				}
			}
		}

		return array(
			'program_id' => $d['id'],
			'info' => trim(implode("\n", $d['info'])),
			'extended_info' => trim(implode("\n", $d['extended_info'])),
			'title' => trim($d['title']),
			'subtitle' => trim($d['subtitle']) ? trim($d['subtitle']) : '',
			'description' => trim($d['description']),
			'short_description' => trim($d['short_description']),
			'company' => trim($d['company']),
			'year' => intval($d['year']),
			'country' => trim($d['country']),
			'creators' => trim(implode("\n", $d['creators'])),
			'contributors' => trim(implode("\n", $d['contributors'])),
			'genre' => trim(implode("\n", $d['genre'])),
			'quality' => is_null($d['quality']) ? '' : $d['quality'],
			'pg' => is_null($d['pg']) ? '' : $d['pg'],
			'duration' => $d['duration'],
			'ratio' => is_null($d['ratio']) ? '' : $d['ratio'],
			'hasSubtitle' => boolval($d['hasSubtitle']),
			'isSeries' => boolval($d['isSeries']),
			'seriesId' => base64_decode($d['seriesId']) ?: '',
			'episode' => intval($d['episode']),
			'episodes' => intval($d['episodes']),
			'released' => $release_ts ? date('Y-m-d H:i:s', $release_ts) : '0000-00-00 00:00:00'
		);
	}

	public function insert_ignore_programs($programs, $batch_size = 100)
	{
		if (!is_array($programs) || count($programs) === 0)
		{
			return 0;
		}
	
		$keys = array_keys(reset($programs));
		sort($keys);
	
		$qb_keys = array();
		foreach ($keys as $k)
		{
			$qb_keys[] = $this->db->protect_identifiers($k, FALSE, TRUE);
		}
	
		foreach ($programs as $row)
		{
			ksort($row); // puts $row in the same order as our keys
			
			$clean = array();
			foreach ($row as $value)
			{
				$clean[] = $this->db->escape($value);
			}
			
			$row = $clean;
	
			$qb_vals[] = '('.implode(',', $row).')';
		}
	
		$re = 0;
		$records_num = count($qb_vals);
		if ($records_num > 0)
		{
			for ($inserted = 0; $inserted < $records_num; $inserted += $batch_size)
			{
				$next_values = array_slice($qb_vals, $inserted, $batch_size);
				
				if ($this->db->query($this->_insert_ignore_batch($this->db
					->protect_identifiers('programs', TRUE, TRUE, FALSE), $qb_keys, $next_values)))
				{
					$re += $this->db->affected_rows();
				}
			}
		}
	
		return $re;
	}

	protected function _insert_ignore_batch($table, $keys, $values)
	{
		return 'INSERT IGNORE INTO '.$table.' ('.implode(', ', $keys).') VALUES '.implode(', ', $values);
	}

	public function get_programs($search = '', $limit = 10, $offset = 0, $select = false)
	{
		if ($select === false)
		{
			$select = array(
				'program_id',
				'title',
				'subtitle',
				'info',
				'extended_info',
				'short_description',
				'description',
				'company',
				'year',
				'country',
				'creators',
				'contributors',
				'genre',
				'quality',
				'pg',
				'duration',
				'ratio',
				'hasSubtitle',
				'isSeries',
				'seriesId',
				'episode',
				'episodes',
				'added',
				'released'
			);
		}
	
		if ($search)
		{
			if (strlen($search) > 3 && substr(strtoupper($search), 0, 3) === 'M3-') {
				$pids = explode(',', $search);
	
				$program_ids = [];
				foreach($pids as $pid) {
					if (substr(trim($pid),0,3) === 'M3-') {
						$program_ids[] = trim($pid);
					}
				}

				$this->db->where_in('program_id', $program_ids);
			} else {
				$this->db
					->like('title', $search)
					->or_like('subtitle', $search)
					->or_like('info', $search)
					->or_like('extended_info', $search)
					->or_like('description', $search)
					// ->or_like('short_description', $search)
					// ->or_like('company', $search)
					->or_like('creators', $search)
					->or_like('contributors', $search)
					->or_like('genre', $search)
					->or_where('program_id', $search);
			}
		}
	
		$total = $this->db
			->count_all_results('programs', FALSE);
	
		$items = $this->db
			->select($select)
			->limit($limit, $offset)
			->order_by('id', 'DESC')
			->get()
			->result_array();
	
		return array(
			'total' => $total,
			'items' => $items
		);
	}

	public function return_missing_program_ids($ids)
	{
		$ids = array_unique($ids);
		$id_list = implode("','", $ids);
		$q = $this->db
			->query("SELECT program_id FROM programs WHERE program_id IN ('{$id_list}')");
		
		foreach($q->result_array() as $r)
		{
			if (($key = array_search($r['program_id'], $ids)) !== false)
			{
				unset($ids[$key]);
			}
		}

		return $ids;
	}

	public function return_programs_csv_query()
	{
		return $this->db
			->select('program_id,title,subtitle,episode,episodes,seriesId,quality,year,duration,short_description,released')
			->order_by('id', 'DESC')
			->get('programs');
	}

	public function get_program_id_by_id($id)
	{
		return $this->db
			->select('program_id')
			->where('id', $id)
			->get('programs')
			->row_array();
	}

	public function replace_program($data)
	{
		$this->db->replace('programs', $data);

		return $this->db->affected_rows();
	}
}
