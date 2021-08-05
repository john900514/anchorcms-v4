<template>
    <div v-if="showThisElement">
        <label>{{ watsTheLabel }}</label>

        <div class="ld-cont">
            <select class="ld-select" :name="name" v-model="selectedLoc">
                <option value="" disabled>{{ nullValueLabel }}</option>
                <option v-if="availableLocations !== ''" v-for="(name, id) in availableLocations" :value="id">{{ name }}</option>
            </select>
        </div>
    </div>
</template>

<script>
import { mapMutations, mapGetters, mapActions } from 'vuex';
export default {
    name: "LocationDeptSelectComponent",
    props: ['label','name', 'value', 'attrs'],
    watch: {
        availableLocations(locations) {
            if(locations === '') {
                this.nullValueLabel = 'Not Available'
            }
            else {
                if(this.selectedRole === 'admin') {
                    this.nullValueLabel = 'Select a Department'
                    // @todo - if there's a preloaded value and its in the list, preload it
                }
                else
                {
                    this.nullValueLabel = 'Select a Location'
                }
            }

            let _this = this;
            setTimeout(function() {
                $('.ld-select').select2({
                    theme: "bootstrap"
                }).on('select2:select', function(e) {
                    _this.selectedLoc = $(this).val();

                });
            }, 250)
        },
        selectedRole(role) {
            switch(role) {
                case 'admin':
                    this.nullValueLabel = 'Loading Departments...'
                    this.getDepartments();
                    setTimeout(function() {
                        $('.ld-select').select2({
                            theme: "bootstrap"
                        });
                    }, 250)
                    this.showThisElement = true;
                break;

                case 'developer':
                default:
                    this.showThisElement = false;
            }
        },
        selectedClient(client) {
            if(client !== '') {
                this.nullValueLabel = 'Loading Locations...'
                this.getLocations();
                setTimeout(function() {
                    $('.ld-select').select2({
                        theme: "bootstrap"
                    });
                }, 250)
                this.showThisElement = true;
            }
        },
    },
    data() {
        return {
            selectedLoc: '',
            locations: '',
            nullValueLabel: 'Not Available',
            showThisElement: false
        };
    },
    computed: {
        ...mapGetters({
            selectedRole: 'usersCrud/role',
            selectedClient: 'usersCrud/client',
            availableLocations: 'usersCrud/locations'
        }),
        watsTheLabel() {
            switch(this.selectedRole) {
                case 'admin':
                    return 'Department';

                default:
                    return 'Location';
            }
        },
    },
    methods: {
        ...mapActions({
            getDepartments: 'usersCrud/getCapeAndBayDepartments',
            getLocations: 'usersCrud/getClientLocations'
        })
    },
    mounted() {
        let _this = this;
        if((this.value !== '') && (this.value !== undefined))
        {
            console.log('Preloaded value - '+this.value);
            this.selectedLoc = this.value;
        }

        setTimeout(function () {
            $('.ld-select').select2({
                theme: "bootstrap"
            });
        }, 250)
    }
}
</script>

<style scoped>
@media screen {
    .ld-cont, .ld-cont select {
        width: 100%;
    }

}
</style>
