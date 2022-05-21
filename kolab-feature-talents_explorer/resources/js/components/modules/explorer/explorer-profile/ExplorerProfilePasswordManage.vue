<template>
    <div class="explorer-profile-password-manage row">
        <div class="password-manage-form col-12">
            <form class="explorer-form" method="post" style="padding-left: 30px;" v-on:submit.prevent="postPasswordChange">
                <div class="explorer-form-main-label">
                    <div class="icon-container"><span class="picto-explorer-lock"/></div>
                    <h2>Mot de passe*</h2>
                </div>

                <div class="input-container">
                    <label>Mot de passe actuel</label>
                    <input v-model="currentPassword" class="password-input" placeholder="Entrez votre mot de passe actuel" type="password">
                </div>
                <div class="input-container">
                    <label>Nouveau mot de passe - 8 caractères minimum avec au moins une Majuscule et 1 symbole</label>
                    <input v-model="newPassword" class="password-input" placeholder="Nouveau mot de passe" type="password">
                </div>
                <div class="input-container">
                    <label>Confirmez le nouveau mot de passe</label>
                    <input v-model="newPasswordConfirm" class="password-input" placeholder="Confirmez votre nouveau mot de passe" type="password">
                    <p style="color:red;">{{errorMessage}}</p>
                </div>
                <div class="input-container">
                    <button v-if="!isSended" class="submit-button" type="submit">Modifier le mot de passe</button>
                    <button disabled class="submit-button button-sended" v-else-if="isSended">Mot de passé édité !</button>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
export default {
    props: [],

    data() {
        return {
            currentPassword : '',
            newPassword : '',
            newPasswordConfirm : '',
            isSended : false,
            errorMessage : null
        }
    },
    created() {

    },
    mounted() {
    },
    methods: {
        postPasswordChange() {
            axios.patch('/api/user/password-change', {
                old_password : this.currentPassword,
                new_password : this.newPassword,
                confirm_password : this.newPasswordConfirm,
            }).then(res => {
                if (res.success === true) {
                    this.isSended = true;
                    this.errorMessage = null;
                }
            }).catch(error => {
                this.errorMessage = error.response.data.message
            });
        },
    },
}
</script>
