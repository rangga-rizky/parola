<template>
    <div>
        <!-- Content Header (Page header) -->
			<div class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1 class="m-0 text-dark"><i class="fa fa-laptop"></i> {{title}}</h1>
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div><!-- /.container-fluid -->
			</div>
			<!-- /.content-header -->

			<!-- Main content -->
			<section class="content">
				<div class="container-fluid">
					
					<div class="row">
						<div class="col-6">
							<div class="card">
								<div class="card-body">
									<word-cloud
									  title="Word CLoud"
									  :url="'/api/dashboard/'+slug+'/top-words'">
									></word-cloud>
								</div>								
							</div>							
						</div>

						<div class="col-6">
							<div class="card">
								<div class="card-body">
									<line-chart 
										title="Jumlah data Masuk"
										:url="'/api/dashboard/'+slug+'/frekuensi'"></line-chart>
								</div>								
							</div>							
						</div>

					</div>

					<!-- <div class="row">
						<div class="col-3" v-for="(cluster,index) in clusteredWords" :key="index">
							<div class="card" @click="fetchByCluster(index)" :class="{ 'activated-cluster': isClusterActive(index+1) }">
								<div class="card-header">
									<h6>Cluster {{index+1}}</h6>
								</div>
								<div class="card-body">
									<div class="bg-blue" v-for="(word,index) in cluster" :key="index">
										{{word.name}} <span class="badge badge-light">{{word.weight}}</span>
									</div>&nbsp;
								</div>								
							</div>							
						</div>
					</div> -->

					<div class="row">
					<div class="col-12">					
						<div class="card">
							<div class="card-header">
								<strong>Jumlah Data : </strong>{{getNumberOfData}}<br>
								<strong>Data Terbaru : </strong>{{meta.latest_data}}<br><br>
							</div>
							<div class="card-body">
								<span v-if="isLoading" >
									<i class="fa fa-spinner fa-spin"></i> Loading
								</span>
								<table v-if="!isLoading" class="table table-bordered table-hover">
									<thead>
										<tr>
											<th>id</th>
											<th>Data</th>
											<th>predicted</th>
											<th>Date</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="(complaint) in complaints"  v-bind:key="complaint.id" >
											<td>{{ complaint.id }}</td>
											<td>{{ complaint.tweet }}</td>
											<td>{{ complaint.predicted }}</td>
											<td>{{ complaint.date }}</td>
										</tr>										
									</tbody>
									<tfoot>
									</tfoot>
								
								</table>
							</div>
							<!-- /.card-body -->
							<div class="card-footer">								
								<pagination v-bind:pagination="paginator"  @paginate="fetch()" ></pagination>
							</div>
						</div>
					</div>
				</div>

				</div><!-- /.container-fluid -->
			</section>
			<!-- /.content -->
    </div>
</template>
<script>
    export default {
    	props:['slug'],

    	data() {
		    return {
		      loading:true,	
			  clusteredWords : [],
		      complaints:[],
		      paginator: {},
		      meta:{},
		      title:"",
			  cluster:0		      
		    };
		},

		created(){
			this.fetch();
			this.fetchClusteredWords();
 	    },

 	    methods:{
 	    	fetch() {
		     	axios.get('/api/complaints/category/'+this.slug+'?page='+this.paginator.current_page)
			        .then(({data}) => {
				        this.complaints = data.data;
				        this.title = data.title;
				        this.meta = data.meta;
				        this.paginator = data.paginator;
				        this.loading = false;
				      })
			        .catch((error) => {	     
					   alert("Terjadi Kesalahan pada server");
					   this.loading = false;
		        }); 
		     },

			 fetchByCluster(cluster) {
				if((cluster+1) == this.cluster){
					this.cluster = 0;
					this.fetch();
				} 
				else{
					this.cluster = cluster+1;
					axios.get('/api/complaints/category/'+this.slug+'?cluster='+cluster+'&page='+this.paginator.current_page)
						.then(({data}) => {
							this.complaints = data.data;
							this.title = data.title;
							this.meta = data.meta;						
							console.log(data.meta);
							this.paginator = data.paginator;
							this.loading = false;
						})
						.catch((error) => {	     
						alert("Terjadi Kesalahan pada server");
						this.loading = false;
					}); 
				}
		     },

			 fetchClusteredWords() {
		     	axios.get('/api/dashboard/'+this.slug+'/clustered-words')
			        .then(({data}) => {
				        this.clusteredWords = data;
				      })
			        .catch((error) => {	     
					   alert("Terjadi Kesalahan pada server");
		        }); 
		     },

			 
			isClusterActive(cluster) {
            	return this.cluster === cluster;
          	},	 
 	    },

		 
		computed:{
			isLoading(){
				return this.loading;
			},

			getNumberOfData(){
				return this.meta.number_of_data;
			},

		}
 	}
</script>
<style>
	.bg-blue{
		background-color: lightseagreen;
		color: white;
		padding: 3px;
		margin-right:2px;
		margin-top: 2px;
		display: inline-block;
		border-radius: 20%;
	}

	.activated-cluster{
		background-color: #3f51b5;
		color:white;
	}	
</style>