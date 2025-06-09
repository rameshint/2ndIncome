<?php
include_once('vendor/autoload.php');
use PHPtricks\Orm\Database;
$db = Database::connect();
class comments{
	private $fields = ['module', 'category', 'notes'];
    private $tablename = 'comments';

    public function fetchall()
    {
        global $db;
        return $db->table($this->tablename)->select()->results();
    }

    public function fetch($id)
    {
        global $db;
        return $db->table($this->tablename)->find($id)->results();
    }

    public function save($request)
    {
        global $db;
        $params = Array();
        foreach ($this->fields as $field) {
            if (isset($request[$field])) {
                $params[$field] = $request[$field];
            }
        }

        if ($db->table($this->tablename)->insert($params)) {
			$commentid = $db->lastInsertedId();
			
			foreach($request['rel'] as $table => $id){
				$sql = "insert into comments_rel(commentid, tablename, id)values($commentid, '$table', $id)";
				$db->query($sql);
			}
            return true;
        }
        return false;
    }

}
