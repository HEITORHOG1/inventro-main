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

              // Formatar valores para exibição (2 casas decimais)
              var nitsaleFormatted = parseFloat(nitsale || 0).toFixed(2).replace('.', ',');
              var nitpurchaseFormatted = parseFloat(nitpurchase || 0).toFixed(2).replace('.', ',');

              var salesChartCanvas = document.getElementById('revenue-chart-canvas').getContext('2d');
              var salesChartData = {
    labels  : ["Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"],
    datasets: [
      {
        label               : 'Compras',
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
        label               : 'Vendas',
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
        },
        ticks: {
          callback: function(value) {
            return icon + ' ' + parseFloat(value).toFixed(2);
          }
        }
      }]
    },
    tooltips: {
      callbacks: {
        label: function(tooltipItem, data) {
          var label = data.datasets[tooltipItem.datasetIndex].label || '';
          return label + ': ' + icon + ' ' + parseFloat(tooltipItem.yLabel).toFixed(2);
        }
      }
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
        'Total Vendas ('+icon+' '+nitsaleFormatted+')',
        'Total Compras ('+icon+' '+nitpurchaseFormatted+')',
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
    tooltips: {
      callbacks: {
        label: function(tooltipItem, data) {
          var label = data.labels[tooltipItem.index] || '';
          var value = parseFloat(data.datasets[0].data[tooltipItem.index] || 0).toFixed(2);
          return icon + ' ' + value;
        }
      }
    }
  }
  //Create pie or douhnut chart
  var pieChart = new Chart(pieChartCanvas, {
    type: 'pie',
    data: pieData,
    options: pieOptions
  });

          })
