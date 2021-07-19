<template>
    <div class="the-place">
        <video-bg v-show="showVideo" video-url="https://player.vimeo.com/video/447175009?title=0&portrait=0&muted=1&autopause=0&loop=1&background=1&app_id=122963"
            :video-opacity="0.8"
        ></video-bg>
        <div class="top-left links">
            <a href="https://capeandbay.com" style="color: rgba(1,44,128,0.85)">Cape&Bay</a>
        </div>
        <div class="top-right links">
            <button type="button" class="login-button" @click="toggleLoginModal()"><i class="far fa-bars"></i></button>
        </div>

        <div class="content">
            <div class="title m-b-md" @mouseenter="showVideo = true" @mouseleave="showVideo = false">
                <img src="https://amchorcms-assets.s3.amazonaws.com/anchorCMSLogo.png">
            </div>

            <div class="links">

            </div>
        </div>
        <sweet-modal ref="loginModal" :width="768"
        >
            <login-screen
                :show-forgot-password="showForgotPassword"
                :show-registration="showRegistration"
                :forgot-password-url="forgotPasswordUrl"
                :registration-url="registrationUrl"
                :login-url="loginUrl"
                :csrf-field="csrfField"
            >
            </login-screen>
        </sweet-modal>
    </div>
</template>

<script>
import LoginScreen from "../../components/LoginModal";

export default {
    name: "WelcomeSplashScreen",
    components: {
        LoginScreen
    },
    props: ['showForgotPassword', 'showRegistration', 'forgotPasswordUrl', 'registrationUrl', 'loginUrl', 'csrfField'],
    data() {
        return {
            showVideo: false
        };
    },
    methods: {
        toggleLoginModal() {
            this.$refs.loginModal.open();
        }
    },
}
</script>

<style scoped>

</style>
