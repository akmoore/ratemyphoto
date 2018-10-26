<template>
    <el-main>
        <el-row :gutter="20" type="flex" justify="center">
            <el-col :xs="24" :sm="22" :md="20" style="margin-top:20px;margin-bottom:20px;">
                <el-table
                    v-if="staff"
                    :data="staff"
                    style="width: 100%">
                    <el-table-column type="expand">
                        <div slot-scope="props" class="photo-grid">
                            <div v-if="props.row.photos.length" v-for="photo in props.row.photos" :key="photo.id">
                                <el-tag v-html="photo.image_name" class="photo-id" type="success" v-if="photo.preferred"></el-tag>
                                <el-tag v-html="photo.image_name" class="photo-id" v-else></el-tag>
                            </div>
                            <div v-if="!props.row.photos.length">
                                {{props.row.full_name}} has no photos.
                            </div>
                        </div>
                    </el-table-column>
                    <el-table-column
                        prop=""
                        label="Image"
                        width="80">
                        <template slot-scope="scope">
                            <img :src="`https://akmoore.nyc3.digitaloceanspaces.com${primaryImage(scope.row.photos)['image_sm']}`" class="image" v-if="primaryImage(scope.row.photos)">
                            <img src="/images/staff_no_photo.png" class="image" v-else>
                        </template>
                    </el-table-column>
                    <el-table-column
                        prop="full_name"
                        label="Name"
                        width="180">
                    </el-table-column>
                    <el-table-column
                        prop="email"
                        label="Email"
                        width="180">
                    </el-table-column>
                    <el-table-column
                        prop="has_logged_in"
                        label="Logged In?"
                        width="200">
                        <template slot-scope="scope">
                            <div v-if="scope.row.logins.length">
                                {{convertedDate(scope.row.logins[scope.row.logins.length - 1]['created_at'])}}
                                <br>
                                <span style="letter-spacing: 2px;"><small><b>{{scope.row.logins[scope.row.logins.length - 1]['type'].toUpperCase()}}</b></small></span>
                            </div>
                            <div v-else>No</div>
                        </template>
                    </el-table-column>
                    <el-table-column
                        label="# Photos"
                        width="80">
                        <template slot-scope="scope">
                            {{ scope.row.photos.length }}
                        </template>
                    </el-table-column>
                    <el-table-column
                        label="Preferred"
                        >
                        <template slot-scope="scope">
                            <div v-if="primaryImage(scope.row.photos)">
                                {{primaryImage(scope.row.photos)['image_name']}}
                            </div>
                        </template>
                    </el-table-column>
                    <el-table-column
                      fixed="right"
                      label="Operations"
                      width="120">
                        <template slot-scope="scope">
                            <el-button class="outline-none" type="text" size="small" @click="openImagesModal(scope.row)">
                                Add <i class="el-icon-picture el-icon-right"></i>
                            </el-button>
                            <router-link :to="{ name: 'staff', params: { slug: scope.row.slug }}" class="edit-link">
                                Edit <i class="el-icon-edit el-icon-right"></i>
                            </router-link>
                        </template>
                    </el-table-column>
                </el-table>
            </el-col>
        </el-row>

        <el-dialog
            :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false"
            :title="selectedTitle"
            width="80%"
            :visible.sync="addImagesModal">
            <vue-dropzone ref="myVueDropzone" id="dropzone" :options="{
                url: `/api/image?staff=${selectedStaff.id}`,
                thumbnailWidth: 150,
                chunking: true,
                maxFilesize: 10,
                chunkSize: 2000000, //two megabytes
                headers: { 
                    Authorization: `Bearer ${token}`,
                }
            }" v-if="selectedStaff"></vue-dropzone>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="closeImagesModal()">Done</el-button>
            </span>
        </el-dialog>
        
    </el-main>
</template>

<script>
import dayjs from 'dayjs'
import {getToken} from '../local_storage'
import vue2Dropzone from 'vue2-dropzone'
import {mapActions, mapState} from 'vuex'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
export default {
    name: 'AdminView',
    components:{
        vueDropzone: vue2Dropzone
    },
    data(){
        return {
            addImagesModal: false,
            // dropzoneOptions: {
            //     url: null,
            //     thumbnailWidth: 150,
            //     maxFilesize: 0.5,
            //     headers: { 
            //         Authorization: `Bearer ${getToken()}`,
            //         "Content-Type": "application/json"
            //     }
            // },
            selectedStaff: null
        }
    },
    computed:{
        ...mapState(['staff']),
        token(){
            return getToken()
        },
        selectedTitle(){
            if(this.selectedStaff){
                return `Add image(s): ${this.selectedStaff.full_name}`
            }
            return `Add Image(s): `
        }
    },
    created(){
        this.getAllStaff()
    },
    methods:{
        ...mapActions(['getAllStaff']),
        convertedDate(date){
            return dayjs(date).format('MM/DD/YYYY @ hh:mm a')
        },
        openImagesModal(staff){
            this.selectedStaff = staff
            this.addImagesModal = true
            // this.$refs.myVueDropzone.enable()
        },
        closeImagesModal(){
            this.selectedStaff = null
            this.addImagesModal = false
            this.$refs.myVueDropzone.removeAllFiles()
            // this.$refs.myVueDropzone.disable()
        },
        primaryImage(photos){
            let primary = photos.find(photo => photo.preferred === 1)
            return primary
        }
    }
}
</script>

<style scoped>

    .image{
        border-radius: 5px;
    }

    .outline-none{
        outline: none;
    }

    .photo-grid{
        display: flex;
        flex-wrap: wrap;
    }

        .photo-grid .photo-id{
            margin-right: 5px;
            margin-bottom: 5px;
        }

    .edit-link{
        font-size: 12px;
        color: #409EFF;
        text-decoration: none;
        margin-left: 8px;

    }
        .edit-link:hover{
            color: #66b1ff;
        }
</style>
