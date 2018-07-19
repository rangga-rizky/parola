<template>
	<div>
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark"><i class="fa fa-list-alt"></i> Edit Kategori</h1>
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
									name="category"
									placeholder="Nama Kategory"
									label="Kategori"
                                    :value="category"
									:error="errors.category[0]"
									info=""
									type="text"
									@changed="changeCategory"
								/>

								<button @click="submit" class="btn btn-success" :disabled="pending == true">
									<i :class="{'fa fa-edit':!isPending, 'fa fa-circle-o-notch fa-spin':isPending}" ></i> Update
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

    created(){
        this.fetch();
    },

	data(){
		return{
            pending:false,
			category:"",
			errors:{
				category:[undefined]
			}
		}
	},

    methods:{
        fetch(){
		  	axios.get('/api/categories/'+this.id)
		        .then(({data}) => {
                    this.category = data.category;
		        })
		        .catch((error) => {	    
                   if(error.response.status == 404){
                       alert("Data tidak ditemukan");
                       this.$router.push("/categories")
                   }else{
                       alert("Terjadi Kesalahan pada server");
                   } 
		    });  
		},

		changeCategory(text){
			this.category = text;
		},

		submit(){
			this.pending = true;
                axios.put('/api/categories/'+this.id,{
                    category:this.category,
                    project_id:1,
                })
                .then(({data}) => {                    
					this.pending = false;
					this.$router.push("/categories")
                })
                .catch((error) => {      
                    if(error.response.status == 422){
						this.errors = error.response.data.errors;
                    }else{                       
                        alert("Terjadi Kesalahan pada server");
                    } 
                    this.pending = false;
            });  
                         
		}
	},

	computed:{
		isPending(){
			return this.pending;
		}
	}
}
</script>
