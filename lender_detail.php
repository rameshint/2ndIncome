<?php
include 'header.php';
include_once 'model/lenders.php';
$lender = (new lenders())->fetch($_GET['id'])[0];
$color =  $colors[array_rand($colors)];
?>
<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
        <div class="col-md-12">
            <div class="card card-widget widget-user">
                <!-- Add the bg color to the header using any of the bg-* classes -->

                    <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header bg-<?=$color?>">
                        <!-- /.widget-user-image -->
                        <h4 class="widget-user-username"><?=$lender->name?></h4>
                        <h6 class="widget-user-desc"><?= $lender->address?>, Ph: <?= $lender->primary_contact_no?></h6>
                    </div>
                    <div class="card-footer p-0">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    Total Investment <span class="float-right badge bg-<?=$color?> text-md"><?=number_format($lender->net_investment)?></span>
                                </a>
                            </li>
							<li class="nav-item">
                                <a href="#" class="nav-link">
                                    Current Balance <span class="float-right badge bg-<?=$color?> text-md"><?=number_format($lender->current_balance)?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    Interest Earned <span class="float-right badge bg-<?=$color?> text-md"><?=number_format($lender->interest_earnings)?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    Pending Interest <span class="float-right badge bg-<?=$color?> text-md"><?=number_format($lender->interest_earnings - $lender->interest_settled)?></span>
                                </a>
                            </li>
                        </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12" style="margin-bottom: 10px;">
                    <table class="w-100">
                        <tr>
                            <td class="w-50">
                                <button type="button" class="btn btn-success w-100" data-toggle="modal" id="credit-btn" data-target="#modal-credit">
                                    <strong>CREDIT</strong>
                                </button>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger w-100" id="debit-btn" data-toggle="modal" data-target="#modal-debit">
                                    <strong>DEBIT</strong>
                                </button>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal fade" id="modal-credit">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h4 class="modal-title">INVEST HERE...</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form id="investment_form" action="investment_save.php" method="post">
                                <input type="hidden" name="lenderid" value="<?=$_GET['id']?>">
                                <input type="hidden" name="transaction_type" value="C">
                                <input type="hidden" name="transaction_category" value="Loan">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="credit_amount">Amount</label>
                                        <input type="number" name="amount" class="form-control" step=".01"  required id="credit_amount" >
                                    </div>
                                    <div class="form-group">
                                        <label for="credit_date">Txn Date</label>
                                        <input type="date" name="txn_date" class="form-control"  required id="credit_date" value="<?php echo date("Y-m-d")?>" >
                                    </div>
                                    <div class="form-group">
                                        <label for="credit_bankdate">Bank Date</label>
                                        <input type="date" name="bank_date" class="form-control"  id="credit_bankdate"  value="<?php echo date("Y-m-d")?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="credit_narration">Narration</label>
                                        <input type="text" name="description" class="form-control"  required id="credit_narration" >
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-success">Save</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
                <div class="modal fade" id="modal-debit">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h4 class="modal-title">WITHDRAW HERE...</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form id="investment_form" action="investment_save.php" method="post">
                                <input type="hidden" name="lenderid" value="<?=$_GET['id']?>">
                                <input type="hidden" name="transaction_type" value="D">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="debit_amount">Amount</label>
                                        <input type="number" name="amount" class="form-control" step=".01"  required id="debit_amount" >
                                    </div>
                                    <div class="form-group">
                                        <label for="debit_date">Date</label>
                                        <input type="date" name="txn_date" class="form-control"  required id="debit_date" value="<?php echo date("Y-m-d")?>" >
                                    </div>
                                    <div class="form-group">
                                        <label for="debit_bankdate">Bank Date</label>
                                        <input type="date" name="bank_date" class="form-control"  required id="debit_bankdate"  value="<?php echo date("Y-m-d")?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="debit_narration">Narration</label>
                                        <input type="text" name="description" class="form-control"  required id="debit_narration" >
                                    </div>
                                    <div class="form-group">
                                        <label for="debit_category">Category</label>
                                        <select name="transaction_category" class="form-control"  required id="debit_category" >
                                            <option>Loan</option>
                                            <option>Interest</option>
                                            <option>Expense</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-danger">Save</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.modal-content -->
                    </div>
                    <!-- /.modal-dialog -->
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Transaction History</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body" style="padding: 0px !important;">

                            <table class="w-100 table-striped display" id="example" cellpadding="3">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th width="8%">Txn Date</th>
                                        <th width="8%">Bank Date</th>
                                        <th>Narration</th>
                                        <th width="8%">Credit</th>
                                        <th width="8%">Debit</th>
                                        <th width="8%">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>

                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>
</section>
<?php
include 'footer.php';
?>

<script>
function ExcelDateToJSDate(serial) {
   var utc_days  = Math.floor(serial - 25569);
   var utc_value = utc_days * 86400;                                        
   var date_info = new Date(utc_value * 1000);

   var fractional_day = serial - Math.floor(serial) + 0.0000001;

   var total_seconds = Math.floor(86400 * fractional_day);

   var seconds = total_seconds % 60;

   total_seconds -= seconds;

   var hours = Math.floor(total_seconds / (60 * 60));
   var minutes = Math.floor(total_seconds / 60) % 60;

   return new Date(date_info.getFullYear(), date_info.getMonth(), date_info.getDate(), hours, minutes, seconds);
}
$(document).ready(function(){

    // Initialize DataTable
    var transactionTable = $("#example").DataTable({
        processing: true,
        serverSide: true,
        order: [[0, 'desc']],
        ajax: {
            url: 'get_lender_transactions.php',
            type: 'POST',
            data: {
                lender_id: <?=$_GET['id']?>
            }
        },
        columns: [
            { data: 0, name: 'id' },
            { data: 1, name: 'txn_date' },
            { data: 2, name: 'bank_date' },
            { data: 3, name: 'description' },
            { data: 4, name: 'credit', className: 'text-right' },
            { data: 5, name: 'debit', className: 'text-right' },
            { data: 6, name: 'balance', className: 'text-right' }
        ],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        language: {
            processing: "Loading transactions...",
            emptyTable: "No transactions found",
            info: "Showing _START_ to _END_ of _TOTAL_ transactions",
            infoEmpty: "Showing 0 to 0 of 0 transactions",
            infoFiltered: "(filtered from _MAX_ total transactions)"
        }
    });

    // Handle form submissions to refresh table after adding transactions
    $('#investment_form').on('submit', function(e) {
        // Allow form to submit normally, but refresh table after page reload
        setTimeout(function() {
            transactionTable.ajax.reload();
        }, 100);
    });

	$("#debit-btn").on("click", function(){
		navigator.clipboard.readText()
		  .then(text => {
			// `text` contains the text read from the clipboard
			textArr = text.split("\t")

            let pattern1 = /^\d{2}-[a-zA-Z]{3}-\d{2}$/g
            let pattern2 = /^\d{2}-\d{2}-\d{4}$/g
            if(textArr[1].match(pattern1)){
                dateVal = $.format.date(new Date(textArr[1]), "yyyy-MM-dd")
            }else if(textArr[1].match(pattern2)){
                dateValArr = textArr[1].split("-")
                dateVal = $.format.date(new Date(dateValArr[2]+"-"+dateValArr[1]+'-'+dateValArr[0]), "yyyy-MM-dd")
            }else{
                dateVal = textArr[1]
            }
            
		
			$("#debit_amount").val(textArr[5].replace(",",""))
			$("#debit_date").val(dateVal)
			$("#debit_bankdate").val(dateVal)
			$("#debit_narration").val($.trim(textArr[4]))
		  })
		  .catch(err => {
			// maybe user didn't grant access to read from clipboard
			console.log('Something went wrong', err);
		  });
	});
	
	$("#credit-btn").on("click", function(){
		navigator.clipboard.readText()
		  .then(text => {
			// `text` contains the text read from the clipboard
				textArr = text.split("\t")
                let pattern1 = /^\d{2}-[a-zA-Z]{3}-\d{2}$/g
                let pattern2 = /^\d{2}-\d{2}-\d{4}$/g
                if(textArr[1].match(pattern1)){
                    dateVal = $.format.date(new Date(textArr[1]), "yyyy-MM-dd")
                }else if(textArr[1].match(pattern2)){
                    dateValArr = textArr[1].split("-")
                    dateVal = $.format.date(new Date(dateValArr[2]+"-"+dateValArr[1]+'-'+dateValArr[0]), "yyyy-MM-dd")
                }else{
                    dateVal = textArr[1]
                }
				$("#credit_amount").val(textArr[6].replace(",",""))
				$("#credit_date").val(dateVal)
				$("#credit_bankdate").val(dateVal)
				$("#credit_narration").val($.trim(textArr[4]))
		  })
		  .catch(err => {
			// maybe user didn't grant access to read from clipboard
			console.log('Something went wrong', err);
		  });
	});
});
</script>
