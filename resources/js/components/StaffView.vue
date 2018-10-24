<template>
    <el-main>
        <el-row :gutter="20" type="flex" justify="center" v-if="user.role === 'staff'">
            <el-col :xs="24" :sm="12" :md="8" style="margin-top:20px;margin-bottom:20px;">
                <el-alert
                    title="Welcome to Rate My Photo!"
                    type="success"
                    :description="message"
                    @close="alertClose"
                    center>
                </el-alert>
            </el-col>
        </el-row>
        <el-row :gutter="20">
            <el-col 
                :xs="24" :sm="12" 
                :md="8" :lg="6"
                :xl="4" 
                v-for="(photo, photoIndex) in photos" :key="photoIndex" class="image-card">
                <el-card :body-style="{ padding: '0px' }" shadow="hover" style="position:relative;">
                    <i class="el-icon-success success-icon" v-if="photo.preferred"></i>
                    <img :src="`https://akmoore.nyc3.digitaloceanspaces.com${photo.image_sm}`" :alt="photo.image_name" class="image">

                    <div style="padding: 14px;">
                        <span>{{photo.image_name}}</span>
                        <div class="bottom">
                            <div>
                                <el-tooltip class="item" effect="dark" content="Download" placement="top">
                                    <el-button class="outline-none" size="medium" type="primary" icon="el-icon-download" circle style="margin-right:-5px;" @click="downloadImage(photo)"></el-button>
                                </el-tooltip>
                                <el-tooltip class="item" effect="dark" content="View" placement="top">
                                    <!-- <el-button class="outline-none" size="medium" type="primary" icon="el-icon-view" circle @click="showLargePhoto(photo)"></el-button>                                     -->
                                    <el-button class="outline-none" size="medium" type="primary" icon="el-icon-view" circle @click="index = photoIndex"></el-button>                                    
                                </el-tooltip>
                            </div>
                            <div>
                                <el-tooltip class="item" effect="dark" content="Select" placement="top" v-if="!photo.preferred">
                                    <el-button class="outline-none" size="medium" type="primary" icon="el-icon-check" circle @click="prefer(photo)"></el-button>
                                </el-tooltip>
                            </div>
                        </div>
                    </div>
                </el-card>
            </el-col>
        </el-row>
        <!-- <el-dialog
            v-if="this.currentPhoto"
            :title="this.currentPhoto.image_name"
            :visible.sync="dialogVisible"
            @closed="dialogClosed"
            width="80%">
            <div style="">
                <img :src="`https://akmoore.nyc3.digitaloceanspaces.com${this.currentPhoto.image_md}`" :alt="this.currentPhoto.image_name" style="max-width: 60%; border-radius:10px;display: block;margin-left: auto;margin-right: auto;">
            </div>
        </el-dialog> -->
        <gallery :images="galleryPhotos" :index="index" @close="index = null"></gallery>
    </el-main>
</template>

<script>
import {mapActions, mapState} from 'vuex'
import VueGallery from 'vue-gallery'

export default {
    name: 'StaffView',
    props:['user'],
    components: {
      'gallery': VueGallery
    },
    data(){
        return {
            currentPhoto: null,
            dialogVisible: false,
            index: null
        }
    },
    mounted(){
        this.getImages()
        console.log(this.user)
    },
    computed:{
        ...mapState(['currentPhotos']),
        photos(){
            return this.currentPhotos
        },
        message(){
            return ` ${this.user.first_name}, here are the selected images from your photoshoot. Please select (by clicking the check mark button) the photo that you want featured on the staff photo wall.`
        },
        galleryPhotos(){
            let photos = this.currentPhotos.map(photo => `https://akmoore.nyc3.digitaloceanspaces.com${photo.image_md}`)
            return photos
        }
    },
    methods:{
        ...mapActions(['getPhotos', 'downloadPhoto', 'preferPhoto']),
        getImages(){
            this.getPhotos(this.user.id);
        },
        alertClose(){
            console.log('alert closed.')
        },
        showLargePhoto(photo){
            this.currentPhoto = photo
            this.dialogVisible = true
        },
        dialogClosed(){
            this.currentPhoto = null
        },
        downloadImage(photo){
            this.downloadPhoto(photo)
                .then(response => {
                    // window.location.assign(photo.image_org)
                    window.location.assign(response)
                })
                .catch(err => {})
        },
        prefer(photo){
            this.preferPhoto(photo)
                .then(response => {
                    this.$message({
                      message: 'Great Choice! Thank you for selecting.',
                      type: 'success'
                    });
                })
        }
    }
}
</script>
<style scoped>
    .success-icon{
        color: rgb(255, 255, 255);
        position: absolute;
        top: 10px;
        left: 10px;
        font-size: 40px;
    }

    .outline-none{
        outline: none;
    }
</style>
