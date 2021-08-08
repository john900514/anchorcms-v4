<template>
    <div class="col-12">
        <div class="detail-title col-12">
            <h3 class="text-center">Details</h3>
        </div>
        <div class="details col-12">
            <ul>
                <li v-for="(prop, propName) in displayableItemProps">
                    <div class="row  justify-content-between">
                        <p>{{ propName }}</p>
                        <p v-html="prop"></p>
                    </div>
                </li>
                <li v-if="loading"> Loading Remaining Item Details... </li>
                <li v-if="!loading" v-for="(prop, propName) in displayableItemDetailFields">
                    <concealed-item v-if="isConcealed(propName)" :details="prop"></concealed-item>
                    <div class="row  justify-content-between" v-else>
                        <p>{{ propName }}</p>
                        <p v-html="prop"></p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';

import ConcealedItem from "./ConcealedVaultItemComponent";
export default {
    name: "VaultItemDetailsComponent",
    components: {
        ConcealedItem
    },
    props: ['activeItem'],
    watch: {
        activeItem(item) {
            if(item !== '') {
                console.log('Viewing some details on '+item.title)
                this.triggerDetailFetch();
            }
            else {
                console.log("Resetting details");
            }

        },
        itemDetails(details) {
            this.loading = false;
            console.log('New Deets', details);
        }
    },
    data() {
        return {
            loading: false
        };
    },
    computed: {
        ...mapGetters({
            vaultId: 'secretVault/vaultId',
            itemDetails: 'secretVault/vaultItemDetails'
        }),
        displayableItemDetailFields() {
            let results = {};

            if(this.itemDetails !== '') {
                for(let x in this.itemDetails.fields) {
                    let field = this.itemDetails.fields[x]
                    switch(field.type) {
                        case 'STRING':
                        case 'MENU':
                            if(('value' in field) && (field.value !== undefined)) {

                                if('label' in field) {
                                    switch(field['label']) {
                                        case 'rememberme':
                                        case 'wp-submit':
                                            break;

                                        case 'log':
                                            if(field.id !== '') {
                                                results[field.id] = `<p>${field.value}</p>`
                                            }
                                            break;

                                        default:
                                            results[field.label] = `<p>${field.value}</p>`
                                    }

                                }
                                else if(field.id !== '') {
                                    results[field.id] = `<p>${field.value}</p>`
                                }
                            }
                            break;

                        case 'CONCEALED':
                            results[`concealed-${x}`] = this.itemDetails.fields[x]
                            break;

                    }
                }
            }


            return results;
        },
        displayableItemProps() {
            let results = {};

            for(let x in this.activeItem) {
                switch(x) {
                    case 'category':
                    case 'version':
                        results[x] = this.activeItem[x];
                        break;

                    case 'urls':
                        for(let y in this.activeItem[x]) {
                            results['URL '+y] = `<a href="${this.activeItem[x][y].href}" target="_blank" class="btn btn-link"><i class="fad fa-external-link"></i> Visit</a>` ;
                        }
                    break;
                }
            }

            return results;
        }
    },
    methods: {
        ...mapActions({
            getDetails: 'secretVault/fetchItemDetails'
        }),
        triggerDetailFetch() {
            this.loading = true;
            this.getDetails({
                vaultId: this.vaultId,
                itemId: this.activeItem.id
            })
        },
        isConcealed(name) {
            return name.includes('concealed')
        }
    },
    mounted() {

    }
}
</script>

<style scoped>
    @media screen {
        ul {
            list-style: none;
        }

    }
</style>
