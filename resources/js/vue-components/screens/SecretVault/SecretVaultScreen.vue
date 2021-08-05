<template>
    <div class="row col-md-8 col-sm-12 press-start">
        <div class="card bg-blue col-12">
            <div class="card-body">
                <p class="text-center col-12 cool-icon" @mouseenter="toggleHover(true)" @mouseleave="toggleHover(false)" @click="shutOff()">
                    <router-link to="/access/vault/entry">
                        <i class="fad fa-dharmachakra" :class="animated"></i>
                    </router-link>
                </p>
                <p class="text-center col-12 hovery-text">{{ isHovering ? 'Tap to Access' : 'The Vault' }}</p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "SecretVaultScreen",
    data() {
        return {
            isHovering: false
        };
    },
    computed: {
        animated() {
            let r = '';

            if(this.isHovering) {
                r = 'faa-shake faa-slow animated text-shart';
            }

            return r;
        }
    },
    methods: {
        shutOff() {
            let _this = this;
            $('.press-start').fadeOut();
            setTimeout(function() {
                _this.$emit('done')
            }, 1000)
        },
        toggleHover(flag) {
            this.isHovering = flag;
        }
    },
}
</script>

<style scoped>
    @media screen {
        .press-start {
            height: 100%;
        }

        .cool-icon {
            font-size: 10em;
            text-decoration: none;
        }

        a {
            color: #000;
        }

        .hovery-text {
            font-size: 2.5rem;
            font-weight: 600;
        }
    }
    @media screen and (max-width: 999px) {
        .press-start {
            min-height: 30em;
            height: 100%;
        }
    }
    @media screen and (min-width: 1000px) {
        .press-start {
            min-height: 20em;
        }
    }
</style>
