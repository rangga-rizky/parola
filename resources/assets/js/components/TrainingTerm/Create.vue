<template>
	<div>
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark"><i class="fa fa-list-alt"></i> Tambah Kata Kunci</h1>
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
					<div class="col-6">
						<div class="card">
							<div class="card-body">
								<text-field 
									name="term"
									placeholder="Kata Kunci"
									label="Kata Kunci"
									:error="errors.term[0]"
									info=""
									type="text"
									@changed="changeTerm"
								/>

                                <div class="form-group">
                                    <label for="">Kategori</label>
                                    <select class="form-control"  v-model="selectedCategory">
                                        <option v-for="category in categories" v-bind:value="category.id" :key="category.id">
                                            {{ category.category }}
                                        </option>
	                                </select>
                                </div>

								 <div class="form-group">
                                    <label for="">Bobot</label>
                                    <select class="form-control"  v-model="selectedScore">
                                        <option v-for="score in scores" v-bind:value="score.id" :key="score.id">
                                            {{ score.label }}
                                        </option>
	                                </select>
                                </div>


								<button @click="submit" class="btn btn-success" :disabled="pending == true">
									<i :class="{'fa fa-plus':!isPending, 'fa fa-circle-o-notch fa-spin':isPending}" ></i> Tambah
								</button>
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
	data(){
		return{
            pending:false,
            selectedCategory:"AVG",
			selectedScore:"AVG",
            categories:[],
			scores:[
				{id:"MAX",label:"MAX WEIGHT"},
				{id:"AVG",label:"AVG WEIGHT"},
				{id:"MIN",label:"MIN WEIGHT"}
			],
			term:"",
			errors:{
				term:[undefined]
			}
		}
    },
    
    created() {
		  	this.fetchCategory();
	},

    methods:{
		changeTerm(text){
			this.term = text;
		},

		submit(){
			this.pending = true;
                axios.post('/api/training-terms',{
                    term:this.term,
                    category_id:this.selectedCategory,
					score:this.selectedScore,
                    project_id:1,
                })
                .then(({data}) => {                    
					this.pending = false;
					this.$router.push("/training-terms")
                })
                .catch((error) => {      
                    if(error.response.status == 422){
						this.errors = error.response.data.errors;
                    }else{                       
                        alert("Terjadi Kesalahan pada server");
                    } 
                    this.pending = false;
            });  
                         
        },
         fetchCategory() {
		    	axios.get('/api/categories')
			        .then(({data}) => {
                        this.categories = data;
                        this.selectedCategory = data[0].id;
				      })
			        .catch((error) => {	     
					   alert("Terjadi Kesalahan pada server");
		        });  
		    },
	},

	computed:{
		isPending(){
			return this.pending;
		}
	}
}
</script>
