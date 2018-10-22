<template>
    <el-container>
        <el-header>
            <el-row type="flex" justify="space-between">
                <el-col>
                    <img src="/images/Rate_My_Photo.svg" class="image-header">
                    <!-- Rate My Photo -->
                </el-col>
                <el-col>
                    <el-dropdown @command="handleCommand">
                        <span class="el-dropdown-link person-name">
                            {{user.full_name}} <i class="el-icon-arrow-down el-icon--right"></i>
                        </span>
                        <el-dropdown-menu slot="dropdown">
                            <el-dropdown-item command="profile">Profile</el-dropdown-item>
                            <el-dropdown-item command="logout">Logout</el-dropdown-item>
                            <el-dropdown-item v-if="user.role === 'admin'" divided command="addStaff">Add Staff</el-dropdown-item>
                        </el-dropdown-menu>
                    </el-dropdown>
                </el-col>
            </el-row>
        </el-header>

        <router-view></router-view>

        <!-- Add Staff Modal -->
        <el-dialog :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false" title="Add Staff" :visible.sync="dialogFormVisible">
            <el-form :model="addStaffForm" :rules="addStaffFormRules" ref="addStaffForm" label-width="120px">
                <el-form-item label="Name" prop="name">
                    <el-input v-model="addStaffForm.name" autocomplete="off"></el-input>
                </el-form-item>
                <el-form-item label="Email" prop="email">
                    <el-input type="email" v-model="addStaffForm.email" autocomplete="off"></el-input>
                </el-form-item>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button @click="closeStaffModal('addStaffForm')">Cancel</el-button>
                <el-button type="primary" @click="newStaff('addStaffForm')">Confirm</el-button>
            </span>
        </el-dialog>

        <!-- Session Ending Soon Modal -->
        <el-dialog
            :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false"
            title="Session Ending Soon"
            :visible.sync="dialogTimerVisible"
            width="60%">
            <span>Do you want to extend your session?</span>
            <span slot="footer" class="dialog-footer">
                <el-button @click="dontExtendSession">No</el-button>
                <el-button type="primary" @click="extendSession()">Yes</el-button>
            </span>
        </el-dialog>

        <!-- Session Has Ended Modal -->
        <el-dialog
            :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false"
            title="Session Ended"
            :visible.sync="dialogSessionEndedVisible"
            width="60%">
            <span>Your session has ended. Log back in to continue.</span>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="sessionEnded()">OK</el-button>
            </span>
        </el-dialog>

        <!-- Profile Modal -->
        <el-dialog
            :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false"
            :title="user.full_name"
            :visible.sync="dialogProfileVisible"
            width="70%">
            <el-form ref="profileForm" :model="profileForm" :rules="profileFormRules" label-position="top" >
                <el-collapse v-model="activeNames">
                    <el-collapse-item title="Change Email" name="1">
                        <el-form-item prop="email">
                            <el-input v-model="profileForm.email" placeholder="Enter New Email"></el-input>
                        </el-form-item>
                    </el-collapse-item>
                    <el-collapse-item title="Change Password" name="2" v-if="has_password">
                        <el-form-item prop="current_password">
                            <el-input type="password" v-model="profileForm.current_password" placeholder="Current Password"></el-input>
                        </el-form-item>
                        <el-form-item  prop="new_password">
                            <el-input type="password" v-model="profileForm.new_password" placeholder="New Password "></el-input>
                        </el-form-item>
                    </el-collapse-item>
                    <el-collapse-item title="Add Password" name="3" v-else>
                        <el-form-item prop="new_password">
                            <el-input type="password" v-model="profileForm.new_password" placeholder="Add New Password"></el-input>
                        </el-form-item>
                    </el-collapse-item>
                </el-collapse>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button @click="closeProfileModal('profileForm')">Cancel</el-button>
                <el-button type="primary" @click="updateProfile('profileForm')">Submit</el-button>
            </span>
        </el-dialog>
    </el-container>
</template>

<script>
import {mapState, mapActions} from 'vuex'
import {getUser, getToken} from '../local_storage'
import jwt from 'jwt-decode'
import dayjs from 'dayjs'

export default {
    name: "Dashboard",
    data(){
        return{
            dialogFormVisible: false,
            dialogTimerVisible: false,
            dialogProfileVisible: false,
            dialogSessionEndedVisible: false,
            addStaffForm:{
                name: null,
                email: null
            },
            profileForm:{
                email: null,
                new_password: null,
                current_password: null
            },
            addStaffFormRules: {
                email: [
                { required: true, message: 'Please enter an email address.', trigger: 'blur' },
                { type: 'email', message: 'Must be a valid email address.', trigger: 'blur'}
                ],
                name: [
                { required: true, message: 'Please enter a name.', trigger: 'blur' }
                ]
            },
            profileFormRules:{
                email: [
                    { type: 'email', message: 'Must be a valid email address.', trigger: 'blur'}
                ],
                new_password: [
                    {min: 8, message: 'Password must be at least eight characters in length.', trigger: 'blur' }
                ],
                current_password:[
                    {min: 8, message: 'Password must be at least eight characters in length.', trigger: 'blur' }
                ]
            },
            activeNames: [],
            user: null,
            timer: null,
            extension: true,
            has_password: false
        }
    },
    created(){
        this.user = getUser()
        this.startTimer()
        this.userHasPassword()
    },
    computed:{
        currentUser(){
            return getUser()
        }
    },
    methods:{
        ...mapActions(['addStaff','logoutUser', 'refreshToken', 'editProfile']),
        handleCommand(command) {
            if(command === "addStaff"){
                this.dialogFormVisible = true
            }else if(command === 'logout'){
                this.logUserOut()
            }else if(command === 'profile'){
                this.dialogProfileVisible = true
            }
        },
        newStaff(formName){
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    this.addStaff(this.addStaffForm).then(response => {
                        // console.log(response)
                        this.resetForm(formName)
                    }).catch(err => console.log(err))
                } else {
                    console.log('error submit!!');
                    return false;
                }
            });
        },
        checkTimer(){
            let decoded = jwt(getToken())
            let exp = decoded.exp
            let expUnix = dayjs.unix(exp)
            // console.log({exp, expUnix})
            let expDateOrg = expUnix
            let expDateMinus5min = expUnix.subtract(5, 'm')
            let currentTime = dayjs()

            let currentTimer = expDateMinus5min.diff(currentTime, 'minute', true)
            let expired = expDateOrg.diff(currentTime, 'minute', true)

            // console.log({expDateOrg,expDateMinus5min,currentTime,currentTimer,expired})
            if(currentTimer > 0){
                return;
                // console.log({greater: currentTimer})
            }else if(expired <= 0){
                // console.log({lesser: currentTimer})
                console.log('session ended');
                this.dialogSessionEndedVisible = true
                // this.stopTimer()
            }else{
                if(!this.extension){
                    return
                }else{
                    this.dialogTimerVisible = true;
                }
            }
        },
        startTimer(){
            this.timer = setInterval(() => this.checkTimer(), 2000)
        },
        stopTimer(){
            clearInterval(this.timer)
        },
        dontExtendSession(){
            this.extension = false
            this.dialogTimerVisible = false
        },
        extendSession(){
            this.stopTimer()
            this.refreshToken().then(response => {
                this.dialogTimerVisible = false
                this.timer = null
                this.startTimer()
            })
        },
        sessionEnded(){
            this.logUserOut()
        },
        logUserOut(){
            this.logoutUser().then(response => {
                this.stopTimer()
                this.$router.push('/');
            }).catch(err => { 
                this.stopTimer()
                this.$router.push('/'); 
            })
        },
        userHasPassword(){
            let decoded = jwt(getToken())
            console.log(decoded)
            this.has_password = decoded.has_password
        },
        updateProfile(formName){
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    this.editProfile(this.profileForm).then(response => {
                        console.log(response)
                        if(response.added){this.has_password = true}
                        this.$message.success(response.message);
                        this.resetForm(formName)
                        this.dialogProfileVisible = false
                    }).catch(err => {
                        this.$message.error(err.error);
                        this.resetForm(formName)
                    })
                } else {
                    console.log('error submit!!');
                    this.resetForm(formName)
                    return false;
                }
            });
        },
        resetForm(formName) {
            this.$refs[formName].resetFields();
        },
        closeStaffModal(formName){
            this.dialogFormVisible = false
            this.resetForm(formName)
        },
        closeProfileModal(formName){
            this.dialogProfileVisible = false
            this.resetForm(formName)
        }
    }
}
</script>
<style>
    .el-header, .el-footer {
        background-color: #B3C0D1;
        color: #333;
        text-align: center;
        line-height: 60px;
    }

    .el-main {
        background-color: rgb(247, 251, 255);
        color: #333;
        height: calc(100vh - 60px);
    }

    .image-header{
        max-width: 150px;
        margin-top: -15px;
    }

    .image{
        max-width: 100%;
    }

    .image-card{
        margin-bottom: 20px;
    }

    .bottom{
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
    }

    .outline-none{
        outline: none;
    }

    .person-name{
        cursor: pointer;
        user-select: none;
    }
</style>
