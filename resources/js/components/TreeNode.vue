<template>
    <ul>
      <li v-for="node in nodes" :key="node.id">
        <div @click="toggle(node)" class="node">
          {{ node.title }} ({{ node.slug }})
          <span v-if="node.children && node.children.length">[{{ expanded[node.id] ? '-' : '+' }}]</span>
        </div>
        <TreeNode
          v-if="node.children && node.children.length && expanded[node.id]"
          :nodes="node.children"
          @select-page="$emit('select-page', $event)"
        />
      </li>
    </ul>
  </template>
  
  <script>
  import { ref } from 'vue';
  
  export default {
    name: 'TreeNode',
    props: {
      nodes: {
        type: Array,
        required: true,
      },
    },
    setup(props, { emit }) {
      const expanded = ref({});
  
      const toggle = (node) => {
        expanded.value[node.id] = !expanded.value[node.id];
        emit('select-page', node.id);
      };
  
      return {
        expanded,
        toggle,
      };
    },
  };
  </script>
  
  <style scoped>
  ul {
    list-style-type: none;
    padding-left: 20px;
  }
  .node {
    cursor: pointer;
    padding: 5px;
  }
  .node:hover {
    background-color: #f0f0f0;
  }
  </style>