<template>
  <DefaultNavbar transparent />
  <Header>
    <div
        class="page-header align-items-start min-vh-100"
        :style="{
        backgroundImage:
          'url(https://images.unsplash.com/photo-1497294815431-9365093b7331?ixlib=rb-1.2.1&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1950&q=80)'
      }"
        loading="lazy"
    >
      <span class="mask bg-gradient-dark opacity-6"></span>


      <div class="container my-auto">
        <div class="row">
          <div class="col-lg-4 col-md-8 col-12 mx-auto">
            <div class="card z-index-0 fadeIn3 fadeInBottom">
              <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <div class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1">
                  <h4 class="text-white font-weight-bolder text-center mt-2 mb-0">
                    Prihlásenie
                  </h4>
                </div>
              </div>
              <div class="card-body">
                <form role="form" class="text-start" @submit.prevent="login">
                  <div class="form-container">
                    <div class="form-group">
                      <div class="input-group input-group-outline my-3">
                        <input class="form-control" type="email" id="email" v-model="email" placeholder="Email" required />
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="input-group input-group-outline my-3">
                        <input class="form-control" type="password" id="password" v-model="password" placeholder="Heslo" required autocomplete="current-password" />
                      </div>
                    </div>
                  </div>
                  <div class="text-center">
                    <MaterialButton class="my-4 mb-2" variant="gradient" color="success" fullWidth type="submit">
                      Prihlásiť sa
                    </MaterialButton>
                  </div>
                  <p v-if="loginError" style="color: red;">{{ loginError }}</p> <!-- Display login error message -->
                </form>
                <p class="mt-4 text-sm text-center">
                  Don't have an account?
                  <router-link to="/pages/landing-pages/signup" class="text-success text-gradient font-weight-bold">
                    Sign up
                  </router-link>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>


      <footer class="footer position-absolute bottom-2 py-2 w-100">
        <div class="container">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-12 col-md-6 my-auto">
              <div
                  class="copyright text-center text-sm text-white text-lg-start"
              >
                © {{ new Date().getFullYear() }}, made with
                <i class="fa fa-heart" aria-hidden="true"></i> by
                <a
                    href="https://www.creative-tim.com"
                    class="font-weight-bold text-white"
                    target="_blank"
                >Creative Tim</a
                >
                for a better web.
              </div>
            </div>
            <div class="col-12 col-md-6">
              <ul
                  class="nav nav-footer justify-content-center justify-content-lg-end"
              >
                <li class="nav-item">
                  <a
                      href="https://www.creative-tim.com"
                      class="nav-link text-white"
                      target="_blank"
                  >Creative Tim</a
                  >
                </li>
                <li class="nav-item">
                  <a
                      href="https://www.creative-tim.com/presentation"
                      class="nav-link text-white"
                      target="_blank"
                  >About Us</a
                  >
                </li>
                <li class="nav-item">
                  <a
                      href="https://www.creative-tim.com/blog"
                      class="nav-link text-white"
                      target="_blank"
                  >Blog</a
                  >
                </li>
                <li class="nav-item">
                  <a
                      href="https://www.creative-tim.com/license"
                      class="nav-link pe-0 text-white"
                      target="_blank"
                  >License</a
                  >
                </li>
              </ul>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </Header>
</template>


<script>
import axios from 'axios';
import Header from "@/examples/Header.vue";
import MaterialInput from "@/components/MaterialInput.vue";
import MaterialButton from "@/components/MaterialButton.vue";
import DefaultNavbar from "@/examples/navbars/NavbarDefault.vue";
import MaterialSwitch from "@/components/MaterialSwitch.vue";

export default {
  name: 'AdminView',
  components: {MaterialSwitch, DefaultNavbar, MaterialButton, MaterialInput, Header},
  data() {
    return {
      email: '',
      password: '',
      loginError: '' // Variable to store login error message
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
        localStorage.setItem('token', token); // Save the token to local storage
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`; // Set the token as the default authorization header for all subsequent requests

        // Check user role and redirect accordingly
        this.checkUserRole();
      } catch (error) {
        this.loginError = 'Používateľ neexistuje.'; // Set login error message
        // Handle login error (e.g., display error message)
      }
    },
    async checkUserRole() {
      try {
        const response = await axios.get('http://localhost:8000/api/user');

        const isAdmin = response.data.is_admin;
        if (isAdmin === 1) {
          this.$router.push({ name: 'admin-dashboard' }); // Redirect to the admin dashboard
        } else {
          this.$router.push({ name: 'user-dashboard' }); // Redirect to the user dashboard
        }
      } catch (error) {

        this.loginError = 'Používateľ neexistuje.'; // Set user does not exist error message
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
