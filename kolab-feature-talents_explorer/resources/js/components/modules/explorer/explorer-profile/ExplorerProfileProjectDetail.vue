<template>
    <div class="modal-mask">
        <div class="modal-wrapper" v-on:click="emitCloseModal">
            <div class="module__explorer_project_detail" id="vue__explorer_project_detail">
                <div class="main-container row text-center" style="background: none;">
                    <div class="col-12">
                        <h1 style="text-align: center; width:100%;">{{ project.name }}</h1>
                        <span class="project-date">23 avr. 2021</span>
                        <p>
                            {{ project.description }}
                        </p>
                        <div class="project-main-media-container">
                            <button type="button" @click="toggleMedia('left')" class="media-selector">
                                <span style="width: 25px; height: 41px" class="picto-explorer-project-selector-left"/>
                            </button>
                            <div class="project-main-media">

                                <explorer-youtube-player v-if="videoOrigin === 'youtube' && !showSelectedMedia"
                                                         :video-url="video_url"/>
                                <explorer-vimeo-player v-else-if="videoOrigin === 'vimeo' && !showSelectedMedia"
                                                       :video-url="video_url"/>
                                <img style="width:100%;" v-else :src="selectedMedia.path" alt="Selected Image">


                            </div>
                            <button type="button" @click="toggleMedia('right')" class="media-selector">
                                <span style="width: 25px; height: 41px" class="picto-explorer-project-selector-right"/>
                            </button>
                        </div>


                        <div class="project-carousel-container">
                            <img v-on:click="setSelectedMedia(media)" class="project-carousel-item"
                                 v-for="media in project.medias" :src="media.path" alt="Carousel Selector Item"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['project'],
    data() {
        return {
            video_url: this.project.video_url,
            selectedMedia: this.project.medias[0],
            showSelectedMedia: false
        }
    },
    computed: {
        videoOrigin() {
            if (this.video_url !== null) {
                if (this.video_url.search('youtu') !== -1) {
                    return 'youtube';
                } else if (this.video_url.search('vimeo') !== -1) {
                    return 'vimeo';
                } else {
                    return 'error';
                }
            }
        }
    },
    mounted() {
        console.log(this.pro)
        if (this.videoOrigin === 'error') {
            this.showSelectedMedia = true;
        }
    },
    methods: {
        setSelectedMedia(media) {
            this.showSelectedMedia = true;
            this.selectedMedia = media;
        },
        emitCloseModal(event) {
            if (event.target.className === "modal-wrapper") {
                this.$emit('close-modal');
            }
        },
        toggleMedia(way) {
            if (this.showSelectedMedia) {
                let currentIndex = this.project.medias.findIndex(element => element === this.selectedMedia);
                let newIndex = currentIndex;
                if (way === "right") {
                    newIndex++;
                } else {
                    newIndex--;
                }

                if (newIndex >= this.project.medias.length || newIndex < 0) {
                    newIndex = 0;
                    this.showSelectedMedia = false;
                }

                this.selectedMedia = this.project.medias[newIndex];
            } else {
                this.showSelectedMedia = true;
            }

        }
    },
}


Vue.component('explorer-youtube-player', {
    props: ['videoUrl'],
    data() {
    },
    computed: {
        embedUrl() {
            let youtubeId;
            if (this.videoUrl.search('embed/') !== -1) {
                youtubeId = this.videoUrl.slice(this.videoUrl.search('embed/') + 6, this.videoUrl.length)
            } else if (this.videoUrl.search('v=') !== -1) {
                youtubeId = this.videoUrl.slice(this.videoUrl.search('v=') + 2, this.videoUrl.length)
            } else if (this.videoUrl.search('youtu.be/') !== -1) {
                youtubeId = this.videoUrl.slice(this.videoUrl.search('youtu.be/') + 9, this.videoUrl.length)
            } else {
                console.log('Youtube Video Link Error')
            }

            return 'https://www.youtube.com/embed/' + youtubeId
        }
    },
    methods: {},
    template: `
        <div style="width: 100%;height: 100%;">
        <iframe width="100%" height="500px" :src="embedUrl"
                title="YouTube video player" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen></iframe>
        </div>
    `
});

Vue.component('explorer-vimeo-player', {
    props: ['videoUrl'],
    data() {
    },
    computed: {
        embedUrl() {
            let vimeoId;

            if (this.videoUrl.search('vimeo.com/') !== -1) {
                vimeoId = this.videoUrl.slice(this.videoUrl.search('vimeo.com/') + 10, this.videoUrl.length)
            }

            return "https://player.vimeo.com/video/" + vimeoId + "?title=0&byline=0&portrait=0\""
        }
    },
    methods: {},
    template: `
        <div style="width: 100%;height: 100%;">
        <iframe
            :src="embedUrl"
            width="100%" height="500px" frameborder="0"
            allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>
        </div>
    `
});
</script>
