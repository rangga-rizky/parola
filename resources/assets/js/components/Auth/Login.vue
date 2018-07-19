<template>
 <div class="sign-in-body" >
    <div class="text-center" style="margin:auto">
       <form class="form-signin" @submit.prevent="login({ email, password })" >
        <img class="mb-4" src="https://getbootstrap.com/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72">
        <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
        <label for="inputEmail" class="sr-only">Email address</label>
        <input type="email" v-model="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" v-model="password" id="inputPassword" class="form-control" placeholder="Password" required>
        
        <button class="btn btn-lg btn-primary btn-block" type="submit" :disabled="isPending">
           <i v-if="isPending"  class="fa fa-login fa-refresh fa-spin"></i>Sign in</button><br> 
            <div v-if="Object.keys(getError).length > 0" class="alert alert-danger">
            <strong>Error!</strong> {{getError}}
          </div>      
        <p class="mt-5 mb-3 text-muted">created by Ranggaantok@gmail.com</p>
      </form>
    </div>
  </div>
</template>

<script>

export default {
   data() {
    return {
      email: "",
      password: "",
      pending:false,
    }
  },

  methods: {
    login() {
      this.pending = true;
      this.$store.dispatch("auth/loginUser", {
        email: this.email,
        password: this.password
      }).then((res) => {      
        this.pending = false;
        this.$router.go("/dashboard")
      }).catch(err =>  {
        this.pending = false
        } );
    },
       
  },

  computed: {
    isPending(){
      return this.pending;
    },

    getError() {
      return this.$store.getters['auth/getErrors'];
    },
  }
  
}
</script>

<style>
html,
body {
  height: 100%;
}

body{
  
  background-color: #f5f5f5;
}

.sign-in-body {
  display: -ms-flexbox;
  display: flex;
  -ms-flex-align: center;
  align-items: center;
  padding-top: 40px;
  padding-bottom: 40px;
   margin: auto;
}

.form-signin {
  width: 100%;
  max-width: 330px;
  padding: 15px;
  margin: auto;
}

.fa-login {
    margin-left: -12px;
    margin-right: 8px;
}

.form-signin .checkbox {
  font-weight: 400;
}
.form-signin .form-control {
  position: relative;
  box-sizing: border-box;
  height: auto;
  padding: 10px;
  font-size: 16px;
}
.form-signin .form-control:focus {
  z-index: 2;
}
.form-signin input[type="email"] {
  margin-bottom: -1px;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}
.form-signin input[type="password"] {
  margin-bottom: 10px;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}

</style>
