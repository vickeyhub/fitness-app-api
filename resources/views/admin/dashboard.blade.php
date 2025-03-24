@extends('layouts.admin')
@section('content')


        <div class="wrapper wrapper-content  animated fadeInRight">
            <!-- <a href="addcategory.html" class="btn btn-primary">Add
                        Category</a> -->
            <div class="row m-t-xs">
                <div class="col-md-12">
                    <h2>Hello, Welcome Here.</h2>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="ibox float-e-margins">
                                <!-- <div class="ibox-title">
                                            <h5 class="text-center">Total No of Clients</h5>
                                        </div> -->
                                <div class="ibox-content"
                                    style="border-bottom: 10px solid transparent; border-image: linear-gradient(to right, #47c1cf, #3e558b); border-image-slice: 1;">
                                    <div>
                                        <a href="" class="text-info">
                                            <h2 class=""><span><i class="fa fa-users"></i></span> Total No of
                                                Users
                                            </h2>
                                        </a>
                                    </div>
                                    <div class="counter" data-target="254322"></div>
                                    <!-- <h1 class="no-margins ">2,346</h1> -->
                                    <!-- <div style="margin-bottom:40px"> -->
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="ibox float-e-margins">
                                <!-- <div class="ibox-title">
                                                <h5>Total No of Technician</h5>
                                            </div> -->
                                <div class="ibox-content"
                                    style="border-bottom: 10px solid transparent; border-image: linear-gradient(to right, #47c1cf, #3e558b); border-image-slice: 1;">
                                    <div>
                                        <a href="" class="text-info">
                                            <h2><span><i class="fa fa-building"></i></span> Total No of Gym
                                            </h2>
                                        </a>
                                    </div>
                                    <div class="counter" data-target="12000"></div>
                                    <!-- <h1 class="no-margins ">2,346</h1> -->

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="ibox float-e-margins">
                                <!-- <div class="ibox-title">
                                                <h5>Total No of Tickets</h5>
                                            </div> -->
                                <div class="ibox-content"
                                    style="border-bottom: 10px solid transparent; border-image: linear-gradient(to right, #47c1cf, #3e558b); border-image-slice: 1;">
                                    <div>
                                        <a href="" class="text-info">
                                            <h2><span><i class="fa fa-file"></i></span> Total No of Booking</h2>
                                        </a>
                                    </div>
                                    <div class="counter" data-target="1000"></div>
                                    <!-- <h1 class="no-margins ">2,346</h1> -->
                                    <!-- <div style="margin-bottom:40px"></div> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">

                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <h5>Total Users
                                        <small></small>
                                    </h5>
                                </div>
                                <div class="ibox-content">
                                    <div class="row">
                                        <div class="col-sm-9 m-b-xs">
                                            <div data-toggle="buttons" class="btn-group">
                                                <label class="btn btn-sm btn-white"> <input type="radio" id="option1"
                                                        name="options"> Day </label>
                                                <label class="btn btn-sm btn-white"> <input type="radio" id="option2"
                                                        name="options"> Week </label>
                                                <label class="btn btn-sm btn-white active"> <input type="radio"
                                                        id="option3" name="options"> Month </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="input-group"><input type="text" placeholder="Search"
                                                    class="input-sm form-control"> <span class="input-group-btn">
                                                    <button type="button" class="btn btn-sm btn-primary">
                                                        Go!</button> </span></div>
                                        </div>
                                    </div>
                                    <div>
                                        <canvas id="myChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">

                            <div class="ibox float-e-margins">
                                <div class="ibox-title">
                                    <h5>Total Bookings
                                        <small></small>
                                    </h5>
                                </div>
                                <div class="ibox-content">
                                    <div class="row">
                                        <div class="col-sm-9 m-b-xs">
                                            <div data-toggle="buttons" class="btn-group">
                                                <label class="btn btn-sm btn-white"> <input type="radio" id="option1"
                                                        name="options"> Day </label>
                                                <label class="btn btn-sm btn-white"> <input type="radio" id="option2"
                                                        name="options"> Week </label>
                                                <label class="btn btn-sm btn-white active"> <input type="radio"
                                                        id="option3" name="options"> Month </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="input-group"><input type="text" placeholder="Search"
                                                    class="input-sm form-control"> <span class="input-group-btn">
                                                    <button type="button" class="btn btn-sm btn-primary">
                                                        Go!</button> </span></div>
                                        </div>
                                    </div>
                                    <div>
                                        <canvas id="myChartone"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    showMethod: 'slideDown',
                    timeOut: 4000
                };
                toastr.success(' Hello ', 'Welcome to Admin');

            }, 1300);


            var data1 = [
                [0, 4],
                [1, 8],
                [2, 5],
                [3, 10],
                [4, 4],
                [5, 16],
                [6, 5],
                [7, 11],
                [8, 6],
                [9, 11],
                [10, 30],
                [11, 10],
                [12, 13],
                [13, 4],
                [14, 3],
                [15, 3],
                [16, 6]
            ];
            var data2 = [
                [0, 1],
                [1, 0],
                [2, 2],
                [3, 0],
                [4, 1],
                [5, 3],
                [6, 1],
                [7, 5],
                [8, 2],
                [9, 3],
                [10, 2],
                [11, 1],
                [12, 0],
                [13, 2],
                [14, 8],
                [15, 0],
                [16, 0]
            ];
            $("#flot-dashboard-chart").length && $.plot($("#flot-dashboard-chart"), [
                data1, data2
            ], {
                series: {
                    lines: {
                        show: false,
                        fill: true
                    },
                    splines: {
                        show: true,
                        tension: 0.4,
                        lineWidth: 1,
                        fill: 0.4
                    },
                    points: {
                        radius: 0,
                        show: true
                    },
                    shadowSize: 2
                },
                grid: {
                    hoverable: true,
                    clickable: true,
                    tickColor: "#d5d5d5",
                    borderWidth: 1,
                    color: '#d5d5d5'
                },
                colors: ["#1ab394", "#1C84C6"],
                xaxis: {},
                yaxis: {
                    ticks: 4
                },
                tooltip: false
            });

            var doughnutData = {
                labels: ["App", "Software", "Laptop"],
                datasets: [{
                    data: [300, 50, 100],
                    backgroundColor: ["#a3e1d4", "#dedede", "#9CC3DA"]
                }]
            };


            var doughnutOptions = {
                responsive: false,
                legend: {
                    display: false
                }
            };


            var ctx4 = document.getElementById("doughnutChart").getContext("2d");
            new Chart(ctx4, {
                type: 'doughnut',
                data: doughnutData,
                options: doughnutOptions
            });

            var doughnutData = {
                labels: ["App", "Software", "Laptop"],
                datasets: [{
                    data: [70, 27, 85],
                    backgroundColor: ["#a3e1d4", "#dedede", "#9CC3DA"]
                }]
            };


            var doughnutOptions = {
                responsive: false,
                legend: {
                    display: false
                }
            };


            var ctx4 = document.getElementById("doughnutChart2").getContext("2d");
            new Chart(ctx4, {
                type: 'doughnut',
                data: doughnutData,
                options: doughnutOptions
            });

        });
    </script>
    <!-- counterscript -->
    <script>
        const counters = document.querySelectorAll('.counter');

        counters.forEach(counter => {
            counter.innerText = '0';

            const updateCounter = () => {
                const target = +counter.getAttribute('data-target')
                const c = +counter.innerText;

                const increment = target / 150;

                if (c < target) {
                    counter.innerText = `${Math.ceil(c + increment)}`
                    setTimeout(updateCounter, 1)
                } else {
                    counter.innerText = target
                }
            }
            updateCounter();
        })
    </script>
    <!-- mychartjs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('myChart');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                datasets: [{
                    label: 'Total Users',
                    data: [12, 19, 3, 5, 2, 3],
                    borderWidth: 1,
                    backgroundColor: "#3e558b"
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    <script>
        const xValues = [100, 200, 300, 400, 500, 600, 700, 800, 900, 1000];

        new Chart("myChartone", {
            type: "line",
            data: {
                labels: xValues,
                datasets: [{
                    data: [1600, 1700, 1700, 1900, 2000, 2700, 4000, 5000, 6000, 7000],
                    borderColor: "green",
                    fill: false,
                    label: "Total Users"
                }, {
                    data: [300, 700, 2000, 5000, 6000, 4000, 2000, 1000, 200, 100],
                    borderColor: "#47c1cf",
                    fill: false,
                    label: "Total Gym"
                }, {
                    data: [4000, 2000, 1000, 200, 100, 300, 700, 2000, 5000, 6000],
                    borderColor: "#32385a",
                    fill: false,
                    label: "Total Bookings"
                }]
            },
            options: {
                legend: {
                    display: false
                }
            }
        });
    </script>
@endsection
