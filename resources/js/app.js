import "./bootstrap";

import Alpine from "alpinejs";

// --- TAMBAHKAN DARI SINI ---
import { createIcons, icons } from "lucide";

import Toastify from "toastify-js";
window.Toastify = Toastify;

// TAMBAHKAN DUA BARIS INI
import Chart from "chart.js/auto"; 
window.Chart = Chart;

// Panggil createIcons() setelah DOM (halaman) selesai dimuat
document.addEventListener("DOMContentLoaded", () => {
    createIcons({ icons });
});
// --- SAMPAI SINI ---

window.Alpine = Alpine;

Alpine.start();
