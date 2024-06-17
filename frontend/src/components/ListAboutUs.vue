<template>
    <div>
      <h1>O nás</h1>
      <router-link :to="{ name: 'about-us.create' }">Vytvoriť novú položku</router-link>
  
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Foto</th>
            <th>Citát</th>
            <th>Akcie</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in aboutUs" :key="item.id">
            <td>{{ item.id }}</td>
            <td><img :src="item.photo" alt="Photo"></td>
            <td>{{ item.quote }}</td>
            <td>
              <router-link :to="{ name: 'about-us.edit', params: { id: item.id } }">Upraviť</router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script setup lang="ts">
  import axios from 'axios';
  import { ref, onMounted } from 'vue';
  import { useRouter } from 'vue-router';
  
  const aboutUs = ref([]);
  
  const router = useRouter();
  
  onMounted(async () => {
    try {
      const response = await axios.get('http://localhost:8000/api/about-us');
      aboutUs.value = response.data;
    } catch (error) {
      console.error('Chyba pri načítavaní údajov o nás:', error);
    }
  });
  
  const createLink = router.resolve({ name: 'about-us.create' }).href;
  </script>
  