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
     
         series: [{
            type: 'wordcloud',
            data: [],
            name: 'Occurrences'
         }],

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
}
</script>