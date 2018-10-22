<template>
    <el-row :gutter="20">
        <el-col :xs="24" :sm="12" v-if="user" style="padding: 20px;">
            <el-card :body-style="{ padding: '20px' }">
                <img :src="primaryImage" alt="" style="max-width: 100%;" v-if="primaryImage">
                <img src="/images/pexels-doughnut.jpg" class="image" v-else>
                <div style="padding: 14px;">
                    <el-input placeholder="First Name" v-model="user.first_name" class="mt mb"></el-input>
                    <el-input placeholder="Last Name" v-model="user.last_name" class="mb"></el-input>
                    <el-input placeholder="Email" v-model="user.email" class="mb"></el-input>
                    <div class="mt">
                        <el-button type="primary">Save</el-button>
                        <el-button type="danger">Delete</el-button>
                    </div>
                </div>
            </el-card>
        </el-col>
        <el-col :xs="24" :sm="12" v-if="user" style="padding: 20px;">
            <el-col :span="6" v-for="photo in user.photos" :key="photo.id" style="margin-bottom: 15px;">
                <el-card :body-style="{ padding: '7px' }">
                    <img :src="`https://akmoore.nyc3.digitaloceanspaces.com${photo.image_thumb}`" alt="" style="max-width: 100%;">
                    <div>
                        <span>{{photo.image_name}}</span>
                        <el-button type="danger" size="mini" style="outline:none;margin: 0 auto;">Delete</el-button>
                    </div>
                </el-card>
            </el-col>
        </el-col>
    </el-row>
</template>

<script>
import {mapActions} from 'vuex'

export default {
    name: 'Staff',
    data(){
        return {
            user: null
        }
    },
    created(){
        this.getStaff()
    },
    computed:{
        primaryImage(){
            let image = this.user.photos.find(photo => photo.preferred)
            if(image){
                return `https://akmoore.nyc3.digitaloceanspaces.com${image['image_sm']}`
            }else{
                return null;
            }
            
        }
    },
    methods:{
        ...mapActions(['getStaffProfile']),
        getStaff(){
            this.getStaffProfile(this.$route.params.slug).then(response => {
                console.log(response)
                this.user = response
            }).catch(err => console.log(err))
        }
    }
}
</script>
<style scoped>
    .mb{
        margin-bottom: 10px;   
    }

    .mt{
        margin-top: 10px;
    }
</style>
