import axios from "axios";
const state = () => ({
    infosInternationalStatus: null,
    infosInternationalMessage: null,
    infosInternationalData: [],
 
});
const getters = {

    getInfosInternationalStatus(state) {
        return state.infosInternationalStatus;
    },

    getInfosInternationalMessage(state) {
        return state.infosInternationalMessage;
    },

    getInfosInternationalData(state) {
        return state.infosInternationalData;
    },
 
 
}

const actions = {
    async internationalDataRequest({ commit }) {
        try {
            const response = await axios.get("/api/frontoffice/header/international");

            commit("setInfosInternationalStatus", "success");
            commit("setInfosInternationalMessage", response.data.message);
            commit("setInfosInternationalData", response.data.data);

        } catch (error) {
            if (error.response) {
                // erreur API (404, 422, 500...)
                commit("setInfosInternationalStatus", "error");
                commit("setInfosInternationalMessage", error.response.data.message);
            } else {
                // erreur réseau
                commit("setInfosInternationalStatus", "error");
                commit("setInfosInternationalMessage", "Erreur réseau");
            }
        }
    },

     

}

const mutations = {
    setInfosInternationalStatus(state, value) {
        state.infosInternationalStatus = value;
    },

    setInfosInternationalMessage(state, value) {
        state.infosInternationalMessage = value;
    },

    setInfosInternationalData(state, value) {
        state.infosInternationalData = value;
    },
 
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
