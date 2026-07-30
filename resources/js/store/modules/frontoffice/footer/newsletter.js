import axios from "axios";
const state = () => ({
    newsletterStatus: null,
    newsletterMessage: null,
    newsletterErrors: [],
});

const getters = {
    getNewsletterStatus(state) {
        return state.newsletterStatus;
    },

    getNewsletterMessage(state) {
        return state.newsletterMessage;
    },

    getNewsletterErrors(state) {
        return state.newsletterErrors;
    },
}

const actions = {
    async newsletterRequest({ commit }, payload) {
        try {
            const response = await axios.post("/api/frontoffice/footer/newsletter", payload);

            commit("setNewsletterStatus", "success");
            commit("setNewsletterMessage", response.data.message);
            commit("setNewsletterErrors", []);

        } catch (error) {
            if (error.response) {
                const status = error.response.status;

                if (status === 422) {
                    
                    commit("setNewsletterStatus", "failed");
                    commit("setNewsletterMessage", error.response.data.message);
                    commit("setNewsletterErrors", error.response.data.errors || []);
                } else {
                    commit("setNewsletterStatus", "error");
                    commit("setNewsletterMessage", error.response.data.message || "Erreur serveur");
                    commit("setNewsletterErrors", []);
                }

            } else {
                commit("setNewsletterStatus", "error");
                commit("setNewsletterMessage", "Erreur réseau");
                commit("setNewsletterErrors", []);
            }
        }
    }

}

const mutations = {
    setNewsletterStatus(state, value) {
        state.newsletterStatus = value;
    },

    setNewsletterMessage(state, value) {
        state.newsletterMessage = value;
    },

    setNewsletterErrors(state, value) {
        state.newsletterErrors = value;
    },

};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
