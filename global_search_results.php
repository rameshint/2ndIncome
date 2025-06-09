<?php
include_once 'model/dashboard.php';
$obj = new dashboard();
$results = $obj->getGlobalSearchResults($_GET['q']);
echo '<span class="dropdown-item dropdown-header">'.count($results).' Results</span>
                    <div class="dropdown-divider"></div>                    ';
foreach($results as $result){
	$url = "lender_detail.php";
	if($result->type == 'Borrower'){
			$url = "borrower_detail.php";
	}
	echo '<a href="'.$url.'?id='.$result->id.'" class="dropdown-item">
                        '.$result->name.'
                        <span class="float-right text-muted text-sm">'.$result->type.'</span>
                    </a>
					<div class="dropdown-divider"></div>';
}
echo '<div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item dropdown-footer">See All Results</a>';
?>