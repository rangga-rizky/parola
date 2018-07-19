function load_dashboard_chart(url,chartType){
 $.getJSON(url)                
 .done(function (data) {          
  $('.ui.active.inverted.dimmer').attr("class","ui disabled inverted dimmer");
  var pie_data = new Array();
  var words = new Array();
  var word_count = new Array();
  var bigrams = new Array();
  var bigrams_count = new Array();
  j = 0;
  k = 0;

  for (var i in data.number_of_predicted){
    pie_data[i] = {
      name: data.number_of_predicted[i]["predicted_category"],
      y: data.number_of_predicted[i]["jumlah"]
    }
  }

  for (var key in data.word_count){
    words[j] = key;
    word_count[j] = data.word_count[key];
    j++;
  }

   for (var key in data.top_bigrams){
    bigrams[k] = key;
    bigrams_count[k] = data.top_bigrams[key];
    k++;
  }
     
  barChart("Jumlah Kata",words,word_count,"bar");
  barChart("Jumlah Kata",bigrams,bigrams_count,"bar-bigrams");
  pieChart(null,pie_data,"pie");        

})               
 .fail(function (jqXHR, textStatus, err) {
  $('.loader-element').attr("class","ui disabled inverted dimmer");
  alert("Failed, Please Try Again");
})
}


function barChart(title,labels,data,divId,mode = "bar"){
 Highcharts.chart(divId, {
  chart: {
    type: mode
  },
  title: {
    text: null
  },
  subtitle: {
    text: null
  },
  xAxis: {
    categories: labels,
    title: {
      text: null
    }
  },
  yAxis: {
    min: 0,
    title: {
      text: title,
      align: 'high'
    },
    labels: {
      overflow: 'justify'
    }
  },
  plotOptions: {
    bar: {
      dataLabels: {
        enabled: false
      }
    }
  },
  legend: {
    layout: 'vertical',
    align: 'right',
    verticalAlign: 'top',
    x: -40,
    y: 80,
    floating: true,
    borderWidth: 1,
    backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
    shadow: true
  },
  credits: {
    enabled: false
  },
  series: [{
    name: "jumlah",
    data: data
  }, ]
});

}


function pieChart(title,data,divId){
  Highcharts.chart(divId, {
    chart: {
      plotBackgroundColor: null,
      plotBorderWidth: null,
      plotShadow: false,
      type: 'pie'
    },
    title: {
      text: title
    },
    tooltip: {
      pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
    },
    plotOptions: {
      pie: {
        allowPointSelect: true,
        cursor: 'pointer',
        dataLabels: {
          enabled: true,
          format: '<b>{point.name}</b>: {point.percentage:.1f} %',
          style: {
            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
          }
        }
      }
    },
    series: [{
      name: '',
      colorByPoint: true,
      data: data
    }]
  });  

}