<template>
  <div>
    <h1>Vytvoriť novú položku</h1>

    <form @submit.prevent="createAboutUs">
      <div>
        <label for="photo">Foto:</label>
        <!-- Zmena typu vstupu na file pre nahranie fotografie -->
        <input type="file" id="photo" ref="photoInput" @change="handleFileUpload" accept="image/*" required>
      </div>
      <div>
        <label for="quote">Citát:</label>
        <textarea id="quote" v-model="formData.quote" required></textarea>
      </div>
      <button type="submit">Vytvoriť</button>
    </form>
  </div>
</template>

<script setup lang="ts">
import axios from 'axios';
import { ref } from 'vue';

const formData = ref({
  photo: null, // Uloženie súboru
  quote: ''
});

const createAboutUs = async () => {
  try {
    // Vytvorenie FormData objektu pre odoslanie údajov na server
    const formDataToSend = new FormData();
    formDataToSend.append('photo', formData.value.photo); // Pridanie fotografie do formy
    formDataToSend.append('quote', formData.value.quote); // Pridanie citátu do formy

    const response = await axios.post('http://localhost:8000/api/about-us', formDataToSend);
    console.log('Nová položka bola vytvorená:', response.data);
    formData.value.photo = null; // Resetovanie formy po úspešnom vytvorení
    formData.value.quote = '';
  } catch (error) {
    console.error('Chyba pri vytváraní položky:', error);
  }
};

// Funkcia na spracovanie nahraného súboru
const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    formData.value.photo = file; // Uloženie nahraného súboru do formy
  }
};
</script>
