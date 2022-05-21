<template>
    <div class="page__explorer_talent" id="vue__explorer_talent">
        <div class="main-container row" style="background: none;">
            <div style="height: 100vh" class="explorer-profile-resume-container">
                <explorer-profile-resume :user="userComputed" :is-freelance="isFreelance"
                                         :is-self-profile="isSelfProfile"
                                         :user-to-see="userToSeeComputed" @profile-updated="updateUserInfos"/>
            </div>

            <div v-if="isSelfProfile && isFreelance" class="col-9">
                <explorer-freelance-profile-manage :user="userComputed" :isFreelance="isFreelance"
                                                   @profile-updated="updateUserInfos"/>
            </div>

            <div v-else-if="isSelfProfile && !isFreelance" class="col-9">
                <explorer-client-profile-manage :user="userComputed" :isFreelance="isFreelance"
                                                @profile-updated="updateUserInfos"/>
            </div>

            <div v-else class="col-9">
                <explorer-other-profile :user="userComputed" :user-to-see="userToSee"/>
            </div>
        </div>
        <button class="explorer-logout-button" @click="logout"><span
            style="width: 10px; height: 10px; margin-right: 10px"
            class="picto-explorer-logout"/>Se déconnecter
        </button>
    </div>
</template>
<script>
export default {
    props: ['user', 'userToSee'],
    computed: {
        isFreelance: function () {
            return this.user.role.name === 'talent';
        },
        isSelfProfile() {
            return this.user.id === this.userToSee.id
        },
        userComputed() {
            if (this.updatedUser === null) {
                return this.user;
            } else {
                return this.updatedUser;
            }
        },
        userToSeeComputed() {
            if (this.isSelfProfile) {
                return this.userComputed;
            } else {
                return this.userToSee;
            }
        }

    },
    methods: {
        logout() {
            window.location.href = '/logout';
        },
        updateUserInfos() {
            axios.get('/api/talent/' + this.user.id).then(
                res => {
                    this.updatedUser = res.data.datas;
                }
            )
        }
    },
    data() {
        return {
            currentPage: null,
            updatedUser: null
        }
    },
    created() {
        if (this.currentPage == null) {
            this.currentPage = "projects";
        }
    },
    mounted() {
        $('body').toggleClass('theme-explorer');
    },
}

Vue.component('explorer-profile-resume', {
    props: ['user', 'isFreelance', 'isSelfProfile', 'userToSee'],
    data() {
        return {
            showImgPlaceholder: false,
            showAddPortfolioModal: false,
            showProposeMissionModal: false
        }
    },
    methods: {
        getUserInitial(talent) {
            return talent.firstname.charAt(0) + talent.lastname.charAt(0);
        },
        onImgError() {
            this.showImgPlaceholder = !this.showImgPlaceholder;
        },
        addPortfolioModalOpen() {
            this.showAddPortfolioModal = true;
            $('body').toggleClass('body-modal');
        },
        addPortfolioModalClose() {
            this.showAddPortfolioModal = false;
            $('body').toggleClass('body-modal');
        },
        proposeMissionModalOpen() {
            this.showProposeMissionModal = true;
            $('body').toggleClass('body-modal');
        },
        proposeMissionModalClose() {
            this.showProposeMissionModal = false;
            $('body').toggleClass('body-modal');
        },
        emitProfileUpdated() {
            this.$emit('profile-updated')
        },
        changeProfilePicture() {
            // Get the selected file
            let files = $('#file')[0].files;

            if (files.length > 0) {

                let fd = new FormData();

                // Append data
                fd.append('file', files[0]);

                axios.post("/api/user/picture-change", fd, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                }).then(res => {
                        this.emitProfileUpdated();
                    }
                )
            }
        },
        selectFile() {
            let fileInputElement = this.$refs.file;
            fileInputElement.click();
        }
    },
    template: `
        <div class="main-profile-resume">
        <input hidden id="file" ref="file" name="file" type="file" @change="changeProfilePicture">
        <div class="profile-image">
            <span v-if="showImgPlaceholder" class="user-initials">{{ getUserInitial(userToSee) }}</span>
            <img v-else :src="'/upload/avatars/' + userToSee.avatar" style="max-width: 100%" alt="profile"
                 @error="onImgError">
        </div>
        <div v-if="isSelfProfile" class="edit-profile-image-container">
            <span style="height:12px; width: 12px;position: inherit;"
                  class="picto-explorer-pencil-pink"/>
            <a @click="selectFile" class="edit-profile-image">Modifier la
                photo de profil</a>
        </div>
        <div v-if="isSelfProfile" class="separator"/>

        <div class="text-center">
            <h1>{{ userToSee.name }}</h1>
            <span class="job">{{ userToSee.job.name }}</span>
            <br/>
            <a v-if="userToSee.website !== null" :href="userToSee.website">{{ userToSee.website }}</a>
            <p v-if="isFreelance">{{ userToSee.description }}</p>

            <button v-if="isSelfProfile && isFreelance" class="button" type="button" @click="addPortfolioModalOpen">
                Ajouter un projet personnel
            </button>
            <button v-if="!isSelfProfile && !isFreelance" type="button" @click="proposeMissionModalOpen" class="button">
                Proposer une mission
            </button>
        </div>

        <div v-if="isFreelance" class="separator"/>

        <div v-if="isFreelance" class="main-skills">
            <h1>Compétences</h1>
            <ul class="skills-list">
                <li v-for="skill in userToSee.skills" class="skill">{{ skill.name }}</li>
            </ul>
        </div>
        <explorer-profile-portfolio-add v-if="showAddPortfolioModal" @close-modal="addPortfolioModalClose"/>
        <explorer-mission-propose-modal v-if="showProposeMissionModal" :user-to-propose="userToSee" @close-modal="proposeMissionModalClose"/>
        </div>`,
});

Vue.component('explorer-freelance-profile-manage', {
    props: ['user', "isFreelance"],
    data() {
        return {
            currentPage: null,
        }
    },
    created() {
        if (this.currentPage == null) {
            this.currentPage = "projects";
        }
    },
    methods: {
        setCurrentPage(current) {
            this.currentPage = current;
        },
        emitProfileUpdated() {
            this.$emit('profile-updated')
        }
    },
    template: `
        <div class="main-explorer-profile-form">
        <ul class="explorer-profile-menu">
            <li v-on:click="setCurrentPage('projects')" v-bind:class="{active:this.currentPage==='projects'}">
                <button>Mes projets</button>
            </li>
            <li v-on:click="setCurrentPage('availability')"
                v-bind:class="{active:this.currentPage==='availability'}">
                <button>Mes disponibilités mission freelance</button>
            </li>
            <li v-on:click="setCurrentPage('infos-form')"
                v-bind:class="{active:this.currentPage==='infos-form'}">
                <button>Mes informations</button>
            </li>
            <li v-on:click="setCurrentPage('password-manage')"
                v-bind:class="{active:this.currentPage==='password-manage'}">
                <button>Mot de passe</button>
            </li>
        </ul>

        <div class="separator"/>

        <div v-if="this.currentPage==='projects'">
            <explorer-profile-projects-list :user="user" :isSelf="true"/>
        </div>
        <div v-else-if="this.currentPage==='availability'">

        </div>
        <div v-else-if="this.currentPage==='infos-form'">
            <explorer-profile-infos-form :isFreelance="isFreelance" :user="user" @form-updated="emitProfileUpdated"/>
        </div>
        <div v-else-if="this.currentPage==='password-manage'">
            <explorer-profile-password-manage/>
        </div>

        </div>`
})

Vue.component('explorer-client-profile-manage', {
    props: ['user', 'isSelf'],
    data() {
        return {
            currentPage: null,
            formUser: {...this.user}
        }
    },
    created() {
        if (this.currentPage == null) {
            this.currentPage = "infos-form";
        }
    },
    methods: {
        setCurrentPage(current) {
            this.currentPage = current;
        },
    },
    template: `
        <div class="main-explorer-profile-form">
        <ul class="explorer-profile-menu">
            <li v-on:click="setCurrentPage('infos-form')"
                v-bind:class="{'active-client-side':this.currentPage==='infos-form'}">
                <button>Mes informations personnelles</button>
            </li>
            <li v-on:click="setCurrentPage('password-manage')"
                v-bind:class="{'active-client-side':this.currentPage==='password-manage'}">
                <button>Mot de passe</button>
            </li>
        </ul>

        <div class="separator"/>

        <div v-if="this.currentPage==='infos-form'">
            <explorer-profile-infos-form :user="user" :is-freelance="false"/>
        </div>
        <div v-else-if="this.currentPage==='password-manage'">
            <explorer-profile-password-manage/>
        </div>
        </div>
    `
});

Vue.component('explorer-other-profile', {
    props: ['user', 'userToSee'],
    data() {
        return {
            nbProjects: 0
        }
    },
    methods: {
        onUpdateNbProjects(value) {
            this.nbProjects = value;
        }
    },
    template: `
        <div>
        <div class="external-project-list-title-container">
            <h1 v-if="nbProjects > 0">{{ nbProjects }} Projets</h1>
            <h1 v-else>{{ nbProjects }} Projet</h1>
        </div>
        <explorer-profile-projects-list :user="userToSee" :isSelf="false" v-on:update-nb-projects="onUpdateNbProjects"/>
        </div>
    `
})
</script>
