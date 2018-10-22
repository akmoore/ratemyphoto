<template>
    <el-container>
        <template v-if="user.role === 'admin'">
            <admin-view></admin-view>
        </template>
        <template v-else>
            <staff-view :user="currentUser"></staff-view>
        </template>
    </el-container>
</template>

<script>
import AdminView from '../components/AdminView'
import StaffView from '../components/StaffView'
import {getUser, getToken} from '../local_storage'

export default {
    name: "DashboardMain",
    components:{
        AdminView,
        StaffView
    },
    data(){
        return{
            user: null,
        }
    },
    created(){
        this.user = getUser()
    },
    computed:{
        currentUser(){
            return getUser()
        }
    }
}
</script>