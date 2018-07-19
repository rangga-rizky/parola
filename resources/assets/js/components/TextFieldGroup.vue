<template>
    <div class="form-group">
    <label for="">{{label}}</label>
    <input 
        :type="type"  
        v-model="text" 
        class="form-control"
        :class="{'is-invalid': isError }"
        :placeholder="placeholder"
        :disabled="disabled"
        :name="name"
        @change="chageText()"/>
   <small v-if="info" class="form-text text-muted">{{info}}</small>    
   <div v-if="error" class="invalid-feedback">{{error}}</div>
  </div>
</template>
<script>
export default {
    data(){
		return{
			text:""
		}
    },

    watch: {
        value: function (value) {
            this.text = value
        }
    },

    props : { 
        name:String,
        placeholder:String,
        label:String,
        value:String,
        error:String,
        info:String,
        type:String,
        disabled:String
    },

    methods:{
        chageText(){
            this.$emit('changed',this.text);
        }
    },

    computed: {
        isError() {
           return this.error !== undefined
        },
    } 
    

    
}
</script>
