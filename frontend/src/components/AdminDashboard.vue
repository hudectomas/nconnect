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


<!-- Vytvorenie speakera -->
      <div class="speaker-box">
        <h2>Pridať rečníka</h2>
        <form @submit.prevent="createSpeaker" class="speaker-form">
          <!-- Polia pre informácie o rečníkovi -->
          <div class="form-group">
            <label for="speakerName">Meno:</label>
            <input type="text" id="speakerName" v-model="speakerName" required>
          </div>
          <div class="form-group">
            <label for="shortDescription">Krátky popis:</label>
            <textarea id="shortDescription" v-model="shortDescription"></textarea>
          </div>
          <div class="form-group">
            <label for="longDescription">Dlhý popis:</label>
            <textarea id="longDescription" v-model="longDescription"></textarea>
          </div>
          <div class="form-group">
            <label for="image">Obrázok:</label>
            <input type="file" id="image" ref="image" accept="image/*" required>
          </div>
          <div class="form-group">
            <label for="socialLinks">Sociálne odkazy:</label>
            <input type="text" id="socialLinks" v-model="socialLinks">
          </div>
          <button type="submit">Pridať rečníka</button>
        </form>
      </div>

    <!-- Zobrazenie speakerov -->
    <h2>Speakers</h2>
    <div v-if="speakers.length > 0" class="speakers-container">
      <div v-for="speaker in speakers" :key="speaker.id" class="speaker-card">
        <div class="speaker-image">
          <img :src="speaker.imageUrl" alt="Speaker's Picture">
        </div>
        <div class="speaker-info">
          <h3>{{ speaker.name }}</h3>
          <p>{{ speaker.short_description }}</p>
          <p>{{ speaker.long_description }}</p>
          <a :href="speaker.social_links" target="_blank">
            <i class="fab fa-instagram"></i> <!-- Instagram icon -->
          </a>
        </div>
        <div class="speaker-actions">
          <button @click="editSpeaker(speaker)">Edit</button>
          <button v-if="editingSpeakerId === speaker.id" @click="updateSpeaker(speaker.id)">Update</button>
          <button @click="removeSpeaker(speaker.id)" class="remove-btn">Remove</button>
        </div>
      </div>
    </div>
    <div v-else>
      <p>No speakers available at the moment.</p>
    </div>

    <br>
    <br>

    <!-- Vytvorenie seminára -->
    <div class="seminar-box">
      <h2>Pridať seminár</h2>
      <form @submit.prevent="createSeminar" class="seminar-form">
        <!-- Polia pre informácie o seminári -->
        <div class="form-group">
          <label for="seminarName">Názov:</label>
          <input type="text" id="seminarName" v-model="seminar.name" required>
        </div>
        <div class="form-group">
          <label for="speaker">Rečník:</label>
          <select id="speaker" v-model="seminar.selectedSpeaker">
            <option v-for="speaker in speakers" :value="speaker.id">{{ speaker.name }}</option>
          </select>
        </div>
        <div class="form-group">
          <label for="seminarShortDescription">Krátky popis:</label>
          <textarea id="seminarShortDescription" v-model="seminar.seminarShortDescription"></textarea>
        </div>
        <div class="form-group">
          <label for="seminarLongDescription">Dlhý popis:</label>
          <textarea id="seminarLongDescription" v-model="seminar.seminarLongDescription"></textarea>
        </div>
        <button type="submit">Pridať seminár</button>
      </form>
    </div>

    <!-- Zobrazenie seminárov -->
    <h2>Semináre</h2>
    <div v-if="seminars.length > 0" class="seminars-container">
      <div v-for="seminar in seminars" :key="seminar.id" class="seminar-card">
        <div class="seminar-info">
          <h3>{{ seminar.title }}</h3>
          <p>Rečník: {{ findSpeakerName(seminar.speaker_id) }}</p>
          <p>{{ seminar.short_description }}</p>
          <p>{{ seminar.long_description }}</p>
        </div>
        <div class="seminar-actions">
          <button @click="editSeminar(seminar)">Editovať</button>
          <button v-if="editingSeminarId === seminar.id" @click="updateSeminar(seminar.id)">Aktualizovať</button>
          <button @click="removeSeminar(seminar.id)" class="remove-btn">Odstrániť</button>
        </div>
      </div>
    </div>
    <div v-else>
      <p>Žiadne semináre nie sú momentálne dostupné.</p>
    </div>

    <br>
    <br>

    <!--Pridanie stage -->
    <div class="stage-box">
      <h2>Pridať stage</h2>
      <form @submit.prevent="createStage" class="stage-form">
        <div class="form-group">
          <label for="stageName">Názov:</label>
          <input type="text" id="stageName" v-model="stageName" required>
        </div>
        <div class="form-group">
          <label for="stageDescription">Popis:</label>
          <textarea id="stageDescription" v-model="stageDescription"></textarea>
        </div>
        <button type="submit">Pridať stage</button>
      </form>
    </div>

    <br>

    <!--Zobrazenie stage -->
    <h2>Stages</h2>
    <div v-if="stages.length > 0" class="stages-container">
      <div v-for="stage in stages" :key="stage.id" class="stage-card">
        <div class="stage-info">
          <h3>{{ stage.name }}</h3>
          <p>{{ stage.description }}</p>
        </div>
        <div class="stage-actions">
          <button @click="editStage(stage)">Edit</button>
          <button v-if="editingStageId === stage.id" @click="updateStage(stage.id)">Update</button>
          <button @click="removeStage(stage.id)" class="remove-btn">Remove</button>
        </div>
      </div>
    </div>
    <div v-else>
      <p>No stages available at the moment.</p>
    </div>

    <br>
    <br>

<!--pridať časové okno-->
    <div class="time-slot-box">
      <h2>Pridať časové okno</h2>
      <form @submit.prevent="createTimeSlot" class="time-slot-form">
        <div class="form-group">
          <label for="stage">Stage:</label>
          <select id="stage" v-model="selectedStageId" required>
            <option v-for="stage in stages" :key="stage.id" :value="stage.id">{{ stage.name }}</option>
          </select>
        </div>
        <div class="form-group">
          <label for="startTime">Začiatok:</label>
          <input type="datetime-local" id="startTime" v-model="startTime" required>
        </div>
        <div class="form-group">
          <label for="endTime">Koniec:</label>
          <input type="datetime-local" id="endTime" v-model="endTime" required>
        </div>
        <button type="submit">Pridať časové okno</button>
      </form>
    </div>

    <br>

    <!--zobraziť časové okná-->
    <h2>Time Slots</h2>
    <div v-if="timeSlots.length > 0" class="time-slots-container">
      <div v-for="timeSlot in timeSlots" :key="timeSlot.id" class="time-slot-card">
        <div class="time-slot-info">
          <h3>{{ getStageName(timeSlot.stage_id) }}</h3>
          <p>{{ formatDate(timeSlot.start_time) }} - {{ formatDate(timeSlot.end_time) }}</p>
        </div>
        <div class="time-slot-actions">
          <button @click="editTimeSlot(timeSlot)">Edit</button>
          <button v-if="editingTimeSlotId === timeSlot.id" @click="updateTimeSlot(timeSlot.id)">Update</button>
          <button @click="removeTimeSlot(timeSlot.id)" class="remove-btn">Remove</button>
        </div>
      </div>
    </div>
    <div v-else>
      <p>No time slots available at the moment.</p>
    </div>

    <br>
    <br>

    <div>
      <h2>Pages</h2>
      <form @submit.prevent="createPage" class="gallery-form">
        <div class="form-group">
          <label for="galleryName">Názov stránky:</label>
          <input type="text" v-model="pageTitle" required />
        </div>
        <Tiptap v-model="pageContent" />

        <button type="submit">Save</button>
      </form>

      <h2>Všetky stránky</h2>
      <table>
        <thead>
        <tr>
          <th>Názov</th>
          <th>Operácia</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="page in pageList">
          <td>{{ page.title }}</td>
          <td><button @click="async () => await deletePage(page.id)">Zmazať</button></td>
        </tr>
        </tbody>
      </table>
    </div>





    <CreateAboutUs />
  </div>
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
      selectedYear: '',

      speakers: [],
      speakerName: '',
      shortDescription: '',
      longDescription: '',
      socialLinks: '',
      editingSpeakerId: null,

      seminars: [], // Array of seminar objects
      editingSeminarId: null, // ID of the seminar being edited
      seminar: { // Object for the seminar form fields
        name: '',
        selectedSpeaker: '',
        seminarShortDescription: '',
        seminarLongDescription: ''
      },

      pageTitle: '',
      pageContent: '',
      pageList: [],

      stages: [],
      stageName: '',
      stageDescription: '',
      editingStageId: null,

      timeSlots: [],
      selectedStageId: '',
      startTime: '',
      endTime: '',
      editingTimeSlotId: null,

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


    async fetchStages() {
      try {
        const response = await axios.get('http://localhost:8000/api/stages');
        this.stages = response.data;
      } catch (error) {
        console.error('Error fetching stages:', error);
      }
    },
    async fetchTimeSlots() {
      try {
        const response = await axios.get('http://localhost:8000/api/time-slots');
        this.timeSlots = response.data;
      } catch (error) {
        console.error('Error fetching time slots:', error);
      }
    },
    async createTimeSlot() {
      try {
        const response = await axios.post('http://localhost:8000/api/time-slots', {
          stage_id: this.selectedStageId,
          start_time: this.startTime,
          end_time: this.endTime,
        });
        this.timeSlots.push(response.data);
        this.selectedStageId = '';
        this.startTime = '';
        this.endTime = '';
      } catch (error) {
        console.error('Error creating time slot:', error);
      }
    },
    async updateTimeSlot(timeSlotId) {
      try {
        const response = await axios.put(`http://localhost:8000/api/time-slots/${timeSlotId}`, {
          stage_id: this.selectedStageId,
          start_time: this.startTime,
          end_time: this.endTime,
        });
        const index = this.timeSlots.findIndex(ts => ts.id === timeSlotId);
        this.timeSlots[index] = response.data;
        this.editingTimeSlotId = null;
        this.selectedStageId = '';
        this.startTime = '';
        this.endTime = '';
      } catch (error) {
        console.error('Error updating time slot:', error);
      }
    },
    editTimeSlot(timeSlot) {
      this.editingTimeSlotId = timeSlot.id;
      this.selectedStageId = timeSlot.stage_id;
      this.startTime = timeSlot.start_time.slice(0, 16); // Format to datetime-local input
      this.endTime = timeSlot.end_time.slice(0, 16);
    },
    async removeTimeSlot(timeSlotId) {
      if (confirm('Are you sure you want to remove this time slot?')) {
        try {
          await axios.delete(`http://localhost:8000/api/time-slots/${timeSlotId}`);
          this.timeSlots = this.timeSlots.filter(ts => ts.id !== timeSlotId);
        } catch (error) {
          console.error('Error removing time slot:', error);
        }
      }
    },
    getStageName(stageId) {
      const stage = this.stages.find(s => s.id === stageId);
      return stage ? stage.name : 'Unknown Stage';
    },
    formatDate(date) {
      return new Date(date).toLocaleString();
    },




    async createStage() {
      try {
        const response = await axios.post('http://localhost:8000/api/stages', {
          name: this.stageName,
          description: this.stageDescription,
        });
        this.stages.push(response.data);
        this.stageName = '';
        this.stageDescription = '';
      } catch (error) {
        console.error('Error creating stage:', error);
      }
    },
    async updateStage(stageId) {
      try {
        const response = await axios.put(`http://localhost:8000/api/stages/${stageId}`, {
          name: this.stageName,
          description: this.stageDescription,
        });
        const index = this.stages.findIndex(s => s.id === stageId);
        this.stages[index] = response.data;
        this.editingStageId = null;
        this.stageName = '';
        this.stageDescription = '';
      } catch (error) {
        console.error('Error updating stage:', error);
      }
    },
    editStage(stage) {
      this.editingStageId = stage.id;
      this.stageName = stage.name;
      this.stageDescription = stage.description;
    },
    async removeStage(stageId) {
      if (confirm('Are you sure you want to remove this stage?')) {
        try {
          await axios.delete(`http://localhost:8000/api/stages/${stageId}`);
          this.stages = this.stages.filter(s => s.id !== stageId);
        } catch (error) {
          console.error('Error removing stage:', error);
        }
      }
    },


    async createSpeaker() {
      try {
        let formData = new FormData();
        formData.append('name', this.speakerName);
        formData.append('short_description', this.shortDescription);
        formData.append('long_description', this.longDescription);
        formData.append('image', this.$refs.image.files[0]); // Get the file from the ref
        formData.append('social_links', this.socialLinks);

        const response = await axios.post('http://localhost:8000/api/speaker', formData);

        console.log('New speaker was created:', response.data);

        // Fetch speakers again to ensure the list is up to date
        await this.fetchSpeakers();

        // Reset the form fields and file input
        this.speakerName = '';
        this.shortDescription = '';
        this.longDescription = '';
        this.$refs.image.value = ''; // Reset the file input
        this.socialLinks = '';
      } catch (error) {
        console.error('Error creating speaker:', error);
      }
    },


    // Method to handle removing a speaker
      removeSpeaker(speakerId) {
        if (confirm('Are you sure you want to remove this speaker?')) {
          // Use axios to call API endpoint
          axios.delete(`http://localhost:8000/api/speakers/${speakerId}`)
              .then(response => {
                // Remove the speaker from the speakers array
                this.speakers = this.speakers.filter(s => s.id !== speakerId);
              })
              .catch(error => {
                console.error('Error removing speaker:', error);
              });
        }
      },

    async updateSpeaker(speakerId) {
      let formData = new FormData();
      formData.append('name', this.speakerName);
      formData.append('short_description', this.shortDescription);
      formData.append('long_description', this.longDescription);
      // Check if a new image file was provided
      if (this.$refs.image.files.length > 0) {
        formData.append('image', this.$refs.image.files[0]);
      }
      formData.append('social_links', this.socialLinks);

      try {
        formData.append('_method', 'PUT');
        const response = await axios.post(`http://localhost:8000/api/speakers/${speakerId}`, formData);

        console.log('Speaker updated:', response.data);

        // Fetch speakers again to ensure the list is up to date
        await this.fetchSpeakers();

        // Reset edit state and form fields
        this.editingSpeakerId = null;
        this.speakerName = '';
        this.shortDescription = '';
        this.longDescription = '';
        this.$refs.image.value = ''; // Reset the file input
        this.socialLinks = '';
      } catch (error) {
        console.error('Error updating speaker:', error);
      }
    },

    editSpeaker(speaker) {
      this.editingSpeakerId = speaker.id;
      // Set form fields to the values of the speaker being edited
      this.speakerName = speaker.name;
      this.shortDescription = speaker.short_description;
      this.longDescription = speaker.long_description;
      this.socialLinks = speaker.social_links;
      // If you want to display the existing image, you can set a temporary variable here
      this.existingImageUrl = speaker.imageUrl; // Assuming the API returns the image URL as imageUrl
    },

    // New method to fetch speakers
    async fetchSpeakers() {
      try {
        const response = await axios.get('http://localhost:8000/api/speakers');
        this.speakers = response.data;
      } catch (error) {
        console.error('Error fetching speakers:', error);
      }
    },

    async createSeminar() {
      try {
        let formData = new FormData();
        formData.append('title', this.seminar.name);
        formData.append('speaker_id', this.seminar.selectedSpeaker);
        formData.append('short_description', this.seminar.seminarShortDescription);
        formData.append('long_description', this.seminar.seminarLongDescription);

        const response = await axios.post('http://localhost:8000/api/seminars', formData);

        console.log('New seminar was created:', response.data);

        // Fetch seminars again to ensure the list is up to date
        await this.fetchSeminars();

        // Reset the form fields
        this.resetSeminarForm();
      } catch (error) {
        console.error('Error creating seminar:', error);
      }
    },

    removeSeminar(seminarId) {
      if (confirm('Are you sure you want to remove this seminar?')) {
        axios.delete(`http://localhost:8000/api/seminars/${seminarId}`)
            .then(response => {
              // Remove the seminar from the seminars array
              this.seminars = this.seminars.filter(s => s.id !== seminarId);
            })
            .catch(error => {
              console.error('Error removing seminar:', error);
            });
      }
    },

    async updateSeminar(seminarId) {
      let formData = new FormData();
      formData.append('title', this.seminar.name);
      formData.append('short_description', this.seminar.seminarShortDescription);
      formData.append('long_description', this.seminar.seminarLongDescription);
      formData.append('speaker_id', this.seminar.selectedSpeaker);
      formData.append('_method', 'PUT');

      try {
        const response = await axios.post(`http://localhost:8000/api/seminars/${seminarId}`, formData);
        console.log('Seminar updated:', response.data);

        // Fetch seminars again to ensure the list is up to date
        await this.fetchSeminars();

        // Reset edit state and form fields
        this.editingSeminarId = null;
        this.resetSeminarForm();
      } catch (error) {
        console.error('Error updating seminar:', error);
      }
    },

    editSeminar(seminar) {
      this.editingSeminarId = seminar.id;

      // Set form fields to the values of the seminar being edited
      this.seminar.name = seminar.title;
      this.seminar.selectedSpeaker = seminar.speaker_id;
      this.seminar.seminarShortDescription = seminar.short_description;
      this.seminar.seminarLongDescription = seminar.long_description;
    },

    findSpeakerName(speakerId) {
      const speaker = this.speakers.find(s => s.id === speakerId);
      return speaker ? speaker.name : 'Unknown Speaker';
    },

    resetSeminarForm() {
      // Reset the form fields
      this.seminar.name = '';
      this.seminar.selectedSpeaker = '';
      this.seminar.seminarShortDescription = '';
      this.seminar.seminarLongDescription = '';
    },

    // New method to fetch seminars
    async fetchSeminars() {
      try {
        const response = await axios.get('http://localhost:8000/api/seminars');
        this.seminars = response.data;
      } catch (error) {
        console.error('Error fetching seminars:', error);
      }
    },




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
          this.$router.push('admin');
          return;
        }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        await axios.post('http://localhost:8000/api/admin');
      } catch (error) {
        console.error('Prístup zamietnutý:', error);
        localStorage.removeItem('token');
        this.$router.push('signin-basic');
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
      this.$router.push('signin-basic');
    },
    addYear() {
      this.selectedYears.push('');
    },
    removeYear(index) {
      this.selectedYears.splice(index, 1);
    },
    async createPage() {
      try {
        const response = await axios.post('http://localhost:8000/api/create-pages', {
          title: this.pageTitle,
          content: this.pageContent,
        })
        this.fetchPages()
        console.log('Page created:', response.data)
      } catch (error) {
        console.error('Error creating page:', error)
      }
    },
    async deletePage(id: number) {
      try {
        const response = await axios.delete('http://localhost:8000/api/page/' + id);
        this.fetchPages()
        console.log('Page deleted:', response.data)
      } catch (error) {
        console.error('Error deleting page:', error)
      }
    },
    async fetchPages() {
      try {
        const response = await axios.get('http://localhost:8000/api/pages')
        console.log('pages:', response.data)
        this.pageList = response.data
      } catch (error) {
        console.error('Error creating page:', error)
      }
    },
  },
  async mounted() {
    await this.fetchSpeakers();
    await this.fetchSeminars();
    this.fetchPages();
    this.fetchStages();
    this.fetchTimeSlots();
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
.speaker-form, .seminar-form {
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

/* CSS */
.speakers-container, .seminars-container {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}

.speaker-card, .seminar-card {
  flex: 1;
  min-width: 300px; /* Adjust as needed */
  border: 1px solid #ccc;
  padding: 1rem;
}

.speaker-image img {
  max-width: 100%;
  height: auto;
}

.speaker-info, .seminar-info {
  margin-top: 1rem;
}

.speaker-actions button, .seminar-actions {
  margin-right: .5rem;
}

/* Container for stages */
.stages-container, .time-slots-container {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
}

/* Individual stage card */
.stage-card, .time-slot-card {
  flex: 1;
  min-width: 300px; /* Adjust as needed */
  border: 1px solid #ccc;
  padding: 1rem;
  background-color: #fff;
  border-radius: 8px;
}

/* Stage information */
.stage-info, .time-slot-info {
  margin-top: 1rem;
}

/* Actions for each stage */
.stage-actions button, .time-slot-actions button {
  margin-right: .5rem;
}

/* Form for creating and editing stages */
.stage-form, .time-slot-form {
  max-width: 600px;
  margin: 20px auto;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 10px;
  background-color: #f9f9f9;
}

/* Form group styling */
.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: .5rem;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: .5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
}

/* Buttons styling */
button {
  padding: .5rem 1rem;
  border: none;
  border-radius: 4px;
  color: #000000;
  cursor: pointer;
}





</style>