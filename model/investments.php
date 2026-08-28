<?php
 
include_once('vendor/autoload.php');
use PHPtricks\Orm\Database;
$db = Database::connect();

class investments
{
    private $fields = ['txn_date','bank_date', 'amount','transaction_type','lenderid', 'current_balance', 'description','transaction_category', 'interest_rate', 'borrower_id'];
    private $tablename = 'investments';
    public function fetchall(){
        global $db;
        return $db->table($this->tablename)->select(['id','txn_date','amount','transaction_type', 'lenderid', 'current_balance','description', 'bank_date','transaction_category', 'interest_rate', 'borrower_id'])->results();
    }

    public function fetch($id){
        global $db;
        return $db->table($this->tablename)->find($id)->results();
    }

    public function save($request){
        global $db;
        $params = Array();

        $sql = "select net_investment from lenders where id =".$request['lenderid'];
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

    public function interest_to_loan_save($request){
        global $db;
        $params = Array();
        $total_amount = 0;
        foreach ($request['borrower_id'] as $index => $borrower_id){
           
                $borrower_id = $borrower_id;
                $amount = $request['amount'][$index];

                
                if($amount > 0){
                    $params = [
                        'lenderid' => $request['lenderid'], 
                        'txn_date' => $request['txn_date'],
                        'bank_date' => $request['bank_date'],
                        'amount' => $amount,
                        'transaction_type' => 'D',
                        'transaction_category' => 'Interest', 
                        'description' => 'Interest transferred to Loan for Accounting',
                        'borrower_id' => $borrower_id
                    ];
                    (new investments())->save($params);
                }
                $total_amount += $amount;
             
        }

        if($total_amount > 0) {
            $params = [
                'lenderid' => $request['lenderid'], 
                'txn_date' => $request['txn_date'],
                'bank_date' => $request['bank_date'],
                'amount' => $total_amount,
                'transaction_type' => 'C',
                'transaction_category' => 'Loan', 
                'description' => 'Interest transferred to Loan for Accounting' 
            ];
            (new investments())->save($params);
        }
 
 
        return false;
    }
}