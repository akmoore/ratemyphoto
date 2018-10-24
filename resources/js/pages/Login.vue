<template>
    <div class="full-wh flexbox">
        <el-row >
            <el-col :span="18" :offset="3" style="display:flex;justify-content:center;">
                <img src="/images/Rate_My_Photo.svg" class="image">
            </el-col>
            <el-col :xs="20" :sm="12" :md="10" :lg="8" :xl="8" style="margin-right:auto;margin-left:auto;float:none;padding-top:40px;">
                <el-card class="box-card" >
                    <div slot="header" class="clearfix">
                        Login
                    </div>
                    <el-form ref="loginForm" :rules="rules" :model="credentials" label-position="top" label-width="120px" v-if="passwordEmailLogin">
                        <template v-if="!showTokenForm">
                            <el-form-item label="Email" prop="email">
                                <el-input v-model="credentials.email"></el-input>
                            </el-form-item>
                            <el-form-item>
                                <el-button type="primary" @click="loginEmail('loginForm')" class="outline-none">Send Email</el-button>
                            </el-form-item>
                            <el-form-item>
                                <el-button type="text" @click="passwordEmailLogin = false" class="outline-none">Email & Password Login</el-button>
                            </el-form-item>
                        </template>
                        <template v-else>
                            <el-form-item label="Enter Your Access Code" prop="token">
                                <el-input v-model="credentials.token"></el-input>
                            </el-form-item>
                            <el-form-item>
                                <el-button type="primary" @click="validateToken('loginForm')" class="outline-none">Validate Code</el-button>
                            </el-form-item>
                        </template>
                    </el-form>
                    <el-form ref="loginForm" :rules="rules" :model="credentials" label-position="top" label-width="120px" v-else>
                        <el-form-item label="Email" prop="email">
                            <el-input v-model="credentials.email"></el-input>
                        </el-form-item>
                        <el-form-item label="Password" prop="password">
                            <el-input type="password" v-model="credentials.password"></el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="primary" @click="login('loginForm')" class="outline-none">Login</el-button>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="text" @click="passwordEmailLogin = true" class="outline-none">Email Only Login</el-button>
                        </el-form-item>
                    </el-form>
                </el-card>
            </el-col>
        </el-row>
        
    </div>
</template>

<script>
import {mapActions} from 'vuex'

export default {
    name: "Login",
    data(){
        return{
            passwordEmailLogin: true,
            showTokenForm: false,
            credentials:{
                email: null,
                password: null,
                token: null
            },
            rules: {
                email: [
                    { required: true, message: 'Please enter your email address.', trigger: 'blur' },
                    { type: 'email', message: 'Must be a valid email address.', trigger: 'blur'}
                ],
                password: [
                    { required: true, message: 'Please enter your password.', trigger: 'blur' }
                ],
                token: [
                    {required: true, min: 9, max: 9, message: 'Please enter a valid token.', trigger: 'blur'}
                ]
            }
        }
    },
    methods:{
        ...mapActions(['loginUser', 'loginEmailOnly', 'checkToken']),
        login(formName){
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    this.loginUser(this.credentials).then(response => {
                        this.$router.push('/dashboard')
                    }).catch(err => {
                        console.log(err)
                        this.$alert(`${err.error}. Try again.`, 'Error', {
                          confirmButtonText: 'OK',
                          callback: action => {
                            this.resetForm(formName)
                          }
                        });
                    })
                } else {
                    console.log('error submit!!');
                    return false;
                }
            });
        },
        loginEmail(formName){
            this.$refs[formName].validate(valid => {
                if(valid){
                    this.loginEmailOnly(this.credentials).then(response => {
                        console.log(response)
                        this.showTokenForm = true;
                    }).catch(err => {
                        this.$message.error('Unable to verifry email. Try again.');
                        this.resetForm(formName);
                    })
                }
            })
        },
        validateToken(formName){
            this.$refs[formName].validate(valid => {
                if(valid){
                    this.checkToken(this.credentials).then(response => {
                        this.$router.push('/dashboard')
                    }).catch(err => {
                        this.$alert(`${err.error} Try again.`, 'Error', {
                          confirmButtonText: 'OK',
                          callback: action => {
                            this.resetForm(formName)
                            this.showTokenForm = false;
                          }
                        });
                    })
                }else {
                    console.log('error submit!!');
                    return false;
                }
            })
        },
        resetForm(formName) {
            this.$refs[formName].resetFields();
        }
    }
}
</script>

<style scoped>
    .outline-none{
        outline: none;
    }

    .box-card{
        margin-top: 9.5em;
    } 

    .image{
        width: 250px;
        margin-bottom: -90px;
        margin-top: 70px;
    }
</style>
