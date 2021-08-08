<template>
    <div class="row  justify-content-between" v-else>
        <p class="col-3 text-left">{{ details.label }}</p>
        <p class="col-4 pw" v-if="showConcealed" ref="mylink">{{ details.value }}</p>
        <p class="col-5 text-right">
            <button type="button" class="btn btn-success" @click="toggleClipboard(details.value)" v-if="showConcealed">{{ copyBtnText }}</button>
            <button type="button" class="btn btn-info" @click="toggleConcealed()">{{ btnText }}</button>
        </p>
    </div>
</template>

<script>
export default {
    name: "ConcealedVaultItemComponent",
    props: ['details'],
    data() {
        return {
            showConcealed: false,
            addedToClipboard: false
        }
    },
    computed: {
        btnText() {
            let r = 'Show'

            if(this.showConcealed) {
                r = 'Hide';
            }

            return r
        },
        copyBtnText() {
            let r = 'Copy'

            if(this.addedToClipboard) {
                r = 'Copied!';
            }

            return r
        }
    },
    methods: {
        toggleConcealed() {
            this.showConcealed = !this.showConcealed;
            this.resetConcealed();
        },
        toggleClipboard(value) {
            let element = this.$refs.mylink;
            let range = document.createRange();
            range.selectNode(element);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);

            document.execCommand("copy");

            this.addedToClipboard = true;
            this.resetConcealed();
        },
        resetConcealed() {
            let _this = this;
            setTimeout(function() {
                _this.addedToClipboard = false;
                _this.showConcealed = false;
            }, 5000)
        }
    },
    mounted() {
        console.log('concealed details - ', this.details)
    }
}
</script>

<style scoped>

</style>
