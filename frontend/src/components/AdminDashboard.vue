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
<<<<<<< HEAD
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
=======
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
>>>>>>> a815bcfab3f43946a57b96b4e5498779468fdaa9
      </tbody>
    </table>
    <div v-else>
      <p>Zatiaľ nie sú k dispozícii žiadni používatelia.</p>
    </div>
<<<<<<< HEAD
    <br><br>
    <h2>Editor</h2>
    <input v-model="pageTitle" placeholder="Zadajte názov podstránky" />
    <TiptapEditor v-model="pageContent"/>
    <button @click="publishContent">Publikovať</button>
=======

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
          <th>Prihlásení používatelia</th> <!-- Nový stĺpec -->
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
>>>>>>> a815bcfab3f43946a57b96b4e5498779468fdaa9
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminDashboard',
  data() {
    return {
      users: [],
<<<<<<< HEAD
      pageTitle: '',
      pageContent: ''
=======
      sessions: [],
      sessionName: '',
      sessionCapacity: null,
      sessionStartTime: '',
      sessionEndTime: '',
      sessionUsersMap: {}, // Mapa pre ukladanie údajov o používateľoch prihlásených do jednotlivých relácií
>>>>>>> a815bcfab3f43946a57b96b4e5498779468fdaa9
    };
  },
  created() {
    this.checkAdmin();
    this.fetchUsers();
<<<<<<< HEAD
=======
    this.fetchSessions();
    this.fetchSessionUsers(); // Načítanie údajov o používateľoch prihlásených do relácií
>>>>>>> a815bcfab3f43946a57b96b4e5498779468fdaa9
  },
  methods: {
    async checkAdmin() {
      try {
        const token = localStorage.getItem('token');
        if (!token) {
<<<<<<< HEAD
          this.$router.push({name: 'admin'});
=======
          this.$router.push({ name: 'admin' });
>>>>>>> a815bcfab3f43946a57b96b4e5498779468fdaa9
          return;
        }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        await axios.post('http://localhost:8000/api/admin');
      } catch (error) {
        console.error('Prístup zamietnutý:', error);
        localStorage.removeItem('token');
<<<<<<< HEAD
        this.$router.push({name: 'signin-basic'});
=======
        this.$router.push({ name: 'signin-basic' });
>>>>>>> a815bcfab3f43946a57b96b4e5498779468fdaa9
      }
    },
    async fetchUsers() {
      try {
        const response = await axios.get('http://localhost:8000/api/users');
<<<<<<< HEAD
        console.log(response.data);
=======
>>>>>>> a815bcfab3f43946a57b96b4e5498779468fdaa9
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
        // jablko, že dáta sú v tvare [{ session_id: ..., user_id: ...}, ...]
        // Tu môžeš údaje upraviť podľa formátu, v akom sú dáta poskytnuté
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
        console.error('Chyba pri získavaní údajov o prihlásených používateľoch do relácií:', error);
      }
    },
    sessionUsers(sessionId) {
      return this.sessionUsersMap[sessionId] || [];
    },
    async setSponsor(userId) {
      try {
        const response = await axios.put(`http://localhost:8000/api/users/${userId}/set-sponsor`);
        console.log(response.data);
      } catch (error) {
        console.error('Chyba pri nastavovaní používateľa ako sponzora:', error);
      }
    },


    async createSession() {
      try {
        // Implementácia metódy createSession
      } catch (error) {
        console.error('Chyba pri vytváraní session:', error);
      }
    },
    logout() {
      localStorage.removeItem('token');
<<<<<<< HEAD
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
=======
      this.$router.push({ name: 'signin-basic' });
>>>>>>> a815bcfab3f43946a57b96b4e5498779468fdaa9
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
<<<<<<< HEAD
=======
}

.session-form {
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
>>>>>>> a815bcfab3f43946a57b96b4e5498779468fdaa9
}
</style>
