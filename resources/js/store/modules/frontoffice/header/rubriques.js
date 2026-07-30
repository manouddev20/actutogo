import axios from "axios";
const state = () => ({
    infosRubriquesStatus: null,
    infosRubriquesMessage: null,
    infosRubriquesData: [],
 
});
const getters = {

    getInfosRubriquesStatus(state) {
        return state.infosRubriquesStatus;
    },

    getInfosRubriquesMessage(state) {
        return state.infosRubriquesMessage;
    },

    getInfosRubriquesData(state) {
        return state.infosRubriquesData;
    },
 
}

const actions = {
    async rubriquesDataRequest({ commit }) {
        try {
            const response = await axios.get("/api/frontoffice/header/rubriques");

            commit("setInfosRubriquesStatus", "success");
            commit("setInfosRubriquesMessage", response.data.message);
            commit("setInfosRubriquesData", response.data.data);

        } catch (error) {
            if (error.response) {
                // erreur API (404, 422, 500...)
                commit("setInfosRubriquesStatus", "error");
                commit("setInfosRubriquesMessage", error.response.data.message);
            } else {
                // erreur réseau
                commit("setInfosRubriquesStatus", "error");
                commit("setInfosRubriquesMessage", "Erreur réseau");
            }
        }
    },
 
 
}

const mutations = {
    setInfosRubriquesStatus(state, value) {
        state.infosRubriquesStatus = value;
    },

    setInfosRubriquesMessage(state, value) {
        state.infosRubriquesMessage = value;
    },

    setInfosRubriquesData(state, value) {
        state.infosRubriquesData = value;
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
