//Import the frameworks
import Vue from 'vue'
import VueRouter from 'vue-router'

//Import the Store
import store from './store'

//Import local_storage.js
import {getToken, getUser} from './local_storage'

//Import pages
import Dashboard from './pages/Dashboard'
import DashboardMain from './pages/DashboardMain'
import Login from './pages/Login'
import Staff from './pages/Staff'

//Hook-up vue-router to Vue framework
Vue.use(VueRouter)

//Routes
const routes = [
    {
        path: '/', 
        name: 'login', 
        component: Login,
        beforeEnter: (to, from, next) => {
            if(getToken()){
                next('/dashboard')
            }else{
                next()
            }
        }
    },
    {
        path: '/dashboard',
        component: Dashboard,
        beforeEnter: (to, from, next) => {
            if (!getToken()) { 
                next('/')
            } else {
                next()
            }
        },
        children: [
            {
                path: '/',
                name: 'dashboard',
                component: DashboardMain,
            },
            {
                path: ':slug',
                name: 'staff',
                component: Staff,
                beforeEnter: (to, from, next) => {
                    let user = getUser()
                    if (user.role !== 'admin') { 
                        next('/dashboard')
                    } else {
                        next()
                    }
                }
            },
        ]
    },
    { path: '*', redirect: '/dashboard' }
];

//Export Router
export default new VueRouter({
  routes,
  mode: 'history',
});

