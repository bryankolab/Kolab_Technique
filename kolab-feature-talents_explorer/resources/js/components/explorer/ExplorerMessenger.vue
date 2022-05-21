<template>
    <div class="page_explorer_messenger" id="vue__explorer_messenger">
        <div class="conversations-list-container">
            <explorer-messenger-conversations-list v-on:update-selected-conversation="handleUpdateSelectedConversation"
                                                   :is-freelance="isFreelance" :user="user"
                                                   :conversations-list="conversationsList"
                                                   :selected-conversation="selectedConversation"/>
        </div>

        <div class="conversation-container">
            <explorer-messenger-conversation :conversation="selectedConversation" :user="user"
                                             :is-freelance="isFreelance"/>
        </div>

        <div class="right-card-container">
            <div class="mission-progression-container">
                <explorer-messenger-summary-card :mission="selectedMission"/>

            </div>
            <div class="mission-summary-container">
                <explorer-messenger-progression-card :mission-proposition="selectedProposition"
                                                     :mission-status="missionStatus" :is-freelance="isFreelance"/>

            </div>
            <div class="mission-drive-container">
                <explorer-messenger-drive-card :drive="selectedDrive"/>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['user'],
    data() {
        return {
            selectedConversation: null,
            conversationsList: [],
        }
    },
    async created() {

        await this.getConversations();

        setInterval(() => {
            this.getConversations();
        }, 2000);
    },
    mounted() {
        $('body').toggleClass('theme-explorer');
    },
    computed: {
        isFreelance: function () {
            return this.user.role.name === "talent";
        },
        selectedMission: function () {
            if (this.selectedConversation !== null) {
                return this.selectedConversation.proposition.mission;
            } else {
                return null;
            }
        },
        missionStatus: function () {
            if (this.selectedConversation !== null) {
                return this.selectedConversation.proposition.status;
            } else {
                return null;
            }
        },
        selectedProposition: function () {
            if (this.selectedConversation !== null) {
                return this.selectedConversation.proposition;
            } else {
                return null;
            }
        },
        selectedDrive: function () {
            if (this.selectedConversation !== null) {
                return this.selectedConversation.proposition.drive;
            } else {
                return null;
            }
        }
    },
    methods: {
        handleUpdateSelectedConversation(value) {
            this.selectedConversation = value;
        },
        async getConversations() {
            await axios.get("/api/explorer/missions/conversations", {
                params: {}
            }).then(res => {
                this.conversationsList = res.data;
                this.updateCurrentProposition();
            }).catch(error => console.log(error));
        },
        updateCurrentProposition() {
            if (this.selectedConversation === null) {
                this.selectedConversation = this.conversationsList[0];
            } else {
                let updatedSelectedConversation = this.conversationsList.find(element => {
                    return element.id === this.selectedConversation.id
                });

                if (updatedSelectedConversation.proposition.status !== this.selectedConversation.proposition.status) {
                    this.selectedConversation = updatedSelectedConversation;
                }
            }
        }
    },

}
</script>
