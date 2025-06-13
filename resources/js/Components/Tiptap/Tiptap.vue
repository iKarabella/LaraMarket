<script>
  import Highlight from '@tiptap/extension-highlight'
  import StarterKit from '@tiptap/starter-kit'
  import Image from '@tiptap/extension-image'
  import { Editor, EditorContent } from '@tiptap/vue-3'
  import MenuBar from './Partials/Tiptap.vue'
    
  export default {
      name: 'Tiptap',
      components: {
        EditorContent,
        MenuBar,
        Image,
      },
      props: {
        editor: {
          type: Object,
          required: true,
        },
        content: {
          type: String,
          requred: false,
          default: ""
        },
        eclass: {type:String, default:''},
        forminfo: {type:Object, default:{uuid:null, post_id:null, controller:null}}
      },
      emits: ['updateContent'],
    
      data() {
        return {
          provider: null,
          editor: null,
          status: 'connecting',
          content: this.content,
          editor_class: 'editor '+this.eclass
        }
      },
      beforeUnmount() {
        this.editor.destroy();
      },
      mounted() {
        this.editor = new Editor({
          extensions: [
            StarterKit.configure({ //['paragraph', 'doc', 'text', 'bold', 'italic', 'strike', 'heading']
              history:false,
            }),
            Highlight.configure({
              multicolor: true,
            }),
            Image
          ],
          content: this.content,
          onUpdate: ({ editor }) => {
            this.$emit("updateContent", editor.getHTML());
          }
        })
      },
      methods: {
        //
      },
      beforeUnmount() {
        this.editor.destroy()
      },
  }
</script> 
<template>
    <div>
        <div :class="editor_class">
            <menu-bar 
              class="editor_header"
              :editor="editor"
              :forminfo="forminfo"
            />
            <editor-content 
              class="editor_content"
              :editor="editor"
            />
        </div>
    </div>
</template>
<style>
.ProseMirror{
  padding: 0 10px;
  outline: none;
}
.editor {
  background-color: #fff; 
  border: 3px solid #5c5c5c; 
  border-radius: 0.75rem; 
  color: #5c5c5c; 
  display: flex; 
  flex-direction: column;
}
ul{
      display: block;
      list-style-type: disc;
      margin-block-start: 1em;
      margin-block-end: 1em;
      margin-inline-start: 0px;
      margin-inline-end: 0px;
      padding-inline-start: 40px;
      padding: 0 1rem;
}
ol{
      display: block;
      list-style-type: decimal;
      margin-block-start: 1em;
      margin-block-end: 1em;
      margin-inline-start: 0px;
      margin-inline-end: 0px;
      padding-inline-start: 40px;
      padding: 0 1rem;
}
  
    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
      line-height: 1.1;
    }
    h1 {
      display: block;
      font-size: 2em;
      margin-block-start: 0.67em;
      margin-block-end: 0.67em;
      margin-inline-start: 0px;
      margin-inline-end: 0px;
      font-weight: bold
    }
    h2{
      display: block;
      font-size: 1.5em;
      margin-block-start: 0.83em;
      margin-block-end: 0.83em;
      margin-inline-start: 0px;
      margin-inline-end: 0px;
      font-weight: bold
    }
    p {
      display: block;
      margin-block-start: 1em;
      margin-block-end: 1em;
      margin-inline-start: 0px;
      margin-inline-end: 0px;
      font-size: 16px;
    }
    code {
      background-color: rgba(#616161, 0.1);
      color: #616161;
    }
  
    img {
      max-width: 100%;
      height: auto;
    }
  
    blockquote {
      padding-left: 1rem;
      border-left: 2px solid rgba(#0D0D0D, 0.1);
    }
  
    hr {
      border: none;
      border-top: 2px solid rgba(#0D0D0D, 0.1);
      margin: 2rem 0;
    }
</style>