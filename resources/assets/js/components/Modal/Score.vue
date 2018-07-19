<template>
   <div class="modal fade" id="scoreModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Classification Report</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body">
                 <table  class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th>Kategori</th>
                      <th>Precision</th>
                      <th>Recall</th>
                      <th>Jumlah Data</th>
                    </tr>
                  </thead>
                  <tr v-for="(score) in this.scores"  v-bind:key="score.id">
                    <td>{{score.category}}</td>
                    <td>{{score.precission.toFixed(2)}}%</td>
                    <td>{{score.recall.toFixed(2)}}%</td>
                     <td>{{score.number_of_data}}</td>                    
                  </tr>
                  <tr>
                    <td><strong>Akurasi</strong></td>
                    <td colspan="2"><strong>{{accuracy.toFixed(2)}}%</strong></td>
                  </tr>
                </table>
                
              </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {

      props: ['scores','accuracy'],
       

}
</script>
