import axios from "axios";
const state = () => ({
    infosEconomieStatus: null,
    infosEconomieMessage: null,
    infosEconomieData: [],
 
});
const getters = {

    getInfosEconomieStatus(state) {
        return state.infosEconomieStatus;
    },

    getInfosEconomieMessage(state) {
        return state.infosEconomieMessage;
    },

    getInfosEconomieData(state) {
        return state.infosEconomieData;
    },

}

const actions = {
    async economieDataRequest({ commit }) {
        try {
            const response = await axios.get("/api/frontoffice/header/economie");

            commit("setInfosEconomieStatus", "success");
            commit("setInfosEconomieMessage", response.data.message);
            commit("setInfosEconomieData", response.data.data);

        } catch (error) {
            if (error.response) {
                // erreur API (404, 422, 500...)
                commit("setInfosEconomieStatus", "error");
                commit("setInfosEconomieMessage", error.response.data.message);
            } else {
                // erreur réseau
                commit("setInfosEconomieStatus", "error");
                commit("setInfosEconomieMessage", "Erreur réseau");
            }
        }
    },
 
}

const mutations = {
    setInfosEconomieStatus(state, value) {
        state.infosEconomieStatus = value;
    },

    setInfosEconomieMessage(state, value) {
        state.infosEconomieMessage = value;
    },

    setInfosEconomieData(state, value) {
        state.infosEconomieData = value;
    },

    
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
