<script setup lang="ts">
import Tiptap from './Tiptap.vue';
</script>

<template>
  <div id="admin-dashboard">
    <h1>Admin Dashboard</h1>
    <p>Vitajte, Admin!</p>
    <button @click="logout">Odhlásiť sa</button>
    <br><br>
    <h2>Všetci používatelia</h2>
    <!-- Tabuľka používateľov -->
    <table v-if="users.length > 0">
      <thead>
      <tr>
        <th>ID</th>
        <th>Meno</th>
        <th>Email</th>
        <th>Sponzor</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="user in users" :key="user.id" :class="{ 'sponsor-row': user.is_sponsor }">
        <td>{{ user.id }}</td>
        <td>{{ user.name }}</td>
        <td>{{ user.email }}</td>
        <td>{{ user.is_sponsor ? 'Áno' : 'Nie' }}</td>
        <td>
          <button @click="setSponsor(user.id)" class="sponsor-btn">Nastaviť ako sponzor</button>
        </td>
      </tr>
      </tbody>
    </table>
    <div v-else>
      <p>Zatiaľ nie sú k dispozícii žiadni používatelia.</p>
    </div>

    <!-- Formulár pre vytvorenie novej session -->
    <div>
      <h2>Vytvoriť novú session</h2>
      <form @submit.prevent="createSession" class="session-form">
        <div class="form-group">
          <label for="sessionName">Názov:</label>
          <input type="text" id="sessionName" v-model="sessionName" required>
        </div>
        <div class="form-group">
          <label for="sessionCapacity">Kapacita:</label>
          <input type="number" id="sessionCapacity" v-model="sessionCapacity" required>
        </div>
        <div class="form-group">
          <label for="sessionStartTime">Začiatok:</label>
          <input type="datetime-local" id="sessionStartTime" v-model="sessionStartTime" required>
        </div>
        <div class="form-group">
          <label for="sessionEndTime">Koniec:</label>
          <input type="datetime-local" id="sessionEndTime" v-model="sessionEndTime" required>
        </div>
        <button type="submit">Vytvoriť</button>
      </form>
    </div>

    <!-- Tabuľka sessions -->
    <div v-if="sessions.length > 0">
      <h2>Všetky sessions</h2>
      <table>
        <thead>
        <tr>
          <th>Názov</th>
          <th>Začiatok</th>
          <th>Koniec</th>
          <th>Kapacita</th>
          <th>Prihlásení používatelia</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="session in sessions" :key="session.id">
          <td>{{ session.session_name }}</td>
          <td>{{ session.start_time }}</td>
          <td>{{ session.end_time }}</td>
          <td>{{ session.capacity }}</td>
          <td>
            <ul>
              <li v-for="user in sessionUsers(session.id)" :key="user.id">
                {{ user.name }}
              </li>
            </ul>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
    <div v-else>
      <p>Zatiaľ nie sú k dispozícii žiadne sessions.</p>
    </div>

    <!-- Formulár pre nahrávanie nového obrázka -->
    <div>
      <h2>Nahrať obrázok do galérie
      </h2>
      <form @submit.prevent="uploadImage" enctype="multipart/form-data">
        <div class="form-group">
          <label for="image">Obrázok:</label>
          <input type="file" id="image" ref="image" accept="image/*" required>
        </div>
        <div class="form-group">
          <label for="gallery">Galéria:</label>
          <select id="gallery" v-model="selectedGallery" @change="fetchGalleryYears" required>
            <option value="">Vyberte galériu</option>
            <option v-for="gallery in galleries" :key="gallery.id" :value="gallery.id">{{ gallery.name }}</option>
          </select>

        </div>
        <div class="form-group" v-if="galleryYears.length > 0">
          <label for="year">Ročník:</label>
          <select id="year" v-model="selectedYear" required>
            <option value="">Vyberte ročník</option>
            <option v-for="year in galleryYears" :key="year">{{ year }}</option>
          </select>
        </div>
        <div class="form-group">
          <label for="year">Vybraný ročník:</label>
          <span>{{ selectedYear }}</span>
        </div>

        <button type="submit">Nahrať</button>
      </form>
    </div>


    <!-- Formulár pre vytvorenie novej galérie -->
    <div>
      <h2>Vytvoriť novú galériu</h2>
      <form @submit.prevent="createGallery" class="gallery-form">
        <div class="form-group">
          <label for="galleryName">Názov galérie:</label>
          <input type="text" id="galleryName" v-model="galleryName" required>
        </div>
        <div class="form-group" v-for="(year, index) in selectedYears" :key="index">
          <label for="year">Ročník {{ index + 1 }}:</label>
          <input type="text" v-model="selectedYears[index]" placeholder="Ročník" required>
          <button type="button" @click="removeYear(index)">Odstrániť</button>
        </div>
        <button type="button" @click="addYear">Pridať ďalší ročník</button>
        <button type="submit">Vytvoriť</button>
      </form>
    </div>
  </div>
  <CreateAboutUs />

</template>

<script lang="ts">
import axios from 'axios';
import { onMounted } from 'vue';
import CreateAboutUs from './CreateAboutUs.vue';


export default {
  name: 'AdminDashboard',
  data() {
    return {
      users: [],
      sessions: [],
      sessionName: '',
      sessionCapacity: null,
      sessionStartTime: '',
      sessionEndTime: '',
      sessionUsersMap: {},
      galleries: [],
      selectedGallery: '',
      galleryName: '',
      galleryYears: [],
      selectedYears: [''], // Začiatočný ročník
      selectedYear: ''
    };
  },
  created() {
    this.checkAdmin();
    this.fetchUsers();
    this.fetchSessions();
    this.fetchSessionUsers();
    this.fetchGalleries();
  },
  methods: {
    async createGallery() {
      try {
        // Skontrolujte, či galéria s rovnakým názvom už existuje
        const existingGallery = this.galleries.find(gallery => gallery.name === this.galleryName);
        if (existingGallery) {
          console.log('Galéria s týmto názvom už existuje.');
          return; // Ukážte hlásenie a ukončite metódu, ak sa galéria s rovnakým názvom nájde
        }

        const galleryData = {
          name: this.galleryName,
          years: this.selectedYears // Pošlite priamo pole selectedYears
        };

        const response = await axios.post('http://localhost:8000/api/create-gallery', galleryData);
        console.log('Nová galéria bola úspešne vytvorená:', response.data);

        // Voliteľne: aktualizujte zoznam galérií po vytvorení novej
        this.fetchGalleries();

        // Vynulujte polia formulára po úspešnom odoslaní
        this.galleryName = '';
        this.selectedYears = ['']; // Vynulujte pole rokov
      } catch (error) {
        console.error('Chyba pri vytváraní galérie:', error);
        // V prípade chyby vypíšte detaily chyby, ak sú dostupné
        if (error.response && error.response.data) {
          console.error('Detaily chyby:', error.response.data);
        }
      }
    },





    async checkAdmin() {
      try {
        const token = localStorage.getItem('token');
        if (!token) {
          this.$router.push({ name: 'admin' });
          return;
        }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        await axios.post('http://localhost:8000/api/admin');
      } catch (error) {
        console.error('Prístup zamietnutý:', error);
        localStorage.removeItem('token');
        this.$router.push({ name: 'signin-basic' });
      }
    },
    async fetchUsers() {
      try {
        const response = await axios.get('http://localhost:8000/api/users');
        this.users = response.data;
      } catch (error) {
        console.error('Chyba pri získavaní informácií o používateľoch:', error);
      }
    },
    async fetchSessions() {
      try {
        const response = await axios.get('http://localhost:8000/api/session');
        this.sessions = response.data;
      } catch (error) {
        console.error('Chyba pri získavaní informácií o sessions:', error);
      }
    },
    async fetchSessionUsers() {
      try {
        const response = await axios.get('http://localhost:8000/api/session_users');
        response.data.forEach(entry => {
          if (!this.sessionUsersMap[entry.session_id]) {
            this.sessionUsersMap[entry.session_id] = [];
          }
          const user = this.users.find(user => user.id === entry.user_id);
          if (user) {
            this.sessionUsersMap[entry.session_id].push(user);
          }
        });
      } catch (error) {
        console.error('Chyba pri získavaní informácií o používateľoch v sessions:', error);
      }
    },
    async setSponsor(userId) {
      try {
        const response = await axios.put(`http://localhost:8000/api/users/${userId}/set-sponsor`); // Nastaví používateľa ako sponzora
        console.log(response.data); // Log the response data
        // Aktualizujte údaje o používateľovi, ak je to potrebné
      } catch (error) {
        console.error('Chyba pri nastavovaní používateľa ako sponzora:', error);
      }
    },
    async createSession() {
      try {
        const sessionData = {
          session_name: this.sessionName,
          capacity: this.sessionCapacity,
          start_time: this.sessionStartTime,
          end_time: this.sessionEndTime
        };
        const response = await axios.post('http://localhost:8000/api/session', sessionData);
        console.log('Nová session bola vytvorená:', response.data);
        this.sessions.push(response.data);
        this.sessionName = '';
        this.sessionCapacity = null;
        this.sessionStartTime = '';
        this.sessionEndTime = '';
      } catch (error) {
        console.error('Chyba pri vytváraní session:', error);
      }
    },

    sessionUsers(sessionId) {
      return this.sessionUsersMap[sessionId] || [];
    },
    async uploadImage() {
  try {
    const imageFile = this.$refs.image.files[0];
    const gallery = this.galleries.find(gallery => gallery.id === this.selectedGallery);

    if (!gallery) {
      console.error('Galéria s vybraným ID nebola nájdená.');
      return;
    }

    const formData = new FormData();
    formData.append('image', imageFile);
    formData.append('gallery_id', this.selectedGallery);
    formData.append('gallery_name', gallery.name);
    formData.append('years[]', String(parseInt(this.selectedYear)));  // Convert number to string

    const config = {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    };

    const response = await axios.post('http://localhost:8000/api/upload-image', formData, config);
    console.log('Obrázok bol úspešne nahratý:', response.data);
  } catch (error) {
    console.error('Chyba pri nahrávaní obrázka:', error);
    if (error.response && error.response.data) {
      console.error('Detaily chyby:', error.response.data);
    }
  }
}


    ,
    async fetchGalleries() {
      try {
        const response = await axios.get('http://localhost:8000/api/galleries');
        this.galleries = response.data;
      } catch (error) {
        console.error('Chyba pri načítavaní galérií:', error);
      }
    },

    async fetchGalleryYears() {
      try {
        const response = await axios.get(`http://localhost:8000/api/galleries/${this.selectedGallery}/years`);
        const yearsArray = JSON.parse(response.data); // Parse JSON string to JavaScript array
        this.galleryYears = yearsArray.map(year => parseInt(year)); // Map strings to integers
      } catch (error) {
        console.error('Chyba pri načítavaní ročníkov galérie:', error);
      }
    },


    logout() {
      localStorage.removeItem('token');
      this.$router.push({ name: 'signin-basic' });
    },
    addYear() {
      this.selectedYears.push('');
    },
    removeYear(index) {
      this.selectedYears.splice(index, 1);
    }
  }
};
</script>

<style scoped>
#admin-dashboard {
  width: 80%;
  margin: 0 auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead th {
  background-color: #f2f2f2;
  border-bottom: 2px solid #ddd;
  padding: 10px;
  text-align: left;
}

tbody td {
  padding: 10px;
}

.sponsor-btn {
  background-color: #4CAF50;
  color: white;
  padding: 8px 10px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.sponsor-btn:hover {
  background-color: #45a049;
}

.sponsor-row {
  background-color: #FFFF00;
}

.session-form {
  max-width: 600px;
  margin: 20px auto;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 10px;
  background-color: #f9f9f9;
}

.gallery-form {
  max-width: 600px;
  margin: 20px auto;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 10px;
  background-color: #f9f9f9;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
}

.form-group input {
  width: 100%;
  padding: 8px;
  box-sizing: border-box;
  border: 1px solid #ccc;
  border-radius: 5px;
}

.form-group select {
  width: 100%;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 5px;
  box-sizing: border-box;
}
</style>