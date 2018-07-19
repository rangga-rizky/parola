<template>
	<div>
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark"><i class="fa fa-list-alt"></i> Edit Kata Kunci</h1>
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
                                    :value="term"
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
    props:["id"],

	data(){
		return{
            pending:false,
            selectedCategory:"",
            categories:[],
			term:"",
			errors:{
				term:[undefined]
			}
		}
    },
    
    created() {
              this.fetchCategory();
              this.fetch();
	},

    methods:{
		changeTerm(text){
			this.term = text;
        },
        
        fetch(){
		  	axios.get('/api/training-terms/'+this.id)
		        .then(({data}) => {
                    this.term = data.term;
                    this.selectedCategory = data.category_id;
		        })
		        .catch((error) => {	    
                   if(error.response.status == 404){
                       alert("Data tidak ditemukan");
                       this.$router.push("/terms")
                   }else{
                       alert("Terjadi Kesalahan pada server");
                   } 
		    });  
		},

		submit(){
			this.pending = true;
                 axios.put('/api/training-terms/'+this.id,{
                    term:this.term,
                    category_id:this.selectedCategory,
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
