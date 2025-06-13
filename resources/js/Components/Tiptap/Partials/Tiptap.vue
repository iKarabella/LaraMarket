<script>
  import menuItem from './menuItem.vue';
  import { useForm } from '@inertiajs/vue3';
  import { ref } from 'vue';
  
  export default {
    components: {
      menuItem,
    },
    props: {
      editor: {
        type: Object,
        required: true,
      },
      forminfo: {type:Object, default:{uuid:null, post_id:null, controller:null}}
    },
  
    data() {
      return {
        attachmentImage: null,
        items: [
          {
            icon: 'bold',
            title: 'Жирный',
            action: () => this.editor.chain().focus().toggleBold().run(),
            isActive: () => this.editor ? this.editor.isActive('bold') : '',
          },
          {
            icon: 'italic',
            title: 'Курсив',
            action: () => this.editor.chain().focus().toggleItalic().run(),
            isActive: () => this.editor ? this.editor.isActive('italic') : '',
          },
          {
            icon: 'strikethrough',
            title: 'Зачеркнутый',
            action: () => this.editor.chain().focus().toggleStrike().run(),
            isActive: () => this.editor ? this.editor.isActive('strike') : '',
          },
          {
            type: 'divider',
          },
          {
            icon: 'h-1',
            title: 'Заголовок 1',
            action: () => this.editor.chain().focus().toggleHeading({ level: 1 }).run(),
            isActive: () => this.editor ? this.editor.isActive('heading', { level: 1 }) : '',
          },
          {
            icon: 'h-2',
            title: 'Заголовок 2',
            action: () => this.editor.chain().focus().toggleHeading({ level: 2 }).run(),
            isActive: () => this.editor ? this.editor.isActive('heading', { level: 2 }) : '',
          },
          {
            type: 'divider',
          },
          {
            icon: 'mark-pen-fill',
            title: 'Highlight',
            action: () => this.editor.chain().focus().toggleHighlight({ color: '#ffe8a0' }).run(),
            isActive: () => this.editor ? this.editor.isActive('highlight', { color: '#ffe8a0' }) : '',
          },
          {
            icon: 'folder-image-line',
            title: 'Image',
            action: () => {
                //TODO автозагрузка на сайт и получение ссылки без prompt
                if(this.forminfo.uuid && this.forminfo.controller){
                  this.$refs.file.click();
                }
                else{
                  const url = window.prompt('URL');
                  if (url) this.editor.chain().focus().setImage({ src: url }).run();
                }
            },
          },
        ],
      }
    },
    mounted(){
      if(this.forminfo.uuid){
        this.attachmentImage = useForm({
          attachment:null,
          formuid:this.forminfo.uuid,
          post_id:this.forminfo.post_id
        });
      }
    },
    methods: {
      handleFileUpload(){
        if(this.attachmentImage==null || this.$refs.file.files.length<1) return false;
        this.attachmentImage.attachment = this.$refs.file.files[0];
        this.attachmentImage.post(this.forminfo.controller, {
          preserveScroll: true,
          onSuccess: (response) => {
              if(response.props.attached_file){
                this.editor.chain().focus().setImage({ src: response.props.attached_file }).run();
              }
          },
          onError: () => {
            console.log('error response', this.attachmentImage.errors);
          },
        });
      },
    }
  }
</script>
<template>
    <div class="flex menu-items">
      <template v-for="(item, index) in items">
        <div 
          class="divider" 
          v-if="item.type==='divider'"
          :key="`divider${index}`"
        />
        <menu-item
          v-else
          :key="index"
          v-bind="item"
        />
      </template>
    </div>
    <input type="file" class="hidden" ref="file" v-on:change="handleFileUpload()"/>
</template>
<style scoped>
  .divider {
    background-color: rgba(#fff, 0.25);
    margin-right: 0.25rem;
    width:1px;
    height: 1.5em;
  }
  .menu-items {
    background-color: #5c5c5c;
    border:none;
    padding: 3px;
    display: flex;
  }
</style>