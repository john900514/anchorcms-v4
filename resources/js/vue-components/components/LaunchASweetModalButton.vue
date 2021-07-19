<template>
    <div :class="divClass">
        <button :class="buttonClass" @click="toggleModal()">
            <i v-if="renderIcon" :class="buttonIcon"></i> {{ buttonText }}
        </button>
        <sweet-modal ref="modal">
            <!-- @todo - update this to support a vue-component or an iframe -->
            <iframe width="560" height="315" :src="modalSrc" frameborder="0" allowfullscreen></iframe>
        </sweet-modal>
    </div>
</template>

<script>
    export default {
        name: "LaunchASweetModalButton",
        props: ['divClass', 'buttonIcon', 'buttonText', 'buttonClass', 'modalSrc'],
        computed: {
            renderIcon() {
                let r = false;

                if(this.buttonIcon) {
                    r = true;
                }

                return r;
            },
        },
        methods: {
            toggleModal() {
                this.$refs.modal.open();
            },
        }
    }
</script>

<style scoped>

</style>
