<?php
include_once('vendor/autoload.php');
use PHPtricks\Orm\Database;
$db = Database::connect();

class settlement
{
    private $fields = ['loanid','lender_interest', 'recovery','commission', 'excess', 'settlement_date'];
    private $tablename = 'settlement';
    public function fetchall(){
        global $db;
        return $db->table($this->tablename)->select()->results();
    }

    public function fetch($id){
        global $db;
        return $db->table($this->tablename)->find($id)->results();
    }

    public function save($request){
        global $db;
        $params = Array();
        foreach ($this->fields as $field){
            if(isset($request[$field])){
                $params[$field] = $request[$field];
            }
        }

        if(isset($request['id']) && $request['id'] > 0){
            $status = $db->table($this->tablename)->where('id', $request['id'])->update($params);
        }else{
            $status = $db->table($this->tablename)->insert($params);
        }

        return $status;
    }

}
