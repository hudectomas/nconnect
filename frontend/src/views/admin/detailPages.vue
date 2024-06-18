<template>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-8">
        <h1 class="text-center mb-4">{{ page.title }}</h1>
        <div class="content" v-html="page.content"></div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'PageComponent',
  data() {
    return {
      page: {},
    }
  },
  async created() {
    const slug = this.$route.params.slug
    try {
      const response = await axios.get(`http://localhost:8000/api/page/${slug}`)
      this.page = response.data
    } catch (error) {
      console.error('Error fetching page:', error)
    }
  },
}
</script>

<style>
/* Add any styles you need here */
</style>
