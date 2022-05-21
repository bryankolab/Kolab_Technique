<template>
    <div class="modal-mask">
        <div class="modal-wrapper" v-on:click="emitCloseModal">
            <div class="explorer-portfolio-add-modal">
                <div class="explorer-form-modal">
                    <div class="modal-header text-center">
                        <h1 class="modal-title">Ajouter un projet</h1>
                        <p>Vous pouvez proposer un nouveau projet ou bien reproposer vos anciens projets.</p>
                    </div>

                    <form method="post" v-on:submit.prevent="postPortfolio" class="explorer-form portfolio-form">
                        <div class="form-section">
                            <div class="form-title-container">
                                <div class="picto-container"><span class="picto-explorer-pile-white"></span></div>
                                <h2>Titre de votre projet*</h2>
                            </div>
                            <div class="form-section-content">
                                <input v-model="portfolio.name" type="text" required
                                       placeholder="Titre de votre projet (Ex : Spot TV Saint Laurent)"/>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-title-container">
                                <div class="picto-container"><span class="picto-explorer-mission-detail-white"></span>
                                </div>
                                <h2>Ajouter du contenu*</h2><span style="margin-left: 10px;">Ajouter au moins une vidéo ou/et une image</span>
                            </div>
                            <div class="form-section-content">
                                <div class="form-media-type-selectors">
                                    <button @click="toggleVideoLink" type="button" class="form-media-type-selector">
                                        <span class="picto-explorer-video"
                                              style="height: 28px; margin-bottom: 10px"></span>

                                        <span>
                                            Ajouter<br>
                                            une vidéo
                                        </span>
                                    </button>
                                    <button @click="$refs.file.click()" type="button" class="form-media-type-selector">
                                        <span class="picto-explorer-img"
                                              style="height: 25px; margin-bottom: 10px"></span>
                                        <span>
                                        Ajouter une image<br>
                                        {{ 5 - medias.length }} images restante
                                        <input type="file" ref="file" @change="handleFileUploads( $event )"
                                               style="display: none">

                                        </span>
                                    </button>
                                </div>
                                <input v-if="addVideo" v-model="portfolio.video_url" type="text"
                                       placeholder="Lien de votre video"/>
                                <div v-if="medias.length > 0" class="form-sended-medias-container">
                                    <div v-for="(media, index) in medias" class="form-sended-media"><p>{{
                                            media.name
                                        }}<span> ({{ niceBytes(media.size) }})</span>
                                    </p>
                                        <button v-on:click="handleRemoveFile(index)" type="button">X</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-section">
                            <div class="form-title-container">
                                <div class="picto-container"><span class="picto-explorer-pile-white"></span></div>
                                <h2>Description du projet</h2>
                            </div>
                            <div class="form-section-content">
                                <textarea v-model="portfolio.description"
                                          placeholder="Décrivez en quelques lignes votre projet ..."/>
                            </div>
                        </div>

                        <p v-if="formErrorMessage !== null" style="color: red">{{ formErrorMessage }}</p>

                        <button class="form-validation-btn" type="submit">Envoyer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: [],

    data() {
        return {
            portfolio: {
                name: '',
                video_url: '',
                description: ''
            },
            medias: [],
            addVideo: false,
            formErrorMessage: null
        }
    },
    created() {

    },
    mounted() {
    },
    methods: {
        emitCloseModal() {
            if (event.target.className === "modal-wrapper") {
                this.$emit('close-modal');
            }
        },
        postPortfolio() {
            if (this.checkSendFormError()) {
                let fd = new FormData();

                fd.append('portfolio_name', this.portfolio.name);
                fd.append('portfolio_video_url', this.portfolio.video_url);
                fd.append('portfolio_description', this.portfolio.description);
                for (const i of Object.keys(this.medias)) {
                    fd.append('portfolio_medias[]', this.medias[i]);
                }

                axios.post('/api/explorer/portfolios', fd, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    }
                }).then(res => {
                    if (res.success === false) {
                        // Error
                    } else {
                        this.isSended = true;
                        this.$emit('close-modal');
                    }
                });
            }

        },
        handleFileUploads() {
            this.medias.push(event.target.files[0]);
            console.log(this.medias)
        },
        handleRemoveFile(index) {
            if (index > -1) {
                this.medias.splice(index, 1);
            }
        },
        toggleVideoLink() {
            this.addVideo = !this.addVideo;
        },
        niceBytes(x) {
            const units = ['octets', 'Ko', 'Mo', 'Go', 'To'];
            let l = 0, n = parseInt(x, 10) || 0;
            while (n >= 1024 && ++l) {
                n = n / 1024;
            }
            return (n.toFixed(n < 10 && l > 0 ? 1 : 0) + ' ' + units[l]);
        },
        checkSendFormError() {
            if (this.medias.length === 0 && this.portfolio.video_url === '') {
                this.formErrorMessage = "Veuillez ajouter au moins une vidéo ou une image";
                return false;
            }
            this.formErrorMessage = null
            return true;
        }
    },
}
</script>
