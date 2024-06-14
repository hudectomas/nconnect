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
    <br><br>
    <h2>Editor</h2>
    <input v-model="pageTitle" placeholder="Zadajte názov podstránky" />
    <Tiptap v-model="pageContent" />
    <button @click="publishContent">Publikovať</button>
  </div>
</template>

<script lang="ts">
import axios from 'axios';

export default {
  name: 'AdminDashboard',
  data() {
    return {
      users: [],
      pageTitle: '',
      pageContent: ''
    };
  },
  created() {
    this.checkAdmin();
    this.fetchUsers();
  },
  methods: {
    async checkAdmin() {
      try {
        const token = localStorage.getItem('token');
        if (!token) {
          this.$router.push({name: 'admin'});
          return;
        }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        await axios.post('http://localhost:8000/api/admin');
      } catch (error) {
        console.error('Prístup zamietnutý:', error);
        localStorage.removeItem('token');
        this.$router.push({name: 'signin-basic'});
      }
    },
    async fetchUsers() {
      try {
        const response = await axios.get('http://localhost:8000/api/users');
        console.log(response.data);
        this.users = response.data;
      } catch (error) {
        console.error('Chyba pri získavaní informácií o používateľoch:', error);
      }
    },
    async setSponsor(userId) {
      try {
        const response = await axios.put(`http://localhost:8000/api/users/${userId}/set-sponsor`);
        console.log(response.data);
      } catch (error) {
        console.error('Chyba pri nastavovaní používateľa ako sponzora:', error);
      }
    },
    logout() {
      localStorage.removeItem('token');
      this.$router.push({name: 'signin-basic'});
    },
    async publishContent() {
      if (!this.pageTitle || !this.pageContent) {
        alert('Prosím, zadajte názov podstránky a obsah.');
        return;
      }

      try {
        const response = await axios.post('http://localhost:8000/api/pages', {
          title: this.pageTitle,
          content: this.pageContent
        });
        console.log('Podstránka bola publikovaná:', response.data);
        this.pageTitle = '';
        this.pageContent = '';
      } catch (error) {
        console.error('Chyba pri publikovaní podstránky:', error);
      }
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
</style>
