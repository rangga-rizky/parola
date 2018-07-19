<template>
	<div>
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark"><i class="fa fa-list-alt"></i> Tambah Data</h1>
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
					<div class="col-8">
						<div class="card">
							<div class="card-body">
								<text-field 
									name="complaint"
									placeholder="Tambahkan data"
									label="Data"
									:error="errors.complaint[0]"
									info=""
									type="text"
									@changed="changecomplaint"
								/>

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
			complaint:"",
			errors:{
				complaint:[undefined]
			}
		}
	},

    methods:{
		changecomplaint(text){
			this.complaint = text;
		},
		

		submit(){
			this.pending = true;
                axios.post('/api/complaints',{
                    complaint:this.complaint,
                    project_id:1,
                })
                .then(({data}) => {                    
					this.pending = false;
					this.$router.push("/complaints")
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
