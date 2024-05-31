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
          this.$router.push({ name: 'admin' }); // Redirect to login if no token
          return;
        }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        await axios.post('http://localhost:8000/api/admin'); // Validate the token
      } catch (error) {
        console.error('Access denied:', error);
        localStorage.removeItem('token'); // Remove invalid token
        this.$router.push({ name: 'admin' }); // Redirect to login
      }
    },
    logout() {
      localStorage.removeItem('token'); // Remove token from local storage
      this.$router.push({ name: 'admin' }); // Redirect to login
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
