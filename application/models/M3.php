<?php defined('BASEPATH') OR exit('*');

class M3 extends CI_Model {

    public function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

	public function parsePrograms($data)
	{
		$programs = array();

        foreach ($data as $program)
        {
            try
            {
                $programs[] = $this->_parseProgram($program);
            }
            catch(Exception $error)
            {
                log_message('error', 'Unable to parse program: '.json_encode($program));
            }
		}

        return $programs;
	}

    private function _parseProgram($d)
    {
        return array(
            'program_id' => $d['id'],
            'info' => implode('\n', $d['info']),
            'extended_info' => implode('\n', $d['extended_info']),
            'title' => $d['title'],
            'subtitle' => $d['subtitle'],
            'description' => $d['description'],
            'short_description' => $d['short_description'],
            'company' => $d['company'],
            'year' => intval($d['year']) ?: '',
            'country' => $d['country'],
            'creators' => implode('\n', $d['creators']),
            'contributors' => implode('\n', $d['contributors']),
            'genre' => implode('\n', $d['genre']),
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

	public function insertIgnorePrograms($programs)
	{
        $keys = array_keys(reset($programs));
        sort($keys);

        $qb_keys = array();
        foreach ($keys as $k)
		{
			$qb_keys[] = $this->db->protect_identifiers($k, FALSE, TRUE);
        }

        foreach ($programs as $row)
		{
			if (count(array_diff($keys, array_keys($row))) > 0 OR count(array_diff(array_keys($row), $keys)) > 0)
			{
				// batch function above returns an error on an empty array
				$qb_set[] = array();
				return;
			}

			ksort($row); // puts $row in the same order as our keys

            $clean = array();
            foreach ($row as $value)
            {
                $clean[] = $this->db->escape($value);
            }

            $row = $clean;

			$qb_set[] = '('.implode(',', $row).')';
        }

        if ($this->db->query($this->_insert_ignore_batch($this->db->protect_identifiers('programs', TRUE, TRUE, FALSE), $qb_keys, $qb_set)))
        {
            return $this->db->affected_rows();
        }
        else 
        {
            return 0;
        }
	}

    protected function _insert_ignore_batch($table, $keys, $values)
	{
		return 'INSERT IGNORE INTO '.$table.' ('.implode(', ', $keys).') VALUES '.implode(', ', $values);
	}
}
