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
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
          },
          title: {
            text: this.title
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
            data: []
          }]
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
          this.options.series[0].data = data;
      })
      .catch((error) => {      
         console.log(error);
         alert("Terjadi Kesalahan pada server");
     });  
    },
  },

   watch: { 
      	url: function(newVal, oldVal) { 
          console.log(newVal);
            this.fetch();
        }
      }
}
</script>