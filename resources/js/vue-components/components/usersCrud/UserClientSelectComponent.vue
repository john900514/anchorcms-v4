<template>
    <div class="uc-cont" v-if="showElement">
        <label>{{ label }}</label>
        <select class="client-select" :name="name" v-model="selectedClient">
            <option value="" disabled>{{ nullValueLabel }}</option>
            <option v-if="options !== ''" v-for="(name, id) in options" :value="id">{{ name }}</option>
        </select>
    </div>
</template>

<script>
import { mapMutations, mapGetters } from 'vuex';

export default {
    name: "UserClientSelectComponent",
    props: ['label','name','options', 'value', 'attrs'],
    data() {
        return {
            selectedClient: '',
            nullValueLabel: 'Select a Client'
        };
    },
    computed: {
        ...mapGetters({
            selectedRole: 'usersCrud/role'
        }),
        showElement() {
            let r = false;

            switch(this.selectedRole) {
                case 'executive':
                case 'leader':
                case 'rep':
                    r = true;
                    let _this = this;
                    setTimeout(function () {
                        $('.client-select').select2({
                            theme: "bootstrap"
                        }).on('select2:select', function(e) {
                            _this.selectedClient = $(this).val();
                            _this.setClient(_this.selectedClient)
                        });
                    }, 250);
                    break;
            }

            return r;
        }
    },
    methods: {
        ...mapMutations({
            setClient: 'usersCrud/client'
        })
    },
    mounted() {
        console.log('Preloaded attributes - ',this.attrs);
        let _this = this;
        if((this.value !== '') && (this.value !== undefined))
        {
            console.log('Preloaded value - '+this.value);
            this.selectedClient = this.value;
            this.setClient(this.selectedClient)
        }
        setTimeout(function () {
            $('.client-select').select2({
                theme: "bootstrap"
            }).on('select2:select', function(e) {
                _this.selectedClient = $(this).val();
                _this.setClient(_this.selectedClient)
            });
        }, 250)
    }
}
</script>

<style scoped>
@media screen {
    .uc-cont, .uc-cont select {
        width: 100%;
    }

}
</style>
