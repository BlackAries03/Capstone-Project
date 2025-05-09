<?php include 'getName(bm).php'; ?>
<!DOCTYPE html>
<html lang="ms">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="sidebarStyle.css">
  <title>BANTUAN</title>
  <style>
    .content {
      margin-left: 300px;
      height: 100vh;
      background: antiquewhite;
    }

    #title {
      display: flex;
      opacity: 1;
    }

    #title h1 {
      margin-left: 10px;
    }

    .container {
      width: 80%;
      margin: 20px auto;
      background: rgb(198, 195, 195);
      padding: 20px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      border-radius: 1rem;
    }

    .dropdown {
      margin-bottom: 10px;
      background: #fff;
      border-radius: 5px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .dropdown h2 {
      margin: 0;
      padding: 10px;
      cursor: pointer;
      background: #f0f0f0;
      border-bottom: 1px solid #ccc;
    }

    .dropdown-content {
      display: none;
      padding: 10px;
    }

    .dropdown-content ol,
    .dropdown-content ul {
      padding-left: 20px;
    }

    .back {
      margin-top: 10px;
    }

    /* Dark theme */
    .dark .content {
      background: #2e2e2e;
    }

    .dark #title h1 {
      color: #e1e1e1;
    }

    .dark .container {
      background: #3c3c3c;
      color: #e1e1e1;
    }

    .dark .dropdown h2 {
      background: #444;
      color: #e1e1e1;
    }

    .dark .dropdown-content {
      background:rgb(79, 77, 77);
    }
  </style>
  <script>
    document.addEventListener('DOMContentLoaded', (event) => {
      const theme = localStorage.getItem('theme') || 'default';
      if (theme === 'dark') {
        document.body.classList.add('dark');
      }

      document.querySelectorAll('.dropdown h2').forEach(header => {
        header.addEventListener('click', () => {
          // Close all dropdown contents
          document.querySelectorAll('.dropdown-content').forEach(content => {
            content.style.display = 'none';
          });

          // Toggle the clicked dropdown content
          const content = header.nextElementSibling;
          content.style.display = content.style.display === 'block' ? 'none' : 'block';
        });
      });
    });
  </script>
</head>

<body>
<?php include("sidebar(bm).php"); ?>

  <div class="content">
    <div id="title">
      <a href="_Settings(bm).php"><img src="picture/back-button.png" alt="kembali" style="width: 30px; height: 30px;" class="back">
      </a>
      <h1>BANTUAN</h1>
    </div>
    <div class="container">
      <div class="dropdown">
        <h2>Mengurus Profil Anda</h2>
        <div class="dropdown-content">
          <h3>1. Mengemas kini Maklumat Profil:</h3>
          <ol>
            <li>Pergi ke halaman <a href="_Settings(bm).php">Tetapan</a> anda.</li>
            <li>Klik pada "Pengurusan Akaun".</li>
            <li>Kemas kini maklumat anda, seperti nama pengguna, gambar profil dan maklumat hubungan anda.</li>
            <li>Klik "Hantar" untuk menerapkan perubahan.</li>
          </ol>

          <h3>2. Memadam Akaun:</h3>
          <ol>
            <li>Pergi ke halaman <a href="_Settings(bm).php">Tetapan</a> anda.</li>
            <li>Pilih "Padam Akaun".</li>
            <li>Sahkan tindakan anda.</li>
          </ol>
        </div>
      </div>

      <div class="dropdown">
        <h2>Menggunakan Platform</h2>
        <div class="dropdown-content">
          <h3>1. Membuat Pos:</h3>
          <ol>
            <li>Klik pada butang "Buat Pos".</li>
            <li>Tulis pos anda dalam kotak teks.</li>
            <li>Tambah sebarang media (gambar, video) dengan mengklik pada butang "Tambah Media".</li>
            <li>Klik "Pos" untuk menerbitkan kandungan anda.</li>
          </ol>

          <h3>2. Berinteraksi dengan Pos:</h3>
          <ul>
            <li><strong>Menyukai Pos:</strong> Klik butang "Suka" di bawah pos.</li>
            <li><strong>Komen:</strong> Klik pada butang "Komen", tulis komen anda, dan klik "Hantar".</li>
            <li><strong>Berkongsi:</strong> Klik butang "Kongsi" untuk berkongsi pos di profil anda atau dengan rakan-rakan.</li>
          </ul>
        </div>
      </div>

      <div class="dropdown">
        <h2>Pemesejan dan Notifikasi</h2>
        <div class="dropdown-content">
          <h3>1. Menghantar Mesej:</h3>
          <ol>
            <li>Pergi ke bahagian "Mesej".</li>
            <li>Klik pada "Mesej Baru".</li>
            <li>Pilih penerima dari senarai rakan anda.</li>
            <li>Tulis mesej anda dan klik "Hantar".</li>
          </ol>

          <h3>2. Mengurus Notifikasi:</h3>
          <ol>
            <li>Pergi ke bahagian "Notifikasi".</li>
            <li>Klik pada ikon tetapan notifikasi.</li>
            <li>Sesuaikan keutamaan notifikasi anda (contohnya, notifikasi e-mel, notifikasi tolak).</li>
            <li>Klik "Simpan" untuk menerapkan perubahan.</li>
          </ol>
        </div>
      </div>

      <div class="dropdown">
        <h2>Keselamatan Akaun</h2>
        <div class="dropdown-content">
          <h3>1. Menukar Kata Laluan:</h3>
          <ol>
            <li>Pergi ke menu "Tetapan".</li>
            <li>Pilih "Tetapan Akaun".</li>
            <li>Klik pada "Tukar Kata Laluan".</li>
            <li>Masukkan kata laluan semasa anda dan kata laluan baru anda.</li>
            <li>Klik "Simpan" untuk mengemas kini kata laluan anda.</li>
          </ol>

          <h3>2. Pengesahan Dua Faktor:</h3>
          <ol>
            <li>Pergi ke menu "Tetapan Keselamatan".</li>
            <li>Pilih "Pengesahan Dua Faktor".</li>
            <li>Ikuti arahan untuk mengaktifkan pengesahan dua faktor untuk akaun anda.</li>
          </ol>
        </div>
      </div>

      <div class="dropdown">
        <h2>Penyelesaian Masalah</h2>
        <div class="dropdown-content">
          <h3>1. Isu Biasa:</h3>
          <ul>
            <li><strong>Tidak dapat log masuk:</strong> Pastikan e-mel dan kata laluan anda betul. Jika anda terlupa kata laluan anda, klik pada "Lupa Kata Laluan" untuk menetapkannya semula.</li>
            <li><strong>Akaun digodam:</strong> Hubungi pasukan sokongan kami dengan segera untuk melindungi akaun anda.</li>
            <li><strong>Ciri tidak berfungsi:</strong> Kosongkan cache pelayar anda dan pastikan anda menggunakan versi terkini pelayar.</li>
          </ul>

          <h3>2. Hubungi Sokongan:</h3>
          <ul>
            <li><a href="mailto:adam@gmail.com"><strong>E-mel:</strong> adam@gmail.com</a></li>
            <li><a href="https://wa.me/+601234567890"><strong>Telefon:</strong> +60 1234567890</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</body>

</html>

