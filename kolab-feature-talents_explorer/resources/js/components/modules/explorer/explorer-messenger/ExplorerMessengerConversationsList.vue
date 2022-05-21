<template>
    <div class="explorer-messenger-conversation-list">
        <div class="conversations-list-header">
            <a class="go-back-button" href="/explorer">
                <span class="picto-explorer-selector-left-colored"/>
            </a>
            <h1 class="conversation-list-title">Conversations</h1>
        </div>

        <div style="overflow-y: auto; height: 100%">
            <explorer-messenger-conversation-item
                v-for="conversation in conversationsList"
                :conversation='conversation'
                v-on:update-selected-conversation="emitSelectedConversation(conversation)"
                :is-freelance="isFreelance"
                :key="conversation.id"
                :is-active="conversation.id===selectedConversation.id"></explorer-messenger-conversation-item>
        </div>
    </div>
</template>
<script>
export default {
    props: ['user', 'conversationsList', 'selectedConversation', 'isFreelance'],
    methods: {
        emitSelectedConversation(conversation) {
            this.$emit('update-selected-conversation', conversation);
        }
    }
}

Vue.component('explorer-messenger-conversation-item', {
    template: `
        <button @click="handleSelectConversation" type="button" class="explorer-messenger-conversation-list-item"
                :class="{'explorer-messenger-conversation-list-item-active' : isActive}">
        <div v-if="showInitial" class="talent-initials-container">
            <span class="talent-initials">{{ getTalentInitials(conversation.proposition.freelance) }}</span>
        </div>
        <img v-else @error="handleImgError" :src="'/upload/avatars/' + conversation.proposition.freelance.avatar"
             class="profile-image-container"
             alt="profile">
        <div class="mission-infos-container">
            <span v-if="isFreelance" class="mission-contact">{{ conversation.proposition.client.name }}</span>
            <a v-else class="mission-contact" :href="'/explorer/profile/' + conversation.proposition.freelance.id">{{ conversation.proposition.freelance.name }}</a>
            <span class="mission-name">{{ conversation.proposition.mission.name }}</span>
        </div>
        <span class="mission-last-activity">Il y a {{ lastActivity }}</span>
        <span v-if="isNew" class="mission-notification-dot"></span>
        </button>`,
    methods: {
        handleSelectConversation() {
            this.emitSelectedConversation();
            this.patchConversation();
        },
        emitSelectedConversation() {
            this.$emit('update-selected-conversation');
        },
        patchConversation() {
            axios.patch("/api/explorer/missions/conversations/" + this.conversation.id, {
                params: {
                    patch_type: 'update_last_check'
                }
            }).then(res => {
            }).catch(error => console.log(error))
        },
        getTalentInitials(talent) {
            return talent.firstname.charAt(0) + talent.lastname.charAt(0);
        },
        handleImgError() {
            this.showInitial = true;
        }
    },
    data() {
        return {
            showInitial: false
        }
    },
    props: ['isActive', 'conversation', 'isFreelance'],
    mounted() {
        if (this.isActive) {
            this.patchConversation();
        }
    },
    computed: {
        lastActivity() {
            const today = new Date();
            const endDate = new Date(this.conversation.lastMessage.created_at);
            const days = parseInt(Math.abs(endDate - today) / (1000 * 60 * 60 * 24));
            const hours = parseInt(Math.abs(endDate - today) / (1000 * 60 * 60) % 24);
            const minutes = parseInt(Math.abs(endDate.getTime() - today.getTime()) / (1000 * 60) % 60);
            const seconds = parseInt(Math.abs(endDate.getTime() - today.getTime()) / (1000) % 60);

            if (days > 0) {
                if (days > 1) {
                    return days + " jours";
                }
                return days + " jour";
            }

            if (hours > 0) {
                if (hours > 1) {
                    return hours + " heures";
                }
                return hours + " heure";
            }

            if (minutes > 0) {
                if (minutes > 1) {
                    return minutes + " minutes";
                }
                return minutes + " minute";
            }

            if (seconds > 0) {
                if (seconds > 1) {
                    return seconds + " secondes";
                }
                return seconds + " seconde";
            }
        },
        isNew() {
            if (this.isFreelance && this.conversation.last_freelancer_check) {
                if (this.isFreelance) {
                    if (this.conversation.proposition.freelance.name !== this.conversation.lastMessage.createdByUsername) {
                        return Date.parse(this.conversation.last_freelancer_check) < Date.parse(this.conversation.lastMessage.created_at);
                    }
                }
            } else {
                if (!this.isFreelance) {
                    if (this.conversation.proposition.client.name !== this.conversation.lastMessage.createdByUsername) {
                        if (this.conversation.last_client_check) {
                            return Date.parse(this.conversation.last_client_check) < Date.parse(this.conversation.lastMessage.created_at);
                        }
                    }
                }
            }

            return false;
        }
    }
});
</script>
