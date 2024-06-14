<template>
  <div>
    <div class="toolbar">
      <button @click="setHeading(1)">H1</button>
      <button @click="setHeading(2)">H2</button>
      <button @click="setParagraph">P</button>
      <input type="color" @input="setColor($event.target.value)" />
    </div>
    <editor-content :editor="editor" />
  </div>
</template>

<script>
import { Editor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Heading from '@tiptap/extension-heading'
import TextStyle from '@tiptap/extension-text-style'
import Color from '@tiptap/extension-color'

export default {
  components: {
    EditorContent,
  },

  props: {
    modelValue: {
        type: String,
        default: '',
    },
  },
  emits: ['update:modelValue'],

  data() {
    return {
      editor: null,
    }
  },

  mounted() {
    this.editor = new Editor({
      content: '<p>I’m running Tiptap with Vue.js. 🎉</p>',
      extensions: [
        StarterKit,
        Heading.configure({
          levels: [1, 2, 3, 4, 5, 6],
        }),
        TextStyle,
        Color,
      ],
      onUpdate: () => {
          this.$emit('update:modelValue', this.editor.getHTML())
      }
    })
  },

  beforeUnmount() {
    this.editor.destroy()
  },

  methods: {
    setHeading(level) {
      this.editor.chain().focus().toggleHeading({ level }).run()
    },
    setParagraph() {
      this.editor.chain().focus().setParagraph().run()
    },
    setColor(color) {
      this.editor.chain().focus().setColor(color).run()
    },
  },
  watch: {
    modelValue(value) {
        const isSame = this.editor.getHTML() === value;
        if (isSame) {
            return;
        }
        this.editor.commands.setContent(value, false);
    },
  },
}
</script>

<style>
.toolbar {
  margin-bottom: 10px;
}
.toolbar button {
  margin-right: 5px;
}
</style>
