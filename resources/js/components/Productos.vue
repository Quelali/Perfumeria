<template>
  <div class="productos-page">
    <div class="productos-header">
      <div>
        <h2>Productos</h2>
        <p class="subtitle">Buscar por código o nombre</p>
      </div>
      <div class="search-group">
        <input class="search-input" type="text" v-model="searchQuery" placeholder="Buscar código o nombre" />
        <button class="btn btn-secondary" type="button" @click="fetchProductos">Actualizar</button>
        <button class="btn btn-primary" type="button" @click="$parent.currentComponent = 'AñadirProducto'">Añadir Producto</button>
      </div>
    </div>

    <div v-if="mensaje" :class="['alert', mensajeTipo === 'success' ? 'alert-success' : 'alert-error']">
      {{ mensaje }}
    </div>

    <div class="table-responsive" v-if="filteredProductos.length">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Código</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Precio</th>
            <th>Opciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="producto in filteredProductos" :key="producto.id_producto">
            <td>{{ producto.id_producto }}</td>
            <td>{{ producto.codigo }}</td>
            <td>{{ producto.nombre_producto }}</td>
            <td>{{ producto.descripcion }}</td>
            <td>{{ formatoPrecio(producto.precio_producto) }}</td>
            <td>
              <div class="action-buttons">
                <button class="btn btn-secondary btn-sm" type="button" @click="abrirEdicion(producto)">Modificar</button>
                <button class="btn btn-info btn-sm" type="button" @click="abrirStock(producto)">Stock</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <p v-else class="empty-state">
      {{ productos.length ? 'No se encontraron productos con ese criterio.' : 'No hay productos registrados.' }}
    </p>

    <div v-if="mostrarEdicion" class="modal-backdrop" @click.self="cerrarEdicion">
      <div class="modal-card">
        <div class="modal-header">
          <div>
            <h3>Modificar producto</h3>
            <p class="subtitle">Los datos se cargan desde el producto seleccionado.</p>
          </div>
          <button class="btn btn-secondary btn-sm" type="button" @click="cerrarEdicion">Cerrar</button>
        </div>

        <div v-if="edicionMensaje" :class="['alert', edicionMensajeTipo === 'success' ? 'alert-success' : 'alert-error', 'modal-alert']">
          {{ edicionMensaje }}
        </div>

        <form @submit.prevent="actualizarProducto">
          <div class="form-group">
            <label for="editar_codigo">Código</label>
            <input id="editar_codigo" v-model="formEdit.codigo" type="text" required maxlength="50" />
          </div>
          <div class="form-group">
            <label for="editar_nombre_producto">Nombre del producto</label>
            <input id="editar_nombre_producto" v-model="formEdit.nombre_producto" type="text" required maxlength="50" />
          </div>
          <div class="form-group">
            <label for="editar_descripcion">Descripción</label>
            <input id="editar_descripcion" v-model="formEdit.descripcion" type="text" required maxlength="100" />
          </div>
          <div class="form-group">
            <label for="editar_precio_producto">Precio</label>
            <input id="editar_precio_producto" v-model.number="formEdit.precio_producto" type="number" step="0.01" min="0" required />
          </div>

          <div class="modal-actions">
            <button class="btn btn-secondary" type="button" @click="cerrarEdicion">Cancelar</button>
            <button class="btn btn-primary" type="submit">Guardar cambios</button>
          </div>
        </form>
      </div>
    </div>

    <div v-if="mostrarStock" class="modal-backdrop" @click.self="cerrarStock">
      <div class="modal-card modal-card-wide">
        <div class="modal-header">
          <div>
            <h3>Stock del producto</h3>
            <p class="subtitle">{{ productoStock ? productoStock.nombre_producto : 'Producto seleccionado' }}</p>
          </div>
          <button class="btn btn-secondary btn-sm" type="button" @click="cerrarStock">Cerrar</button>
        </div>

        <div v-if="stockMensaje" :class="['alert', stockMensajeTipo === 'success' ? 'alert-success' : 'alert-error', 'modal-alert']">
          {{ stockMensaje }}
        </div>

        <div v-if="cargandoStock" class="stock-loading">Cargando stock...</div>
        <div v-else-if="stockDelProducto.length" class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>ID Stock</th>
                <th>Ubicación</th>
                <th>Cantidad</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="stock in stockDelProducto" :key="stock.id_stock">
                <td>{{ stock.id_stock }}</td>
                <td>{{ stock.ubicacion ? stock.ubicacion.nombre : 'Sin ubicación' }}</td>
                <td>{{ stock.cantidad }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <p v-else class="empty-state modal-empty">No hay stock registrado para este producto.</p>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      productos: [],
      stocks: [],
      searchQuery: '',
      mensaje: '',
      mensajeTipo: 'success',
      edicionMensaje: '',
      edicionMensajeTipo: 'success',
      stockMensaje: '',
      stockMensajeTipo: 'success',
      mostrarEdicion: false,
      mostrarStock: false,
      cargandoStock: false,
      productoStock: null,
      formEdit: {
        id_producto: null,
        codigo: '',
        nombre_producto: '',
        descripcion: '',
        precio_producto: ''
      }
    };
  },
  mounted() {
    this.fetchProductos();
  },
  computed: {
    filteredProductos() {
      const query = this.searchQuery.trim().toLowerCase();
      if (!query) {
        return this.productos;
      }

      return this.productos.filter((producto) => {
        const codigo = producto.codigo ? String(producto.codigo).toLowerCase() : '';
        const nombre = producto.nombre_producto ? producto.nombre_producto.toLowerCase() : '';
        return codigo.includes(query) || nombre.includes(query);
      });
    },
    stockDelProducto() {
      if (!this.productoStock) {
        return [];
      }

      const productoId = Number(this.productoStock.id_producto);
      return this.stocks.filter((stock) => Number(stock.id_producto) === productoId);
    }
  },
  methods: {
    async fetchProductos() {
      try {
        const response = await axios.get('/api/productos');
        this.productos = Array.isArray(response.data) ? response.data : [];
      } catch (error) {
        this.mensajeTipo = 'error';
        this.mensaje = 'No se pudo cargar la lista de productos.';
        console.error(error);
      }
    },
    async fetchStocks() {
      try {
        const response = await axios.get('/api/stocks');
        this.stocks = Array.isArray(response.data) ? response.data : [];
        return this.stocks;
      } catch (error) {
        this.stockMensajeTipo = 'error';
        this.stockMensaje = 'No se pudo cargar el stock del producto.';
        console.error(error);
        return [];
      }
    },
    abrirEdicion(producto) {
      this.edicionMensaje = '';
      this.formEdit = {
        id_producto: producto.id_producto,
        codigo: producto.codigo || '',
        nombre_producto: producto.nombre_producto || '',
        descripcion: producto.descripcion || '',
        precio_producto: producto.precio_producto ?? ''
      };
      this.mostrarEdicion = true;
    },
    cerrarEdicion() {
      this.mostrarEdicion = false;
    },
    async actualizarProducto() {
      try {
        const payload = {
          codigo: this.formEdit.codigo,
          nombre_producto: this.formEdit.nombre_producto,
          descripcion: this.formEdit.descripcion,
          precio_producto: this.formEdit.precio_producto
        };

        await axios.put(`/api/productos/${this.formEdit.id_producto}`, payload);
        this.edicionMensajeTipo = 'success';
        this.edicionMensaje = 'Producto modificado correctamente.';
        this.cerrarEdicion();
        await this.fetchProductos();
      } catch (error) {
        this.edicionMensajeTipo = 'error';
        if (error.response && error.response.data) {
          if (error.response.data.errors) {
            this.edicionMensaje = Object.values(error.response.data.errors).flat().join(' ');
          } else if (error.response.data.message) {
            this.edicionMensaje = error.response.data.message;
          } else {
            this.edicionMensaje = 'No se pudo modificar el producto.';
          }
        } else {
          this.edicionMensaje = 'No se pudo modificar el producto.';
        }
        console.error(error);
      }
    },
    async abrirStock(producto) {
      this.productoStock = producto;
      this.mostrarStock = true;
      this.cargandoStock = true;
      this.stockMensaje = '';

      try {
        if (!this.stocks.length) {
          await this.fetchStocks();
        }
      } finally {
        this.cargandoStock = false;
      }
    },
    cerrarStock() {
      this.mostrarStock = false;
      this.productoStock = null;
      this.cargandoStock = false;
      this.stockMensaje = '';
    },
    formatoPrecio(valor) {
      return parseFloat(valor).toFixed(2);
    }
  }
};
</script>

<style scoped>
.action-buttons {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.btn-sm {
  padding: 8px 12px;
  font-size: 14px;
  width: auto;
}

.btn-info {
  background-color: #0f766e;
  color: #fff;
}

.btn-info:hover {
  background-color: #115e59;
}

.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15, 23, 42, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  z-index: 50;
}

.modal-card {
  width: min(640px, 100%);
  background: #fff;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 24px 60px rgba(15, 23, 42, 0.25);
}

.modal-card-wide {
  width: min(840px, 100%);
}

.modal-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 16px;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 18px;
}

.modal-alert {
  margin-bottom: 16px;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
  color: #333;
}

.form-group input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  box-sizing: border-box;
  font-size: 14px;
}

.stock-loading {
  padding: 12px 0;
  color: #334155;
}

.modal-empty {
  margin-top: 8px;
}

@media (max-width: 768px) {
  .modal-header,
  .modal-actions {
    flex-direction: column;
    align-items: stretch;
  }

  .modal-actions .btn,
  .modal-header .btn {
    width: 100%;
  }
}
</style>