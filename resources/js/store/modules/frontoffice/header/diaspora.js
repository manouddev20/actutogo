import axios from "axios";
const state = () => ({
    infosDiasporaStatus: null,
    infosDiasporaMessage: null,
    infosDiasporaData: [],
 
});
const getters = {

    getInfosDiasporaStatus(state) {
        return state.infosDiasporaStatus;
    },

    getInfosDiasporaMessage(state) {
        return state.infosDiasporaMessage;
    },

    getInfosDiasporaData(state) {
        return state.infosDiasporaData;
    },
 
}

const actions = {
    async diasporaDataRequest({ commit }) {
        try {
            const response = await axios.get("/api/frontoffice/header/diaspora");

            commit("setInfosDiasporaStatus", "success");
            commit("setInfosDiasporaMessage", response.data.message);
            commit("setInfosDiasporaData", response.data.data);

        } catch (error) {
            if (error.response) {
                // erreur API (404, 422, 500...)
                commit("setInfosDiasporaStatus", "error");
                commit("setInfosDiasporaMessage", error.response.data.message);
            } else {
                // erreur réseau
                commit("setInfosDiasporaStatus", "error");
                commit("setInfosDiasporaMessage", "Erreur réseau");
            }
        }
    },
 
 
}

const mutations = {
    setInfosDiasporaStatus(state, value) {
        state.infosDiasporaStatus = value;
    },

    setInfosDiasporaMessage(state, value) {
        state.infosDiasporaMessage = value;
    },

    setInfosDiasporaData(state, value) {
        state.infosDiasporaData = value;
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
