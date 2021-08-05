<template>
    <vault-list
        :loading="loading"
        :vaults="availableVaults"
        :errors="errors"
    ></vault-list>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex';
    import VaultList from "../../vue-components/screens/SecretVault/ListOfVaultsScreen";
    export default {
        name: "ListOfVaultsPage",
        watch: {
            availableVaults(vaults) {
                if(vaults.length > 0) {
                    this.loading = false;
                }
                else if(this.errors !== '') {
                    this.loading = false;
                }
            }
        },
        data() {
            return {
                loading: true,
            };
        },
        components: {
            VaultList
        },
        computed: {
            ...mapGetters({
                availableVaults: 'secretVault/availableVaults',
                errors: 'secretVault/vaultError'
            })
        },
        methods: {
            ...mapActions({
                getVaultRooms: 'secretVault/fetchVaultList'
            })
        },
        mounted() {
            if(this.availableVaults.length === 0) {
                this.getVaultRooms();
            }
            console.log('The world is now yours.', this.$route);
        }
    }
</script>

<style scoped>

</style>
