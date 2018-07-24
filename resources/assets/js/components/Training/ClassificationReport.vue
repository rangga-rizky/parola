<template>
	<div>
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark"><i class="fa fa-list-alt"></i> Classification Report</h1>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.container-fluid -->
		</div>
		<!-- /.content-header -->

		<!-- Main content -->
		<section class="content">
			<div class="container-fluid">
				<!-- Small boxes (Stat box) -->
				<div class="row">
					<div class="col-12">
						<div class="card">
                            <span v-if="isLoading" >
								<i class="fa fa-spinner fa-spin"></i> Loading
							</span>
                            <div v-if="!isLoading" class="card-header">   
                                <strong>Jumlah Data : </strong>{{num_of_data}}
                            </div>
							<div v-if="!isLoading" class="card-body">
								<table  class="table table-bordered table-hover ">
									<thead class="thead-dark">
										<tr>
											<th rowspan="2">Category</th>
											<th colspan="2">Naive Bayes Classifier</th>
                                          <th colspan="2">Correlation Meassurement(Binary Matrice)</th> 
                                            <th colspan="2">Correlation Meassurement</th>
                                            <th rowspan="2">Jumlah Data</th>
										</tr>
                                        <tr>
                                            <th>Precission</th>
                                            <th>Recall</th>
                                            <th>Precission</th>
                                            <th>Recall</th>
                                            <th>Precission</th>
                                            <th>Recall</th>
                                        </tr>
									</thead>
									<tbody>
										<tr v-for="(category, index) in categories"  v-bind:key="category.id" >
											<td>{{ category }}</td>
											<td>{{ dataBayes.scores[index].precission.toFixed(2)}}%</td>
                                            <td>{{ dataBayes.scores[index].recall.toFixed(2)}}%</td>
                                           <td>{{ dataManual.scores[index].precission.toFixed(2)}}%</td>
                                            <td>{{ dataManual.scores[index].recall.toFixed(2)}}%</td>
                                            <td>{{ dataAuto.scores[index].precission.toFixed(2)}}%</td>
                                            <td>{{ dataAuto.scores[index].recall.toFixed(2)}}%</td>
                                            <td>{{ dataBayes.scores[index].number_of_data  }}</td>                                           
										</tr>						
                                        <tr>
                                            <td ><strong>Accuracy</strong></td>
                                            <td colspan="2"><strong>{{dataBayes.accuracy.toFixed(3)}}%</strong></td>
                                             <td colspan="2"><strong>{{dataManual.accuracy.toFixed(3)}}%</strong></td> 
                                            <td colspan="2"><strong>{{dataAuto.accuracy.toFixed(3)}}%</strong></td>
                                        </tr>				
									</tbody>
									<tfoot>
									</tfoot>
								
								</table>
							</div>
							<!-- /.card-body -->
							<div class="card-footer">								
								
							</div>
						</div>
					</div>
				</div>
				<!-- /.row -->
			</div><!-- /.container-fluid -->
		</section>
		<!-- /.content -->
	</div>
</template>
<script>
    export default {
    	 data() {
		    return {
              scores:[],
              num_of_data: 0,
              dataBayes: [],
              dataAuto: [],
              dataManual: [],
              categories: [],
		      loading:true,		      
		    };
		  },

		  created() {
		    this.fetch();
		  },

		  methods: {
		    fetch() {
		    	axios.get('/api/trainings/score/all')
			        .then(({data}) => {
                        this.num_of_data = data.number_of_data;
                        this.categories = data.categories;
                        this.dataBayes = data.bayes;
                        this.dataAuto = data.auto;
                        this.dataManual = data.manual;
                        this.loading = false;
				      })
			        .catch((error) => {	     
					   alert("Terjadi Kesalahan pada server");
					   this.loading = false;
		        });  
		    },

		  
		},

		computed: {
		    isLoading(){
		      return this.loading;
		    }
		}	


    }
</script>