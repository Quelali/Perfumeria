<template>
  <div id="app" class="app-container">
    <nav class="app-nav">
      <button class="nav-btn" :class="{ active: currentComponent === 'Usuarios' }" @click="currentComponent = 'Usuarios'">Usuarios</button>
      <button class="nav-btn" :class="{ active: currentComponent === 'Productos' }" @click="currentComponent = 'Productos'">Productos</button>
      <button class="nav-btn" :class="{ active: currentComponent === 'AñadirProducto' }" @click="currentComponent = 'AñadirProducto'">Añadir Producto</button>
      <button class="nav-btn" :class="{ active: currentComponent === 'RegistrarEntrada' }" @click="currentComponent = 'RegistrarEntrada'">Registrar Entrada</button>
      <button class="nav-btn" :class="{ active: currentComponent === 'RegistrarSalida' }" @click="currentComponent = 'RegistrarSalida'">Registrar Salida</button>
      <button class="nav-btn" :class="{ active: currentComponent === 'Ubicaciones' }" @click="currentComponent = 'Ubicaciones'">Ubicaciones</button>
      <button class="nav-btn" :class="{ active: currentComponent === 'Stocks' }" @click="currentComponent = 'Stocks'">Stocks</button>
      <button class="nav-btn" :class="{ active: currentComponent === 'Entradas' }" @click="currentComponent = 'Entradas'">Entradas</button>
      <button class="nav-btn" :class="{ active: currentComponent === 'Salidas' }" @click="currentComponent = 'Salidas'">Salidas</button>
      <button class="nav-btn" :class="{ active: currentComponent === 'Reportes' }" @click="currentComponent = 'Reportes'">Reportes</button>
      <button class="nav-btn logout-btn" @click="logout">Cerrar Sesión</button>
    </nav>
    <component :is="currentComponent" />
  </div>
</template>

<script>
import Usuarios from './components/Usuarios.vue';
import Productos from './components/Productos.vue';
import Ubicaciones from './components/Ubicaciones.vue';
import Stocks from './components/Stocks.vue';
import Entradas from './components/Entradas.vue';
import Salidas from './components/Salidas.vue';
import AñadirProducto from './components/AñadirProducto.vue';
import RegistrarEntrada from './components/RegistrarEntrada.vue';
import RegistrarSalida from './components/RegistrarSalida.vue';
import Reportes from './components/Reportes.vue';

export default {
  components: {
    Usuarios,
    Productos,
    Ubicaciones,
    Stocks,
    Entradas,
    Salidas,
    AñadirProducto,
    RegistrarEntrada,
    RegistrarSalida,
    Reportes
  },
  data() {
    return {
      currentComponent: 'Usuarios'
    };
  },
  mounted() {
    const user = localStorage.getItem('user');
    if (!user) {
      window.location.href = '/login';
    }
  },
  methods: {
    logout() {
      // Crear form para post
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '/logout';
      const csrf = document.createElement('input');
      csrf.type = 'hidden';
      csrf.name = '_token';
      csrf.value = document.querySelector('meta[name="csrf-token"]').content;
      form.appendChild(csrf);
      document.body.appendChild(form);
      form.submit();
    }
  }
};
</script>