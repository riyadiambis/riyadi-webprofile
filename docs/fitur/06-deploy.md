# Fitur 06 — Deploy dan Operasional

Fase roadmap: 6. Fase pertama yang menyentuh server. Sebelum fase ini, seluruh pekerjaan dilakukan di laptop.

## Prasyarat dari pemilik
- Disk container `web-hosting` di Proxmox dinaikkan ke minimal 30 GiB.
- Domain aktif dan DNS-nya dikelola di Cloudflare.
- Container bisa di-SSH lewat Tailscale.

## Cakupan
- Caddy sebagai web server, HTTPS otomatis.
- Systemd service supaya aplikasi hidup lagi setelah container di-restart.
- Cloudflare Tunnel dari container ke domain. Tanpa port forwarding di router.
- Panel admin dibatasi: hanya lewat jaringan Tailscale atau di belakang Cloudflare Access.
- Skrip backup harian yang menyalin berkas SQLite dan direktori storage ke lokasi lain di server.
- Langkah deploy ulang dicatat di `README.md` agar bisa diulang tanpa mengingat-ingat.

## Di luar cakupan
Meta tag, Open Graph, sitemap, RSS, halaman 404, dan Lighthouse. Semuanya masuk Fase 7, lihat `07-poles.md`.

## Kriteria lolos
Domain dibuka dari jaringan seluler menampilkan situs dengan HTTPS, panel admin tidak bisa dibuka dari luar, dan aplikasi hidup lagi sendiri setelah container di-restart.

Seluruh smoke test dari fase sebelumnya tetap hijau di lingkungan pengembangan.
