import axios from "axios";
const state = () => ({
    infosArticlesPopularsStatus: null,
    infosArticlesPopularsMessage: null,
    infosArticlesPopularsData: [],
});
const getters = {
    getInfosArticlesPopularsStatus(state) {
        return state.infosArticlesPopularsStatus;
    },

    getInfosArticlesPopularsMessage(state) {
        return state.infosArticlesPopularsMessage;
    },

    getInfosArticlesPopularsData(state) {
        return state.infosArticlesPopularsData;
    },
};

const actions = {
    async articlesPopularsDataRequest({ commit }) {
        try {
            const response = await axios.get(
                "/api/frontoffice/footer/articles_populars",
            );

            commit("setInfosArticlesPopularsStatus", "success");
            commit("setInfosArticlesPopularsMessage", response.data.message);
            commit("setInfosArticlesPopularsData", response.data.data);
        } catch (error) {
            if (error.response) {
                // erreur API (404, 422, 500...)
                commit("setInfosArticlesPopularsStatus", "error");
                commit(
                    "setInfosArticlesPopularsMessage",
                    error.response.data.message,
                );
            } else {
                // erreur réseau
                commit("setInfosArticlesPopularsStatus", "error");
                commit("setInfosArticlesPopularsMessage", "Erreur réseau");
            }
        }
    },
};

const mutations = {
    setInfosArticlesPopularsStatus(state, value) {
        state.infosArticlesPopularsStatus = value;
    },

    setInfosArticlesPopularsMessage(state, value) {
        state.infosArticlesPopularsMessage = value;
    },

    setInfosArticlesPopularsData(state, value) {
        state.infosArticlesPopularsData = value;
    },
};

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations,
};
