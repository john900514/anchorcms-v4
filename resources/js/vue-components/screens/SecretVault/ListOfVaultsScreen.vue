<template>
    <div class="col-12 vault-list">
        <div class="card col-12 bg-blue">
            <div class="card-header row">
                <button type="button" class="col-1 btn btn-link">
                    <i v-if="false" class="fad fa-hand-point-left go-back-btn text-dark"></i>
                </button>

                <h2 class="text-light col-10 text-center">{{ headText }}</h2>
                <button type="button" class="col-1 btn btn-link" @click="emitLockout()">
                    <i class="fad fa-lock lock-out-btn text-dark"></i>
                </button>
            </div>

            <div class="card-body row flex-wrap justify-content-center bg-light overflow-scroll">
                <div class="card bg-light col-md-4 col-sm-12" v-if="vaults.length === 0">
                    <div class="card-body col-12 column text-center">
                        <div class="loady-spinny col-12 text-center"><i class="fad fa-spinner-third animated faa-spin"></i></div>
                        <p class="loading-text">{{ loadingText }}</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-12" v-for="(vault, idx) in vaults">
                    <div class="card-header text-center bg-blue segment">
                        <h3 class="text-light">{{ vault.name }}</h3>
                    </div>
                    <div class="card-body col-12 column text-center bg-secondary segment-bottom">
                        <p class="loading-text">Things Inside:  {{ vault.items }}</p>
                        <p class="loading-text">{{ vault.description }}</p>
                        <p class="loading-text"><i>Last Updated: {{ vault.updatedAt }}</i></p>
                        <button type="button" class="btn btn-danger" @click="triggerVaultOpen(idx)"><i class="fad fa-lock-open-alt"></i> Open</button>
                    </div>
                </div>
            </div>
        </div>
        <sweet-modal modal-theme="dark" overlay-theme="dark" ref="loaderModal" :blocking="true" :hide-close-button="true">
            <i class="fad fa-spinner-third animated faa-spin loader-icon"></i>
            <br />
            <br />
            <p>Getting {{ activeVault['name'] }} Items...</p>
        </sweet-modal>
        <sweet-modal icon="success" modal-theme="dark" overlay-theme="dark" ref="successModal">
            Got 'em!
        </sweet-modal>
    </div>
</template>

<script>

export default {
    name: "ListOfVaultsScreen",
    props: ['vaults','loading', 'errors', 'items'],
    watch: {
        vaults(vaults) {

        },
        items(items) {
            if(items.length > 0) {
                this.$refs.loaderModal.close();
                let _this = this;

                setTimeout(function() {

                    _this.$refs.successModal.open();

                    setTimeout(function() {
                        _this.$router.push({name: 'vault-items'});
                    }, 1500)
                }, 250)
            }
        }
    },
    data() {
        return {
            activeVault: ''
        };
    },
    computed: {
        headText() {
            let t = 'Loading...'

            if(!this.loading) {
                t = 'Available Vault Rooms'

                if(this.errors !== '') {
                    t = 'No Vaults Available'
                }
            }

            return t;
        },
        loadingText() {
            let t = 'Accessing Available Vault Rooms...';

            if(this.errors !== '') {
                t = this.errors;
            }

            return t;
        },
    },
    methods: {
        triggerVaultOpen(idx) {
            console.log(this.vaults[idx])
            this.activeVault = this.vaults[idx];
            this.$refs.loaderModal.open();
            this.$emit('selected', idx);
        },
        emitGoBack() {
            this.$emit('go-back');
        },
        emitLockout() {
            this.$emit('lockout');
        },
    },
    mounted() {},
}
</script>

<style scoped>
    @media screen {

        .loady-spinny i {
            font-size: 2em;
            color: #000;

        }

        .loading-text {
            font-style: italic;
        }

        .segment {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-top: 1px solid #000;
            padding: 0;
        }

        .segment-bottom {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-bottom: 1px solid #000;


            margin-bottom: 1rem;
        }

        .segment h3 {
            font-size: 1.5em;
        }

        .loader-icon {
            font-size: 3em;
        }

        .lock-out-btn {
            margin-right: 0;
            font-size: 1.5em
        }
        .go-back-btn {
            font-size: 1.5em
        }

        .overflow-scroll {
            overflow: scroll;
            max-height: 30em;
        }
    }
</style>
