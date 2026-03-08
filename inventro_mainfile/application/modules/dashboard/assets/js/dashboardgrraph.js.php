<?php header('Content-Type: application/javascript; charset=UTF-8'); ?>
"use strict";
$(function (){
              var month=$("#month").val();
              var totalpurchase=$("#totalpurchase").val();
              var str2 = totalpurchase.substring(0, totalpurchase.length - 1);
              var res = str2.split(",");
              var totalsale=$("#totalsale").val();
              var nitsale=$("#nitsale").val();
              var nitpurchase=$("#nitpurchase").val();
              var str3 = totalsale.substring(0, totalsale.length - 1);
              var res3 = str3.split(",");
              var icon=$("#currencyicon").val();
              var salesChartCanvas = document.getElementById('revenue-chart-canvas').getContext('2d');
              var salesChartData = {
    labels  : [<?php
                    for ($i=1; $i <= 12; $i++) {
                        if ($i==1) {
                            echo '"January",';
                        }elseif ($i==2) {
                            echo '"February",';
                        }elseif ($i==3) {
                            echo '"March",';
                        }elseif ($i==4) {
                            echo '"April",';
                        }elseif ($i==5) {
                            echo '"May",';
                        }elseif ($i==6) {
                           echo '"June",';
                        }elseif ($i==7) {
                           echo '"July",';
                        }elseif ($i==8) {
                           echo '"August",';
                        }elseif ($i==9) {
                           echo '"September",';
                        }elseif ($i==10) {
                           echo '"October",';
                        }elseif ($i==11) {
                           echo '"November",';
                        }elseif ($i==12) {
                           echo '"December"';
                        }
                    }
                ?>],
    datasets: [
      {
        label               : 'Purchase',
        backgroundColor     : '#F7DC6F',
        borderColor         : 'rgba(60,141,188,0.8)',
        pointRadius          : false,
        pointColor          : '#3b8bba',
        pointStrokeColor    : 'rgba(60,141,188,1)',
        pointHighlightFill  : '#fff',
        pointHighlightStroke: 'rgba(60,141,188,1)',
        data                : [0,0]
      },
      {
        label               : 'Sale',
        backgroundColor     : '#28B463',
        borderColor         : 'rgba(210, 214, 222, 1)',
        pointRadius         : true,
        pointColor          : 'rgba(210, 214, 222, 1)',
        pointStrokeColor    : '#c1c7d1',
        pointHighlightFill  : '#fff',
        pointHighlightStroke: 'rgba(220,220,220,1)',
        data                : [0,0]
      },
    ]
  }

               salesChartData.datasets[0].data = res;
               salesChartData.datasets[1].data = res3;
 

  var salesChartOptions = {
    maintainAspectRatio : false,
    responsive : true,
    legend: {
      display: true
    },
    scales: {
      xAxes: [{
        gridLines : {
          display : true,
        }
      }],
      yAxes: [{
        gridLines : {
          display : true,
        }
      }]
    }
  }

  // This will get the first returned node in the jQuery collection.
  var salesChart = new Chart(salesChartCanvas, { 
      type: 'bar', 
      data: salesChartData, 
      options: salesChartOptions
    }
  )


 var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
  var pieData        = {
    labels: [
        'Total Sales ('+icon+' '+nitsale+')', 
        'Total Purchase ('+icon+' '+nitpurchase+')',
    ],
    datasets: [
      {
        data: [nitsale,nitpurchase],
        backgroundColor : ['#28B463', '#F7DC6F'],
      }
    ]
  }
  var pieOptions = {
    legend: {
      display: true
    },
    maintainAspectRatio : true,
    responsive : true,
  }
  //Create pie or douhnut chart
  // You can switch between pie and douhnut using the method below.
  var pieChart = new Chart(pieChartCanvas, {
    type: 'pie',
    data: pieData,
    options: pieOptions      
  });

          })
