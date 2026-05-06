<template>
  <div class="registrar-salida">
    <h3>Registrar Salida</h3>

    <div v-if="message" :class="messageType" class="message">
      {{ message }}
      <button @click="clearMessage" class="close-btn">&times;</button>
    </div>

    <div class="form-group">
      <label for="search-producto">Buscar Producto</label>
      <input
        id="search-producto"
        v-model="searchQuery"
        type="text"
        placeholder="Buscar por código o nombre"
      />
      <div v-if="searchQuery && filteredProductos.length" class="search-results">
        <div
          v-for="producto in filteredProductos"
          :key="producto.id_producto"
          class="result-item"
          @click="selectProducto(producto)"
        >
          {{ producto.nombre_producto }} ({{ producto.codigo }})
        </div>
      </div>
      <div v-if="searchQuery && !filteredProductos.length" class="search-results">
        <div class="result-item empty">No se encontraron productos</div>
      </div>
    </div>

    <div class="form-group">
      <label for="id_ubicacion">Ubicación</label>
      <select id="id_ubicacion" v-model="form.id_ubicacion" @change="clearWarningMessage">
        <option value="">Seleccionar ubicación</option>
        <option v-for="ubicacion in ubicaciones" :key="ubicacion.id_ubicacion" :value="ubicacion.id_ubicacion">
          {{ ubicacion.nombre }}
        </option>
      </select>
    </div>

    <div v-if="items.length" class="items-table-container">
      <table class="items-table">
        <thead>
          <tr>
            <th>Producto</th>
            <th>Código</th>
            <th>Precio unitario</th>
            <th>Cantidad</th>
            <th>Ubicación</th>
            <th>Total</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in items" :key="item.id_producto + '-' + item.id_ubicacion + '-' + index">
            <td>{{ item.nombre_producto }}</td>
            <td>{{ item.codigo }}</td>
            <td>
              <input type="number" step="0.01" min="0" v-model.number="item.precio_unitario" class="item-price" />
            </td>
            <td>
              <input type="number" min="1" v-model.number="item.cantidad" class="item-quantity" />
            </td>
            <td>{{ ubicacionNombre(item.id_ubicacion) }}</td>
            <td>{{ formatoPrecio(item.cantidad * item.precio_unitario) }}</td>
            <td>
              <button class="btn btn-danger" @click="removeItem(index)">Eliminar</button>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="totales">
        <strong>Total general:</strong> {{ formatoPrecio(grandTotal) }}
      </div>

      <button class="btn btn-primary" @click="submitEntries" :disabled="!items.length">
        Registrar salidas
      </button>
    </div>

    <div v-else class="empty-state">
      Selecciona un producto y agrégalo a la tabla para crear la salida.
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'RegistrarSalida',
  data() {
    return {
      form: {
        id_producto: '',
        productoSeleccionado: null,
        id_ubicacion: '',
        cantidad: 1,
        precio_unitario: 0
      },
      productos: [],
      ubicaciones: [],
      items: [],
      user: null,
      searchQuery: '',
      message: '',
      messageType: ''
    };
  },
  computed: {
    filteredProductos() {
      const query = this.searchQuery.trim().toLowerCase();
      if (!query) {
        return [];
      }
      return this.productos.filter((producto) => {
        const codigo = producto.codigo ? String(producto.codigo).toLowerCase() : '';
        const nombre = producto.nombre_producto ? producto.nombre_producto.toLowerCase() : '';
        return codigo.includes(query) || nombre.includes(query);
      });
    },
    grandTotal() {
      return this.items.reduce((sum, item) => sum + item.cantidad * item.precio_unitario, 0);
    }
  },
  async mounted() {
    await this.fetchUser();
    await this.fetchProductos();
    await this.fetchUbicaciones();
  },
  methods: {
    async fetchUser() {
      const user = localStorage.getItem('user');
      if (user) {
        this.user = JSON.parse(user);
      } else {
        this.$parent.currentComponent = 'Login';
      }
    },
    async fetchProductos() {
      try {
        const response = await axios.get('/api/productos');
        const data = Array.isArray(response.data) ? response.data : (response.data.data || []);
        this.productos = data.filter(p => p.id_producto && p.nombre_producto && p.codigo);
      } catch (error) {
        console.error('Error al cargar productos de ct_productos:', error);
        this.message = 'Error al cargar productos';
        this.messageType = 'error';
      }
    },
    async fetchUbicaciones() {
      try {
        const response = await axios.get('/api/ubicaciones');
        const data = Array.isArray(response.data) ? response.data : (response.data.data || []);
        this.ubicaciones = data.filter(u => u.id_ubicacion && u.nombre);
      } catch (error) {
        console.error('Error al cargar ubicaciones de ct_ubicaciones:', error);
        this.message = 'Error al cargar ubicaciones';
        this.messageType = 'error';
      }
    },
    selectProducto(producto) {
      if (!this.form.id_ubicacion) {
        this.message = 'Selecciona una ubicación antes de agregar el producto a la tabla.';
        this.messageType = 'warning';
        return;
      }

      this.form.productoSeleccionado = producto;
      this.form.id_producto = producto.id_producto;
      this.form.precio_unitario = (parseFloat(producto.precio_producto) || 0) * 1.05;
      this.form.cantidad = 1;
      this.searchQuery = '';
      this.addItem();
    },
    ubicacionNombre(id) {
      const ubicacion = this.ubicaciones.find(u => u.id_ubicacion === id);
      return ubicacion ? ubicacion.nombre : 'Sin ubicación';
    },
    formatoPrecio(valor) {
      return Number(valor).toFixed(2);
    },
    clearMessage() {
      this.message = '';
      this.messageType = '';
    },
    clearWarningMessage() {
      if (this.messageType === 'warning') {
        this.message = '';
        this.messageType = '';
      }
    },
    addItem() {
      if (!this.form.productoSeleccionado) {
        this.message = 'Selecciona un producto antes de agregarlo.';
        this.messageType = 'warning';
        return;
      }
      if (!this.form.id_ubicacion) {
        this.message = 'Selecciona una ubicación antes de agregar el producto.';
        this.messageType = 'warning';
        return;
      }

      this.items.push({
        id_producto: this.form.id_producto,
        nombre_producto: this.form.productoSeleccionado.nombre_producto,
        codigo: this.form.productoSeleccionado.codigo,
        precio_unitario: this.form.precio_unitario,
        cantidad: this.form.cantidad,
        id_ubicacion: this.form.id_ubicacion
      });

      this.form.productoSeleccionado = null;
      this.form.id_producto = '';
      this.form.cantidad = 1;
      this.form.precio_unitario = 0;
    },
    removeItem(index) {
      this.items.splice(index, 1);
    },
    async submitEntries() {
      if (!this.items.length) {
        this.message = 'Agrega al menos un producto a la tabla antes de registrar.';
        this.messageType = 'warning';
        return;
      }

      try {
        const detalles = this.items.map(item => ({
          id_producto: item.id_producto,
          id_ubicacion: item.id_ubicacion,
          cantidad: item.cantidad,
          precio_unitario: item.precio_unitario
        }));

        const payload = {
          usuario_email: this.user.Email,
          detalles
        };

        const response = await axios.post('/api/salidas', payload);
        console.log('Salidas registradas:', response.data);
        this.items = [];
        this.form.productoSeleccionado = null;
        this.form.id_producto = '';
        this.form.cantidad = 1;
        this.form.precio_unitario = 0;
        this.searchQuery = '';
        this.message = 'Salidas registradas exitosamente';
        this.messageType = 'success';
      } catch (error) {
        console.error('Error al registrar salidas:', error);
        this.message = 'Error al registrar salidas: ' + (error.response?.data?.message || error.message);
        this.messageType = 'error';
      }
    }
  }
};
</script>

<style scoped>
.registrar-salida {
  padding: 20px;
}

.message {
  padding: 10px 15px;
  margin-bottom: 15px;
  border-radius: 4px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.message.success {
  background-color: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.message.error {
  background-color: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}

.message.warning {
  background-color: #fff3cd;
  color: #856404;
  border: 1px solid #ffeaa7;
}

.close-btn {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
  color: inherit;
}

.form-group {
  margin-bottom: 15px;
  position: relative;
}

label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

input, select {
  width: 100%;
  padding: 8px;
  box-sizing: border-box;
  border: 1px solid #ddd;
  border-radius: 4px;
}

input:focus, select:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

.search-results {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border: 1px solid #ddd;
  border-top: none;
  max-height: 200px;
  overflow-y: auto;
  z-index: 10;
  border-radius: 0 0 4px 4px;
}

.result-item {
  padding: 10px 8px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.result-item:hover:not(.empty) {
  background-color: #f0f0f0;
}

.result-item.empty {
  cursor: default;
  color: #999;
}

.producto-info {
  background-color: #f9f9f9;
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 15px;
  margin-bottom: 20px;
}

.producto-info h4 {
  margin-top: 0;
  margin-bottom: 10px;
  color: #333;
}

.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
  padding-bottom: 8px;
  border-bottom: 1px solid #eee;
}

.info-row:last-child {
  border-bottom: none;
  margin-bottom: 0;
  padding-bottom: 0;
}

.info-row label {
  font-weight: bold;
  margin-bottom: 0;
  min-width: 100px;
}

.info-row span,
.info-row input {
  text-align: right;
  flex: 1;
}

.items-table-container {
  margin-top: 20px;
}

.items-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 15px;
}

.items-table th,
.items-table td {
  border: 1px solid #ddd;
  padding: 10px;
  text-align: left;
}

.items-table th {
  background: #f7f7f7;
}

.item-quantity {
  width: 80px;
  padding: 6px;
}

.item-price {
  width: 100px;
  padding: 6px;
}

.totales {
  margin-bottom: 15px;
  font-size: 1rem;
}

.btn {
  padding: 10px 15px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  width: 100%;
  font-size: 16px;
}

.btn-secondary {
  background-color: #6c757d;
  margin-top: 10px;
}

.btn-danger {
  background-color: #dc3545;
}

.btn:hover:not(:disabled) {
  opacity: 0.95;
}

.btn:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.empty-state {
  margin-top: 20px;
  color: #555;
}
</style>