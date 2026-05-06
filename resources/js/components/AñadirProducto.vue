<template>
    <div class="añadir-producto">
      <h3>Añadir producto</h3>
      
      <div v-if="mensaje" :class="['alert', mensajeTipo === 'success' ? 'alert-success' : 'alert-error']">
        {{ mensaje }}
      </div>

      <form @submit.prevent="submitForm">
        <div class="form-group">
          <label for="codigo">Código</label>
          <input id="codigo" v-model="form.codigo" type="text" required maxlength="50" placeholder="Código único del producto" />
        </div>
        <div class="form-group">
          <label for="nombre_producto">Nombre del producto</label>
          <input id="nombre_producto" v-model="form.nombre_producto" type="text" required maxlength="50" placeholder="Nombre del producto" />
        </div>
        <div class="form-group">
          <label for="descripcion">Descripción</label>
          <input id="descripcion" v-model="form.descripcion" type="text" required maxlength="100" placeholder="Descripción del producto" />
        </div>
        <div class="form-group">
          <label for="precio_producto">Precio</label>
          <input id="precio_producto" v-model.number="form.precio_producto" type="number" step="0.01" min="0" required placeholder="0.00" />
        </div>
        <button type="submit" class="btn btn-primary">Guardar Producto</button>
      </form>

    </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AñadirProducto',
  data() {
    return {
      form: {
        codigo: '',
        nombre_producto: '',
        descripcion: '',
        precio_producto: ''
      },
      productos: [],
      mensaje: '',
      mensajeTipo: 'success'
    };
  },
  mounted() {
    this.fetchProductos();
  },
  methods: {
    async fetchProductos() {
      try {
        const response = await axios.get('/api/productos');
        const data = Array.isArray(response.data) ? response.data : (response.data.data || []);
        this.productos = data.filter(p => p.id_producto && p.nombre_producto && p.codigo);
        console.log('Productos cargados de ct_productos:', this.productos.length);
      } catch (error) {
        console.error('Error al cargar productos de ct_productos:', error);
        this.mensaje = 'Error al cargar la lista de productos';
        this.mensajeTipo = 'error';
      }
    },
    formatoPrecio(valor) {
      return parseFloat(valor).toFixed(2);
    },
    validarFormulario() {
      if (!this.form.codigo.trim()) {
        this.mensaje = 'El código del producto es requerido';
        this.mensajeTipo = 'error';
        return false;
      }
      if (!this.form.nombre_producto.trim()) {
        this.mensaje = 'El nombre del producto es requerido';
        this.mensajeTipo = 'error';
        return false;
      }
      if (!this.form.descripcion.trim()) {
        this.mensaje = 'La descripción del producto es requerida';
        this.mensajeTipo = 'error';
        return false;
      }
      if (this.form.precio_producto <= 0) {
        this.mensaje = 'El precio debe ser mayor a 0';
        this.mensajeTipo = 'error';
        return false;
      }
      return true;
    },
    async submitForm() {
      if (!this.validarFormulario()) {
        return;
      }
      
      try {
        const response = await axios.post('/api/productos', this.form);
        console.log('Producto creado en ct_productos:', response.data);
        
        this.mensaje = 'Producto guardado exitosamente en ct_productos';
        this.mensajeTipo = 'success';
        
        // Resetear el formulario
        this.form = { codigo: '', nombre_producto: '', descripcion: '', precio_producto: '' };
        
        // Recargar la lista de productos
        await this.fetchProductos();
      } catch (error) {
        console.error('Error al crear producto en ct_productos:', error);
        
        if (error.response?.data?.message) {
          this.mensaje = 'Error: ' + error.response.data.message;
        } else if (error.response?.data?.errors) {
          const errors = error.response.data.errors;
          this.mensaje = 'Error: ' + Object.values(errors).flat().join(', ');
        } else {
          this.mensaje = 'Error al guardar el producto en ct_productos';
        }
        this.mensajeTipo = 'error';
      }
    }
  }
};
</script>

<style scoped>
.añadir-producto {
  padding: 20px;
}

h3 {
  margin-top: 0;
  color: #333;
}

h4 {
  color: #333;
  margin-top: 30px;
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

.form-group input:focus {
  outline: none;
  border-color: #007bff;
  box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}

.btn {
  padding: 10px 20px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
  width: 100%;
}

.btn:hover {
  background-color: #0056b3;
}

.btn:active {
  background-color: #004085;
}

.alert {
  padding: 12px 16px;
  margin-bottom: 15px;
  border-radius: 4px;
  border-left: 4px solid;
}

.alert-success {
  background-color: #d4edda;
  color: #155724;
  border-color: #c3e6cb;
}

.alert-error {
  background-color: #f8d7da;
  color: #721c24;
  border-color: #f5c6cb;
}

.productos-list {
  margin-top: 30px;
}

.table-responsive {
  overflow-x: auto;
  margin-top: 15px;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
  border-radius: 4px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.data-table thead {
  background-color: #f8f9fa;
  border-bottom: 2px solid #ddd;
}

.data-table th {
  padding: 12px;
  text-align: left;
  font-weight: bold;
  color: #333;
}

.data-table td {
  padding: 12px;
  border-bottom: 1px solid #ddd;
}

.data-table tbody tr:hover {
  background-color: #f9f9f9;
}

.data-table tbody tr:last-child td {
  border-bottom: none;
}
</style>