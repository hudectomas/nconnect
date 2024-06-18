<template>
  <div>
    <h1>Vytvoriť novú položku</h1>

    <form @submit.prevent="formData.id ? updateAboutUs() : createAboutUs()">
      <div>
        <label for="photo">Foto:</label>
        <input type="text" id="photo" v-model="formData.photo" required>
      </div>
      <div>
        <label for="quote">Citát:</label>
        <textarea id="quote" v-model="formData.quote" required></textarea>
      </div>
      <button type="submit">{{ formData.id ? 'Aktualizovať' : 'Vytvoriť' }}</button>
    </form>

    <h2>Všetky položky</h2>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Foto</th>
          <th>Citát</th>
          <th>Upraviť</th>
          <th>Vymazať</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in items" :key="item.id">
          <td>{{ item.id }}</td>
          <td>{{ item.photo }}</td>
          <td>{{ item.quote }}</td>
          <td><button @click="editItem(item)">Upraviť</button></td>
          <td><button @click="deleteItem(item.id)">Vymazať</button></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<script setup lang="ts">
import axios from 'axios';
import { ref, onMounted } from 'vue';

const formData = ref({
  id: null,
  photo: '',
  quote: ''
});

const items = ref([]); // To store all items from the database

const fetchItems = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/about-us');
    items.value = response.data;
  } catch (error) {
    console.error('Chyba pri načítavaní položiek:', error);
  }
};

const createAboutUs = async () => {
  try {
    const response = await axios.post('http://localhost:8000/api/about-us', {
      photo: formData.value.photo,
      quote: formData.value.quote,
    });
    console.log('Nová položka bola vytvorená:', response.data);
    formData.value.photo = '';
    formData.value.quote = '';
    await fetchItems(); // Refresh items after creating a new one
  } catch (error) {
    console.error('Chyba pri vytváraní položky:', error);
  }
};

const editItem = (item) => {
  // Populate the form with the item data to edit
  formData.value.id = item.id;
  formData.value.photo = item.photo;
  formData.value.quote = item.quote;
};

const updateAboutUs = async () => {
  try {
    const response = await axios.put(`http://localhost:8000/api/about-us/${formData.value.id}`, {
      photo: formData.value.photo,
      quote: formData.value.quote,
    });
    console.log('Položka bola aktualizovaná:', response.data);
    formData.value.id = null;
    formData.value.photo = '';
    formData.value.quote = '';
    await fetchItems(); // Refresh items after updating
  } catch (error) {
    console.error('Chyba pri aktualizácii položky:', error);
  }
};

const deleteItem = async (itemId) => {
  try {
    await axios.delete(`http://localhost:8000/api/about-us/${itemId}`);
    console.log('Položka bola vymazaná');
    await fetchItems(); // Refresh items after deleting
  } catch (error) {
    console.error('Chyba pri vymazávaní položky:', error);
  }
};

onMounted(fetchItems);
</script>
<style scoped>
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

table, th, td {
  border: 1px solid black;
}

th, td {
  padding: 10px;
  text-align: left;
}

button {
  padding: 5px 10px;
  cursor: pointer;
}

button:hover {
  background-color: #ddd;
}
</style>
