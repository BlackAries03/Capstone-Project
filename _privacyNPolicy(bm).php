<?php include 'getName(bm).php'; ?>
<!DOCTYPE html>
<html lang="ms">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="sidebarStyle.css">
  <title>Privasi dan Dasar</title>
  <style>
    .content {
        margin-left: 300px;
        height: 170vh;
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
        height: auto;
        width: 80%;
        margin-top: 20px;
        margin-left: auto;
        margin-right: auto;
        background: rgb(198, 195, 195);
        padding: 60px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 1rem;
    }

    .back {
        margin-top: 10px;
    }

    /* Dark theme styles */
    .dark .content {
        background: #2e2e2e; /* Dark background color */
    }

    .dark #title {
        opacity: 1;
    }

    .dark #title h1 {
        margin-left: 10px;
        color: #e1e1e1; /* Dark theme text color */
    }

    .dark .container {
        background: #3c3c3c; /* Dark container background */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5); /* Darker box shadow */
        color: #e1e1e1; /* Dark theme text color */
    }

    .dark .back {
        margin-top: 10px;
        color: #e1e1e1; /* Dark theme text color */
    }
  </style>
  <script>
    document.addEventListener('DOMContentLoaded', (event) => {
      const theme = localStorage.getItem('theme') || 'default';
      if (theme === 'dark') {
        document.body.classList.add('dark');
      }
    });
  </script>
</head>

<body>
<?php include("sidebar(bm).php"); ?>

  <div class="content">
    <div id="title">
      <a href="_Settings(bm).php"><img src="picture\back-button.png" alt="kembali" style="width: 30px; height: 30px;"
          class="back">
      </a>
      <h1>Privasi & Dasar</h1>
    </div>
    <div class="container">
      <h1>Dasar Privasi</h1>
      <br>
      <p>
        <b>Pengumpulan Data</b>: "Kami mengumpul maklumat yang anda berikan terus kepada kami, seperti apabila anda
        mencipta atau mengubah suai akaun anda, menyertai ciri interaktif dan meminta sokongan pelanggan. Jenis
        maklumat yang kami kumpulkan termasuk nama anda, alamat e-mel, nombor telefon, gambar profil, pos,
        komen dan sebarang maklumat lain yang anda pilih untuk berikan.
      </p>
      <br>
      <p>
        <b>Penggunaan Data</b>: "Kami menggunakan maklumat yang kami kumpul untuk menyediakan, mengekalkan dan memperbaiki perkhidmatan kami,
        termasuk untuk memperibadikan pengalaman anda, menyediakan sokongan pelanggan dan berkomunikasi dengan anda tentang produk,
        perkhidmatan dan tawaran."
      </p>
      <br>
      <p>
        <b>Perkongsian Data</b>: "Kami mungkin berkongsi maklumat anda dengan vendor pihak ketiga, penyedia perkhidmatan dan rakan kongsi yang
        melaksanakan perkhidmatan bagi pihak kami, seperti pemprosesan pembayaran, analisis data dan perkhidmatan pelanggan. Kami
        memerlukan pihak ketiga ini untuk melindungi maklumat anda dan hanya menggunakannya untuk tujuan yang kami tentukan."
      </p>
      <br>
      <p>
        <b>Hak Pengguna</b>: "Anda mempunyai hak untuk mengakses, membetulkan, memadam atau memindahkan data peribadi anda. Anda boleh
        melaksanakan hak ini dengan menghubungi kami melalui saluran yang disediakan dalam bahagian 'Hubungi Kami' dalam dasar ini."
      </p>
      <br>
      <p>
        <b>Keselamatan Data</b>: "Kami melaksanakan pelbagai langkah keselamatan untuk melindungi maklumat peribadi anda daripada
        akses, penggunaan atau pendedahan yang tidak dibenarkan. Langkah-langkah ini termasuk penyulitan, kawalan akses dan penilaian
        keselamatan secara berkala."
      </p>
      <br>
      <hr>

      <br>
      <h1>Terma Perkhidmatan</h1>
      <br>
      <p>
        <b>Pendaftaran Akaun</b>: "Untuk menggunakan perkhidmatan kami, anda mesti mencipta akaun dengan memberikan maklumat yang tepat dan lengkap.
        Anda bertanggungjawab untuk mengekalkan kerahsiaan kelayakan akaun anda dan untuk semua aktiviti yang berlaku di bawah akaun anda."
      </p>
      <br>
      <p>
        <b>Tingkah Laku Pengguna</b>: "Anda bersetuju untuk tidak terlibat dalam sebarang aktiviti yang berbahaya, menyinggung perasaan atau menyalahi undang-undang.
        Ini termasuk gangguan, ucapan kebencian dan menyiarkan kandungan yang melanggar hak harta intelek orang lain."
      </p>
      <br>
      <p>
        <b>Hak Milik Kandungan</b>: "Anda mengekalkan hak milik kandungan yang anda cipta dan kongsi di platform kami. Walau bagaimanapun, dengan
        menyiarkan kandungan, anda memberikan kami lesen bukan eksklusif, bebas royalti, di seluruh dunia untuk menggunakan, memaparkan dan
        mengedarkan kandungan anda untuk tujuan mengendalikan dan mempromosikan perkhidmatan kami."
      </p>
      <br>
      <p>
        <b>Penamatan Perkhidmatan</b>: "Kami berhak untuk menggantung atau menamatkan akaun anda jika anda melanggar terma ini atau terlibat dalam tingkah laku
        yang kami anggap berbahaya kepada komuniti atau perkhidmatan kami."
      </p>
      <br>
      <p>
        <b>Had Liabiliti</b>: "Kami tidak bertanggungjawab ke atas sebarang kerosakan atau kerugian yang timbul daripada penggunaan perkhidmatan kami oleh anda.
        Jumlah liabiliti kami kepada anda untuk sebarang tuntutan yang timbul daripada penggunaan perkhidmatan kami adalah terhad kepada jumlah yang anda
        telah membayar kepada kami dalam dua belas bulan yang lalu."
      </p>
      <br>
    </div>
  </div>
</body>

</html>
