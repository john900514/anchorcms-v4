<template>
    <div class="row col-md-8 col-sm-12 enter-password">
        <div class="card bg-blue col-12">
            <div class="card-body">
                <h1 class="text-center col-12 cool-icon">
                    <i :class="sickIcon"></i>
                </h1>
                <div class="col-12 justify-content-center">
                    <div class="input-group justify-content-center">
                        <div class="input-group-prepend" v-show="(inputPassword !== '') || (validationStatus === 'logged_in')"><span class="input-group-text"><i :class="sideIcon"></i></span></div>
                        <input class="form-control"
                               v-if="(validationStatus !== 'success') && (validationStatus !== 'logged_in')"
                               type="password"
                               v-model="inputPassword"
                               placeholder="Enter Your Login Password"
                               :disabled="validated"
                               @keypress="(e) => validateInput(e)"
                        />
                        <span class="input-group-append" v-show="(inputPassword !== '') || (validationStatus === 'logged_in')">
                              <button class="btn" :class="submitClass" type="button" @click="submitInput()" :disabled="validated">{{ submitText }}</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "VaultEntryScreen",
    props: ['validationStatus'],
    watch: {
        validated(flag) {
            if(flag === true) {
                console.log('emitting password');

            }
        },
        validationStatus(status) {
            switch(status) {
                case 'session_expired':
                    this.validated = false
                    break;

                case 'logged_in':
                    this.validated = true
                    break;
            }
        }

    },
    data() {
        return {
            inputPassword: '',
            validated: false,
        }
    },
    computed: {
        sickIcon() {
            let r = 'fad fa-lock-alt';

            if(this.inputPassword !== '') {
                r = 'fad fa-key';

                if(this.validated) {
                    r = 'fad fa-lock-alt animated faa-ring faa-fast';

                    if(this.validationStatus === 'success') {
                        r = 'fad fa-lock-open-alt';
                    }
                }
                else if(this.validationStatus === 'session_expired') {
                    r = 'fad fa-spider-black-widow faa-ring animated'
                    let _this = this;
                    setTimeout(function() {
                        _this.inputPassword = '';
                    }, 2500)
                }
            }
            else if(this.validationStatus === 'logged_in') {
                r = 'fad fa-portal-enter animated faa-passing faa-slow';
            }

            return r;
        },
        sideIcon() {
            let r = 'fad fa-lock-alt';

            if(this.validated) {
                r = 'fad fa-spinner-third animated faa-spin';

                if(this.validationStatus === 'success') {
                    r = 'fad fa-thumbs-up text-success';
                }
                else if(this.validationStatus === 'logged_in') {
                    r = 'fad fa-lock-open-alt';
                }
            }
            else if(this.validationStatus === 'session_expired') {
                r = 'fad fa-skull animated faa-pulse';
            }

            return r;
        },
        submitText() {
            let t = 'Submit'

            if(this.validated) {
                t = 'Validating...'

                if(this.validationStatus === 'success') {
                    t = 'Success!'
                }
                else if(this.validationStatus === 'logged_in') {
                    t = 'Restoring..'
                }
            }
            else if(this.validationStatus === 'session_expired') {
                t = 'Invalid!';
            }

            return t;
        },
        submitClass() {
            let c = 'btn-chartreuse';

            if(this.validated) {
                c = 'btn-primary';

                if(this.validationStatus === 'success') {
                    c = 'btn-success';
                }
                else if(this.validationStatus === 'logged_in') {
                    c = 'btn-dark'
                }
            }
            else if(this.validationStatus === 'session_expired') {
                c = 'btn-danger';
            }


            return c;
        },
    },
    methods: {
        validateInput(e) {
            if((e.code === 'Enter') || (e.code === 'NumpadEnter')) {
                this.submitInput()
            }
            else {
                switch(this.validationStatus) {
                    case 'session_expired':
                        this.$emit('status', 'not_logged_in');
                    break;

                    default:
                        console.log('Pressed '+ e.code);
                }
            }

        },
        submitInput() {
            this.validated = true;
            this.$emit('password', this.inputPassword)
        }
    },
    mounted() {
        if(this.validationStatus === 'logged_in') {
            this.validated = true;
        }
    }
}
</script>

<style scoped>
    @media screen {
        .enter-password {
            height: 100%;
        }

        .cool-icon {
            font-size: 10em;
            text-decoration: none;
        }

        a {
            color: #000;
        }

        .hovery-text {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .btn-primary {
            border: 2px solid #000;
        }
    }
    @media screen and (max-width: 999px) {
        .enter-password {
            min-height: 30em;
            height: 100%;
        }
    }
    @media screen and (min-width: 1000px) {
        .enter-password {
            min-height: 20em;
        }
    }
</style>
