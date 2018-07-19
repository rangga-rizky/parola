const SET_CURRENT_USER = "SET_CURRENT_USER";
const GET_ERRORS = "GET_ERRORS";
const LOGOUT = "LOGOUT";
import setAuthToken from  './../setAuthToken'
import jwt_decode from "jwt-decode"

const state = {
    isAuthenticated: !!localStorage.getItem("jwtToken"),
    user:{},
    errors:{}
  }
  
  // getters
  const getters = {
    getErrors: (state) => {
        return state.errors;
    },

    isAuthenticated: (state) => {
        return state.isAuthenticated;
    }

  }
  
  // actions
  const actions = {
    loginUser({ commit,dispatch},userData) {           
        return new Promise((resolve, reject) => {     
            axios.post('/api/login',userData)
            .then(res =>{
                const {token} = res.data.data;
                localStorage.setItem('jwtToken',token);
                setAuthToken(token);
                const decoded = jwt_decode(token);
                dispatch("setCurrentUser",decoded);
                resolve(res)
            }).catch(err => {
                console.log(err);
                commit(GET_ERRORS,err.response.data.error);
                reject(new Error(err));
                }
            );
        });
    },

    setCurrentUser({ commit},decoded){        
        commit(SET_CURRENT_USER,decoded);
    },

    logoutUser({ commit}){      
        localStorage.removeItem('jwtToken')  
        commit(SET_CURRENT_USER,{});
        commit(LOGOUT);
    },
  }
  
  // mutations
  const mutations = {
    [GET_ERRORS] (state,errors) {
        state.errors = errors;
    },

    [SET_CURRENT_USER] (state,user) {
        state.user = user;
    },
    [LOGOUT] (state) {
        state.isAuthenticated = false;
    },
  }
  
  export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
  }