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
                    Sign Up
                  </h4>
                </div>
              </div>
              <div class="card-body">
                <form role="form" class="text-start" @submit.prevent="register">
                  <div class="form-container">
                    <div class="form-group">
                      <div class="input-group input-group-outline my-3">
                        <input class="form-control" v-model="name" id="name" type="text" placeholder="meno" required />
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="input-group input-group-outline my-3">
                        <input class="form-control" v-model="email" id="email" type="email" placeholder="email" required />
                      </div>
                    </div>
                    <div class="form-group">
                      <div class="input-group input-group-outline my-3">
                        <input class="form-control" v-model="password" id="password" type="password" placeholder="heslo" required />
                      </div>
                    </div>
                  </div>
                  <div class="text-center">
                    <MaterialButton class="my-4 mb-2" variant="gradient" color="success" fullWidth type="submit">
                      Sign Up
                    </MaterialButton>
                  </div>
                </form>
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

<script setup>
import { onMounted, ref } from "vue";
import axios from "axios";

// example components
import DefaultNavbar from "@/examples/navbars/NavbarDefault.vue";
import Header from "@/examples/Header.vue";

//Vue Material Kit 2 components
import MaterialInput from "@/components/MaterialInput.vue";
import MaterialButton from "@/components/MaterialButton.vue";

// material-input
import setMaterialInput from "@/assets/js/material-input";

let email = ref('');
let password = ref('');
let name = ref('');

const register = async () => {
  try {

    console.log({
      name: name.value,
      email: email.value,
      password: password.value,
    });

    const response = await axios.post('http://localhost/nconnect/backend/public/api/register', {
      name: name.value,
      email: email.value,
      password: password.value,
    }, {
      headers: {
        'Content-Type': 'application/json'
      }
    });


    console.log("JSON Response", response.data); // Logs the JSON response to the console
  } catch (err) {
    if (err.response) {
      console.error("JSON error Response",err.response.data); // Logs the JSON error response to the console
    } else {
      console.error("other errors",err); // Logs other errors (like network errors) to the console
    }
  }
}

onMounted(() => {
  setMaterialInput();
});
</script>

<style scoped>

.form-container {
  width: 300px;
  display: flex;
  flex-direction: column;
  margin: auto;
}

.form-group {
  margin-bottom: 15px;
}

input {
  padding: 10px;
  border-radius: 5px;
  border: 1px solid #ccc;
}
</style>
