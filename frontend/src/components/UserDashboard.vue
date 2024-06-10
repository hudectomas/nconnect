<template>
  <div id="user-dashboard">
    <h1>User Dashboard</h1>
    <p>Welcome, <b>{{ username }}</b>!</p> <!-- Display user's name from the database -->
    <button @click="logout">Logout</button>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'UserDashboard',
  data() {
    return {
      username: ''
    };
  },
  created() {
    this.checkUser();
  },
  methods: {
    async checkUser() {
      try {
        const token = localStorage.getItem('token');
        if (!token) {
          this.$router.push({ name: 'signin-basic' });
          return;
        }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        const response = await axios.get('http://localhost:8000/api/user');
        if (response && response.data && response.data.name) {
          this.username = response.data.name; // Set username from the database response
        } else {
          console.error('Error: No username found in response');
          this.$router.push({ name: 'user-login' });
        }
      } catch (error) {
        console.error('Access denied:', error);
        localStorage.removeItem('token');
        this.$router.push({ name: 'user-login' });
      }
    },
    logout() {
      localStorage.removeItem('token');
      this.$router.push({ name: 'signin-basic' });
    }
  }
};
</script>
<style scoped>
#user-dashboard {
  width: 300px;
  margin: 0 auto;
}
</style>
