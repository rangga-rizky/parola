<template>
	<div>
		<!-- Content Header (Page header) -->

		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark">Data Latih (Correlation Measurement)</h1>
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
							<div class="card-header">	
								<p><strong>Jumlah Data : {{number_of_data}}</strong></p>
								<p>Akurasi : {{accuracy}} %</p>
									<div class="progress">
								  <div class="progress-bar bg-success" role="progressbar" v-bind:style="{width:accuracy+'%' }" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">{{accuracy}}</div>
								</div>
								<br>							
								<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#scoreModal">
									  Classification Report
								</button><br><br>
								<select class="form-control"   @change="fetch" v-model="selectedCategory">
									<option value="0">Semua Kategori</option>
	                                <option v-for="category in categories" v-bind:value="category.id" :key="category.id">
	                                    {{ category.category }}
	                                </option>
	                             </select>
								<modal-score v-bind:scores="scores" v-bind:accuracy="accuracy"></modal-score>
							</div>

							<div class="card-body">
								<span v-if="isLoading" >
									<i class="fa fa-spinner fa-spin"></i> Loading
								</span>
									 <div class="table-responsive" style="overflow: auto">
								<table v-if="!isLoading" class="table table-bordered table-hover">
									<thead>
										<tr>
											<th>id</th>
											<th>Pengaduan</th>
											<th>Words</th>
											<th>Label</th>
											<th>Predicted</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(training) in trainings"  v-bind:key="training.id" >
											<td>{{ training.id }}</td>
											<td>{{ training.pengaduan }}</td>
											<td>{{ training.words}}</td>
											<td>{{ training.label }}</td>
											<td>{{ training.prediksi_korelasi}}</td>
										</tr>										
									</tbody>
									<tfoot>
									</tfoot>
								
								</table>
									 </div>
							</div>
							<!-- /.card-body -->
							<div class="card-footer">								
								<pagination v-bind:pagination="paginator"  @paginate="fetch()" ></pagination>
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
		      title:"",
		      paginator:{},
		      trainings:[],
			  selectedCategory:"0",
		      categories:[],
		      scores:[],
		      accuracy:0,
		      number_of_data:0,
		      loading:true,		      
		    };
		  },

		  created() {
			  this.fetchCategory();
		    this.fetch();
		    this.fetchScore();
		  },


		  methods: {
		    fetch() {
				let categoryParam = "";
		    	if(this.selectedCategory != "0"){
		    		categoryParam = "&category_id="+this.selectedCategory;
		    	}
		    	axios.get('/api/trainings?page='+this.paginator.current_page+categoryParam)
			        .then(({data}) => {
				        this.trainings = data.data;
				        this.paginator = data.paginator;
				        this.loading = false;
				      })
			        .catch((error) => {	     
					   alert("Terjadi Kesalahan pada server");
					   this.loading = false;
		        });  
		    },    

		    
		    fetchScore() {
		    	axios.get('/api/trainings/score/auto' )
			        .then(({data}) => {
				        this.accuracy = data.accuracy;
				        this.number_of_data = data.number_of_data;
				        this.scores = data.scores;
				      })
			        .catch((error) => {	     
					   alert("Terjadi Kesalahan pada server");
		        });  
		    }, 

		  	  fetchCategory() {
		    	axios.get('/api/categories')
			        .then(({data}) => {
				        this.categories = data;
				      })
			        .catch((error) => {	     
					   alert("Terjadi Kesalahan pada server");
		        });  
		    },
		},

		computed: {
		    isLoading(){
		      return this.loading;
		    },	   

		}	


    }
</script>