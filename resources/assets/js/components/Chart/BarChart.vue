<template>
  <highcharts  :options="options"></highcharts>
</template>

<script>

var Highcharts = require('highcharts');
export default {
  props: {
      title: {
          type: String,
          required: true
      },
      url: {
          type: String,
          required: true
      }
  },

  data () {
    return {
      options: {
          chart: {
            type: "bar"
          },
          title: {
            text: this.title
          },
          subtitle: {
            text: null
          },
          xAxis: {
            categories: [],
            title: {
              text: null
            }
          },
          yAxis: {
            min: 0,
            title: {
              text: null,
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
            backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
            borderWidth: 1,
            shadow: true
          },
          credits: {
            enabled: false
          },
          series: [{
            name: "jumlah",
            data: []
          }, ]
      }
    }
  },


  created() {
    this.fetch();
  },

  methods:{
    fetch() {
      axios.get(this.url)
      .then(({data}) => {   
          this.options.xAxis.categories = data.labels;
          this.options.series[0].data = data.data;
      })
      .catch((error) => {      
         console.log(error);
         alert("Terjadi Kesalahan pada server");
     });  
    },
  },

  watch: { 
      	url: function(newVal, oldVal) { 
            this.fetch();
        }
      }
}
</script>