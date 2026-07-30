import axios from "axios";
const state = () => ({
    infosSocieteStatus: null,
    infosSocieteMessage: null,
    infosSocieteData: [],
});
const getters = {

    getInfosSocieteStatus(state) {
        return state.infosSocieteStatus;
    },

    getInfosSocieteMessage(state) {
        return state.infosSocieteMessage;
    },

    getInfosSocieteData(state) {
        return state.infosSocieteData;
    },

}

const actions = {
    async societeDataRequest({ commit }) {
        try {
            const response = await axios.get("/api/frontoffice/header/societe");

            commit("setInfosSocieteStatus", "success");
            commit("setInfosSocieteMessage", response.data.message);
            commit("setInfosSocieteData", response.data.data);

        } catch (error) {
            if (error.response) {
                // erreur API (404, 422, 500...)
                commit("setInfosSocieteStatus", "error");
                commit("setInfosSocieteMessage", error.response.data.message);
            } else {
                // erreur réseau
                commit("setInfosSocieteStatus", "error");
                commit("setInfosSocieteMessage", "Erreur réseau");
            }
        }
    },

}

const mutations = {
    setInfosSocieteStatus(state, value) {
        state.infosSocieteStatus = value;
    },

    setInfosSocieteMessage(state, value) {
        state.infosSocieteMessage = value;
    },

    setInfosSocieteData(state, value) {
        state.infosSocieteData = value;
    },

}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
