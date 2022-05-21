<template>
    <div class="modal-mask">
        <div class="modal-wrapper" id="quote-modal-wrapper" style="vertical-align: top" @click="handleCloseModal">
            <div class="modal-container" id="quote-modal-container">
                <div class="explorer-form-modal" style="height: fit-content">
                    <div class="modal-header text-center" style="padding-top:10px; padding-bottom: 30px;">
                        <h1 class="modal-title">Préparez votre devis</h1>
                    </div>

                    <form method="post" v-on:submit.prevent="postQuote" class="explorer-form">
                        <div class="form-section">
                            <div class="form-title-container">
                                <div class="picto-container"><span class="picto-explorer-pile-white"></span></div>
                                <h2>Décrivez avec précision votre prestation* (Livrables que recevra votre client)</h2>
                            </div>
                            <div class="form-section-content">
                                <input v-model="quote.currentQuoteItem" type="text"
                                       placeholder="Livrable" style="margin-bottom: 5px"/>
                                <div class="form-quote-items-container">
                                    <div v-for="quoteItem in quote.quoteItems" class="form-quote-item">
                                        <span>{{ quoteItem }}</span>
                                        <button type="button" @click="handleRemoveQuoteItem(quoteItem)"><span
                                            class="picto-explorer-quote-cross" style="height: 10px; width:10px"/>
                                        </button>
                                    </div>
                                </div>

                                <button class="form-add-quote-item-button" type="button" @click="handleAddQuoteItem">+
                                </button>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-title-container">
                                <div class="picto-container"><span class="picto-explorer-eur-white"></span></div>
                                <h2>Prix HT* (Kolab est entièrement gratuit et sans commission)</h2>
                            </div>
                            <div class="form-section-content">
                                <input v-model="quote.price" type="text"
                                       placeholder="Prix*"/>
                            </div>
                        </div>
                        <div class="form-section">
                            <div class="form-title-container">
                                <div class="picto-container"><span class="picto-explorer-mission-detail-white"></span>
                                </div>
                                <h2>Délai de livraison*</h2>
                            </div>
                            <div class="form-section-content">
                                <select v-model="quote.deadline" style="width: 100%">
                                    <option value="" disabled selected>Choisissez votre délai de livraison*</option>
                                    <option>Le lendemain</option>
                                    <option>Dans 2 jours</option>
                                    <option>Dans 3 jours</option>
                                    <option>Dans 4 jours</option>
                                </select>
                                <textarea v-model="quote.comment"
                                          placeholder="Commentaires"/>
                            </div>
                        </div>
                        <p v-if="formErrorMessage" style="color: red">{{this.formErrorMessage}}</p>
                        <button type="submit" class="form-validation-btn">Envoyer le devis</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import modal from "../../../layout/Modal";
import ExplorerConfirmQuitModal from "../ExplorerConfirmQuitModal";

export default {
    components: {ExplorerConfirmQuitModal},
    props: ['user', 'conversation', 'isFreelance'],
    data() {
        return {
            quote: {
                quoteItems: [],
                currentQuoteItem: '',
                price: '',
                deadline: '',
                comment: ''
            },
            showConfirmCloseModal: false,
            formErrorMessage: null
        }
    },

    mounted() {

    },
    computed: {},
    methods: {
        postQuote() {
            if(this.checkSendFormError()){
                axios.post('/api/explorer/missions/conversations/' + this.conversation.id + '/quotes', {
                    params: {
                        quote: this.quote
                    }
                }).then(res => {
                    this.emitCloseModal();
                }).catch(error => console.log(error));
            }
        },
        handleAddQuoteItem() {
            if (this.quote.currentQuoteItem.length > 0) {
                this.quote.quoteItems.push(this.quote.currentQuoteItem);
                this.quote.currentQuoteItem = '';
            }
        },
        handleRemoveQuoteItem(item) {
            const index = this.quote.quoteItems.indexOf(item);
            if (index > -1) {
                this.quote.quoteItems.splice(index, 1); // 2nd parameter means remove one item only
            }
        },
        emitCloseModal() {
            this.$emit('close-modal');
        },
        handleCloseModal() {
            if (event.target.className === "modal-container" || event.target.className === "modal-wrapper") {
                this.emitCloseModal();
            }
        },
        openConfirmCloseModal() {
            this.showConfirmCloseModal = true;
        },
        closeConfirmCloseModal() {
            this.showConfirmCloseModal = false;
        },
        checkSendFormError() {
            if (this.quote.quoteItems.length === 0) {
                this.formErrorMessage = "Veuillez ajouter au moins un livrable";
                return false;
            }
            this.formErrorMessage = null
            return true;
        }
    },
}
</script>
