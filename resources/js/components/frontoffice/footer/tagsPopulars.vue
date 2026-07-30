<script setup>
import { ref, onMounted } from 'vue'; 
import { useStore } from 'vuex';

const store = useStore();
const loading = ref(true);
const tagsPopularsData = ref({});
const tagsPopularsMessage = ref(null);
const empty = ref(2);

const getResults = async () => {
  loading.value = true;
  await store.dispatch('tagsPopulars/tagsPopularsDataRequest', {});

  const status = store.getters['tagsPopulars/getInfosTagsPopularsStatus'];
  const message = store.getters['tagsPopulars/getInfosTagsPopularsMessage'];
  const data = store.getters['tagsPopulars/getInfosTagsPopularsData'];

  if (status === 'success') {
    tagsPopularsData.value = data;
    empty.value = 0;
  } else {
    tagsPopularsMessage.value = message;
    empty.value = status === 'empty' ? 1 : 3;
  }
  loading.value = false;
};
 
const tag = (slug) => {
  window.location = `/tags/${slug}`;
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
        <ul class="list-inline">
					<li class="list-inline-item"  v-for="info in tagsPopularsData" :key="info.id"><span @click="tag(info.slug)" style="cursor: pointer" class="btn btn-sm btn-success">#{{ info.name }}</span></li>
				</ul>
    </div>
    <!-- Hot topics END -->
</template>
 