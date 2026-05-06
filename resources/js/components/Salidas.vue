<template>
  <div class="section-page">
    <div class="section-header">
      <h2>Salidas</h2>
      <button class="btn btn-primary" @click="fetchSalidas">Actualizar</button>
    </div>

    <div v-if="mensaje" :class="['alert', mensajeTipo === 'success' ? 'alert-success' : 'alert-error']">
      {{ mensaje }}
    </div>

    <div class="table-responsive" v-if="salidas.length">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Usuario Email</th>
            <th>Total</th>
            <th>Fecha</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="salida in salidas" :key="salida.id_salida">
            <td>{{ salida.id_salida }}</td>
            <td>{{ salida.usuario_email }}</td>
            <td>{{ salida.total_salida }}</td>
            <td>{{ salida.fecha_salida }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <p v-else class="empty-state">No hay salidas registradas.</p>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      salidas: [],
      mensaje: '',
      mensajeTipo: 'success'
    };
  },
  mounted() {
    this.fetchSalidas();
  },
  methods: {
    async fetchSalidas() {
      try {
        const response = await axios.get('/api/salidas');
        this.salidas = response.data;
        this.mensaje = '';
      } catch (error) {
        this.mensajeTipo = 'error';
        this.mensaje = 'No se pudieron cargar las salidas.';
        console.error(error);
      }
    }
  }
};
</script>