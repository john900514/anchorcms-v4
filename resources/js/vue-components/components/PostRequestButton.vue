<template>
    <div :class="divClass">
        <button :class="buttonClass" @click="submitForm()">
            <i v-if="renderIcon" :class="buttonIcon"></i> {{ buttonText }}
        </button>
        <form method="POST" :action="actionUrl" :id="linkId">
            <input type="hidden" name="_token" :value="csrf">
            <input type="hidden" :value="userId" name="userId" />
        </form>
    </div>
</template>

<script>
export default {
    name: "PostRequestButton",
    props: ['linkId', 'divClass', 'buttonIcon', 'buttonText', 'buttonClass', 'actionUrl', 'userId', 'csrf'],
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
        submitForm() {
            $('#'+this.linkId).submit();
        }
    }
}
</script>

<style scoped>

</style>
