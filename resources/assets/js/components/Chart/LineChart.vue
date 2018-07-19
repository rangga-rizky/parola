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

        title: {
            text: this.title
        },

        subtitle: {
            text: 'Per bulan'
        },

        yAxis: {
            title: {
                text: 'Jumlah data'
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle'
        },

        xAxis: {
               categories: []
        },

        series: [{
            name: 'Jumlah Data',
            data: []
        }],

        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                },
                chartOptions: {
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom'
                    }
                }
            }]
        }
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
          this.options.series[0].data = data.values;
      })
      .catch((error) => {      
         console.log(error);
         alert("Terjadi Kesalahan pada server");
     });  
    },
  },
}
</script>