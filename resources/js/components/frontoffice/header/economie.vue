<script setup>
import { ref, onMounted } from 'vue';
import moment from 'moment';
import { useStore } from 'vuex';

const store = useStore();
const loading = ref(true);
const economieData = ref({});
const economieMessage = ref(null);
const empty = ref(2);

const getResults = async () => {
  loading.value = true;
  await store.dispatch('economie/economieDataRequest', {});

  const status = store.getters['economie/getInfosEconomieStatus'];
  const message = store.getters['economie/getInfosEconomieMessage'];
  const data = store.getters['economie/getInfosEconomieData'];

  if (status === 'success') {
    economieData.value = data;
    empty.value = 0;
  } else {
    economieMessage.value = message;
    empty.value = status === 'empty' ? 1 : 3;
  }
  loading.value = false;
};

const author = (slug) => {
  window.location = `/authors/${slug}`;
};

const article = (slug) => {
  window.location = `/${slug}`;
};

const getImage = (slug) => slug;

onMounted(() => {
  getResults();
});
</script>

<template>
  <div class="container">
    <div v-if="loading">
      <div class="d-flex justify-content-center mt-3">
        <div class="spinner-border text-success" role="status">
          <span class="sr-only">Loading...</span>
        </div>
      </div>
    </div>

    <div v-else class="row g-4 p-3 flex-fill">
        <div  v-if="empty === 1">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                  <div style="position: relative; height: 250px;">
                      <img :src="`/assets/images/empty.png`" style="width: 100px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);" alt="empty">
                  </div>
                  <h5 style="text-align: center; margin-top: -50px"> {{ economieMessage  }} </h5>
                </div>
                <div class="col-md-3"></div>
            </div>
        </div>

        <div  v-if="empty === 3">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6">
                  <div style="position: relative; height: 250px;">
                      <img :src="`/assets/images/error.png`" style="width: 100px; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);" alt="empty">
                  </div>
                  <h5 style="text-align: center; margin-top: -50px"> {{ economieMessage  }} </h5>
                </div>
                <div class="col-md-3"></div>
            </div>
        </div>

      <div
        v-else-if="empty === 0"
        v-for="result in economieData"
        :key="result.id"
        class="col-sm-6 col-lg-3"
      >
        <div class="card bg-transparent" v-if="result.image_cover_url">
          <img class="card-img rounded" :src="getImage(result.image_cover_url)" style="height: 150px; object-fit: cover" :alt="result.title" />
          <div class="card-body px-0 pt-3">
            <h6 class="card-title mb-0">
              <span @click="article(result.slug)" class="btn-link text-reset fw-bold" style="cursor: pointer" v-html="result.title"></span>
            </h6>
            <ul class="nav nav-divider align-items-center text-uppercase small mt-2">
              <li class="nav-item">
                <span @click="author(result.author_slug)" class="text-reset btn-link" style="cursor: pointer">{{ result.author_name }}</span>
              </li>
              <li class="nav-item">{{ moment(result.date_publish).format('DD/MM/YYYY') }}</li>
            </ul>
          </div>
        </div>
        <div v-else>
            <div class="card mb-4">
                <div class="card-body border rounded-3">

                    <h6 class="card-title mb-0"><span @click="article(result.slug)" style="cursor: pointer" class="btn-link text-reset fw-bold" v-html="result.title"></span></h6>
                    <p class="card-text" v-html="truncate_content"> </p>
                    <!-- Card info -->
                    <ul class="nav nav-divider align-items-center text-uppercase small mt-2">
                        <li class="nav-item">
                            <span @click="author(result.author_slug)" style="cursor: pointer" class="text-reset btn-link">{{ result.author_name }}</span>
                        </li>
                        <li class="nav-item">{{ moment(result.date_publish).format("DD/MM/YYYY") }}</li>
                    </ul>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>
