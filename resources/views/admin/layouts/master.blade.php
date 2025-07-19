<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en" data-theme="light">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Wowdash - Bootstrap 5 Admin Dashboard HTML Template</title>
    <link
      rel="icon"
      type="image/png"
      href="{{ asset('admin') }}/assets/images/favicon.png"
      sizes="16x16"
    />
    <!-- remix icon font css  -->
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/remixicon.css" />
    <!-- BootStrap css -->
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/bootstrap.min.css" />
    <!-- Apex Chart css -->
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/apexcharts.css" />
    <!-- Data Table css -->
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/dataTables.min.css" />
    <!-- Text Editor css -->
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/editor-katex.min.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/editor.atom-one-dark.min.css" />
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/editor.quill.snow.css" />
    <!-- Date picker css -->
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/flatpickr.min.css" />
    <!-- Calendar css -->
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/full-calendar.css" />
    <!-- Vector Map css -->
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/jquery-jvectormap-2.0.5.css" />
    <!-- Popup css -->
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/magnific-popup.css" />
    <!-- Slick Slider css -->
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/slick.css" />
    <!-- prism css -->
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/prism.css" />
    <!-- file upload css -->
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/file-upload.css" />

    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/lib/audioplayer.css" />
    <!-- main css -->
    <link rel="stylesheet" href="{{ asset('admin') }}/assets/css/style.css" />
  </head>
  <body></body>
</html>

 @include('admin.layouts.include.sidebar')
 @include('admin.layouts.include.header')



  @yield('main-content')
  <footer class="d-footer">
  <div class="row align-items-center justify-content-between">
    <div class="col-auto">
      <p class="mb-0">© 2025 WowDash. All Rights Reserved.</p>
    </div>
    <div class="col-auto">
      <p class="mb-0">Made by <span class="text-primary-600">wowtheme7</span></p>
    </div>
  </div>
</footer>
</main>
  <!-- jQuery library js -->
  <script src="{{ asset('admin') }}/assets/js/lib/jquery-3.7.1.min.js"></script>
  <!-- Bootstrap js -->
  <script src="{{ asset('admin') }}/assets/js/lib/bootstrap.bundle.min.js"></script>
  <!-- Apex Chart js -->
  <script src="{{ asset('admin') }}/assets/js/lib/apexcharts.min.js"></script>
  <!-- Data Table js -->
  <script src="{{ asset('admin') }}/assets/js/lib/dataTables.min.js"></script>
  <!-- Iconify Font js -->
  <script src="{{ asset('admin') }}/assets/js/lib/iconify-icon.min.js"></script>
  <!-- jQuery UI js -->
  <script src="{{ asset('admin') }}/assets/js/lib/jquery-ui.min.js"></script>
  <!-- Vector Map js -->
  <script src="{{ asset('admin') }}/assets/js/lib/jquery-jvectormap-2.0.5.min.js"></script>
  <script src="{{ asset('admin') }}/assets/js/lib/jquery-jvectormap-world-mill-en.js"></script>
  <!-- Popup js -->
  <script src="{{ asset('admin') }}/assets/js/lib/magnifc-popup.min.js"></script>
  <!-- Slick Slider js -->
  <script src="{{ asset('admin') }}/assets/js/lib/slick.min.js"></script>
  <!-- prism js -->
  <script src="{{ asset('admin') }}/assets/js/lib/prism.js"></script>
  <!-- file upload js -->
  <script src="{{ asset('admin') }}/assets/js/lib/file-upload.js"></script>
  <!-- audioplayer -->
  <script src="{{ asset('admin') }}/assets/js/lib/audioplayer.js"></script>
  
  <!-- main js -->
  <script src="{{ asset('admin') }}/assets/js/app.js"></script>

<script>
    // ===================== Income VS Expense Start =============================== 
    function createChartTwo(chartId, color1, color2) {
        var options = {
            series: [{
                name: 'income',
                data: [48, 35, 50, 32, 48, 40, 55, 50, 60]
            }, {
                name: 'Expenses',
                data: [12, 20, 15, 26, 22, 30, 25, 35, 25]
            }],
            legend: {
                show: false 
            },
            chart: {
                type: 'area',
                width: '100%',
                height: 270,
                toolbar: {
                    show: false
                },
                padding: {
                    left: 0,
                    right: 0,
                    top: 0,
                    bottom: 0
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3,
                colors: [color1, color2], // Use two colors for the lines
                lineCap: 'round'
            },
            grid: {
                show: true,
                borderColor: '#D1D5DB',
                strokeDashArray: 1,
                position: 'back',
                xaxis: {
                    lines: {
                        show: false
                    }
                },
                yaxis: {
                    lines: {
                        show: true
                    }
                },
                row: {
                    colors: undefined,
                    opacity: 0.5
                },
                column: {
                    colors: undefined,
                    opacity: 0.5
                },
                padding: {
                    top: -20,
                    right: 0,
                    bottom: -10,
                    left: 0
                },
            },
            colors: [color1, color2], // Set color for series
            fill: {
                type: 'gradient',
                colors: [color1, color2], // Use two colors for the gradient
                // gradient: {
                //     shade: 'light',
                //     type: 'vertical',
                //     shadeIntensity: 0.5,
                //     gradientToColors: [`${color1}`, `${color2}00`], // Bottom gradient colors with transparency
                //     inverseColors: false,
                //     opacityFrom: .6,
                //     opacityTo: 0.3,
                //     stops: [0, 100],
                // },
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0.5,
                    gradientToColors: [undefined, `${color2}00`], // Apply transparency to both colors
                    inverseColors: false,
                    opacityFrom: [0.4, 0.6], // Starting opacity for both colors
                    opacityTo: [0.3, 0.3], // Ending opacity for both colors
                    stops: [0, 100],
                },
            },
            markers: {
                colors: [color1, color2], // Use two colors for the markers
                strokeWidth: 3,
                size: 0,
                hover: {
                    size: 10
                }
            },
            xaxis: {
                labels: {
                    show: false
                },
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                tooltip: {
                    enabled: false
                },
                labels: {
                    formatter: function (value) {
                        return value;
                    },
                    style: {
                        fontSize: "14px"
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function (value) {
                    return "$" + value + "k";
                    },
                    style: {
                    fontSize: "14px"
                    }
                },
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm'
                }
            }
        };

        var chart = new ApexCharts(document.querySelector(`#${chartId}`), options);
        chart.render();
    }

    createChartTwo('incomeExpense', '#487FFF', '#FF9F29');
    // ===================== Income VS Expense End =============================== 

    // ================================ Users Overview Donut chart Start ================================ 
    var options = { 
      series: [30, 30, 20, 20],
      colors: ['#FF9F29', '#487FFF', '#45B369', '#9935FE'],
      labels: ['Purchase', 'Sales', 'Expense', 'Gross Profit'] ,
      legend: {
          show: false 
      },
      chart: {
        type: 'donut',    
        height: 270,
        sparkline: {
          enabled: true // Remove whitespace
        },
        margin: {
            top: 0,
            right: 0,
            bottom: 0,
            left: 0
        },
        padding: {
          top: 0,
          right: 0,
          bottom: 0,
          left: 0
        }
      },
      stroke: {
        width: 0,
      },
      dataLabels: {
        enabled: true
      },
      responsive: [{
        breakpoint: 480,
        options: {
          chart: {
            width: 200
          },
          legend: {
            position: 'bottom'
          }
        }
      }],
    };

    var chart = new ApexCharts(document.querySelector("#userOverviewDonutChart"), options);
    chart.render();
    // ================================ Users Overview Donut chart End ================================ 
  
    // ================================ Purchase & sale chart End ================================ 
    var options = {
      series: [{
        name: 'Net Profit',
        data: [44, 100, 40, 56, 30, 58, 50]
      }, {
        name: 'Free Cash',
        data: [60, 120, 60, 90, 50, 95, 90]
      }],
      colors: ['#45B369', '#FF9F29'],
      labels: ['Active', 'New', 'Total'] ,
      
      legend: {
          show: false 
      },
      chart: {
        type: 'bar',
        height: 260,
        toolbar: {
          show: false
        },
      },
      grid: {
          show: true,
          borderColor: '#D1D5DB',
          strokeDashArray: 4, // Use a number for dashed style
          position: 'back',
      },
      plotOptions: {
        bar: {
          borderRadius: 4,
          columnWidth: 8,
        },
      },
      dataLabels: {
        enabled: false
      },
      states: {
        hover: {
        filter: {
            type: 'none'
            }
        }
    },
      stroke: {
        show: true,
        width: 0,
        colors: ['transparent']
      },
      xaxis: {
        categories: ['Mon', 'Tues', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun'],
      },
      fill: {
        opacity: 1,
        width: 18,
      },
    };

    var chart = new ApexCharts(document.querySelector("#purchaseSaleChart"), options);
    chart.render();
  // ================================ Purchase & sale chart End ================================ 

</script>


</body>
</html>
