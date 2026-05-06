<template>
  <div class="section-page">
    <div class="section-header">
      <h2>Stock</h2>
      <button class="btn btn-primary" @click="fetchStocks">Actualizar</button>
    </div>

    <div v-if="mensaje" :class="['alert', mensajeTipo === 'success' ? 'alert-success' : 'alert-error']">
      {{ mensaje }}
    </div>

    <div class="table-responsive" v-if="stocks.length">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Producto</th>
            <th>Ubicación</th>
            <th>Cantidad</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="stock in stocks" :key="stock.id_stock">
            <td>{{ stock.id_stock }}</td>
            <td>{{ stock.producto.nombre_producto }}</td>
            <td>{{ stock.ubicacion.nombre }}</td>
            <td>{{ stock.cantidad }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <p v-else class="empty-state">No hay stock registrado.</p>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      stocks: [],
      mensaje: '',
      mensajeTipo: 'success'
    };
  },
  mounted() {
    this.fetchStocks();
  },
  methods: {
    async fetchStocks() {
      try {
        const response = await axios.get('/api/stocks');
        this.stocks = response.data;
        this.mensaje = '';
      } catch (error) {
        this.mensajeTipo = 'error';
        this.mensaje = 'No se pudo cargar el stock.';
        console.error(error);
      }
    }
  }
};
</script>