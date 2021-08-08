<template>
    <vault-entry
        :validation-status="validationStatus"
        @password="pw => fetchValidation(pw)"
        @status="setStatus"
    ></vault-entry>
</template>

<script>
import { mapActions, mapGetters, mapMutations } from 'vuex';
import VaultEntry from "../../vue-components/screens/SecretVault/VaultEntryScreen.vue";

export default {
    name: "VaultIndex",
    components: {
        VaultEntry
    },
    watch: {
        validationStatus(status) {
            switch(status) {
                case 'success':
                case 'logged_in':
                    let _this = this;
                    setTimeout(function() {
                        _this.$router.push({name: 'vault-list'})
                    }, 2500)
                break;

            }

        }
    },
    data() {
        return {

        }
    },
    computed: {
        ...mapGetters({
            validationStatus: 'secretVault/validationStatus'
        }),
    },
    methods: {
        ...mapMutations({
            vaultState: 'secretVault/set'
        }),
        ...mapActions({
            validate: 'secretVault/validatePassword'
        }),
        fetchValidation(pw) {
            console.log('Validating password...');
            this.validate(pw);
        },
        setStatus(status) {
            this.vaultState({name: 'validationStatus', val: status})
        }
    },
    mounted() {
        console.log('Enter yo password, sucka!', this.$route.params);
    },
}
</script>

<style scoped>

</style>
