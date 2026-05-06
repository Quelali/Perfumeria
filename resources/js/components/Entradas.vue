<template>
  <div class="section-page">
    <div class="section-header">
      <h2>Entradas</h2>
      <button class="btn btn-primary" @click="fetchEntradas">Actualizar</button>
    </div>

    <div v-if="mensaje" :class="['alert', mensajeTipo === 'success' ? 'alert-success' : 'alert-error']">
      {{ mensaje }}
    </div>

    <div class="table-responsive" v-if="entradas.length">
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
          <tr v-for="entrada in entradas" :key="entrada.id_entrada">
            <td>{{ entrada.id_entrada }}</td>
            <td>{{ entrada.usuario_email }}</td>
            <td>{{ entrada.total_entrada }}</td>
            <td>{{ entrada.fecha_entrada }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <p v-else class="empty-state">No hay entradas registradas.</p>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      entradas: [],
      mensaje: '',
      mensajeTipo: 'success'
    };
  },
  mounted() {
    this.fetchEntradas();
  },
  methods: {
    async fetchEntradas() {
      try {
        const response = await axios.get('/api/entradas');
        this.entradas = response.data;
        this.mensaje = '';
      } catch (error) {
        this.mensajeTipo = 'error';
        this.mensaje = 'No se pudieron cargar las entradas.';
        console.error(error);
      }
    }
  }
};
</script>