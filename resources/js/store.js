import Vue from 'vue'
import Vuex from 'vuex'

//Plugins
import jwt from 'jwt-decode'
import dayjs from 'dayjs'
import {getToken, setToken, setName, setRole, setId, getUser, destroyUser} from './local_storage'


const User = getUser()

Vue.use(Vuex)
const baseUrl = `${window.location.origin}`

export default new Vuex.Store({
    state:{
        currentUserToken: getToken(),
        currentUserName: User.full_name,
        currentUserFirstName: User.first_name,
        currentUserRole: User.role,
        staff: null,
        currentPhotos: [],
        timer: null,
        fiveMinuteWarning: false
    },
    getters:{},
    actions:{
        //Authentication
        loginUser({commit, dispatch}, credentials){
            return new Promise((resolve, reject) => {
                axios.post(`${baseUrl}/api/auth/login`, credentials).then(response => {
                    // console.log(response.data)
                    commit('setCurrentUserData', response.data)
                    resolve(response.data)
                }).catch(err => reject(err.response))
            })
        },
        logoutUser({commit}){
            return new Promise((resolve, reject) => {
                axios.post(`${baseUrl}/api/auth/logout`, null,{headers: {Authorization: `Bearer ${getToken()}`}})
                     .then(response => {
                        commit('unsetCurrentUserData')
                        resolve(response.data)
                     })
                     .catch(err => {
                        commit('unsetCurrentUserData')
                        reject(err.response.data)
                     })
            })
        },
        refreshToken({commit}){
            return new Promise((resolve, reject) => {
                axios.post(`${baseUrl}/api/auth/refresh`, null, {headers: {Authorization: `Bearer ${getToken()}`}})
                     .then(response => {
                        // console.log(response.data)
                        commit('setCurrentUserData', response.data)
                        resolve()
                     })
                     .catch(err => reject(err.response.data))
            })
        },
        loginEmailOnly({commit}, credentials){
            return new Promise((resolve, reject) => {
                axios.post(`${baseUrl}/api/auth/email-only`, credentials)
                     .then(response => {
                        //  console.log(response)
                        resolve(response.data)
                     })
                     .catch(err => reject(err.response.data))
            })
        },
        checkToken({commit}, credentials){
            return new Promise((resolve, reject) => {
                axios.post(`${baseUrl}/api/auth/validate-token`, credentials)
                     .then(response => {
                        commit('setCurrentUserData', response.data)
                        resolve(response)
                     })
                     .catch(err => reject(err.response.data))
            })
        },
        editProfile({commit}, input){
            return new Promise((resolve, reject) => {
                axios.post(`${baseUrl}/api/auth/profile`, input, {headers: {Authorization: `Bearer ${getToken()}`}})
                     .then(response => {
                         resolve(response.data)
                     })
                     .catch(err => reject(err.response.data))
            })
        },

        //Staff
        getAllStaff({commit}){
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/api/staff`, {headers: {Authorization: `Bearer ${getToken()}`}})
                     .then(response => {
                        //  console.log(response)
                        commit('setStaff', response.data)
                        resolve(response.data)
                     })
                     .catch(err => reject(err.response.data))
            })
        },
        addStaff({commit, dispatch}, staff){
            return new Promise((resolve, reject) => {
                // console.log(this.getToken)
                axios.post(`${baseUrl}/api/staff`, staff, {headers: {Authorization: `Bearer ${getToken()}`}})
                    .then(response => {
                        resolve(response.data)
                        dispatch('getAllStaff')
                    })
                    .catch(err => reject(err.response.data))
            })
        },
        getStaffProfile({commit}, slug){
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/api/profile/${slug}`, {headers: {Authorization: `Bearer ${getToken()}`}})
                     .then(response => {
                         resolve(response.data)
                     })
                     .catch(err => reject(err.response.data))
            })
        },
        updateStaff({commit}, staff){
            return new Promise((resolve, reject) => {
                axios.patch(`${baseUrl}/api/profile/${staff.slug}`, staff, {headers: {Authorization: `Bearer ${getToken()}`}})
                     .then(response => {
                         resolve(response.data)
                     })
                     .catch(err => reject(err.response.data))
            })
        },
        deleteStaff({commit}, staffId){
            return new Promise((resolve, reject) => {
                axios.delete(`${baseUrl}/api/profile/${staffId}`, {headers: {Authorization: `Bearer ${getToken()}`}})
                     .then(response => {
                         resolve(response.data)
                     })
                     .catch(err => reject(err.response.data))
            })
        },

        //Photos
        getPhotos({commit}, staff){
            // console.log(staff)
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/api/images?staff=${staff}`, {headers: {Authorization: `Bearer ${getToken()}`}})
                     .then(response => {
                         resolve(response.data)
                         commit('setPhotos', response.data)
                     })
                     .catch(err => reject(err.response.data))
            })
        },
        downloadPhoto({commit}, photo){
            return new Promise((resolve, reject) => {
                axios.get(`${baseUrl}/api/download/${photo.id}`, {headers: {Authorization: `Bearer ${getToken()}`}})
                     .then(response => {
                        // console.log(response.data)
                        resolve(response.data)
                     })
                     .catch(err => console.log(err.response.data))
            })
        },
        preferPhoto({commit, dispatch}, photo){
            return new Promise((resolve, reject) => {
                axios.post(`${baseUrl}/api/prefer/${photo.id}`, null, {headers: {Authorization: `Bearer ${getToken()}`}})
                .then(response => {
                   //  console.log(response)
                    dispatch('getPhotos', response.data)
                   //  resolve(response.data)
                })
                .catch(err => reject(err.response.data))
            })
            
        },
        deletePhoto({commit}, photo){
            return new Promise((resolve, reject) => {
                axios.delete(`${baseUrl}/api/images/${photo}`, {headers: {Authorization: `Bearer ${getToken()}`}})
                     .then(response => {
                         resolve(response.data)
                     }).catch(err => reject(err.response.data))
            })
        }
    },
    mutations:{
        setCurrentUserData(state, payload){
            let decoded = jwt(payload.access_token)
            setToken(payload.access_token)
        },
        setStaff(state, payload){
            state.staff = payload
        },
        unsetCurrentUserData(state){
            destroyUser()
        },
        setPhotos(state, payload){
            state.currentPhotos = payload
        },
    }
})