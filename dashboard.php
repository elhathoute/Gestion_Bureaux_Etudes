<?php
include 'header.php';

$curDate = date('Y-m-d');
$date=date_create(date("Y-m-d"));
date_add($date,date_interval_create_from_date_string("1 week ago"));
$weekAgo = date_format($date,"Y-m-d");

$period = array($curDate,$weekAgo);

?>


<div class="pagetitle">
    <div class="row">
        <div class="col-md-9">
            <h1>Tableau de bord</h1>
        </div>
        <div class="col-md-3">
            <!-- <input type="date" name="" id=""> -->
            <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;border-radius:6px">
                <!-- <i class="fa fa-calendar"></i>&nbsp; -->
                <i class="bi bi-calendar"></i>&nbsp;
                <span></span> <i class="fa fa-caret-down"></i>
            </div>
        </div>
    </div>
   

</div>
<!--End of Page Title-->
<section class="section dashboard">
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-xxl-4 col-md-6">
                    <div class="card info-card sales-card">

                        <!-- <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <li class="dropdown-header text-start">
                                    <h6>Filter</h6>
                                </li>

                                <li><a class="dropdown-item" id="btn-sales-week" href="javascript:void(0)">This Week</a></li>
                                <li><a class="dropdown-item" id="btn-sales-month" href="javascript:void(0)">This Month</a></li>
                                <li><a class="dropdown-item" id="btn-sales-year" href="javascript:void(0)">This Year</a></li>
                            </ul>
                        </div> -->

                        <div class="card-body">
                            <!-- <h5 class="card-title">Sales <span id="salesPeriodTxt">| This Month</span></h5> -->
                            <h5 class="card-title">Ventes <span>| </span></h5>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-cart"></i>
                                </div>
                                <div class="ps-3">
                                    
                                    <h6 id="salesDashTxt"><?= countInvDashSales($period);?></h6>
                                    <span class="text-success small pt-1 fw-bold">Total des ventes</span> <span class="text-muted small pt-2 ps-1"></span>

                                </div>
                            </div>
                        </div>

                    </div>
                </div><!-- End Sales Card -->



                <!-- Revenue Card -->
                <div class="col-xxl-4 col-md-6">
                    <div class="card info-card revenue-card">

                        <!-- <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <li class="dropdown-header text-start">
                                    <h6>Filter</h6>
                                </li>

                                <li><a class="dropdown-item" id="btn-revenue-week" href="javascript:void(0)">This Week</a></li>
                                <li><a class="dropdown-item" id="btn-revenue-month" href="javascript:void(0)">This Month</a></li>
                                <li><a class="dropdown-item" id="btn-revenue-year" href="javascript:void(0)">This Year</a></li>
                            </ul>
                        </div> -->

                        <div class="card-body">
                            <!-- <h5 class="card-title">Revenue <span id="revenuePeriodTxt">| This Week</span></h5> -->
                            <h5 class="card-title">Revenu <span>| </span></h5>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-currency-dollar"></i>
                                </div>
                                <div class="ps-3" style="overflow:auto;">
                                    <h6 id="revenueDashTxt"><?= countInvPayDash($period); ?> DH</h6>
                                    <span class="text-success small pt-1 fw-bold">Revenu total</span> <span class="text-muted small pt-2 ps-1"></span>

                                </div>
                            </div>
                        </div>

                    </div>
                </div><!-- End Revenue Card -->

                <!-- Customers Card -->
                <div class="col-xxl-4 col-md-6">

                    <div class="card info-card customers-card">

                        <!-- <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <li class="dropdown-header text-start">
                                    <h6>Filter</h6>
                                </li>

                                <li><a class="dropdown-item" id="btn-client-week" href="javascript:void(0)">This Week</a></li>
                                <li><a class="dropdown-item" id="btn-client-month" href="javascript:void(0)">This Month</a></li>
                                <li><a class="dropdown-item" id="btn-client-year" href="javascript:void(0)">This Year</a></li>
                            </ul>
                        </div> -->

                        <div class="card-body">
                            <!-- <h5 class="card-title">Customers <span id="cusPeriodTxt">| This Year</span></h5> -->
                            <h5 class="card-title">Maitres d'ouvrage <span>| </span></h5>

                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="ps-3">
                                    <h6 id="cusDashTxt"><?= countClientDash($period); ?></h6>
                                    <span class="text-danger small pt-1 fw-bold">Nombre total de clients</span> <span class="text-muted small pt-2 ps-1"></span>

                                </div>
                            </div>

                        </div>
                    </div>

                </div><!-- End Customers Card -->


            </div>
        </div>
    </div>

    <!-- revenue Chart -->
    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Revenu <span style="color:#899bbd;font-size:14px;font-weight:400">/Cette semaine</span></h5>
                            <div class="chart-container">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Rapport <span style="color:#899bbd;font-size:14px;font-weight:400">/Cette semaine</span></h5>
                            <div class="chart-container">
                                <canvas id="dashChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- chart section -->

</section>

<script>
    //     const DATA_COUNT = 7;
    // const NUMBER_CFG = {count: DATA_COUNT, min: -100, max: 100};
    <?php
    //sales
    $sales = weeklyDashSales();
    $days = $sales["days"];
    $invs =  $sales["invs"];
    //cutomers
    $customers = weeklyDashCustomers();
    $cusDays = $customers["days"];
    $cusInvs = $customers['invs'];

    //Revenue
    $revenue = weeklyDashRevenue();
    $revDays = $revenue["days"];
    $revInvs = $revenue['invs'];

    ?>

    //reverse array
    function reverseDate(strDate) {
        return strDate.split("-").reverse().join("-");
    }

    //function to replace english days with french
    function replaceDay(day) {
        const frenchDays = {
            "Monday": "Lundi",
            "Tuesday": "Mardi",
            "Wednesday": "Mercredi",
            "Thursday": "Jeudi",
            "Friday": "Vendredi",
            "Saturday": "Samedi",
            "Sunday": "Dimanche"
        }

        for (let x in frenchDays) {
            if (day.toLowerCase() == x.toLowerCase()) {
                return frenchDays[x];
            }
        }
    }

    //create function to get last week dayes
    function getLastWeekDays() {
        const now = new Date();
        // const lastWeekDates = [];
        const lastWeekDays = [];
        for (i = 7; i >= 1; i--) {
            var d = new Date(now.getFullYear(), now.getMonth(), now.getDate() - i);
            lastWeekDays.push(reverseDate(d.toLocaleDateString("fr-CA")));
            // lastWeekDays.push(replaceDay(d.toLocaleDateString("en-US", {
            //     weekday: "long"
            // })));

        }
        return lastWeekDays;
    }


    //sales
    const days = <?= json_encode($days); ?>;
    const invs = <?= json_encode($invs); ?>;
    const salesDataArr = [];
    for (let i = 0; i < days.length; i++) {
        salesDataArr.push({
            x: reverseDate(days[i]),
            y: parseInt(invs[i])
        })
    }

    //customers
    const cusDays = <?= json_encode($cusDays); ?>;
    const cusInvs = <?= json_encode($cusInvs); ?>;
    const cusDataArr = [];
    for (let i = 0; i < cusDays.length; i++) {
        cusDataArr.push({
            x: reverseDate(cusDays[i]),
            y: parseInt(cusInvs[i])
        })
    }

    const labels = getLastWeekDays();
    const data = {
        labels: labels,
        datasets: [{
                label: 'Sales',
                data: salesDataArr,
                borderColor: '#4154f1',
                backgroundColor: '#f6f6fe',

            },
            {
                label: 'Customers',
                data: cusDataArr,
                borderColor: '#ff771d',
                backgroundColor: '#ffecdf',

            }
        ]
    };

    const config = {
        type: 'line',
        data: data,
        options: {
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            },
            scales: {
                x: {
                    ticks: {
                        autoSkip: false,
                        minRotation: 30
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                    },
                },
            },
        },
    };



    const ctx = document.getElementById('dashChart');
    new Chart(ctx,
        config,
        data
    );

    //Revenue Chart

    const revDays = <?= json_encode($revDays); ?>;
    const revInvs = <?= json_encode($revInvs); ?>;
    const revDataArr = [];
    for (let i = 0; i < revDays.length; i++) {
        revDataArr.push({
            x: reverseDate(revDays[i]),
            y: parseInt(revInvs[i])
        })
    }

    const revData = {
        labels: labels,
        datasets: [{
            label: 'Sales',
            data: revDataArr,
            borderColor: '#2eca6a',
            backgroundColor: '#e0f8e9',
            fill: true,
        }]
    };

    const revConfig = {
        type: 'line',
        data: revData,
        options: {
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            },
            scales: {
                x: {
                    ticks: {
                        autoSkip: false,
                        minRotation: 30
                    }
                }
            }
        },
    };



    const revCtx = document.getElementById('revenueChart');
    new Chart(revCtx,
        revConfig,
        revData
    );
</script>

<?php include "footer.php"; ?>