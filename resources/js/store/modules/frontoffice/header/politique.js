import axios from "axios";
const state = () => ({
    infosPolitiqueStatus: null,
    infosPolitiqueMessage: null,
    infosPolitiqueData: [],
});
const getters = {

    getInfosPolitiqueStatus(state) {
        return state.infosPolitiqueStatus;
    },

    getInfosPolitiqueMessage(state) {
        return state.infosPolitiqueMessage;
    },

    getInfosPolitiqueData(state) {
        return state.infosPolitiqueData;
    },

}

const actions = {
    async politiqueDataRequest({ commit }) {
        try {
            const response = await axios.get("/api/frontoffice/header/politique");

            commit("setInfosPolitiqueStatus", "success");
            commit("setInfosPolitiqueMessage", response.data.message);
            commit("setInfosPolitiqueData", response.data.data);

        } catch (error) {
            if (error.response) {
                // erreur API (404, 422, 500...)
                commit("setInfosPolitiqueStatus", "error");
                commit("setInfosPolitiqueMessage", error.response.data.message);
            } else {
                // erreur réseau
                commit("setInfosPolitiqueStatus", "error");
                commit("setInfosPolitiqueMessage", "Erreur réseau");
            }
        }
    },

}

const mutations = {
    setInfosPolitiqueStatus(state, value) {
        state.infosPolitiqueStatus = value;
    },

    setInfosPolitiqueMessage(state, value) {
        state.infosPolitiqueMessage = value;
    },

    setInfosPolitiqueData(state, value) {
        state.infosPolitiqueData = value;
    },
}

export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
