<?php defined('BASEPATH') OR exit('*');

class M3 extends CI_Model {

    public function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

	public function parse_programs($data)
	{
		$programs = array();

        foreach ($data as $program)
        {
            try
            {
                $programs[] = $this->_parse_program($program);
            }
            catch(Exception $error)
            {
                log_message('error', 'Unable to parse program: '.json_encode($program));
            }
		}

        return $programs;
	}

    private function _parse_program($d)
    {
        return array(
            'program_id' => $d['id'],
            'info' => implode("\n", $d['info']),
            'extended_info' => implode("\n", $d['extended_info']),
            'title' => $d['title'],
            'subtitle' => $d['subtitle'],
            'description' => $d['description'],
            'short_description' => $d['short_description'],
            'company' => $d['company'],
            'year' => intval($d['year']) ?: '',
            'country' => $d['country'],
            'creators' => implode("\n", $d['creators']),
            'contributors' => implode("\n", $d['contributors']),
            'genre' => implode("\n", $d['genre']),
            'quality' => $d['quality'],
            'pg' => $d['pg'],
            'duration' => $d['duration'],
            'ratio' => $d['ratio'],
            'hasSubtitle' => boolval($d['hasSubtitle']),
            'isSeries' => boolval($d['isSeries']),
            'seriesId' => base64_decode($d['seriesId']) ?: '',
            'episode' => intval($d['episode']) ?: '',
            'episodes' => intval($d['episodes']) ?: ''
        );
    }

	public function insert_ignore_programs($programs, $batch_size = 100)
	{
        if (!is_array($programs) || count($programs) === 0) {
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
        if ($records_num > 0) {
            for ($inserted = 0; $inserted < $records_num; $inserted += $batch_size) {
                $next_values = array_slice($qb_vals, $inserted, $batch_size);

                if ($this->db->query($this->_insert_ignore_batch($this->db->protect_identifiers('programs', TRUE, TRUE, FALSE), $qb_keys, $next_values)))
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
    
    public function get_programs($search = '', $limit = 10, $offset = 0) {
        $select = array(
            'program_id',
            'title',
            'subtitle',
            'isSeries',
            'episode',
            'episodes',
            'short_description',
            'duration'
        );

        if ($search)
        {
            $this->db
                ->like('title', $search)
                ->or_like('subtitle', $search)
                ->or_like('info', $search)
                ->or_like('extended_info', $search)
                ->or_like('description', $search)
                ->or_like('creators', $search)
                ->or_like('contributors', $search)
                ->or_like('genre', $search)
                ->or_where('program_id', $search);
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

    public function return_missing_program_ids($ids) {
        $id_list = implode("','", $ids);
        $q = $this->db->query("SELECT program_id FROM programs WHERE program_id IN ('{$id_list}')");

        foreach($q->result_array() as $r) {
            if (($key = array_search($r['program_id'], $ids)) !== false) {
                unset($ids[$key]);
            }
        }

        return $ids;
    }

    public function return_programs_csv_query() {
        return $this->db
            ->select('program_id,title,subtitle,episode,episodes,seriesId,quality,year,duration,short_description')
            ->order_by('id', 'DESC')
            ->get('programs');
    }
}
