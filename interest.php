<?php
include_once 'header.php';
$date = date("Y-m-d");
if($_REQUEST['date']!=''){
	$date = $_REQUEST['date'];
}
?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">

                <!-- general form elements -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">PENDING INTEREST & PAYMENT</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->

                    <div class="card-body" style="padding: 3px">
                        <table class="table table-striped">
                            <tr>
                                <td><input type="date" id="interest_cal_date" class="form-control"
                                           value="<?php echo $date ?>"></td>
                                <td>
                                    <button class="form-control btn btn-primary" id="interest-sbt" onclick="fetchPendingInterest()">
                                        Submit
                                    </button>
                                </td>
                            </tr>
                        </table>
                        <div id="interest_pending_list"></div>
                        <div class="modal fade" id="modal-interest-pay">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title"></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form id="transaction_form" action="transaction_mass_save.php" method="post">
                                        <div class="modal-body" style="padding: 5px">

                                        </div>
                                        <div class="modal-footer justify-content-between">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            <button type="submit" id="loan-submit" class="btn btn-success">Save</button>
                                        </div>
                                    </form>
									<table class="table table-striped" cellpadding=3 id="activities">
										<thead>
											<tr>
												<th width=20%>Txn. Date</th>
												<th>Narration</th>
												<th width=20%>Amount</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->


                </div>
                <!-- /.card -->


            </div>
        </div>

    </div>
</section>
<?php
include_once 'footer.php';
?>
<script type="text/javascript">


    function fetchPendingInterest() {

        date = $("#interest_cal_date").val();
        $.ajax({
            url: 'pending_interest_list.php',
            type: 'post',
            data: {"date": date},
            success: function (data, textStatus, jQxhr) {
                $("#interest_pending_list").html(data);

            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        })

    }

    function copyInterest(interest){
        navigator.clipboard.writeText(interest + ' Interest')
    }

    function payInterest(id, name) {
        date = $("#interest_cal_date").val();
        $("#modal-interest-pay").find('.modal-title').html(name)
        $.ajax({
            url: 'interest_pay.php',
            type: 'post',
            data: {"date": date, borrowerid: id},
			async:false,
            success: function (data, textStatus, jQxhr) {
                $("#modal-interest-pay").find('.modal-body').html(data);
                $("#modal-interest-pay").modal('show');
				

            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        })
		
		$.ajax({
            url: 'get_activities.php',
            type: 'post',
            data: {borrowerid: id},
            success: function (data, textStatus, jQxhr) {
                $("#activities tbody").html(data);
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        })
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
                        
                        
						$("#exampleInputTxnDate").val(dateVal)
						$("#exampleInputBankDate").val(dateVal)
						$("#narration").val($.trim(textArr[4]))
				  })
				  .catch(err => {
					// maybe user didn't grant access to read from clipboard
					console.log('Something went wrong', err);
				  });
		
    }
	
	
	


    $(document).ready(function () {
        $(document).on('blur',".loan-input", function(){
            total_interest = 0;
            $('.loan-input').each(function(){
                if(parseInt($(this).val())>0) total_interest +=parseFloat($(this).val());
            })

            $("#total-interest").html(total_interest);
        })

        $("#interest-sbt").click();
		
		$(document).on('click', '.interest-copy', function(){
            tdEl = $(this).closest('td')
            tdEl.prev('td').find('.loan-input').val(tdEl.prev('td').prev('td').text().replace(',','')).focus()
        });


        $(document).on('click', '.interest-copy-total', function(){
            $(document).find(".interest-copy").each(function(){
                $(this).click()    
            })
        })
		
		 
	});

</script>