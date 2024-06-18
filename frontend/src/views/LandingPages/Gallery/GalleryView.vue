<template>
  <div class="gallery">
    <button @click="redirectToHomepage" class="menu-button">Menu</button>

    <h2>Sponzori</h2>
    <div v-if="sponsors.length > 0" class="sponsor-grid">
      <div v-for="(sponsor, index) in sponsors" :key="index" class="sponsor-item">
        <h3>{{ sponsor }}</h3>
      </div>
    </div>
    <div v-else>
      <p>Žiadni sponzori neboli nájdení.</p>
    </div>

    <h2>Sessions</h2>
    <div v-if="sessions.length > 0" class="session-list">
      <table>
        <thead>
        <tr>
          <th>Názov</th>
          <th>Začiatok</th>
          <th>Koniec</th>
          <th>Kapacita</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(session, index) in sessions" :key="index">
          <td>{{ session.session_name }}</td>
          <td>{{ session.start_time }}</td>
          <td>{{ session.end_time }}</td>
          <td>{{ session.capacity }}</td>
        </tr>
        </tbody>
      </table>
    </div>
    <div v-else>
      <p>Pre zvolenú galériu a ročník neboli nájdené žiadne sessions.</p>
    </div>

    <h2>Galéria</h2>

    <div>
      <label for="gallery">Vyberte galériu:</label>
      <select id="gallery" v-model="selectedGallery" @change="fetchGalleryYears">
        <option value="">Vyberte galériu</option>
        <option v-for="gallery in galleries" :key="gallery.id" :value="gallery.name">{{ gallery.name }}</option>
      </select>
    </div>

    <div v-if="selectedGallery">
      <label for="year">Vyberte ročník:</label>
      <select id="year" v-model="selectedYear" @change="fetchImages">
        <option value="">Vyberte ročník</option>
        <option v-for="year in galleryYears" :key="year">{{ year }}</option>
      </select>
    </div>

    <div v-if="images.length > 0">
      <h2>Obrázky</h2>
      <div class="image-container">
        <div v-for="(image, index) in images" :key="index" class="image-frame">
          <img :src="image.url" :alt="image.name" class="gallery-image">
        </div>
      </div>
    </div>
    <div v-else>
      <p>Zatiaľ nie sú k dispozícii žiadne obrázky.</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'GalleryView',
  data() {
    return {
      sponsors: [],
      sessions: [],
      images: [],
      galleries: [],
      selectedGallery: '',
      selectedYear: '',
      galleryYears: [],
      selectedImage: ''
    };
  },
  created() {
    this.fetchInitialData();
  },
  methods: {
    async fetchInitialData() {
      try {
        await Promise.all([
          this.fetchSponsors(),
          this.fetchSessions(),
          this.fetchGalleries()
        ]);
      } catch (error) {
        console.error('Chyba pri načítavaní počiatočných dát:', error);
      }
    },
    async fetchGalleries() {
      try {
        const response = await axios.get('http://localhost:8000/api/galleries');
        this.galleries = response.data;
        console.log('Galleries:', this.galleries);
      } catch (error) {
        console.error('Chyba pri načítavaní galérií:', error);
      }
    },
    async fetchGalleryYears() {
      try {
        if (this.selectedGallery) {
          const selectedGalleryObj = this.galleries.find(gallery => gallery.name === this.selectedGallery);

          if (!selectedGalleryObj) {
            console.error(`Galéria s názvom ${this.selectedGallery} nebola nájdená.`);
            this.galleryYears = [];
            this.images = [];
            return;
          }

          const response = await axios.get(`http://localhost:8000/api/galleries/${selectedGalleryObj.id}/years`);
          console.log('Response for gallery years:', response.data);

          const yearsString = response.data;
          const yearsArray = JSON.parse(yearsString);
          const years = yearsArray.map(year => parseInt(year));

          if (years.some(year => isNaN(year))) {
            console.error('Neplatný formát dát pre ročníky galérie:', years);
            this.galleryYears = [];
          } else {
            this.galleryYears = years;
          }

          this.images = [];
        } else {
          this.galleryYears = [];
          this.images = [];
        }
      } catch (error) {
        console.error('Chyba pri načítavaní ročníkov galérie:', error);
        this.galleryYears = [];
        this.images = [];
      }
    },
    async fetchImages() {
      try {
        if (this.selectedGallery && this.selectedYear) {
          console.log(`Fetching images for gallery ${this.selectedGallery} and year ${this.selectedYear}`);

          const response = await axios.get(`http://localhost:8000/api/images`, {
            params: {
              galleryName: this.selectedGallery,
              year: this.selectedYear
            }
          });

          console.log('Response from server:', response.data);

          if (Array.isArray(response.data)) {
            const images = response.data.map(image => ({
              id: image.id,
              url: image.image_url, // Upravenie prístupu k URL obrázka
              name: image.image_name // Upravenie prístupu k názvu obrázka
            }));

            this.images = images;
          } else {
            console.error('Neplatný formát dát pre obrázky:', response.data);
            this.images = [];
          }
        } else {
          this.images = [];
        }
      } catch (error) {
        console.error('Chyba pri načítavaní obrázkov:', error);
        this.images = [];
      }
    },

    getImageUrl(imageName) {
      const image = this.images.find(img => img.name === imageName);
      return image ? image.url : '';
    },
    async fetchSponsors() {
      try {
        const response = await axios.get('http://localhost:8000/api/sponsors');
        this.sponsors = response.data;
        console.log('Sponsors:', this.sponsors);
      } catch (error) {
        console.error('Chyba pri načítavaní sponzorov:', error);
      }
    },
    async fetchSessions() {
      try {
        const response = await axios.get('http://localhost:8000/api/session');
        this.sessions = response.data;
        console.log('Sessions:', this.sessions);
      } catch (error) {
        console.error('Chyba pri načítavaní sessions:', error);
      }
    },
    redirectToHomepage() {
      window.location.href = 'http://localhost:3000';
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
.image-frame {
  border: 1px solid #ccc;
  padding: 10px;
  margin-bottom: 20px;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.image-frame img {
  max-width: 100%;
  height: auto;
  display: block;
  border-radius: 8px;
}
.sponsor-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.sponsor-item {
  text-align: center;
  border: 1px solid #ccc;
  padding: 10px;
  background-color: #f9f9f9;
  border-radius: 8px;
}

.session-list {
  margin-top: 20px;
}

.session-list table {
  width: 100%;
  border-collapse: collapse;
}

.session-list th,
.session-list td {
  border: 1px solid #ccc;
  padding: 8px;
  text-align: center;
}

.session-list th {
  background-color: #f2f2f2;
}

.menu-button {
  background-color: #4CAF50;
  color: white;
  border: none;
  padding: 10px 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin-bottom: 20px;
  cursor: pointer;
  border-radius: 8px;
  transition: background-color 0.3s ease;
}

.menu-button:hover {
  background-color: #45a049;
}

.filters {
  margin-bottom: 20px;
}

.filters label {
  margin-right: 10px;
}

.filters input {
  margin-right: 20px;
  padding: 5px;
  width: 150px;
}

.filters .load-images-button {
  padding: 8px 12px;
  background-color: #007bff;
  color: white;
  border: none;
  cursor: pointer;
  border-radius: 4px;
}

.filters .load-images-button:hover {
  background-color: #0056b3;
}
</style>
