<template>
  <div>
    <h1>Upraviť položku</h1>

    <form @submit.prevent="updateAboutUs">
      <div>
        <label for="photo">Foto:</label>
        <input type="text" id="photo" v-model="formData.photo" required>
      </div>
      <div>
        <label for="quote">Citát:</label>
        <textarea id="quote" v-model="formData.quote" required></textarea>
      </div>
      <button type="submit">Uložiť zmeny</button>
    </form>
  </div>
</template>

<script setup lang="ts">
import axios from 'axios';
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';

const formData = ref({
  photo: '',
  quote: ''
});

const route = useRoute();

onMounted(async () => {
  try {
    const response = await axios.get(`http://localhost:8000/api/about-us/${route.params.id}`);
    formData.value = response.data;
  } catch (error) {
    console.error('Chyba pri načítavaní údajov o položke:', error);
  }
});

const updateAboutUs = async () => {
  try {
    await axios.put(`http://localhost:8000/api/about-us/${route.params.id}`, formData.value);
    console.log('Položka bola úspešne upravená:', formData.value);
  } catch (error) {
    console.error('Chyba pri úprave položky:', error);
  }
};
</script>
