<template>
    <div>
      <h1>Vytvoriť novú položku</h1>
  
      <form @submit.prevent="createAboutUs">
        <div>
          <label for="photo">Foto:</label>
          <input type="text" id="photo" v-model="formData.photo" required>
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
    photo: '',
    quote: ''
  });
  
  const createAboutUs = async () => {
    try {
      const response = await axios.post('http://localhost:8000/api/about-us', formData.value);
      console.log('Nová položka bola vytvorená:', response.data);
      formData.value.photo = '';
      formData.value.quote = '';
    } catch (error) {
      console.error('Chyba pri vytváraní položky:', error);
    }
  };
  </script>
  