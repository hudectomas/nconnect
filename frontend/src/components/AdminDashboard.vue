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
        <th>Sponzor</th> <!-- Nový stĺpec pre zobrazenie sponzora -->
        <th></th> <!-- Pridané pre tlačítko na konci tabuľky -->
      </tr>
      </thead>
      <tbody>
      <tr v-for="user in users" :key="user.id" :class="{ 'sponsor-row': user.is_sponsor }">
        <td>{{ user.id }}</td>
        <td>{{ user.name }}</td> <!-- Meno bude čierne -->
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
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminDashboard',
  data() {
    return {
      users: [] // Array na uloženie informácií o používateľoch
    };
  },
  created() {
    this.checkAdmin();
    this.fetchUsers(); // Zavolá metódu na získanie informácií o používateľoch pri vytvorení komponentu
  },
  methods: {
    async checkAdmin() {
      try {
        const token = localStorage.getItem('token');
        if (!token) {
          this.$router.push({name: 'admin'}); // Presmeruje na prihlásenie, ak nie je token
          return;
        }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        await axios.post('http://localhost:8000/api/admin'); // Overí token
      } catch (error) {
        console.error('Prístup zamietnutý:', error);
        localStorage.removeItem('token'); // Odstráni neplatný token
        this.$router.push({name: 'signin-basic'}); // Presmeruje na prihlásenie
      }
    },
    async fetchUsers() {
      try {
        const response = await axios.get('http://localhost:8000/api/users'); // Získa informácie o všetkých používateľoch z API
        console.log(response.data); // Log the response data
        this.users = response.data; // Uloží informácie o používateľoch do dátovej premennej
      } catch (error) {
        console.error('Chyba pri získavaní informácií o používateľoch:', error);
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
    logout() {
      localStorage.removeItem('token'); // Odstráni token z lokálneho úložiska
      this.$router.push({name: 'signin-basic'}); // Presmeruje na prihlásenie
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
  background-color: #FFFF00; /* Žlté pozadie pre riadok sponzora */
}
</style>
