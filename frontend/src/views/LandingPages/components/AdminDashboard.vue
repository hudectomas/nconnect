<template>
  <div id="admin-dashboard">
    <h1>Admin Dashboard</h1>
    <p>Welcome, Admin!</p>
    <button @click="logout">Logout</button>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminDashboard',
  created() {
    this.checkAdmin();
  },
  methods: {
    async checkAdmin() {
      try {
        const token = localStorage.getItem('token');
        if (!token) {
          this.$router.push({ name: 'admin' }); // Presmeruje späť na prihlasovací panel, ak sa nenájde token
          return;
        }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        await axios.post('http://localhost:8000/api/admin'); // Overí platnosť tokenu
      } catch (error) {
        console.error('Access denied:', error);
        localStorage.removeItem('token'); // Odstráni neplatný token
        this.$router.push({ name: 'admin' }); // Presmeruje späť na prihlasovací panel
      }
    },
    logout() {
      localStorage.removeItem('token'); // Odstráni token z lokálneho úložiska
      this.$router.push({ name: 'admin' }); // Presmeruje späť na prihlasovací panel
    }
  }
};
</script>

<style scoped>
#admin-dashboard {
  width: 300px;
  margin: 0 auto;
}
</style>
