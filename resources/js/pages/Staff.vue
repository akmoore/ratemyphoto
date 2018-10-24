<template>
    <el-row >
        <el-col :xs="24" :sm="12" :lg="8" :xl="6" v-if="user" style="padding: 20px;">
            <el-card :body-style="{ padding: '20px' }">
                <img :src="primaryImage" alt="" class="image-center" style="max-width: 100%;border-radius:10px;" v-if="primaryImage">
                <img src="/images/staff_no_photo.png" class="image" style="border-radius:10px;" v-else>
                <div style="padding: 14px;margin-top:20px;">
                    <el-form ref="profileForm" :model="profileForm" label-width="90px">
                        <el-form-item label="First Name">
                            <el-input v-model="profileForm.first_name"></el-input>
                        </el-form-item>
                        <el-form-item label="Last Name">
                            <el-input v-model="profileForm.last_name"></el-input>
                        </el-form-item>
                        <el-form-item label="Email">
                            <el-input v-model="profileForm.email"></el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="primary" @click="updateProfile">Save</el-button>
                            <el-button type="danger" @click="deleteUser">Delete</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </el-card>
        </el-col>
        <el-col :xs="24" :sm="12" :lg="16" :xl="18" v-if="user" style="padding: 20px;">
            <el-row :gutter="20">
                <el-col :xs="8" :sm="6" :lg="4" :xl="3" v-for="(photo, photoIndex) in user.photos" :key="photoIndex" style="margin-bottom: 15px;">
                    <el-card :body-style="{ padding: '7px' }">
                        <img :src="`https://akmoore.nyc3.digitaloceanspaces.com${photo.image_thumb}`" alt="" style="max-width: 100%;">
                        <div>
                            <span>{{photo.image_name}}</span>
                            <br>
                            <el-button 
                                type="danger" 
                                size="mini" 
                                style="outline:none;margin: 0 auto;" 
                                @click="deleteImage(photo)" 
                                v-loading.fullscreen.lock="fullscreenLoading"
                                :element-loading-text="`Deleting ${photo.image_name}`"
                                element-loading-background="rgba(255, 255, 255, 0.6)"
                                icon="el-icon-delete"
                                circle
                            ></el-button>
                            <el-button 
                                type="primary" 
                                size="mini" 
                                icon="el-icon-view" 
                                style="outline:none;margin: 0 auto;" 
                                circle
                                @click="index = photoIndex"
                            ></el-button>
                        </div>
                    </el-card>
                </el-col>
            </el-row>
        </el-col>
        <gallery :images="galleryPhotos" :index="index" @close="index = null"></gallery>
    </el-row>
</template>

<script>
import {mapActions} from 'vuex'
import VueGallery from 'vue-gallery'


export default {
    name: 'Staff',
    components: {
      'gallery': VueGallery
    },
    data(){
        return {
            user: null,
            profileForm:{
                first_name: null,
                last_name: null,
                email: null
            },
            fullscreenLoading: false,
            index: null
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
        },
        galleryPhotos(){
            let photos = this.user.photos.map(photo => `https://akmoore.nyc3.digitaloceanspaces.com${photo.image_md}`)
            return photos
        }
    },
    methods:{
        ...mapActions(['getStaffProfile','updateStaff','deleteStaff', 'deletePhoto']),
        getStaff(){
            this.getStaffProfile(this.$route.params.slug).then(response => {
                // console.log(response)
                this.user = response
                this.setProfile(response)
            }).catch(err => {
                this.$message({
                    type: 'error',
                    message: err.message
                });
                this.$router.push('/dashboard')
            })
        },
        setProfile(user){
            this.profileForm.first_name = user.first_name
            this.profileForm.last_name = user.last_name
            this.profileForm.email = user.email
        },
        updateProfile(){
            let data = {...this.profileForm, slug: this.user.slug}
            this.updateStaff(data).then(response => {
                // console.log(response)
                this.$message({
                    type: 'success',
                    message: `${response.full_name}'s profile was successfully updated.`
                });
            }).catch(err => console.log(err))
        },
        deleteUser(){
            this.$confirm(`This will permanently delete ${this.user.full_name}'s profile, Continue?`, 'Warning', {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
                type: 'error'
            }).then(() => {
                this.deleteStaff(this.user.id).then(response => {
                    this.$message({
                        type: 'success',
                        message: `${response.full_name} was successfully deleted.`
                    });
                    this.$router.push('/dashboard')
                }).catch(err => {
                    this.$message({
                        type: 'error',
                        message: err.message
                    });
                })
            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: 'Delete canceled'
                });          
            });
        },
        deleteImage(image){
            console.log(image)
            this.$confirm(`This will permanently delete ${image.image_name}, Continue?`, 'Warning', {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
                type: 'error'
            }).then(() => {
                this.fullscreenLoading = true
                this.deletePhoto(image.id).then(response => {
                    this.$message({
                        type: 'success',
                        message: `${response.image_name} was successfully deleted.`
                    });
                    this.fullscreenLoading = false
                    this.getStaff()
                }).catch(err => {
                    this.$message({
                        type: 'error',
                        message: err.message
                    });
                    this.fullscreenLoading = false
                })
            }).catch(() => {
                this.$message({
                    type: 'info',
                    message: 'Delete canceled.'
                });          
            });
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

    .image-center{
        display: block;
        margin-right: auto;
        margin-left: auto;
    }
</style>
