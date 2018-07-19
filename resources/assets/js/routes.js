import VueRouter from 'vue-router';
import Dashboard from './components/Dashboard/Dashboard.vue';
import CategoryDashboard from './components/Dashboard/CategoryDashboard.vue';
import Category from './components/Category/Category.vue';
import Term from './components/Term/Term.vue';
import TermCreate from './components/Term/Create.vue';
import TermEdit from './components/Term/Edit.vue';
import TrainingTerm from './components/TrainingTerm/TrainingTerm.vue';
import TrainingTermCreate from './components/TrainingTerm/Create.vue';
import TrainingTermEdit from './components/TrainingTerm/Edit.vue';
import TrainingBayes from './components/Training/TrainingBayes.vue';
import TrainingAuto from './components/Training/TrainingAuto.vue';
import TrainingManual from './components/Training/TrainingManual.vue';
import Setting from './components/Setting.vue';
import Complaint from './components/Complaint/Complaint.vue';
import ComplaintCreate from './components/Complaint/Create.vue';
import UploadCsv from './components/Complaint/UploadCsv.vue';
import CategoryCreate from './components/Category/Create.vue';
import CategoryEdit from './components/Category/Edit.vue';
import Login from './components/Auth/Login.vue';
import Layout from './components/layout/Layout.vue';
import Landing from './components/layout/Landing.vue';
import ClassificationReport from './components/Training/ClassificationReport.vue';
import Store from './Store'
import setAuthToken from "./setAuthToken"
import jwt_decode from "jwt-decode"


const onlyAuthenticated = (to, from, next) => {
    if(Store.getters['auth/isAuthenticated']){
        setAuthToken(localStorage.jwtToken)
        const decoded = jwt_decode(localStorage.jwtToken)
        Store.dispatch("auth/setCurrentUser",(decoded))      
        const currentTIme = Date.now() / 1000;
        if(decoded.exp < currentTIme){
          Store.dispatch("auth/logoutUser"); 
          next('/login')
        }
        next()
        return
    }
    next('/login')
}

const onlyUnauthenticated = (to, from, next) => {
    if (!Store.getters['auth/isAuthenticated']) {
        next()
        return
    }
    next('/dashboard')
  }

let routes = [  
    {
        path: '/login',
        name:'login',
        component: Login,
        beforeEnter: onlyUnauthenticated,
    },                
    {
        path: '/',
        component: Landing,
        name:'landing',
        beforeEnter: onlyUnauthenticated,
    },       
    {
        path: '/',
        component: Layout,
        children: [          
            {
                path: '/dashboard/:slug',
                component: CategoryDashboard,
                name:'dashboard-category',
                props: true,
                beforeEnter: onlyAuthenticated,
            },
            {
                path: '/dashboard',
                component: Dashboard,
                name:'dashboard',
                beforeEnter: onlyAuthenticated,
            },
            {
                path: '/categories',
                component: Category,
                name:'categories',
                beforeEnter: onlyAuthenticated,
            },            
            {
                path: '/categories/create',
                component: CategoryCreate,
                name:'categories-create',
                beforeEnter: onlyAuthenticated,
            },
            {
                path: '/categories/:id',
                component: CategoryEdit,
                name:'categories-edit',
                props: true,
                beforeEnter: onlyAuthenticated,
            },
            {
                path: '/terms',
                component: Term,
                name:'terms',
                beforeEnter: onlyAuthenticated,
            },
            {
                path: '/terms/create',
                component: TermCreate,
                name:'terms-create',
                beforeEnter: onlyAuthenticated,
            },
            {
                path: '/terms/:id',
                component: TermEdit,
                name:'terms-edit',
                props:true,
                beforeEnter: onlyAuthenticated,
                
            },
            {
                path: '/training-terms',
                component: TrainingTerm,
                name:'training-terms',
                beforeEnter: onlyAuthenticated,
            },
            {
                path: '/training-terms/create',
                component: TrainingTermCreate,
                name:'training-terms-create',
                beforeEnter: onlyAuthenticated,
            },
            {
                path: '/training-terms/:id',
                component: TrainingTermEdit,
                name:'training-terms-edit',
                beforeEnter: onlyAuthenticated,
                props:true
            },
            {
                path: '/settings',
                component: Setting,
                name:'setting',
                beforeEnter: onlyAuthenticated,
            },
            {
                path: '/trainings/score',
                component: ClassificationReport,
                name:'training-score',
                beforeEnter: onlyAuthenticated,
            },
            {
                path: '/trainings/bayes',
                component: TrainingBayes,
                name:'training-bayes',
                beforeEnter: onlyAuthenticated,
            },
            {
                path: '/trainings/auto',
                component: TrainingAuto,
                name:'training-auto',
                beforeEnter: onlyAuthenticated,
            },
            {
                path: '/trainings/manual',
                component: TrainingManual,
                name:'training-manual',
                beforeEnter: onlyAuthenticated,
            },
            {
                path: '/complaints',
                component: Complaint,
                name:'complaints',
                beforeEnter: onlyAuthenticated,
            },
            {
                path: '/complaints/create',
                component: ComplaintCreate,
                name:'complaints-create',
                beforeEnter: onlyAuthenticated,
            },
            {
                path: '/complaints/upload-csv',
                component: UploadCsv,
                name:'upload-csv',
                beforeEnter: onlyAuthenticated,
            },
        ]
    },
];

const router = new VueRouter({
    routes
});

export default router;