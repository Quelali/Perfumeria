<template>
  <div class="section-page">
    <div class="section-header">
      <h2>Usuarios</h2>
      <button class="btn btn-primary" @click="fetchUsuarios">Actualizar</button>
    </div>

    <div v-if="mensaje" :class="['alert', mensajeTipo === 'success' ? 'alert-success' : 'alert-error']">
      {{ mensaje }}
    </div>

    <div class="table-responsive" v-if="usuarios.length">
      <table class="data-table">
        <thead>
          <tr>
            <th>Email</th>
            <th>Nombre</th>
            <th>Permisos</th>
            <th>Fecha inicio</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="usuario in usuarios" :key="usuario.Email">
            <td>{{ usuario.Email }}</td>
            <td>{{ usuario.nombre_usuario }}</td>
            <td>{{ usuario.nivel_permisos }}</td>
            <td>{{ usuario.fecha_inicio }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <p v-else class="empty-state">No hay usuarios registrados.</p>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      usuarios: [],
      mensaje: '',
      mensajeTipo: 'success'
    };
  },
  mounted() {
    this.fetchUsuarios();
  },
  methods: {
    async fetchUsuarios() {
      try {
        const response = await axios.get('/api/usuarios');
        this.usuarios = response.data;
        this.mensaje = '';
      } catch (error) {
        this.mensajeTipo = 'error';
        this.mensaje = 'No se pudieron cargar los usuarios.';
        console.error(error);
      }
    }
  }
};
</script>