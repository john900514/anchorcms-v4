<template>
    <div class="us-cont">
        <select class="user-select" :name="name" v-model="selectedRole">
            <option value="" disabled>{{ nullValueLabel }}</option>
            <option v-if="options !== ''" v-for="(name, id) in options" :value="id">{{ name }}</option>
        </select>
    </div>
</template>

<script>
import { mapMutations } from 'vuex';

export default {
    name: "UserRolesSelectComponent",
    props: ['name','options', 'value', 'attrs'],
    data() {
        return {
            selectedRole: '',
            nullValueLabel: 'Select a Role'
        };
    },
    methods: {
        ...mapMutations({
            setRole: 'usersCrud/role'
        })
    },
    mounted() {
        console.log('Preloaded attributes - ',this.attrs);
        let _this = this;
        if((this.value !== '') && (this.value !== undefined))
        {
            console.log('Preloaded value - '+this.value);
            this.selectedRole = this.value;
            this.setRole(this.selectedRole)
        }
        setTimeout(function () {
            $('.user-select').select2({
                theme: "bootstrap"
            }).on('select2:select', function(e) {
                _this.selectedRole = $(this).val();
                _this.setRole(_this.selectedRole)
            });
        }, 250)
    }
}
</script>

<style scoped>
    @media screen {
        .us-cont, .us-cont select {
            width: 100%;
        }

    }
</style>
