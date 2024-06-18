<template>
  <div>
    <h1>{{ page.title }}</h1>
    <editor-content :editor="editor" class="prose" />
  </div>
</template>

<script>
import { Editor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import axios from 'axios';

export default {
  components: {
    EditorContent,
  },

  data() {
    return {
      editor: null,
      page: {
        title: '',
        content: '',
      },
    };
  },

  async mounted() {
    // Fetch page content based on slug from API
    const slug = this.$route.params.slug; // Assuming slug is passed as a route parameter
    try {
      const response = await axios.get(`http://localhost:8000/api/pages/${slug}`);
      const pageData = response.data;
      console.log(response.data);


      // Initialize Tiptap editor with fetched content
      this.editor = new Editor({
        content: pageData.content,
        extensions: [
          StarterKit,
          // Add any other Tiptap extensions as needed
        ],
      });

      this.page.title = pageData.title;
      this.page.content = pageData.content;

    } catch (error) {
      console.error('Error fetching page:', error);
    }
  },

  beforeUnmount() {
    if (this.editor) {
      this.editor.destroy();
    }
  },
};
</script>

<style>
.prose {
  border: 1px solid #ccc;
  padding: 10px;
  border-radius: 5px;
}
</style>
