<template>
  <div class="gallery">
    <h2>Galéria</h2>

    <!-- Formulár pre výber názvu galérie a ročníka -->
    <form @submit.prevent="loadImages">
      <label for="galleryName">Vyberte galériu:</label>
      <select v-model="selectedGallery" id="galleryName">
        <option v-for="gallery in galleries" :key="gallery.id" :value="gallery.name">{{ gallery.name }}</option>
      </select>

      <label for="galleryYear">Vyberte ročník:</label>
      <select v-model="selectedYear" id="galleryYear">
        <option v-for="year in years" :key="year">{{ year }}</option>
      </select>

      <button type="submit">Načítať galériu</button>
    </form>

    <!-- Zobrazenie obrázkov pre zvolenú galériu a ročník -->
    <div v-if="selectedImages.length > 0" class="image-grid">
      <div v-for="(image, index) in selectedImages" :key="index" class="image-item">
        <img :src="getImageUrl(image.filename)" :alt="image.alt">
        <p>{{ image.description }}</p>
      </div>
    </div>
    <div v-else>
      <p>Pre zvolenú galériu a ročník neboli nájdené žiadne obrázky.</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      galleries: [],
      years: [],
      selectedGallery: '',
      selectedYear: '',
      selectedImages: []
    };
  },
  created() {
    this.fetchGalleries();
  },
  methods: {
    async fetchGalleries() {
      try {
        const response = await axios.get('http://localhost:8000/api/galleries');
        this.galleries = response.data;
      } catch (error) {
        console.error('Chyba pri načítavaní galérií:', error);
      }
    },
    async loadImages() {
      try {
        // Fetch images based on selected gallery and year
        const imagesResponse = await axios.get(`http://localhost:8000/public/storage/images/${this.selectedGallery}/${this.selectedYear}/`);
        this.selectedImages = imagesResponse.data; // Assuming imagesResponse.data is an array of image objects
      } catch (error) {
        console.error('Error loading images:', error);
        // Handle error state as needed, e.g., show a message to the user
      }
    },
    getImageUrl(filename) {
      // Generate the image URL based on selected gallery, year, and filename
      return `http://localhost:8000/public/storage/images/${this.selectedGallery}/${this.selectedYear}/${filename}`;
    }
  }
};
</script>

<style scoped>
.gallery {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.image-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.image-item {
  text-align: center;
}

.image-item img {
  width: 100%;
  max-width: 100%;
  height: auto;
}
</style>
