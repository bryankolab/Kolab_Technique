<template>
    <div class="page_explorer_membership" id="vue__explorer_membership_client">
        <div class="main-container" style="background: none;">
            <div class="bg-image-left"/>
            <div class="form-container">
                <h1>Pour quel projet recherchez vous un freelance ?</h1>
                <p> Décrivez-nous votre projet en quelques mots et nous vous recontacterons dès que possible.</p>

                <form method="POST" v-on:submit.prevent="postRegistration" class="membership-sub-form">
                    <label for="text-project-desc">Description du projet*</label>
                    <textarea v-model="projectDescription" id="text-project-desc"
                              placeholder="Décrivez votre projet ici..." required></textarea>

                    <label for="select-budget">Votre budget*</label>
                    <select v-model="budget" id="select-budget">
                        <option> < de 100€</option>
                        <option>Entre 100 et 500€</option>
                        <option>Entre 500 et 1000€</option>
                        <option> > de 1000€</option>
                    </select>

                    <button class="submit-button" v-if="!isSended">Envoyer la demande</button>
                    <button disabled class="submit-button button-sended" v-else-if="isSended">Demande envoyée</button>
                </form>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: [],

    data() {
        return {
            projectDescription: null,
            budget: null,
            isSended: false
        }
    },

    mounted() {
        $('body').toggleClass('theme-explorer');
    },

    methods: {
        postRegistration() {
            axios.post('/api/explorer/register', {
                project_description: this.projectDescription,
                budget: this.budget
            }).then(res => {
                if (res.success === false) {
                    // Error
                } else {
                    this.isSended = true;
                }
            });
        }
    },
}
</script>
