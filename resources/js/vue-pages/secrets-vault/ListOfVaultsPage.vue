<template>
    <vault-list
        :loading="loading"
        :vaults="availableVaults"
        :errors="errors"
        :items="availableItems"
        @selected="enterVaultRoom"
        @lockout="triggerLockout"
        @go-back="triggerGoBack"
    ></vault-list>
</template>

<script>
    import { mapActions, mapGetters } from 'vuex';
    import VaultList from "../../vue-components/screens/SecretVault/ListOfVaultsScreen.vue";
    export default {
        name: "ListOfVaultsPage",
        components: {
            VaultList
        },
        watch: {
            availableVaults(vaults) {
                if(vaults.length > 0) {
                    this.loading = false;
                }
                else if(this.errors !== '') {
                    this.loading = false;
                }
            },
            availableItems(items) {
                console.log('updating items;')
                this.loading = false;
            }
        },
        data() {
            return {
                loading: true,
            };
        },
        computed: {
            ...mapGetters({
                availableVaults: 'secretVault/availableVaults',
                availableItems: 'secretVault/availableVaultItems',
                errors: 'secretVault/vaultError'
            })
        },
        methods: {
            ...mapActions({
                getVaultRooms: 'secretVault/fetchVaultList',
                getVaultItems: 'secretVault/fetchVaultItems',
                lockout: 'secretVault/processLockout'
            }),
            enterVaultRoom(idx) {
                this.getVaultItems(idx);
            },
            triggerLockout() {
                console.log('Locking out!');
                this.lockout();
            },
            triggerGoBack() {
                console.log('Going Back!');
                this.$router.go(-2);
            },
        },
        mounted() {
            if(this.availableVaults.length === 0) {
                this.getVaultRooms();
            }
            else {
                this.loading = false;
            }
            console.log('The world is now yours.', this.$route);
        }
    }
</script>

<style scoped>

</style>
