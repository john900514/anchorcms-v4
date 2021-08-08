<template>
    <div class="col-12 vault-items">
        <div class="card col-12 bg-blue">
            <div class="card-header title-row row">
                <button type="button" class="col-1 btn btn-link"  @click="emitGoBack()">
                    <i class="fad fa-hand-point-left go-back-btn text-dark"></i>
                </button>

                <h2 class="text-light col-10 text-center">{{ headText }}</h2>
                <button type="button" class="col-1 btn btn-link" @click="emitLockout()">
                    <i class="fad fa-lock lock-out-btn text-dark"></i>
                </button>
            </div>

            <div class="card-body row flex-wrap justify-content-center bg-light">
                <div class="card bg-danger item-row" v-if="vaultItems.length === 0">
                    <div class="card-body col-12 text-center row align-items-center justify-content-center">
                        <div class="col-1 side-icon-right">
                            <i class="fad fa-times-circle"></i>
                        </div>
                        <div class="col-10 row text-left">
                            <div class="col-12"><small>No Items in this vault.</small></div>
                            <div class="col-12"><small>Try Looking at another vault room.</small></div>
                        </div>
                        <div class="col-1 side-icon-left">
                            <i class="fad fa-times-circle text-danger"></i>
                        </div>
                    </div>
                </div>
                    <vault-item-row v-for="(item, idx) in vaultItems" v-bind:key="idx"
                        :title="item.title"
                        :category="item.category"
                        :item="item"
                        @clicked="toggleModal(idx)"
                    ></vault-item-row>
            </div>
        </div>
        <sweet-modal ref="itemDetailModal" hide-close-button blocking overlay-theme="dark" modal-theme="dark">
            <div slot="title">
                <h2 class="text-center modal-title">{{ activeItem.title }}</h2>
            </div>
            <div slot="default">
                <item-details
                    :active-item="activeItem"
                ></item-details>
            </div>
            <div slot="button">
                <button type="button" class="btn btn-danger" @click="closeModal()"><i class="fad fa-door-closed"></i> Close</button>
            </div>
        </sweet-modal>
    </div>
</template>

<script>

import VaultItemRow from "../../components/secretsVault/VaultItemRowComponent.vue";
import ItemDetails from "../../components/secretsVault/VaultItemDetailsComponent.vue";
export default {
    name: "VaultItemsScreen",
    components: {
        VaultItemRow,
        ItemDetails
    },
    props: ['vaultName', 'vaultItems'],
    watch: {},
    data() {
        return {
            activeItem: ''
        }
    },
    computed: {
        headText() {
            let t = this.vaultName + ' - Available Secrets'

            if((this.vaultName === undefined) || (this.vaultName === '')) {
                t = 'No-Named-Vault\'s - Incredible Secrets'
            }

            return t;
        },
    },
    methods: {
        toggleModal(idx) {
            console.log('Toggling Modal!')
            this.activeItem = this.vaultItems[idx];
            this.$refs.itemDetailModal.open();
        },
        closeModal() {
            this.$refs.itemDetailModal.close();
            this.activeItem = '';
        },
        emitGoBack() {
            this.$emit('go-back');
        },
        emitLockout() {
            this.$emit('lockout');
        },
    }
}
</script>

<style scoped>
    @media screen {
        .card-body {
            padding: 0;
            overflow: scroll;
            max-height: 25em;
        }

        .item-row {
            width: 100%;
            margin-bottom: 0;
        }

        .side-icon-right {
            border-right: 1px solid #000;
        }

        .side-icon-left {
            border-left: 1px solid #000;
        }

        .modal-title {
            margin-top: 0.75rem;
        }

        .lock-out-btn {
            margin-right: 0;
            font-size: 1.5em
        }
        .go-back-btn {
            font-size: 1.5em
        }
    }
</style>
