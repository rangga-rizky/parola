<template>
	<div>
		<!-- Content Header (Page header) -->
		<div class="content-header">
			<div class="container-fluid">
				<div class="row mb-2">
					<div class="col-sm-6">
						<h1 class="m-0 text-dark"><i class="fa fa-book"></i> Upload CSV File</h1>
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
              <form enctype="multipart/form-data" role="form" @submit.prevent="upload()">
							<div class="card-body">
                                <div class="alert alert-warning" role="alert">
                                       Format untuk file csv terdiri dari 2 kolom dengan urutan tanggal ,data_text 
                                </div>
                                  <div v-if="!csv" class="form-group">
                                    <label for="csv">Upload File : </label>
                                    <div class="dropbox">
                                        <input type="file" id="csv"  @change="onFileChange"
                                        accept=".csv" class="input-file" required="">
                                        <p v-if="!csv">
                                            Drag your file(s) here to begin<br> or click to browse
                                        </p>
                                    </div>
                                </div>
                                <div v-else>
                                    <p>{{csv}}</p>
                                    <button class="btn btn-danger" type="button" @click="removeFIle">Remove csv</button><br><br>
                                </div>
							</div>
              
             <div class="card-footer">
                <button type="submit" class="btn btn-primary" :disabled="!hasfile">
                  <i v-if="pending"  class="fa fa-refresh fa-spin"></i>Upload
                </button>
              </div>
              </form>	
						</div>
					</div>
				</div>
				<!-- /.row -->
			</div><!-- /.container-fluid -->
		</section>
		<!-- /.content -->
    <modal-alert v-bind:messages="messages"></modal-alert>
	</div>
</template>
<script>
    export default {
    	 data() {
		    return {
		      messages:"",
		      loading:true,	
          pending:false,
          hasfile:false,
          csv: '',
          attachment:'',		      
		    };
		  },


		methods: {		
             onFileChange(e) {
              var files = e.target.files || e.dataTransfer.files;
              if (!files.length)
                return;
              this.attachment=files[0];
              var fullPath = e.target.value;
              if (fullPath) {
                  var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
                  var filename = fullPath.substring(startIndex);
                  if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
                      filename = filename.substring(1);
                  }                  
                  this.csv = filename;
                  this.hasfile = true;
              }
            },
            
            upload(){
                this.pending = true;   
                this.hasfile = false;
                var formData = new FormData();
                formData.append("file",this.attachment);    
                axios.post('/api/complaints/upload-csv',formData,{
                    headers: {
                        'Authorization' : 'Bearer '+ localStorage.getItem("token"),
                        'Content-Type': 'multipart/form-data'
                    },
                },)
                .then(({data}) => {
                   $('#modalAlert').modal('show');
                    this.messages=data.message;                    
                    this.removeFIle();
                    this.pending = false;
                })
                .catch((error) => {     
                    $('#modalAlert').modal('show');
                    this.messages="Terjadi Kesalahan pada Server";   
                    this.pending = false;
                    this.removeFIle();
                 });
            },

             removeFIle: function (e) {
              this.csv = '';
              this.hasfile = false;
            }

		  
		},

		computed: {
		    isLoading(){
		      return this.loading;
		    },

		    isPending(){
		      return this.pending;
		    }
		}	


    }
</script>
<style>
.dropbox {
    outline: 2px dashed grey; /* the dash box */
    outline-offset: -10px;
    background: lightcyan;
    color: dimgray;
    padding: 10px 10px;
    min-height: 200px; /* minimum height */
    position: relative;
    cursor: pointer;
  }
  
  .input-file {
    opacity: 0; /* invisible but it's there! */
    width: 100%;
    height: 200px;
    position: absolute;
    cursor: pointer;
  }
  
  .dropbox:hover {
    background: lightblue; /* when mouse over to the drop zone, change color */
  }
  
  .dropbox p {
    font-size: 1.2em;
    text-align: center;
    padding: 50px 0;
  }
</style>
