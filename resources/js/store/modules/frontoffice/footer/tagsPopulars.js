import axios from "axios";
const state = () => ({
    infosTagsPopularsStatus:null,
    infosTagsPopularsMessage:null,
    infosTagsPopularsData:[],
});
const getters = {

    getInfosTagsPopularsStatus(state){
        return state.infosTagsPopularsStatus;
    },

    getInfosTagsPopularsMessage(state){
        return state.infosTagsPopularsMessage;
    },

    getInfosTagsPopularsData(state){
        return state.infosTagsPopularsData;
    },

}

const actions = {
    async tagsPopularsDataRequest({ commit }) {
        try {
            const response = await axios.get(
                "/api/frontoffice/footer/tags_populars",
            );

            commit("setInfosTagsPopularsStatus", "success");
            commit("setInfosTagsPopularsMessage", response.data.message);
            commit("setInfosTagsPopularsData", response.data.data);
        } catch (error) {
            if (error.response) {
                // erreur API (404, 422, 500...)
                commit("setInfosTagsPopularsStatus", "error");
                commit(
                    "setInfosTagsPopularsMessage",
                    error.response.data.message,
                );
            } else {
                // erreur réseau
                commit("setInfosTagsPopularsStatus", "error");
                commit("setInfosTagsPopularsMessage", "Erreur réseau");
            }
        }
    },

}

const mutations = {
    setInfosTagsPopularsStatus(state, value){
        state.infosTagsPopularsStatus = value;
    },

    setInfosTagsPopularsMessage(state, value){
        state.infosTagsPopularsMessage = value;
    },

    setInfosTagsPopularsData(state, value){
        state.infosTagsPopularsData = value;
    },

}

export default{
    namespaced:true,
    state,
    getters,
    actions,
    mutations
}
