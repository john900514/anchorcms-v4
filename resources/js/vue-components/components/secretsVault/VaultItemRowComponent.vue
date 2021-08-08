<template>
    <div class="card item-row" :class="cardBg" @mouseenter="hovering = true" @mouseleave="hovering = false" @click="itemClicked()">
        <div class="card-body col-12 text-center row align-items-center justify-content-center">
            <div class="col-2 side-icon-right">
                <i :class="itemIcon" :alt="altTag"></i>
            </div>
            <div class="col-9 row text-left">
                <div class="col-12"><small class="item-title" :class="itemTextColor">{{ title }}</small></div>
                <div class="col-12"><small class="item-desc" :class="itemTextColor">{{ itemDesc }}</small></div>
            </div>
            <div class="col-1 side-icon-left">
                <i class="fad fa-chevron-circle-right"></i>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "VaultItemRowComponent",
    props: ['title', 'category', 'item'],
    data() {
        return {
            hovering: false
        };
    },
    computed: {
        altTag() {
            return 'Category - '+ this.category;
        },
        itemDesc() {
            let t = this.altTag;

            switch(this.category) {
                case 'LOGIN':
                    if('urls' in this.item) {
                        if(this.item.urls.length > 0) {
                            if('href' in this.item.urls[0]) {
                                if(this.item.urls[0].href.length <= 60) {
                                    t = 'Url - '+ this.item.urls[0].href;
                                }

                            }
                        }

                    }
                    break;
            }

            return t;
        },
        itemIcon() {
            let r = '';
            switch(this.category) {
                case 'LOGIN':
                    r = 'fad fa-id-card';
                    break;

                case 'DATABASE':
                    r = 'fad fa-database';
                    break;

                case 'SERVER':
                    r = 'fad fa-server';
                    break;

                case 'SOFTWARE_LICENSE':
                    r = 'fad fa-scroll-old'
                    break;

                case 'DOCUMENT':
                    r = 'fad fa-file-download'
                    break;

                case 'API_CREDENTIAL':
                    r = 'fad fa-user-visor'
                    break;

                default:
                    r = 'fad fa-question';
            }

            if(this.hovering) {
                r = r + ' text-dark';
            }

            return r;
        },
        cardBg() {
            let r = 'bg-light';

            switch(this.category) {
                case 'LOGIN':
                    r = 'bg-success';
                    break;

                case 'DATABASE':
                    r = 'bg-orange';
                    break;

                case 'SERVER':
                    r = 'bg-info';
                    break;

                case 'SOFTWARE_LICENSE':
                    r = 'bg-cyan';
                    break;

                case 'API_CREDENTIAL':
                    r = 'bg-primary'
                    break;

                case 'DOCUMENT':
                    r = 'bg-pink'
                    break;

                default:
                    r = 'bg-dark';
            }

            if(this.hovering) {
                r = 'bg-warning';
            }

            return r
        },
        itemTextColor() {
            let r = 'text-light'

            if(this.hovering) {
                r = 'text-dark';
            }

            return r;
        }
    },
    methods: {
        itemClicked() {
            this.$emit('clicked');
        }
    },
}
</script>

<style scoped>
    @media screen {
        .card-body {
            padding: 0;
        }

        .item-row {
            width: 100%;
            margin-bottom: 0;
            cursor: pointer;
        }

        .side-icon-right {
            border-right: 1px solid #000;
        }

        .side-icon-left {
            border-left: 1px solid #000;
        }

        .item-title {
            font-weight: 800;
        }

        .item-desc {
            font-style: italic;
        }
    }

    @media screen and (max-width: 999px) {
        .side-icon-right {
            font-size: 0.7em;
        }
    }

    @media screen and (min-width: 1000px) {

    }
</style>
