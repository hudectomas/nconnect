<template>
  <div id="user-dashboard">
    <h1>User Dashboard</h1>
    <p>Welcome, <b>{{ username }}</b></p>

    <p v-if="isSponsor"><b>{{ 'Tento chlapec je SPONZOROM' }}</b></p>
    <button :style="{ backgroundColor: isUserLoggedIn ? '#FF5733' : '#4CAF50' }" @click="logout">
      {{ isUserLoggedIn ? 'Odhlásiť sa' : 'Prihlásiť sa' }}
    </button>

    <!-- Zobrazenie všetkých sessions -->
    <div v-if="sessions.length > 0">
      <h2>Všetky Sessions</h2>
      <table>
        <thead>
        <tr>
          <th>Session Name</th>
          <th>Start Time</th>
          <th>End Time</th>
          <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="session in sessions" :key="session.id">
          <td>{{ session.session_name }}</td>
          <td>{{ session.start_time }}</td>
          <td>{{ session.end_time }}</td>
          <td>
            <button :style="{ backgroundColor: isUserRegistered(session) ? '#FF5733' : '#4CAF50' }" @click="prihlasitSa(session)">
              {{ isUserRegistered(session) ? 'Odhlásiť sa' : 'Registrovať sa' }}
            </button>
          </td>
        </tr>
        </tbody>
      </table>
    </div>

    <div v-else>
      <p>No sessions available.</p>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'UserDashboard',
  data() {
    return {
      username: '',
      userId: '',
      sponsor: '',
      isSponsor: false,
      sessions: [],
      isUserLoggedIn: false // Pridanie premennej pre označenie, či je používateľ prihlásený
    };
  },
  created() {
    this.checkUser();
    this.fetchSessions();
  },
  methods: {
    async checkUser() {
      try {
        const token = localStorage.getItem('token');
        if (!token) {
          this.$router.push({name: 'signin-basic'});
          return;
        }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        const response = await axios.get('http://localhost:8000/api/user');
        if (response && response.data && response.data.name) {
          this.username = response.data.name;
          this.userId = response.data.id;
          if (response.data.is_sponsor) {
            this.sponsor = response.data.name;
            this.isSponsor = true;
          }
          this.isUserLoggedIn = true; // Nastavenie premennej na true, ak používateľ je prihlásený
        } else {
          console.error('Error: No username found in response');
          this.$router.push({name: 'user-login'});
        }
      } catch (error) {
        console.error('Access denied:', error);
        localStorage.removeItem('token');
        this.$router.push({name: 'user-login'});
      }
    },
    async fetchSessions() {
      try {
        const response = await axios.get('http://localhost:8000/api/session');
        this.sessions = response.data;
      } catch (error) {
        console.error('Error fetching sessions:', error);
      }
    },
    async prihlasitSa(session) {
      try {
        const token = localStorage.getItem('token');
        if (!token) {
          this.$router.push({name: 'signin-basic'});
          return;
        }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

        const response = await axios.post('http://localhost:8000/api/session_users', {
          session_id: session.id,
          user_id: this.userId,
          session_name: session.session_name,
          user_name: this.username,
          capacity: session.capacity
        });

        if (response.data.success) {
          session.users = response.data.users;
          session.registered = true; // Aktualizácia stavu registrácie používateľa
        }
      } catch (error) {
        console.error('Error logging into session:', error);
      }
    },
    logout() {
      localStorage.removeItem('token');
      this.$router.push({name: 'signin-basic'});
    },
    isUserRegistered(session) {
      return session.registered === true;
    }
  }
};
</script>

<style scoped>
#user-dashboard {
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

button {
  color: white;
  padding: 8px 10px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

button:hover {
  background-color: #45a049;
}
</style>
