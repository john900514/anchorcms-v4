<template>
    <!-- The video -->
    <div id="myVideo">
        <div class="video-overlay"></div>
        <iframe :src="videoUrl" frameborder="0" allowfullscreen :style="{'--vo' : videoOpacity}"></iframe>
    </div>
</template>

<script>
    export default {
        name: "AnimatedBackgroundComponent",
        props: ['videoUrl', 'videoOpacity'],
        mounted() {
            console.log(`starting video - ${this.videoUrl}...`, this.video);
        },
    }
</script>

<style scoped>
#myVideo {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -2;
    pointer-events: none;
    overflow: hidden;
}

#myVideo iframe {
    width: 100vw;
    height: 56.25vw; /* Given a 16:9 aspect ratio, 9/16*100 = 56.25 */
    min-height: 100vh;
    min-width: 177.77vh; /* Given a 16:9 aspect ratio, 16/9*100 = 177.77 */
    position: absolute;
    top: 50%;
    left: 50%;
    opacity: var(--vo);
    transform: translate(-50%, -50%);
}

.video-overlay {
    background: linear-gradient(138deg, rgba(194,210,0,0.85) 0%, rgba(223,242,0,0.85) 28%, rgba(6,54,158,0.85) 50%, rgba(1,44,128,0.85) 83%, rgba(3,26,69,0.85) 100%);
    height: 100%;
    width: 100%;
    position: absolute;
    top: 0px;
    left: 0px;
    z-index: -1;
    opacity: 0.8;
}
</style>
