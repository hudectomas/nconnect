<template>
  <div id="user-dashboard">
    <h1>User Dashboard</h1>
    <p>Welcome, <b>{{ username }}</b></p>
    <p v-if="isSponsor"><b>{{'Tento chlapec je SPONZOROM' }}</b></p>
    <button @click="logout">Logout</button>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'UserDashboard',
  data() {
    return {
      username: '',
      sponsor: '',
      isSponsor: false // Add isSponsor flag
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
          this.$router.push({name: 'signin-basic'});
          return;
        }
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        const response = await axios.get('http://localhost:8000/api/user');
        console.log(response.data); // Log response data for debugging
        if (response && response.data && response.data.name) {
          this.username = response.data.name;
          if (response.data.is_sponsor) {
            this.sponsor = response.data.name; // Set sponsor's name
            this.isSponsor = true; // Set isSponsor to true
          }
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
    logout() {
      localStorage.removeItem('token');
      this.$router.push({name: 'signin-basic'});
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
