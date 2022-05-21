<template>
    <div class="explorer-messenger-conversation">
        <div v-if="!isFetching" class="conversation-message-container" id="message-container">
            <conversation-day-container v-for="(messages, date) in messagesByDay" v-bind:key="date" :date="date"
                                        :messages="messages"
                                        :mission="conversation.proposition.mission"
                                        :conversation='conversation'
                                        :mission-proposition="conversation.proposition" :user="user"
                                        :is-freelance="isFreelance"/>
        </div>
        <div class="conversation-footer">
            <div class="conversation-input-container">
                <textarea v-model="messageToPost" @keyup.enter="postMessage"
                          :style="textAreaStyle" @keyup="resizeTextarea" ref="messageInput"
                          class="conversation-input-text"/>
                <button @click="postMessage" class="conversation-input-button">
                    <span class="picto-explorer-send-message" style="height: 13px"/>
                </button>
            </div>
        </div>
    </div>
</template>
<script>
import Explorer from "../../../explorer/Explorer";
import ExplorerMessengerCancelMissionPopup from "./ExplorerMessengerCancelMissionPopup";

export default {
    components: {ExplorerMessengerCancelMissionPopup, Explorer},
    props: ['user', 'conversation', 'isFreelance'],
    data() {
        return {
            messageToPost: '',
            messagesByDay: null,
            isFetching: false,
            isShowQuoteProposeModalActive: false,
            textAreaHeight: '30px',
            handleDebouncedScroll: null,
            isUserScrolling: false
        }
    },
    created() {
        //this.getConversationMessage();
    },
    mounted() {
        this.getConversationMessage();

        window.setInterval(() => {
            this.getConversationMessage();
        }, 5000);


        this.handleDebouncedScroll = debounce(this.handleScroll, 100);
        window.addEventListener('scroll', this.handleDebouncedScroll);


    },
    computed: {
        textAreaStyle() {
            return {
                'height': this.textAreaHeight
            }
        }
    },
    watch: {
        conversation: function () {
            this.messagesByDay = null;
            this.getConversationMessage();
        }
    },
    beforeDestroy() {
        // I switched the example from `destroyed` to `beforeDestroy`
        // to exercise your mind a bit. This lifecycle method works too.
        window.removeEventListener('scroll', this.handleDebouncedScroll);
    },
    methods: {
        handleScroll(event) {
            this.isUserScrolling = (window.scrollY > 0);
        },
        scrollDown(bypass = false) {
            if (!this.isUserScrolling || bypass) {
                let container = this.$el.querySelector("#message-container");
                container.scrollTop = container.scrollHeight;
            }
        },
        postMessage() {
            axios.post("/api/explorer/missions/conversations/" + this.conversation.id + "/messages", {
                params: {
                    message: this.messageToPost
                }
            }).then(res => {
                this.messageToPost = '';
                this.getConversationMessage();
                setTimeout(() => {
                    this.scrollDown(true);
                }, 300);
                this.textAreaHeight = '30px'
            }).catch(error => console.log(error))
        },
        getConversationMessage() {
            if (this.conversation !== null) {
                axios.get("/api/explorer/missions/conversations/" + this.conversation.id + "/messages", {}).then(res => {
                    this.messagesByDay = res.data;
                    setTimeout(() => {
                        this.scrollDown();
                    }, 300);
                    this.isFetching = false
                }).catch(error => console.log(error))
            }
        },
        showQuoteProposeModal() {
            this.showQuoteProposeModal = true;
        },
        closeQuoteProposeModal() {
            this.showQuoteProposeModal = false;
        },
        resizeTextarea() {
            this.textAreaHeight = `${this.$refs.messageInput.scrollHeight}px`
        },
        updateMessageByDay(res) {
            //if (this.messagesByDay !== res.data) {
            this.messagesByDay = res.data;
            setTimeout(() => {
                this.scrollDown();
            }, 300);
            //}

        }
    },
}

Vue.component('conversation-message', {
    template: `
        <div class="conversation-message" :class="{'conversation-message-disabled' : isDisabled}">
        <a :href="'/explorer/profile/' + message.created_by">
            <div v-if="showInitial"
                 v-bind:class="[isSelf ? 'talent-initials-container-self' : 'talent-initials-container' ]">
                <span class="talent-initials">{{ getTalentInitials(message.createdByUsername) }}</span>
            </div>
            <img v-else @error="handleImgError" :src="'/upload/avatars/' + message.createdByAvatar"
                 v-bind:class="[isSelf ? 'message-profile-picture-self' : 'message-profile-picture' ]"
                 alt="profile"/>
        </a>
        <div class="message-content-container">
            <div class="message-header" v-bind:class="{'self-message' : isSelf }">
                <a :href="'/explorer/profile/' + message.created_by"><span
                    class="message-username">{{ message.createdByUsername }}</span></a>
                <span class="message-hour">{{ messageTime }}</span>
            </div>
            <div class="message-body" v-bind:class="{'self-message' : isSelf }">
                <p class="message-paragraph">{{ message.message }}</p>
                <bubble-mission-summary v-if="messageType==='user_mission_proposal'" :mission="mission"
                                        :mission-proposition="missionProposition" :is-freelance="isFreelance"/>
                <bubble-mission-quote v-if="messageType==='user_quote'" :quote="message.quote"
                                      :mission-proposition="missionProposition" :is-freelance="isFreelance"/>
                <bubble-drive-link v-if="messageType==='user_drive_link'" :drive-link="message.driveLink"/>
            </div>
        </div>
        </div>`,
    props: ['messageType', 'message', 'mission', 'user', 'missionProposition', 'isFreelance', 'isDisabled'],
    data() {
        return {
            showInitial: false
        }
    },
    computed: {
        showButton() {
            return true;
        },
        messageTime() {
            let messageTime = new Date(this.message.created_at);
            let hours = '' + messageTime.getHours();
            if (hours.length === 1) {
                hours = '0' + hours;
            }
            let minutes = '' + messageTime.getMinutes()
            if (minutes.length === 1) {
                minutes = '0' + minutes;
            }
            return hours + ":" + minutes;
        },
        isSelf() {
            return this.message.createdByUsername === this.user.name;
        },
        /* isDisabled() {
             switch (this.message.type) {
                 case 'user_quote':
                     return this.message.missionPropositionStatus !== 'waiting_payment';
                 case 'user_mission_proposal':
                     return this.message.missionPropositionStatus !== 'waiting_quote';
                 default:
                     return false;

             }
         }*/
    },
    mounted() {
    },
    methods: {
        buttonHandler() {
            console.log("click");
        },
        getTalentInitials(talent) {
            let names = talent.split(' ')

            let res = ''

            names.forEach(element => res += element.charAt(0));

            return res;
        },
        handleImgError() {
            this.showInitial = true;
        }
    }
});

Vue.component('conversation-system-message', {
    template: `
        <div v-if="showMessage" class="conversation-message conversation-system-message"
             :class="{'conversation-system-message-disabled': isDisabled}">
        <img src="/images/explorer/kolab-messenger.png" class="message-profile-picture" style="max-width: 100%"
             alt="profile"/>
        <div class="message-content-container">
            <div class="message-header">
                <span class="message-username">{{ systemName }}</span>
                <span class="message-hour">{{ messageTime }}</span>
            </div>
            <div class="message-body">
                <p class="message-paragraph">{{ messageText }}</p>
                <button v-if="showButton" class="message-action-button" @click="buttonHandler" type="button">
                    {{ buttonText }}
                </button>
            </div>
        </div>
        <explorer-messenger-quote-propose-modal v-if="showQuoteProposeModal" :conversation="conversation"
                                                @close-modal="closeQuoteProposeModal"/>
        </div>`,
    props: ['message', 'missionProposition', 'user', 'conversation'],
    data() {
        return {
            systemName: "Kolab",
            showQuoteProposeModal: false
        }
    },
    computed: {
        showButton() {
            switch (this.message.type) {
                case "system_mission_proposition_waiting_delivery":
                    return true;
                case "system_quote_creation":
                    return this.missionProposition.status === "waiting_quote";
                case "system_mission_finished":
                    return this.missionProposition.status === "waiting_closing";
                default:
                    return false;
            }
        },
        isDisabled() {
            switch (this.message.type) {
                case "system_mission_closed" :
                    return false;
                case "system_mission_finished" :
                    return this.message.missionPropositionStatus !== "waiting_closing";
                case "system_quote_creation" :
                    return this.message.missionPropositionStatus !== "waiting_quote";
                case "system_mission_quote_paid":
                    return this.message.missionPropositionStatus !== "waiting_work";
                case "system_mission_canceled":
                    return false;
                case "system_mission_proposition_waiting_delivery":
                    return this.message.missionPropositionStatus !== "waiting_work";
                case "system_mission_taken_by_other":
                    return false;
            }
        },
        buttonText() {
            switch (this.message.type) {
                case "system_mission_proposition_waiting_delivery":
                    return 'Envoyer les livrables';
                case "system_quote_creation":
                    return "Envoyer votre devis";
                case  "system_mission_finished":
                    return "Clôturer la mission"
            }
        },
        messageTime() {
            let messageTime = new Date(this.message.created_at);
            let hours = '' + messageTime.getHours();
            if (hours.length === 1) {
                hours = '0' + hours;
            }
            let minutes = '' + messageTime.getMinutes()
            if (minutes.length === 1) {
                minutes = '0' + minutes;
            }
            return hours + ":" + minutes;
        },
        messageText() {
            switch (this.message.type) {
                case "system_mission_finished" :
                    return "Hello " + this.missionProposition.client.firstname + ", " + this.missionProposition.freelance.firstname + " attend que vous clôturiez la mission afin que nous puissions déclencher le paiement de la mission !  Faites-le au plus vite ou écrivez nous à l’adresse suivante pour signaler un problème : support@kolab.app"
                case "system_mission_closed" :
                    if (this.user.id === this.missionProposition.client.id) {
                        return "👍 Mission terminée, nous procédons au paiement de votre freelance. Merci de nous faire confiance, à bientôt " + this.missionProposition.client.firstname + " !";
                    } else {
                        return "👍 Mission terminée, nous procédons au paiement de votre mission. Vous recevrez les fonds sur votre compte bancaire d’ici 2 à 3 jours. Merci de nous faire confiance, à bientôt " + this.missionProposition.freelance.firstname + " !"
                    }
                case "system_quote_creation":
                    return "Bonjour " + this.missionProposition.freelance.firstname + ", vous venez de recevoir une nouvelle mission freelance. Si elle vous intéresse, réalisez-la sur Kolab pour éviter les mauvaises surprises et sécuriser son paiement en à peine 3 étapes. Et c’est gratuit, évidemment (aucune commission sur la mission)."
                case "system_mission_quote_paid":
                    if (this.user.id === this.missionProposition.client.id) {
                        return "Bravo 👏, le devis est payé. Vos fonds sont conservés en sécurité et ils seront transférés à " + this.missionProposition.freelance.firstname + " uniquement si vous clôturez la mission.";
                    } else {
                        return "Bravo 👏, le devis a été réglé par " + this.missionProposition.client.firstname + " ! Vous pouvez commencer votre mission et envoyer vos livrables via la messagerie (vos fonds sont sécurisés en attendant la fin de la mission).";
                    }
                case "system_mission_taken_by_other":
                    if (this.user.id === this.missionProposition.client.id) {
                        return "Cette mission a déjà été pourvue par un autre freelance de la communauté.";
                    } else {
                        return this.missionProposition.freelance.firstname + ", cette mission vient tout juste d’être acceptée par un autre freelance de la communauté.";
                    }
                case "system_mission_canceled":
                    if (this.user.id === this.missionProposition.client.id) {
                        return "Vous avez annulé cette mission";
                    } else {
                        return this.missionProposition.freelance.firstname + ", cette mission n’est plus disponible et a été annulée par le client."
                    }
            }
        },
        showMessage() {
            switch (this.message.type) {
                case "system_mission_closed" :
                    return true;
                case "system_mission_finished" :
                    return this.user.id === this.missionProposition.client.id;
                case "system_quote_creation" :
                    return this.user.id === this.missionProposition.freelance.id;
                case "system_mission_quote_paid":
                    return true;
                case "system_mission_canceled":
                    return true;
                case "system_mission_taken_by_other":
                    return true;
            }
        }
    },
    mounted() {

    },
    methods: {
        buttonHandler() {
            switch (this.message.type) {
                case "system_mission_proposition_waiting_delivery":
                    return 'Envoyer les livrables';
                case "system_quote_creation":
                    this.openQuoteProposeModal()
                    break;
                case "system_mission_finished":
                    this.closeMission()
                    break;

            }
        },
        openQuoteProposeModal() {
            this.showQuoteProposeModal = true;
        },
        closeQuoteProposeModal() {
            this.showQuoteProposeModal = false;
        },
        closeMission() {
            axios.patch("/api/explorer/missions/propositions/" + this.missionProposition.id, {
                params: {
                    patch_type: 'mission_proposition_close'
                }
            }).then(res => {

            }).catch(error => console.log(error))
        }
    }
});

Vue.component('conversation-day-container', {
    template: `
        <div class="conversation-day-container">
        <span class="conversation-day-container-date">{{ formatedDate }}</span>
        <div v-for="message in messages">
            <conversation-message v-if="message.type.includes('user')" :is-freelance="isFreelance"
                                  :message-type="message.type" :message="message"
                                  :is-disabled="isMessageDisabled(message)"
                                  :mission="mission" :user="user" :mission-proposition="missionProposition"/>
            <conversation-system-message v-else-if="message.type.includes('system')" :user="user" :message="message"
                                         :conversation='conversation'
                                         :mission-proposition="missionProposition"/>
        </div>
        </div>
    `,
    props: ['date', "messages", "mission", "user", "missionProposition", "isFreelance", 'conversation'],
    computed: {
        formatedDate() {
            let dateToFormat = new Date(this.date);
            let options = {dateStyle: 'full'};
            let dateString = new Intl.DateTimeFormat('fr-FR', options).format(dateToFormat);
            return dateString.charAt(0).toUpperCase() + dateString.slice(1);
        }
    },
    methods: {
        isMessageDisabled(message) {
            return this.conversation.messageToDisableDate > message.created_at;
        }
    }


});

Vue.component('bubble-mission-summary', {
    props: ['mission', "missionProposition", 'isFreelance'],
    template: `
        <div class="message-bubble message-bubble-summary">
        <div class="message-bubble-header">
            <div class="mission-summary-icon-container">
                <span class="picto-explorer-mission-summary-colored"></span>
            </div>
            <div class="mission-summary-titles">
                <span class="bubble-mission-name">{{ mission.name }}</span>
                <span class="bubble-mission-type">{{ mission.type }}</span>
            </div>
        </div>
        <div class="message-bubble-body">
            <div class="summary-item">
                <div class="icon-container"><span class="picto-explorer-eur"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">Budget</div>
                    <div class="summary-item-value">{{ mission.budget }} €</div>
                </div>
            </div>
            <div class="summary-item">
                <div class="icon-container"><span class="picto-explorer-calendar"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">Deadline</div>
                    <div class="summary-item-value">{{ mission.deadline }}</div>
                </div>
            </div>
            <div class="summary-item">
                <div class="icon-container"><span class="picto-explorer-notes"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">Description détaillée</div>
                    <div class="summary-item-value">{{ mission.description }}
                    </div>
                </div>
            </div>
        </div>
        <button v-if="showCancelButton" :disabled="disableButton" class="message-bubble-button"
                :class="{'message-bubble-button-disabled' : disableButton}" @click="buttonHandler"
                type="button">
            Annuler cette mission
        </button>
        <explorer-messenger-cancel-mission-popup v-if="showCancelMissionModal" @modal-close="closeCancelMissionModal"
                                                 :mission-proposition="missionProposition"/>
        </div>`,
    data() {
        return {
            showCancelMissionModal: false
        }
    },
    computed: {
        showCancelButton() {
            return !this.isFreelance;
        },
        disableButton() {
            return !(this.missionProposition.status === "waiting_quote");
        }
    },
    mounted() {
    },
    methods: {
        buttonHandler() {
            this.openCancelMissionModal();
        },
        cancelMission() {
            axios.patch("/api/explorer/missions/propositions/" + this.missionProposition.id, {
                params: {
                    patch_type: 'mission_proposition_cancel'
                }
            }).then(res => {

            }).catch(error => console.log(error))
        },
        closeCancelMissionModal() {
            this.showCancelMissionModal = false
        },
        openCancelMissionModal() {
            this.showCancelMissionModal = true
        }
    },

})

Vue.component('bubble-mission-quote', {
    template: `
        <div class="message-bubble message-bubble-quote">
        <div class="message-bubble-header">
            <span class="bubble-quote-title">Devis</span>
        </div>
        <div class="message-bubble-body">
            <div class="summary-item">
                <div class="icon-container"><span class="picto-explorer-pile"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">Inclus dans ce que vous payez</div>
                    <ul>
                        <li v-for="quoteItem in JSON.parse(quote.quote_line)" class="summary-item-value">
                            {{ quoteItem }}
                        </li>
                    </ul>
                </div>
            </div>
            <div class="summary-item">
                <div class="icon-container"><span class="picto-explorer-calendar"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">Livraison de votre commande</div>
                    <div class="summary-item-value">{{ quote.deadline }}</div>
                </div>
            </div>
            <div class="summary-item">
                <div class="icon-container"><span class="picto-explorer-eur"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">Prix</div>
                    <div class="summary-item-value">{{ quote.price }} € HT</div>
                </div>
            </div>
            <div v-if="quote.service_fee !== ''" class="summary-item">
                <div class="icon-container"><span class="picto-explorer-shield-colored"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">Frais de service</div>
                    <div class="summary-item-value">{{ quote.service_fee }} € HT</div>
                </div>
            </div>
            <div class="summary-item">
                <div class="icon-container"><span class="picto-explorer-shield-colored"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">Commentaires</div>
                    <div class="summary-item-value">{{ quote.comments }}</div>
                </div>
            </div>
        </div>
        <div class="message-bubble-footer" style="padding-right: 10px ">
            <div>
                <span class="quote-total">Total : </span>
                <span class="quote-price">{{ quote.price + quote.service_fee }} € HT</span>
            </div>
            <span v-if="quote.status === 'PAID'" class="quote-status-paid">Payé ✓</span>
            <button v-else-if="!isFreelance && quote.status === 'WAITING_PAYMENT'" @click="payQuote"
                    class="quote-button">Paiement sécurisé
            </button>
            <span v-else-if="isFreelance && quote.status === 'WAITING_PAYMENT'" class="quote-status-waiting-payment">En attente de règlement</span>

        </div>
        </div>`,
    methods: {
        payQuote() {
            window.location.href = "/explorer/messenger/quotes/" + this.quote.id + "/checkout";
        },
    },
    mounted() {
        //console.log(this.quote);
    },
    props: ['quote', "isFreelance"],
    data() {
        return {}
    }
})

Vue.component('bubble-drive-link', {
    template: `
        <a class="message-bubble-drive-link" :href="driveLink.link">
        <div class="link-miniature">

        </div>
        <div class="link-description">
            <span class="link-name">{{ driveLink.name }}</span>
            <span class="link-url">{{ driveLink.link }}</span>
        </div>
        </a>`,
    methods: {},
    mounted() {
    },
    props: ['driveLink'],
    data() {
        return {}
    }
})
</script>
