# Fitur 06 — Deploy dan Operasional

Fase roadmap: 6 dan 7.

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
- Langkah deploy ulang dicatat di README agar bisa diulang tanpa mengingat-ingat.

## Poles
- Meta tag, Open Graph, sitemap, RSS untuk journal.
- Halaman 404 yang dibuat sungguh-sungguh.
- Pemeriksaan Lighthouse, target performa mobile minimal 90.

## Kriteria lolos
Domain dibuka dari jaringan seluler menampilkan situs dengan HTTPS, panel admin tidak bisa dibuka dari luar, dan tautan situs yang dibagikan di WhatsApp menampilkan pratinjau yang rapi.
