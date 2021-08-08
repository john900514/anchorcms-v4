<template>
    <div class="container">
        <div class="row col-md-12 inner-container justify-content-center align-items-center">
            <vault-screen @done="toggleVaultScreen" v-if="showVaultScreen"></vault-screen>
            <router-view></router-view>
        </div>
    </div>
</template>

<script>
import { mapMutations } from 'vuex';
import VaultScreen from "../screens/SecretVault/SecretVaultScreen.vue";

export default {
    name: "SecretVaultContainer",
    components: { VaultScreen },
    props: ['authToken', 'vaultToken', 'session'],
    data() {
        return {
            showVaultScreen: true
        };
    },
    methods: {
        ...mapMutations({
            vaultState: 'secretVault/set'
        }),
        toggleVaultScreen() {
            this.showVaultScreen = false;
        },
        setTokens() {
            if((this.authToken !== undefined) && (this.vaultToken !== undefined)) {
                this.vaultState({
                    name: 'authToken',
                    val: this.authToken
                })

                this.vaultState({
                    name: 'vaultToken',
                    val: this.vaultToken
                })
            }
        }
    },
    mounted() {
        // @todo - implement some cache session restore bullshit
        // send the auth and vault tokens to the store
        this.setTokens();
        if(this.session === 'logged_in') {
            this.showVaultScreen = false;
            let _this = this;
            this.$router.push({name:'vault'})
            setTimeout(function() {
                console.log('Restoring session with an auto-validation!')
                _this.vaultState({
                    name: 'validationStatus',
                    val: _this.session
                })
            }, 250);


        }
        console.log('Open Sesame!...seeds?', this.$route);
    }
}
</script>

<style scoped>
    @media screen {
        .container {
            min-height: 20em;
            height: 100%;
            width: 100%;
        }

        .inner-container {
            height: 100%;
        }
    }
</style>
