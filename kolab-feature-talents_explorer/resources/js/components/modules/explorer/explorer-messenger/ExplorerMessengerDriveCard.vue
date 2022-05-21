<template>
    <div class="explorer-messenger-card explorer-messenger-drive-card">
        <button type="button" @click="toggleCard" class="card-header">
            <div class="picto-container"><span class="picto-explorer-link"/></div>
            <h1 class="card-title">Liens</h1>
            <div class="card-dropdown-button">
                <span v-if="isCardOpen" class="picto-explorer-dropdown-up"/>
                <span v-else class="picto-explorer-dropdown-down"/>
            </div>
        </button>
        <div v-show="isCardOpen" class="card-body">
            <div v-if="isEmpty || drive === null" class="empty-card">
                <span class="empty-text">Pas de liens</span>
            </div>
            <div v-else style="position: relative">
                <button v-if="showButton" @click="handleButtonLeft" class="picto-explorer-selector-left drive-items-selector" style="left: 0;"/>
                <button v-if="showButton" @click="handleButtonRight" class="picto-explorer-selector-right drive-items-selector" style="right: 0;"/>

                <div class="drive-items-container">
                    <a v-for="driveLink in driveLinksToShow" :href="driveLink.link" class="drive-item">
                        <div class="drive-item-miniature"></div>
                        <div class="drive-item-infos">
                            <span class="drive-item-name">{{ driveLink.name }}</span>
                            <span class="drive-item-link">{{ driveLink.link }}</span>
                        </div>
                    </a>
                </div>

                <div>
                    <span/>
                    <span/>
                    <span/>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['user', 'drive'],
    data() {
        return {
            isCardOpen: true,
            currentDriveShow: 0
        }
    },

    mounted() {

    },
    computed: {
        isEmpty: function () {
            if (this.drive === null) {
                return true;
            }
            return this.drive.driveLinks.length <= 0;
        },
        driveLinksToShow() {
            let linksToShow = [];

            if (this.drive.driveLinks[this.currentDriveShow] !== undefined) {
                linksToShow.push(this.drive.driveLinks[this.currentDriveShow])
            }

            if (this.drive.driveLinks[this.currentDriveShow + 1] !== undefined) {
                linksToShow.push(this.drive.driveLinks[this.currentDriveShow + 1])
            }
            return linksToShow;
        },
        showButton() {
            return this.drive.driveLinks.length > 2;
        }
    },
    methods: {
        toggleCard() {
            this.isCardOpen = !this.isCardOpen;
        },
        handleButtonLeft() {
            let futureDriveShow = this.currentDriveShow - 1;
            if (futureDriveShow < 0) {
                futureDriveShow = this.drive.driveLinks.length - 2;
            }
            this.currentDriveShow = futureDriveShow;
        },
        handleButtonRight() {
            let futureDriveShow = this.currentDriveShow + 1;
            if (futureDriveShow > this.drive.driveLinks.length - 1) {
                futureDriveShow = this.drive.driveLinks.length = 0;
            }
            this.currentDriveShow = futureDriveShow;
        }
    },
}

</script>
