<template>
  <div id="admin-panel">
    <h1>Admin Panel</h1>
    <form @submit.prevent="login">
      <div>
        <label for="email">Email:</label>
        <input type="email" id="email" v-model="email" required>
      </div>
      <div>
        <label for="password">Heslo:</label>
        <input type="password" id="password" v-model="password" required autocomplete="current-password">
      </div>
      <button type="submit">Prihlásiť sa</button>
    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminPanel',
  data() {
    return {
      email: '',
      password: ''
    };
  },
  methods: {
  async login() {
    try {
      const response = await axios.post('http://localhost:8000/api/login', {
        email: this.email,
        password: this.password
      });

      const token = response.data.token;
      localStorage.setItem('token', token); // Uloží token do lokálneho úložiska
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`; // Nastaví token ako predvolený authorization header pre všetky ďalšie požiadavky
      this.$router.push({ name: 'AdminDashboard' }); // Presmeruje na administrátorský dashboard
    } catch (error) {
      console.error('Prihlásenie zlyhalo:', error);
    }
  }
}

};
</script>

<style scoped>
#admin-panel {
  width: 300px;
  margin: 0 auto;
}
form div {
  margin-bottom: 10px;
}
</style>
