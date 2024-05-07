import { defineStore } from "pinia";
import bootstrap from "bootstrap/dist/js/bootstrap.min.js";

export const useAppStore = defineStore("index", {
  state: () => ({
    bootstrap,
    token: null,
    user: null,
    isAdmin: false,
  }),
  actions: {
    setToken(token) {
      this.token = token;
    },
    setUser(user) {
      this.user = user;
      this.isAdmin = user.role === 'admin';
    },
    logout() {
      this.token = null;
      this.user = null;
      this.isAdmin = false;
    },
  },
});
