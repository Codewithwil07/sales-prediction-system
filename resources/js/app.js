import "./bootstrap";

import Alpine from "alpinejs";

// --- TAMBAHKAN DARI SINI ---
import { createIcons, icons } from "lucide";

// Panggil createIcons() setelah DOM (halaman) selesai dimuat
document.addEventListener("DOMContentLoaded", () => {
    createIcons({ icons });
});
// --- SAMPAI SINI ---

window.Alpine = Alpine;

Alpine.start();
