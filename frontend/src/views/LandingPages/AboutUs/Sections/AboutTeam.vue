<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const items = ref([]);

const fetchItems = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/about-us');
    items.value = response.data;
  } catch (error) {
    console.error('Error fetching items:', error);
  }
};

onMounted(fetchItems);
</script>

<template>
  <section class="pb-5 position-relative bg-gradient-dark mx-n3">
    <div class="container">
      <div class="row">
        <div class="col-md-8 text-start mb-5 mt-5">
          <h3 class="text-white z-index-1 position-relative">
            Povedali o nás..
          </h3>
          <p class="text-white opacity-8 mb-0">
            Tu je niekoľko výrokov od známych ľudí, ktorí sa podieľali na konferencii.
          </p>
        </div>
      </div>
      <div class="row mt-4">
        <div class="col-12">
          <div v-for="item in items" :key="item.id" class="about-us-item">
            <img :src="item.photo" alt="Photo" class="about-us-photo"/>
            <blockquote class="about-us-quote">{{ item.quote }}</blockquote>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.container {
  padding: 20px;
}

h3 {
  margin-bottom: 20px;
}

p {
  margin-bottom: 40px;
}

.about-us-item {
  margin-bottom: 20px;
}

.about-us-photo {
  max-width: 100%;
  height: auto;
  display: block;
}

.about-us-quote {
  font-size: 1.2em;
  font-style: italic;
  margin-top: 10px;
}
</style>
