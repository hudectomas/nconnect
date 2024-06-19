<template>
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
        
      </div>
    </div>
    <div v-else>
      <p>No speakers available at the moment.</p>
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
      imageFile: null,
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

      selectedTimeSlot: '', // Ensure this is initialized properly
      timeSlotAssignments: [], // Ensure these are populated correctly
      editingAssignmentId: null // Ensure this is initialized properly

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
    handleFileChange(event) {
      this.imageFile = event.target.files[0];
      console.log('Vybraný súbor:', this.imageFile);
    },

    findSeminarName(seminarId) {
      const seminar = this.seminars.find(s => s.id === seminarId);
      return seminar ? seminar.title : 'Unknown Seminar';
    },

    findTimeSlot(timeSlotId) {
      return this.timeSlots.find(ts => ts.id === timeSlotId) || {};
    },

    editTimeSlotAssignment(assignment) {
      this.editingAssignmentId = assignment.id;
      this.editingSeminarId = assignment.seminar_id;
      this.selectedTimeSlot = assignment.time_slot_id;
    },

    async removeTimeSlotAssignment(assignmentId) {
      if (confirm('Are you sure you want to remove this assignment?')) {
        try {
          await axios.delete(`http://localhost:8000/api/time-slot-assignments/${assignmentId}`);
          this.timeSlotAssignments = this.timeSlotAssignments.filter(a => a.id !== assignmentId);
        } catch (error) {
          console.error('Error removing assignment:', error);
        }
      }
    },


    async fetchTimeSlotAssignments() {
      try {
        const response = await axios.get('http://localhost:8000/api/time-slot-assignments');
        this.timeSlotAssignments = response.data;
      } catch (error) {
        console.error('Error fetching time slot assignments:', error);
      }
    },

    async createTimeSlotAssignment() {
      try {
        const formData = new FormData();
        formData.append('seminar_id', this.editingSeminarId);
        formData.append('time_slot_id', this.selectedTimeSlot);

        const response = await axios.post('http://localhost:8000/api/time-slot-assignments', formData);
        console.log('New time slot assignment created:', response.data);

        // Fetch assignments again to update the list
        await this.fetchTimeSlotAssignments();

        // Reset the form fields
        this.editingSeminarId = '';
        this.selectedTimeSlot = '';
      } catch (error) {
        console.error('Error creating time slot assignment:', error);
      }
    },

    async updateTimeSlotAssignment(assignmentId) {
      try {
        const formData = new FormData();
        formData.append('time_slot_id', this.selectedTimeSlot);
        formData.append('_method', 'PUT');

        const response = await axios.post(`http://localhost:8000/api/time-slot-assignments/${assignmentId}`, formData);
        console.log('Time slot assignment updated:', response.data);

        // Fetch assignments again to update the list
        await this.fetchTimeSlotAssignments();

        // Reset the form fields
        this.editingAssignmentId = null;
        this.selectedTimeSlot = '';
      } catch (error) {
        console.error('Error updating time slot assignment:', error);
      }
    },



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
        if (index !== -1) {
          this.timeSlots[index] = response.data;
        }
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
        if (!this.imageFile) {
          console.error('Súbor nebol vybraný.');
          return;
        }

        const gallery = this.galleries.find(gallery => gallery.id === this.selectedGallery);
        if (!gallery) {
          console.error('Galéria s vybraným ID nebola nájdená.');
          return;
        }

        const formData = new FormData();
        formData.append('image', this.imageFile);
        formData.append('gallery_id', this.selectedGallery);
        formData.append('gallery_name', gallery.name);
        formData.append('years[]', this.selectedYear); // Zmena na pole

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
    await this.fetchTimeSlots(); // Ensure time slots are fetched
    await this.fetchTimeSlotAssignments(); // Fetch time slot assignments


    this.fetchPages();
    this.fetchStages();
  },




};
</script>