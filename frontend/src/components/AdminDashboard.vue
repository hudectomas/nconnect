<template>
  <div id="admin-dashboard">
    <h1>Admin Dashboard</h1>
    <p>Welcome, Admin!</p>
    <button @click="logout">Logout</button>
  </div>
</template>

<script>
import axios from 'axios';
import Featuring from "@/views/LandingPages/AboutUs/Sections/AboutFeaturing.vue";
import DefaultFooter from "@/examples/footers/FooterDefault.vue";
import Newsletter from "@/views/LandingPages/AboutUs/Sections/AboutNewsletter.vue";
import AboutTeam from "@/views/LandingPages/AboutUs/Sections/AboutTeam.vue";
import DefaultNavbar from "@/examples/navbars/NavbarDefault.vue";
import Information from "@/views/LandingPages/AboutUs/Sections/AboutInformation.vue";
import bg0 from "@/assets/img/bg9.jpg";
import TheWelcome from "@/components/TheWelcome.vue";
import {RouterView} from "vue-router";

export default {
  name: 'AdminDashboard',
  computed: {
    bg0() {
      return bg0
    }
  },
  components: {RouterView, TheWelcome, Information, DefaultNavbar, AboutTeam, Newsletter, DefaultFooter, Featuring},
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
        this.$router.push({ name: 'signin-basic' }); // Redirect to login
      }
    },
    logout() {
      localStorage.removeItem('token'); // Remove token from local storage
      this.$router.push({ name: 'signin-basic' }); // Redirect to login
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
