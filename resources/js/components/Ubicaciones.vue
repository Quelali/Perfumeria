<template>
  <div class="section-page">
    <div class="section-header">
      <h2>Ubicaciones</h2>
      <button class="btn btn-primary" @click="fetchUbicaciones">Actualizar</button>
    </div>

    <div v-if="mensaje" :class="['alert', mensajeTipo === 'success' ? 'alert-success' : 'alert-error']">
      {{ mensaje }}
    </div>

    <div class="ubicacion-form-card">
      <h3>Añadir Ubicación</h3>
      <form @submit.prevent="crearUbicacion">
        <div class="form-group">
          <label for="nombre">Nombre</label>
          <input id="nombre" v-model="form.nombre" type="text" required maxlength="100" />
        </div>
        <button type="submit" class="btn btn-primary">Guardar Ubicación</button>
      </form>
    </div>

    <div class="table-responsive" v-if="ubicaciones.length">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="ubicacion in ubicaciones" :key="ubicacion.id_ubicacion">
            <td>{{ ubicacion.id_ubicacion }}</td>
            <td>{{ ubicacion.nombre }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <p v-else class="empty-state">No hay ubicaciones registradas.</p>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      ubicaciones: [],
      form: {
        nombre: ''
      },
      mensaje: '',
      mensajeTipo: 'success'
    };
  },
  mounted() {
    this.fetchUbicaciones();
  },
  methods: {
    async fetchUbicaciones() {
      try {
        const response = await axios.get('/api/ubicaciones');
        this.ubicaciones = response.data.data || response.data;
        this.mensaje = '';
      } catch (error) {
        this.mensajeTipo = 'error';
        this.mensaje = 'No se pudieron cargar las ubicaciones.';
        console.error(error);
      }
    },
    async crearUbicacion() {
      try {
        const response = await axios.post('/api/ubicaciones', this.form);
        console.log('Ubicación creada:', response.data);
        this.mensaje = 'Ubicación creada exitosamente';
        this.mensajeTipo = 'success';
        this.form.nombre = '';
        await this.fetchUbicaciones();
      } catch (error) {
        this.mensajeTipo = 'error';
        this.mensaje = 'Error al crear la ubicación: ' + (error.response?.data?.message || error.message);
        console.error(error);
      }
    }
  }
};
</script>

<style scoped>
.ubicacion-form-card {
  background-color: #f9f9f9;
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 20px;
  margin-bottom: 20px;
}

.ubicacion-form-card h3 {
  margin-top: 0;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

.form-group input {
  width: 100%;
  padding: 8px;
  box-sizing: border-box;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.btn {
  padding: 10px 15px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.btn:hover {
  background-color: #0056b3;
}

.alert {
  padding: 12px 16px;
  margin-bottom: 15px;
  border-radius: 4px;
}

.alert-success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.alert-error {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.empty-state {
  text-align: center;
  color: #999;
  padding: 20px;
}
</style>