<template>
  <div class="reportes">
    <h3>Reportes</h3>

    <div v-if="message" :class="messageType" class="message">
      {{ message }}
      <button @click="clearMessage" class="close-btn">&times;</button>
    </div>

    <div class="report-filters">
      <div class="filter-item">
        <label>Periodo</label>
        <select v-model="periodType" @change="clearWarningMessage">
          <option value="day">Por día</option>
          <option value="week">Por semana</option>
          <option value="month">Por mes</option>
        </select>
      </div>

      <div class="filter-item" v-if="periodType === 'day'">
        <label>Fecha</label>
        <input type="date" v-model="selectedDate" @change="clearWarningMessage" />
      </div>

      <div class="filter-item" v-if="periodType === 'week'">
        <label>Fecha dentro de la semana</label>
        <input type="date" v-model="selectedDate" @change="clearWarningMessage" />
      </div>

      <div class="filter-item" v-if="periodType === 'month'">
        <label>Mes</label>
        <input type="month" v-model="selectedMonth" @change="clearWarningMessage" />
      </div>
    </div>

    <div class="report-summary">
      <div class="summary-card">
        <h4>Total Ganancias</h4>
        <p>{{ formatoPrecio(totalDifference) }}</p>
      </div>
    </div>

    <div class="period-label">
      <strong>Periodo:</strong> {{ periodLabel }}
    </div>

    <div class="report-tables">
      <div class="report-table">
        <h4>Ventas</h4>
        <table>
          <thead>
            <tr>
              <th>ID salida</th>
              <th>Producto</th>
              <th>Cantidad</th>
              <th>Precio entrada</th>
              <th>Precio salida</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="detalle in filteredSalidaDetails" :key="detalle.id_salida + '-' + detalle.id_detalle_salida">
              <td>{{ detalle.id_salida }}</td>
              <td>{{ detalle.producto_nombre }}</td>
              <td>{{ detalle.cantidad }}</td>
              <td>{{ formatoPrecio(detalle.entrada_price) }}</td>
              <td>{{ formatoPrecio(detalle.precio_unitario) }}</td>
              <td>{{ formatoPrecio(detalle.difference_total) }}</td>
            </tr>
            <tr v-if="!filteredSalidaDetails.length">
              <td colspan="6" class="empty-row">No hay ventas en este periodo.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

function parseDateTime(value) {
  if (!value) return null;
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) {
    return new Date(value.replace(' ', 'T'));
  }
  return date;
}

function toLocalDateKey(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}

function parseDateOnly(value) {
  const [year, month, day] = String(value).split('-').map(Number);
  if (!year || !month || !day) return null;
  return new Date(year, month - 1, day);
}

function getWeekRange(date) {
  const day = date.getDay();
  const diffToMonday = (day + 6) % 7;
  const monday = new Date(date);
  monday.setDate(date.getDate() - diffToMonday);
  monday.setHours(0, 0, 0, 0);
  const sunday = new Date(monday);
  sunday.setDate(monday.getDate() + 6);
  sunday.setHours(23, 59, 59, 999);
  return { start: monday, end: sunday };
}

export default {
  name: 'Reportes',
  data() {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    return {
      periodType: 'day',
      selectedDate: `${year}-${month}-${day}`,
      selectedMonth: `${year}-${month}`,
      entradas: [],
      salidas: [],
      message: '',
      messageType: ''
    };
  },
  computed: {
    filteredSalidas() {
      return this.salidas.filter((salida) => this.isInPeriod(parseDateTime(salida.fecha_salida)));
    },
    filteredSalidaDetails() {
      return this.filteredSalidas.flatMap((salida) => {
        const salidaDate = parseDateTime(salida.fecha_salida);
        return (Array.isArray(salida.detalles) ? salida.detalles : []).map((detalle) => {
          const entradaPrice = this.findEntradaPrecio(detalle, salidaDate);
          return {
            ...detalle,
            id_salida: salida.id_salida,
            producto_nombre: detalle.producto?.nombre_producto || detalle.id_producto,
            entrada_price: entradaPrice,
            difference_total: detalle.cantidad * (Number(detalle.precio_unitario || 0) - entradaPrice)
          };
        });
      });
    },
    totalDifference() {
      return this.filteredSalidaDetails.reduce((sum, detalle) => sum + Number(detalle.difference_total || 0), 0);
    },
    periodLabel() {
      if (this.periodType === 'day') {
        return this.formatDate(this.selectedDate);
      }
      if (this.periodType === 'week') {
        const date = parseDateOnly(this.selectedDate);
        if (!date) return '';
        const { start, end } = getWeekRange(date);
        return `${this.formatDate(toLocalDateKey(start))} - ${this.formatDate(toLocalDateKey(end))}`;
      }
      if (this.periodType === 'month') {
        const [year, month] = this.selectedMonth.split('-');
        return `${this.formatMonthLabel(year, month)}`;
      }
      return '';
    }
  },
  async mounted() {
    await this.loadData();
  },
  methods: {
    async loadData() {
      try {
        const [entradasResponse, salidasResponse] = await Promise.all([
          axios.get('/api/entradas'),
          axios.get('/api/salidas')
        ]);
        this.entradas = Array.isArray(entradasResponse.data) ? entradasResponse.data : entradasResponse.data.data || [];
        this.salidas = Array.isArray(salidasResponse.data) ? salidasResponse.data : salidasResponse.data.data || [];
      } catch (error) {
        console.error('Error al cargar reportes:', error);
        this.message = 'No se pudieron cargar las entradas o salidas.';
        this.messageType = 'error';
      }
    },
    isInPeriod(date) {
      if (!date) return false;
      if (this.periodType === 'day') {
        return toLocalDateKey(date) === this.selectedDate;
      }
      if (this.periodType === 'week') {
        const dateInWeek = parseDateOnly(this.selectedDate);
        if (!dateInWeek) return false;
        const { start, end } = getWeekRange(dateInWeek);
        return date >= start && date <= end;
      }
      if (this.periodType === 'month') {
        const [year, month] = this.selectedMonth.split('-');
        return date.getFullYear().toString() === year && String(date.getMonth() + 1).padStart(2, '0') === month;
      }
      return false;
    },
    formatDate(value) {
      if (!value) return '';
      const date = typeof value === 'string' ? parseDateOnly(value) : new Date(value);
      if (!date) {
        return value;
      }
      if (Number.isNaN(date.getTime())) {
        return value;
      }
      return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
    },
    formatMonthLabel(year, month) {
      const date = new Date(`${year}-${month}-01`);
      return date.toLocaleDateString('es-ES', { month: 'long', year: 'numeric' });
    },
    formatDateTime(value) {
      const date = parseDateTime(value);
      if (!date) return '';
      return date.toLocaleString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    formatoPrecio(valor) {
      return Number(valor).toFixed(2);
    },

    findEntradaPrecio(detalleSalida, salidaDate) {
      const candidates = [];
      this.entradas.forEach((entrada) => {
        const entradaDate = parseDateTime(entrada.fecha_entrada);
        if (!entradaDate || entradaDate > salidaDate) {
          return;
        }
        if (!Array.isArray(entrada.detalles)) {
          return;
        }
        entrada.detalles.forEach((detalleEntrada) => {
          if (
            detalleEntrada.id_producto === detalleSalida.id_producto &&
            detalleEntrada.id_ubicacion === detalleSalida.id_ubicacion
          ) {
            candidates.push({
              fecha_entrada: entradaDate,
              precio_unitario: Number(detalleEntrada.precio_unitario || 0)
            });
          }
        });
      });

      if (!candidates.length) {
        return Number(detalleSalida.precio_unitario || 0);
      }

      candidates.sort((a, b) => b.fecha_entrada - a.fecha_entrada);
      return candidates[0].precio_unitario;
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
    }
  }
};
</script>

<style scoped>
.reportes {
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

.report-filters {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
  margin-bottom: 20px;
}

.filter-item {
  display: flex;
  flex-direction: column;
  min-width: 220px;
}

.filter-item label {
  font-weight: bold;
  margin-bottom: 5px;
}

input,
select {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.report-summary {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 16px;
  margin-bottom: 20px;
}

.summary-card {
  background: #f7f7f7;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 16px;
}

.summary-card h4 {
  margin: 0 0 8px;
}

.summary-card p {
  margin: 0;
  font-size: 1.4rem;
  font-weight: bold;
}

.balance-card.positive {
  background: #d4edda;
  border-color: #c3e6cb;
}

.balance-card.negative {
  background: #f8d7da;
  border-color: #f5c6cb;
}

.balance-card.neutral {
  background: #fff3cd;
  border-color: #ffeaa7;
}

.period-label {
  margin-bottom: 16px;
  font-weight: bold;
}

.report-tables {
  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
}

.report-table table {
  width: 100%;
  border-collapse: collapse;
}

.report-table th,
.report-table td {
  border: 1px solid #ddd;
  padding: 10px;
  text-align: left;
}

.report-table th {
  background: #f4f4f4;
}

.empty-row {
  text-align: center;
  color: #777;
}
</style>
