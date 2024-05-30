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
            this.$router.push({ name: 'AdminPanel' });
          }
          axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
          await axios.post('/api/admin');
        } catch (error) {
          console.error('Access denied:', error);
          this.$router.push({ name: 'AdminPanel' });
        }
      },
      logout() {
        localStorage.removeItem('token');
        this.$router.push({ name: 'AdminPanel' });
      }
    }
  }
  </script>
  
  <style scoped>
  #admin-dashboard {
    width: 300px;
    margin: 0 auto;
  }
  </style>
  