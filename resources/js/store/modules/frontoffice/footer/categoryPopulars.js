import axios from "axios";
const state = () => ({
    infosCategoryPopularsStatus:null,
    infosCategoryPopularsMessage:null,
    infosCategoryPopularsData:[],
});
const getters = {

    getInfosCategoryPopularsStatus(state){
        return state.infosCategoryPopularsStatus;
    },

    getInfosCategoryPopularsMessage(state){
        return state.infosCategoryPopularsMessage;
    },

    getInfosCategoryPopularsData(state){
        return state.infosCategoryPopularsData;
    },

}

const actions = {
    async categoryPopularsDataRequest({ commit }) {
        try {
            const response = await axios.get(
                "/api/frontoffice/footer/category_populars",
            );

            commit("setInfosCategoryPopularsStatus", "success");
            commit("setInfosCategoryPopularsMessage", response.data.message);
            commit("setInfosCategoryPopularsData", response.data.data);
        } catch (error) {
            if (error.response) {
                // erreur API (404, 422, 500...)
                commit("setInfosCategoryPopularsStatus", "error");
                commit(
                    "setInfosCategoryPopularsMessage",
                    error.response.data.message,
                );
            } else {
                // erreur réseau
                commit("setInfosCategoryPopularsStatus", "error");
                commit("setInfosCategoryPopularsMessage", "Erreur réseau");
            }
        }
    },

}

const mutations = {
    setInfosCategoryPopularsStatus(state, value){
        state.infosCategoryPopularsStatus = value;
    },

    setInfosCategoryPopularsMessage(state, value){
        state.infosCategoryPopularsMessage = value;
    },

    setInfosCategoryPopularsData(state, value){
        state.infosCategoryPopularsData = value;
    },

}

export default{
    namespaced:true,
    state,
    getters,
    actions,
    mutations
}
