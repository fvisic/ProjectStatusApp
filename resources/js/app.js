import './bootstrap';
import './webauthn';
import Sortable from 'sortablejs';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

window.Sortable = Sortable;
window.Chart = Chart;
