<template>
    <div class="container mt-4">

      <h1 class="display-5 fw-bold text-primary mb-4">Nested CMS</h1>
  
  
      <div v-if="currentPage" class="card mb-4 shadow-sm">
        <div class="card-body">
          <h2 class="card-title h4 fw-semibold text-dark">{{ currentPage.title }}</h2>
          <p class="card-text text-muted">{{ currentPage.content }}</p>
        </div>
      </div>
  
  
      <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light">
          <h3 class="h5 fw-semibold mb-0">Page Tree</h3>
        </div>
        <div class="card-body">
          <div v-if="isLoadingPages" class="text-muted">Loading pages...</div>
          <TreeNode v-else-if="pages.length" :nodes="pages" @select-page="loadPageById" />
          <div v-else class="text-muted">No pages available.</div>
        </div>
      </div>
  
      
      <div class="card shadow-sm">
        <div class="card-header bg-light">
          <h3 class="h5 fw-semibold mb-0">{{ isEditing ? 'Edit Page' : 'Add Page' }}</h3>
        </div>
        <div class="card-body">
          <form @submit.prevent="savePage">
            <div class="mb-3">
              <label for="parentPage" class="form-label fw-medium">Parent Page</label>
              <select
                id="parentPage"
                v-model="form.parent_id"
                class="form-select"
              >
                <option :value="null">None (Root)</option>
                <option v-for="page in flatPages" :key="page.id" :value="page.id">
                  {{ page.title }} (ID: {{ page.id }})
                </option>
              </select>
            </div>
            <div class="mb-3">
              <label for="slug" class="form-label fw-medium">Slug</label>
              <input
                id="slug"
                v-model="form.slug"
                type="text"
                class="form-control"
                required
              />
            </div>
            <div class="mb-3">
              <label for="title" class="form-label fw-medium">Title</label>
              <input
                id="title"
                v-model="form.title"
                type="text"
                class="form-control"
                required
              />
            </div>
            <div class="mb-3">
              <label for="content" class="form-label fw-medium">Content</label>
              <textarea
                id="content"
                v-model="form.content"
                class="form-control"
                rows="4"
                required
              ></textarea>
            </div>
            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-primary">
                {{ isEditing ? 'Update' : 'Create' }}
              </button>
              <button
                v-if="isEditing"
                type="button"
                class="btn btn-secondary"
                @click="resetForm"
              >
                Cancel
              </button>
              <button
                v-if="currentPage"
                type="button"
                class="btn btn-danger"
                @click="deletePage"
              >
                Delete
              </button>
            </div>
          </form>
          <div v-if="errorMessage" class="alert alert-danger mt-3" role="alert">
            {{ errorMessage }}
          </div>
          <div v-if="successMessage" class="alert alert-success mt-3" role="alert">
            {{ successMessage }}
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { ref, computed, watch } from 'vue';
  import { useRoute } from 'vue-router';
  import api from '@/services/api'; 
  import TreeNode from '../components/TreeNode.vue';
  
  export default {
    name: 'PageView',
    components: { TreeNode },
    setup() {
    
      const route = useRoute();
      const pages = ref([]);
      const currentPage = ref(null);
      const form = ref({ parent_id: null, slug: '', title: '', content: '' });
      const isEditing = ref(false);
      const isLoadingPages = ref(true);
      const errorMessage = ref('');
      const successMessage = ref('');
  
      const flatPages = computed(() => {
        const flatten = (nodes) => {
          const result = [];
          if (!Array.isArray(nodes)) return result;
          nodes.forEach((node) => {
            result.push(node);
            if (node.children && Array.isArray(node.children)) {
              result.push(...flatten(node.children));
            }
          });
          return result;
        };
        return flatten(pages.value);
      });
  
    
      const fetchPages = async () => {
        try {
          isLoadingPages.value = true;
          const response = await api.getAllPages();
          console.log(response?.data?.data?.data);
          pages.value = Array.isArray(response?.data?.data?.data) ? response.data?.data?.data : [];
        } catch (error) {
          console.error('Error fetching pages:', error);
          pages.value = [];
          errorMessage.value = 'Failed to load pages.';
        } finally {
          isLoadingPages.value = false;
        }
      };
  

      const resolvePage = async () => {
        const path = route.path.slice(1);
        if (!path) {
          currentPage.value = null;
          return;
        }
        try {
          const response = await api.resolvePage(path);
          currentPage.value = response.data.data;
        } catch (error) {
          console.error('Error resolving page:', error);
          currentPage.value = null;
        }
      };
  

      const loadPageById = async (id) => {
        try {
          const response = await api.getPage(id);
          console.log(response.data.data,"response")
          currentPage.value = response.data.data;
          form.value = { ...response.data.data };
          isEditing.value = true;
          errorMessage.value = '';
          successMessage.value = '';
        } catch (error) {
          console.error('Error loading page:', error);
          errorMessage.value = 'Failed to load page.';
        }
      };
  
      const savePage = async () => {
        try {
          if (isEditing.value) {
            await api.updatePage(currentPage.value.id, form.value);
            successMessage.value = 'Page updated successfully!';
          } else {
            await api.createPage(form.value);
            successMessage.value = 'Page created successfully!';
          }
          errorMessage.value = '';
          resetForm();
          await Promise.all([fetchPages(), resolvePage()]);
        } catch (error) {
          console.error('Error saving page:', error);
          errorMessage.value = 'Failed to save page.';
          successMessage.value = '';
        }
      };
  
      const deletePage = async () => {
        if (!confirm('Are you sure you want to delete this page?')) return;
        try {
          await api.deletePage(currentPage.value.id);
          resetForm();
          currentPage.value = null;
          await fetchPages();
          successMessage.value = 'Page deleted successfully!';
          errorMessage.value = '';
        } catch (error) {
          console.error('Error deleting page:', error);
          errorMessage.value = 'Failed to delete page.';
        }
      };
  
      const resetForm = () => {
        form.value = { parent_id: null, slug: '', title: '', content: '' };
        isEditing.value = false;
      };
  
      watch(() => route.path, resolvePage);
  
      fetchPages();
      resolvePage();
  
      return {
        pages,
        currentPage,
        form,
        isEditing,
        isLoadingPages,
        errorMessage,
        successMessage,
        flatPages,
        loadPageById,
        savePage,
        deletePage,
        resetForm,
      };
    },
  };
  </script>
  
  <style scoped>
  /* Minimal custom styles to complement Bootstrap */
  .card {
    border: none;
  }
  
  .card-header {
    border-bottom: 1px solid #e9ecef;
  }
  
  .btn {
    padding: 0.5rem 1.5rem;
  }
  
  .form-control, .form-select {
    border-radius: 0.375rem;
  }
  
  .shadow-sm {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
  }
  </style>