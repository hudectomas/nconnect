<script>
import axios from 'axios';
export default {
  data() {
    return {
      speakers: []
    };
  },
  methods: {
    async fetchSpeakers() {
      try {
        const response = await axios.get('http://localhost:8000/api/speakers');
        this.speakers = response.data;
      } catch (error) {
        console.error('Error fetching speakers:', error);
      }
    }
  },
  created() {
    this.fetchSpeakers();
  }
};

</script>
<template>

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
      </div>
    </div>
    <div v-else>
      <p>No speakers available at the moment.</p>
    </div>

</template>
