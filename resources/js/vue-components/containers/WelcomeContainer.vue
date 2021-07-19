<template>
    <welcome-screen
        :show-forgot-password="showForgotPassword"
        :show-registration="showRegistration"
        :forgot-password-url="forgotPasswordUrl"
        :registration-url="registrationUrl"
        :login-url="loginUrl"
        :csrf-field="csrfField"
    ></welcome-screen>
</template>

<script>
    import WelcomeScreen from "../screens/Welcome/WelcomeSplashScreen";

    export default {
        name: "WelcomeContainer",
        components: {
            WelcomeScreen
        },
        props: ['showForgotPassword', 'showRegistration', 'forgotPasswordUrl', 'registrationUrl', 'loginUrl', 'csrfField'],
    }
</script>

<style scoped>

</style>
