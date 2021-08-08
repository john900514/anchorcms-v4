<template>
    <vault-items
        :vault-name="vaultName"
        :vault-items="vaultItems"
        @lockout="triggerLockout"
        @go-back="triggerGoBack"

    ></vault-items>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import VaultItems from "../../vue-components/screens/SecretVault/VaultItemsScreen.vue";
export default {
    name: "VaultItemsPage",
    components: {
        VaultItems
    },
    computed: {
        ...mapGetters({
            activeVault: 'secretVault/activeVault',
            availableVaults: 'secretVault/availableVaults',
            vaultItems: 'secretVault/availableVaultItems',
        }),
        vaultName() {
            return this.availableVaults[this.activeVault]['name'];
        }
    },
    methods: {
        ...mapActions({
            lockout: 'secretVault/processLockout'
        }),
        triggerLockout() {
            console.log('Locking out!');
            this.lockout();

        },
        triggerGoBack() {
            console.log('Going Back!');
            this.$router.push({ name: 'vault-list'});
        },
    },
    mounted() {
        console.log('The treasure awaits...', this.activeVault)
    }
}
</script>

<style scoped>

</style>
