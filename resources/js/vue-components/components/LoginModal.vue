<template>
    <div>
        <div class="row justify-content-center col-12">
            <div class="col-12 col-md-8 col-lg-8">
                <h3 class="text-center mb-4 login-text">Employer/Customer Login</h3>
                <div class="card">
                    <div class="card-body">
                        <form class="col-md-12 p-t-10" role="form" method="POST" :action="loginUrl">
                            <input type="hidden" name="_token" :value="csrfField">

                            <div class="form-group">
                                <label class="control-label">Email Address</label>

                                <div>
                                    <input type="text" class="form-control" name="email" value="" id="username">

                                    <!--
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first($username) }}</strong>
                                    </span>
                                    -->
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label" for="password">Password</label>

                                <div>
                                    <input type="password" class="form-control" name="password" id="password">

                                    <!--
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    -->
                                </div>
                            </div>

                            <div class="form-group">
                                <div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="remember"> Remember Me
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div>
                                    <button type="submit" class="btn btn-block btn-primary">
                                        Login
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center" v-if="showForgotPassword"><a :href="forgotPasswordUrl">Forgot Your Password?</a></div>
                <div class="text-center" v-if="showRegistration">
                    <a :href="registrationUrl">Register</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "LoginModal",
    props: ['showForgotPassword', 'showRegistration', 'forgotPasswordUrl', 'registrationUrl', 'loginUrl', 'csrfField']
}
</script>

<style scoped>
    .login-text {
        color: #fff;
    }
</style>
