<script setup>
import { ref, onMounted } from 'vue';
import moment from 'moment';
import { useStore } from 'vuex';

const store = useStore();
const loading = ref(true);
const categoryPopularsData = ref({});
const categoryPopularsMessage = ref(null);
const empty = ref(2);

const getResults = async () => {
  loading.value = true;
  await store.dispatch('categoryPopulars/categoryPopularsDataRequest', {});

  const status = store.getters['categoryPopulars/getInfosCategoryPopularsStatus'];
  const message = store.getters['categoryPopulars/getInfosCategoryPopularsMessage'];
  const data = store.getters['categoryPopulars/getInfosCategoryPopularsData'];

  if (status === 'success') {
    categoryPopularsData.value = data;
    empty.value = 0;
  } else {
    categoryPopularsMessage.value = message;
    empty.value = status === 'empty' ? 1 : 3;
  }
  loading.value = false;
};

const category = (slug) => {
  window.location = `/${slug}`;
};

onMounted(() => {
  getResults();
});
</script>
<template>
    <!-- Hot topics START -->
        <div class="d-flex justify-content-center" v-if="loading">
            <div class="spinner-border text-dark"  role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        <div class="row" v-else>
            <div class="col-6" v-for="(info, index) in categoryPopularsData" :key="index">
                <ul class="nav flex-column text-white">
                    <li class="nav-item"><span class="nav-link pt-0 text-white" style="cursor: pointer" @click="category(info.slug)" > {{ info.name.toUpperCase() }} </span></li>
                </ul>
            </div>
        </div>

		<!-- Hot topics END -->
</template>
 