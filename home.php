<?php
include 'header.php';
?>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <div class="col-lg-3 col-6">


                <div class="modal fade" id="modal-current-balance">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h4 class="modal-title">Current Balance</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body" style="padding: 3px">
                                <table class="table-striped w-100" cellpadding="5">
                                    <tr>
                                        <th>Accounts</th>
                                        <th width="15%" style="text-align: center">Investment</th>
                                        <th width="15%" style="text-align: center">Loan given</th>
                                        <th width="15%" style="text-align: center">Balance</th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner ">
                        <h3 class="current-balance-value">0000.00</h3>

                        <p>Current Balance</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="#" class="small-box-footer" data-toggle="modal" data-target="#modal-current-balance">More
                        info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="modal fade" id="modal-pending-interest">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h4 class="modal-title">Pending Interest</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body" style="padding: 3px">
                                <table class="table-striped w-100" cellpadding="5">
                                    <tr>
                                        <th>Borrower</th>
                                        <th width="15%" style="text-align: center">Interest</th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="small-box bg-success">

                    <div class="inner">
                        <h3 class="pending-interest-value">0000.00</h3>
                        <p>Pending Interest</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="#" class="small-box-footer" data-toggle="modal" data-target="#modal-pending-interest">More
                        info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="modal fade" id="modal-recoverables">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h4 class="modal-title">Recoverables</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body" style="padding: 3px">
                                <table class="table-striped w-100" cellpadding="5">
                                    <tr>
                                        <th>Lender</th>
										<th>Borrower</th>
                                        <th width="15%" style="text-align: center">Amount</th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 class="recoverables-amount-value">0000.00</h3>
                        <p>Recoverables</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="#" class="small-box-footer" data-toggle="modal" data-target="#modal-recoverables">More
                        info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <div class="modal fade" id="modal-unsettled-transactions">
                    <div class="modal-dialog">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h4 class="modal-title">Pending Interest (Cur)</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <div class="modal-body" style="padding: 3px">
                                <table class="table-striped w-100" cellpadding="5">
                                    <tr>
                                        <th>Borrower</th>
                                        <th width="15%" style="text-align: center">Interest</th>
                                        <th width="15%" style="text-align: center">Collected</th>
                                        <th width="15%" style="text-align: center">Pending</th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 class="unsettled-transactions-value">0000.00</h3>
                        <p>Pending Interest (Cur. Month)</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="#" class="small-box-footer" data-toggle="modal"
                       data-target="#modal-unsettled-transactions">More info <i
                                class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
        </div>
        <!-- /.row -->
         <!-- Interest Collected Trend Chart Row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Interest Collected Trend (Last 12 Months)</h3>
                            <a href="javascript:void(0);">View Report</a>
                        </div>
                    </div>
                    <div class="card-body" style="height:400px;">
                        <div class="d-flex ">
                            <p class="d-flex flex-column">
                                <span class="text-bold text-lg" id="total-interest-collected">0.00</span>
                                <span>Total Interest Collected (12 Months)</span>
                            </p>
                            <p class="ml-auto d-flex flex-column text-right">
                                <span class="text-success" id="interest-growth">
                                    <i class="fas fa-arrow-up"></i> 0%
                                </span>
                                <span class="text-muted">Since last month</span>
                            </p>
                        </div>
                        <!-- /.d-flex -->

                        <div class="position-relative mb-4">
                            <canvas id="interest-trend-chart" height="200"></canvas>
                        </div>

                        <div class="d-flex flex-row justify-content-end">
                            <span class="mr-2">
                                <i class="fas fa-square text-success"></i> Interest Collected
                            </span>
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
            <div class="col-lg-4 "  >
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">ROI Wise Loans</h3>
                        <div class="card-tools">
                            <a href="#" class="btn btn-sm btn-tool">
                                <i class="fas fa-download"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-tool">
                                <i class="fas fa-bars"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body" style="height:450px">
                        <div id="ROIWiseLoansChart"  ></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header border-0">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Turnover</h3>
                            <a href="javascript:void(0);">View Report</a>
                        </div>
                    </div>
                    <div class="card-body" style="height:450px;">
                        <div class="d-flex ">
                            <p class="d-flex flex-column">
                                <span class="text-bold text-lg">18,230.00</span>
                                <span>Interest Received</span>
                            </p>
                            <p class="ml-auto d-flex flex-column text-right">
                                    <span class="text-success">
                                        <i class="fas fa-arrow-up"></i> 33.1%
                                    </span>
                                <span class="text-muted">Since last month</span>
                            </p>
                        </div>
                        <!-- /.d-flex -->

                        <div class="position-relative mb-4">
                            <canvas id="sales-chart" height="300"></canvas>
                        </div>

                        <div class="d-flex flex-row justify-content-end">
                  <span class="mr-2">
                    <i class="fas fa-square text-primary"></i> Borrow
                  </span>

                            <span>
                    <i class="fas fa-square text-gray"></i> Return
                  </span>
                        </div>
                    </div>
                </div>
                <!-- /.card -->
            </div>
            
            <div class="col-lg-8 " style="display: none" >
                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">Online Store Overview</h3>
                        <div class="card-tools">
                            <a href="#" class="btn btn-sm btn-tool">
                                <i class="fas fa-download"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-tool">
                                <i class="fas fa-bars"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                            <p class="text-success text-xl">
                                <i class="ion ion-ios-refresh-empty"></i>
                            </p>
                            <p class="d-flex flex-column text-right">
                            <span class="font-weight-bold">
                            <i class="ion ion-android-arrow-up text-success"></i> 12%
                            </span>
                            <span class="text-muted">CONVERSION RATE</span>
                            </p>
                        </div>
                        <!-- /.d-flex -->
                        <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                            <p class="text-warning text-xl">
                                <i class="ion ion-ios-cart-outline"></i>
                            </p>
                            <p class="d-flex flex-column text-right">
                            <span class="font-weight-bold">
                            <i class="ion ion-android-arrow-up text-warning"></i> 0.8%
                            </span>
                                <span class="text-muted">SALES RATE</span>
                            </p>
                        </div>
                        <!-- /.d-flex -->
                        <div class="d-flex justify-content-between align-items-center mb-0">
                            <p class="text-danger text-xl">
                                <i class="ion ion-ios-people-outline"></i>
                            </p>
                            <p class="d-flex flex-column text-right">
                            <span class="font-weight-bold">
                            <i class="ion ion-android-arrow-down text-danger"></i> 1%
                            </span>
                                <span class="text-muted">REGISTRATION RATE</span>
                            </p>
                        </div>
                        <!-- /.d-flex -->
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row (main row) -->
        
        
        
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

<?php
include 'footer.php';
?>
<script src="plugins/chart.js/Chart.min.js"></script>
<script src="dist/js/pages/dashboard3.js"></script>
<script type="text/javascript" src="dist/js/jquery.canvasjs.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $.ajax({
            url: 'dashboard_ajax.php',
            type: 'post',
            data: {"widget": 'current_balance'},
            success: function (data, textStatus, jQxhr) {
                el = $("#modal-current-balance").find('table');
                total_invesment = 0;
                total_given = 0;
                total_balance = 0;
                $.each(data, function (key, value) {
                    el.append('<tr><td>' + value.account + '</td>' +
                        '<td style="text-align: right">' + value.invesment + '</td>' +
                        '<td style="text-align: right">' + value.given + '</td>' +
                        '<td style="text-align: right">' + value.balance + '</td></tr>')
                    total_invesment += parseFloat(value.invesment)
                    total_given += parseFloat(value.given)
                    total_balance += parseFloat(value.balance)

                })
                el.append('<tr><th style="text-align: right">Total</th>' +
                    '<th style="text-align: right">' + total_invesment.toFixed(2) + '</th>' +
                    '<th style="text-align: right">' + total_given.toFixed(2) + '</th>' +
                    '<th style="text-align: right">' + total_balance.toFixed(2) + '</th></tr>')
                $('.current-balance-value').text(total_balance.toFixed(2))
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        })


        $.ajax({
            url: 'dashboard_ajax.php',
            type: 'post',
            data: {"widget": 'pending_interest'},
            success: function (data, textStatus, jQxhr) {
                el = $("#modal-pending-interest").find('table');
                total_interest = 0;
                $.each(data, function (key, value) {
                    el.append('<tr><td>' + value.borrower + '</td>' +
                        '<td style="text-align: right">' + value.interest + '</td>' +
                        '</tr>')
                    total_interest += parseFloat(value.interest)

                })
                el.append('<tr><th style="text-align: right">Total</th>' +
                    '<th style="text-align: right">' + total_interest.toFixed(2) + '</th></tr>')
                $('.pending-interest-value').text(total_interest.toFixed(2))
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        })


        $.ajax({
            url: 'dashboard_ajax.php',
            type: 'post',
            data: {"widget": 'recoverables'},
            success: function (data, textStatus, jQxhr) {
                el = $("#modal-recoverables").find('table');
                total_amount = 0;
                $.each(data, function (key, value) {
                    el.append('<tr><td>' + value.lender + '</td>' +
						'<td >' + value.borrower + '</td>' +
                        '<td style="text-align: right">' + parseFloat(value.amount) + '</td>' +
                        '</tr>')
                    total_amount += parseFloat(value.amount)

                })
                el.append('<tr><th style="text-align: right" colspan="2">Total</th>' +
                    '<th style="text-align: right">' + total_amount.toFixed(2) + '</th></tr>')
                $('.recoverables-amount-value').text(total_amount.toFixed(2))
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        })

        $.ajax({
            url: 'dashboard_ajax.php',
            type: 'post',
            data: {"widget": 'unsettled_transactions'},
            success: function (data, textStatus, jQxhr) {
                el = $("#modal-unsettled-transactions").find('table');
                total_interest = 0;
                total_collected_amount = 0;
                total_pending = 0;
                $.each(data, function (key, value) {
                    el.append('<tr><td>' + value.borrower + '</td>' +
                        '<td style="text-align: right">' + value.interest + '</td>' +
                        '<td style="text-align: right">' + value.collected_amount + '</td>' +
                        '<td style="text-align: right">' + value.pending + '</td>' +
                        '</tr>')

                    total_interest += parseFloat(value.interest)
                    total_collected_amount += parseFloat(value.collected_amount)
                    total_pending += parseFloat(value.pending)
                })
                el.append('<tr><th style="text-align: right">Total</th>' +
                    '<th style="text-align: right">' + total_interest.toFixed(2) + '</th>' +
                    '<th style="text-align: right">' + total_collected_amount.toFixed(2) + '</th>' +
                    '<th style="text-align: right">' + total_pending.toFixed(2) + '</th></tr>')
                $('.unsettled-transactions-value').text(total_pending.toFixed(2))
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        })

        $.ajax({
            url: 'dashboard_ajax.php',
            type: 'post',
            data: {"widget": 'roi_wise_loans'},
            success: function (data, textStatus, jQxhr) {
            
                var options = {
                    data: [{
                            type: "pie",
                            startAngle: 45,
                            showInLegend: "true",
                            legendText: "{label}",
                            indexLabel: "{label} ({y})",
                            yValueFormatString:"#,##0.#"%"",
                            dataPoints:  data
                    }]
                };
                $("#ROIWiseLoansChart").CanvasJSChart(options);
                 
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        })

        // Interest Collected Trend Chart
        $.ajax({
            url: 'dashboard_ajax.php',
            type: 'post',
            data: {"widget": 'interest_collected_trend'},
            success: function (data, textStatus, jQxhr) {
                // Calculate total interest and growth
                let totalInterest = data.data.reduce((sum, val) => sum + parseFloat(val), 0);
                let currentMonth = data.data[data.data.length - 1] || 0;
                let previousMonth = data.data[data.data.length - 2] || 0;
                let growth = previousMonth > 0 ? ((currentMonth - previousMonth) / previousMonth * 100).toFixed(1) : 0;
                
                // Update summary values
                $('#total-interest-collected').text(totalInterest.toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
                
                // Update growth indicator
                let growthElement = $('#interest-growth');
                if (growth > 0) {
                    growthElement.html('<i class="fas fa-arrow-up"></i> ' + growth + '%').removeClass('text-danger').addClass('text-success');
                } else if (growth < 0) {
                    growthElement.html('<i class="fas fa-arrow-down"></i> ' + Math.abs(growth) + '%').removeClass('text-success').addClass('text-danger');
                } else {
                    growthElement.html('<i class="fas fa-minus"></i> 0%').removeClass('text-success text-danger').addClass('text-muted');
                }

                // Create the chart
                var ctx = document.getElementById('interest-trend-chart').getContext('2d');
                
                var interestTrendChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Interest Collected',
                            data: data.data,
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#28a745',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 6,
                            pointHoverRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        animation: {
                            onComplete: function() {
                                var chartInstance = this;
                                var ctx = chartInstance.chart.ctx;
                                ctx.font = "bold 10px Arial";
                                ctx.fillStyle = "#28a745";
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'bottom';

                                this.data.datasets.forEach(function(dataset, i) {
                                    var meta = chartInstance.getDatasetMeta(i);
                                    meta.data.forEach(function(bar, index) {
                                        var data = dataset.data[index];
                                        if (data > 0) {
                                            var label = '₹' + Math.round(data).toLocaleString('en-IN');
                                            ctx.fillText(label, bar._model.x, bar._model.y - 5);
                                        }
                                    });
                                });
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '₹' + value.toLocaleString('en-IN');
                                    }
                                },
                                grid: {
                                    color: 'rgba(0,0,0,0.1)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        },
                        elements: {
                            point: {
                                hoverBackgroundColor: '#28a745'
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
                        plugins: {
                            tooltip: {
                                backgroundColor: 'rgba(0,0,0,0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                callbacks: {
                                    label: function(context) {
                                        return 'Interest: ₹' + context.parsed.y.toLocaleString('en-IN', {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        });
                                    }
                                }
                            }
                        }
                    }
                });
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log('Error loading interest trend:', errorThrown);
            }
        })
    })
</script>