<template>
    <div class="explorer-messenger-card explorer-messenger-summary-card">
        <button type="button" @click="toggleCard" class="card-header">
            <div class="picto-container"><span class="picto-explorer-infos"/></div>
            <h1 class="card-title">Résumé de la mission</h1>
            <div class="card-dropdown-button">
                <span v-if="isCardOpen" class="picto-explorer-dropdown-up"/>
                <span v-else class="picto-explorer-dropdown-down"/>
            </div>
        </button>
        <div v-show="isCardOpen" class="card-body">
            <div class="summary-item">
                <div class="icon-container"><span class="picto-explorer-pile"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">{{ missionInfos.name }}</div>
                    <div class="summary-item-value">{{ missionInfos.type }}</div>
                </div>
            </div>
            <div class="summary-item">
                <div class="icon-container"><span class="picto-explorer-eur"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">Budget</div>
                    <div class="summary-item-value">{{ missionInfos.budget }}</div>
                </div>
            </div>
            <div class="summary-item">
                <div class="icon-container"><span class="picto-explorer-calendar"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">Deadline</div>
                    <div class="summary-item-value">{{ missionInfos.deadline }}</div>
                </div>
            </div>
            <div class="summary-item">
                <div class="icon-container"><span class="picto-explorer-notes"/></div>
                <div class="summary-item-infos">
                    <div class="summary-item-title">Description détaillée</div>
                    <div class="summary-item-value">{{
                            missionInfos.description
                        }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['mission'],
    data() {
        return {
            isCardOpen: true
        }
    },

    mounted() {

    },
    computed: {
        missionInfos() {
            if (this.mission === null) {
                return {
                    name: '',
                    type: '',
                    budget: '',
                    deadline: '',
                    description: ''
                };
            }
            return this.mission
        }
    },
    methods: {
        toggleCard() {
            this.isCardOpen = !this.isCardOpen;
        }
    },
}
</script>
