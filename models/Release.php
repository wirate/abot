<?php

class ReleaseModel
{
    protected $db = null;
    
    public function __construct()
    {
        $db = mysql_connect('localhost', 'user', 'pass') or die(mysql_error());
        mysql_select_db('torrents', $db);
        
        $this->db = $db;
    }
    
    public function getRelease($searchString)
    {
        $query = "
            SELECT
                `path`,
		`complete`
            FROM
                `basepath`
            WHERE
                `path` LIKE '%" . mysql_real_escape_string($searchString) . "%'
            LIMIT 20";

        $rs = @mysql_query($query, $this->db);
	
	$res = array();
	while($row = mysql_fetch_assoc($rs)) {
		$res[] = $row;
	}
        return $res;
    }
    
    public function __destruct()
    {
        mysql_close($this->db);
    }
}
