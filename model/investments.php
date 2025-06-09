<?php
include_once('vendor/autoload.php');
use PHPtricks\Orm\Database;
$db = Database::connect();

class investments
{
    private $fields = ['txn_date','bank_date', 'amount','transaction_type','lenderid', 'current_balance', 'description','transaction_category'];
    private $tablename = 'investments';
    public function fetchall(){
        global $db;
        return $db->table($this->tablename)->select(['id','txn_date','amount','transaction_type', 'lenderid', 'current_balance','description', 'bank_date','transaction_category',])->results();
    }

    public function fetch($id){
        global $db;
        return $db->table($this->tablename)->find($id)->results();
    }

    public function save($request){
        global $db;
        $params = Array();

        echo $sql = "select net_investment from lenders where id =".$request['lenderid'];
        $lender = $db->query($sql)->results()[0];
        $request['current_balance'] = $lender->net_investment;

        foreach ($this->fields as $field){
            if(isset($request[$field])){
                $params[$field] = $request[$field];
            }
        }

        if($db->table($this->tablename)->insert($params)){
            if ($request['transaction_type'] == 'C') {
                $sql = "update lenders set net_investment = ifnull(net_investment,0) + " . $request['amount'] . " where id = " . $request['lenderid'];
                $db->query($sql);
            }else if($request['transaction_type'] == 'D') {
                $sql = "update lenders set net_investment = ifnull(net_investment,0) - ".$request['amount']." where id = ".$request['lenderid'];
                $db->query($sql);
            }
            return true;
        }
        return false;
    }
}