<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SDM</title>
  
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="{{ asset('template/plugins/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="{{ asset('template/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('template/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('template/plugins/jqvmap/jqvmap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('template/dist/css/adminlte.min.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" />

</head>

<style>

  
    body {
    background-color: #f4f6f9;
}
.custom-navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 60px;
    padding: 0 1rem;
    background-color: #00518d!important;  
    z-index: 1040;
    display: flex;
    align-items: center;
}


/* User Photo */
.custom-navbar .user-photo {
    height: 40px;
    width: 40px;
    border-radius: 50%;
}

/* Nav link styles */
.custom-navbar .nav-link {
    color: black;
    display: flex;
    align-items: center;
}

.custom-navbar .nav-link:hover {
    color: #007bff;
}

/* Dropdown Menu */
.custom-navbar .dropdown-menu {
    background-color: white;
}

.nav-link.dropdown-toggle::after {
    color: white;
}
  .main-sidebar {
    background-color: #1976B2 !important;
    margin-top: 56px;
  }

  .main-sidebar .nav-link i {
    color: white !important;
  }

  .main-sidebar .nav-link p {
    color: white;
  }

  .main-sidebar .nav-link.active,
  .main-sidebar .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.2) !important;
    color: white !important;
  }

  .content-wrapper {
    margin-left: 250px;
    padding-top: 56px;
  }
/* Mengubah warna background pagination */
.pagination {
    background-color: #00518d;  /* Ganti dengan warna yang diinginkan */
    border-radius: 0.25rem;  /* Optional: Memberikan border radius pada pagination */
}

/* Mengubah warna link pagination */
.pagination .page-link {
    color: white;  /* Warna teks saat tidak aktif */
    background-color: #00518d;  /* Warna background link */
    border: 1px solid #00518d;  /* Warna border link */
}

/* Mengubah warna saat hover pada link */
.pagination .page-link:hover {
    background-color: #0A6ABF;  /* Warna hover saat link ditekan */
    color: white;  /* Warna teks saat hover */
}

/* Mengubah warna aktif pagination */
.pagination .active .page-link {
    background-color: #29B6F6;  /* Warna latar belakang saat aktif */
    color: white;  /* Warna teks saat aktif */
    border-color: #29B6F6;  /* Warna border saat aktif */
}

/* Mengubah warna untuk tombol disabled */
.pagination .disabled .page-link {
    background-color: #e0e0e0;  /* Warna latar belakang saat disabled */
    color: #b0b0b0;  /* Warna teks saat disabled */
    border-color: #e0e0e0;  /* Warna border saat disabled */
}

/* kalender */

.fc-header-toolbar {
    display: flex;
    
    
    gap: 10px; /* Jarak antara tombol Today dan prev/next */
}

.fc .fc-button {
    background-color: #31beb4;
    border: none;
    color: white;
    font-weight: bold;
}
.fc .fc-today-button {
    background-color: #31beb4 !important;
    
}

/* Hover tombol "Today" */
.fc .fc-today-button:hover {
    background-color: #279c93 !important;
}
/* Hover button */
.fc .fc-button:hover {
    background-color: #279c93;
    color: #fff;
}


    /* Ubah warna nama hari seperti Sun, Mon, dst */
    .fc .fc-col-header-cell-cushion {
        color: #31beb4; /* biru */
        font-weight: bold;
    }

    /* Ubah warna angka tanggal di kalender */
    .fc .fc-daygrid-day-number {
        color: #31beb4; /* hijau */
        
    }
/* Warna header hari Minggu */
.fc .fc-col-header-cell.fc-day-sun .fc-col-header-cell-cushion {
        color: #ff0000 !important;
        font-weight: bold;
    }
/* Menyamakan tinggi dan font input Select2 dengan input form lain */

.choices__inner {
    min-height: 38px; /* default-nya sekitar 44px, kamu bisa ubah ke 30-35px */
    padding: 4px 8px;
   
  }

  .choices__list--single {
    padding: 0; /* mengurangi tinggi */
  }

  .choices__input {
    
    padding: 4px 6px;
  }

 #lineChart {
    height: 400px !important;
}

/* Sembunyikan nama user di tampilan kecil (<820px) */
@media (max-width: 819px) {
  .user-name {
    display: none !important;
  }
}

/* Tampilkan nama user di tampilan besar (>=820px) */
@media (min-width: 820px) {
  .user-name {
    display: inline-block !important;
  }
  .dropdown-menu {
    position: absolute !important; /* Pastikan dropdown tetap di posisinya */
    right: 0 !important; /* Letakkan ke kanan */
    left: auto !important; /* Hindari bug tampilan */
    top: 100% !important; /* Agar dropdown muncul di bawah icon */
    width: max-content; /* Sesuaikan lebar */
  }
}

  @media (max-width: 768px) {
    .custom-navbar .nav-link span {
        display: none !important;
    }

    .custom-navbar .navbar-brand img {
        height: 40px;
    }

    .custom-navbar .user-photo {
        height: 35px;
        width: 35px;
    }
  .content-wrapper {
    margin-left: 0;
  }

  .main-sidebar {
    margin-top: 60px;
  }

  .navbar .dropdown-menu {
    position: absolute;
    right: 0;
    left: auto;
    width: 100%;
  }
  
  .navbar .nav-item.dropdown .nav-link {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
  }

  #produk-table th,
    #produk-table td {
        min-width: 150px; /* Lebar minimum untuk semua kolom (header dan sel data) */
        /* Anda bisa menyesuaikan nilai '150px' ini sesuai kebutuhan Anda. */
        /* Jika ada konten yang sangat panjang di satu kolom, ini akan memastikan kolom tersebut memiliki setidaknya lebar ini, */
        /* dan scroll horizontal akan muncul jika total lebar melebihi layar. */

        /* Optional: Tambahkan padding untuk sedikit ruang ekstra */
        padding-left: 8px;
        padding-right: 8px;
    }

    /* Override untuk Choices.js container agar sesuai dengan lebar kolom */
    #produk-table td .choices__inner {
        min-width: 100%; /* Pastikan inner container Choices.js mengisi lebar sel */
    }

    /* Pastikan input fields juga mengisi lebar sel */
    #produk-table td input[type="number"],
    #produk-table td input[type="text"] {
        width: 100%;
        box-sizing: border-box; /* Penting agar padding dan border tidak menambah lebar */
    }

    /* Penyesuaian untuk kolom tombol "Tambah" dan "Hapus" */
    #produk-table th:last-child,
    #produk-table td:last-child {
        min-width: 120px; /* Beri sedikit ruang lebih untuk tombol */
        text-align: center; /* Opsional: Pusatkan tombol jika diinginkan */
    }
.small-box .icon {
    display: block !important;
  }
  .small-box .inner {
    text-align: left !important;
  }
  .small-box .inner p {
        font-size: 1.2rem !important; /* Sesuaikan ukuran sesuai keinginan */
    }
}


</style>

<body class="hold-transition sidebar-mini layout-fixed">
<nav class="navbar navbar-light bg-white custom-navbar">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <!-- Kiri: Logo -->
        <a class="navbar-brand mb-0" href="#">
    <!-- Logo untuk laptop (md ke atas) -->
    <img src="{{ asset('gambar/logoputihfull.png') }}" alt="Logo" class="d-none d-md-block" style="height: 50px; width: auto;">
    <!-- Logo untuk mobile (sm ke bawah) -->
    <img src="{{ asset('gambar/logoputih.png') }}" alt="Logo Kecil" class="d-block d-md-none" style="height: 40px; width: 40px;">
</a>

        <!-- Kanan: Pushmenu, Profil -->
        <ul class="navbar-nav flex-row align-items-center">
            <!-- Pushmenu Icon -->
            <li class="nav-item mr-3">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                    <i class="fas fa-bars" style="color: #ffffff;"></i>
                </a>
            </li>

            <!-- Profil Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="{{ asset('gambar/admin.png') }}" alt="User Photo" class="user-photo">
                    <span class="user-name ml-2 text-white">{{ auth()->user()->name ?? 'User' }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right " aria-labelledby="navbarDropdown" >
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt" style="color: #00518d  ;"></i> Logout
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>


  <div class="wrapper">
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <div class="sidebar">
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            @if(auth()->user()->role == 'admin')
            <li class="nav-item">
              <a href="/dashboard/admin" class="nav-link">
                <i class="fas fa-tachometer-alt"></i>
                <p>Dashboard Admin</p>
              </a>
            </li>
            @endif

            @if(auth()->user()->role == 'customerservice')
            <li class="nav-item">
              <a href="/dashboard/customerservice" class="nav-link">
                <i class="fas fa-tachometer-alt"></i>
                <p>Dashboard CS</p>
              </a>
            </li>
            @endif
           
@if(auth()->user()->role == 'editor')
            <li class="nav-item">
              <a href="/dashboard/editor" class="nav-link">
                <i class="fas fa-tachometer-alt"></i>
                <p>Dashboard Editor</p>
              </a>
            </li>
            @endif

            <li class="nav-item">
              <a href="/profil" class="nav-link">
                <i class="fas fa-user-circle"></i>
                <p>Profil</p>
              </a>
            </li>
            
            @if(auth()->user()->role == 'admin')
            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class="fas fa-user"></i>
                <p>
                  Akun Pengguna
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="/users/create" class="nav-link">
                    <i class="fas fa-user-plus"></i>
                    <p>Tambah Akun Baru</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/user/customerservice" class="nav-link">
                    <i class="fas fa-address-book"></i>
                    <p>Daftar CS</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/user/editor" class="nav-link">
                    <i class="fas fa-address-book"></i>
                    <p>Daftar Editor</p>
                  </a>
                </li>
              </ul>
            </li>
            


            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class=" fas fa-box"></i>
                <p>
                  Produk
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="/produk" class="nav-link">
                    <i class="fas fa-list"></i>
                    <p>Daftar Produk</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/jenisproduk" class="nav-link">
                    <i class="fas fa-tag"></i>
                    <p>Kategori Produk</p>
                  </a>
                </li>
              </ul>
            </li>


<li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class="fas fa-bullhorn"></i>
                <p>
                  Iklan
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="/kampanye" class="nav-link">
                    <i class="fas fa-key"></i>
                    <p>Kode Iklan</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/iklan/import" class="nav-link">
                    <i class="fas fa-file-invoice"></i>
                    <p>Laporan Iklan</p>
                  </a>
                </li>
               
              </ul>
            </li>

           
            @endif


            @if(auth()->user()->role == 'customerservice')
            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class=" fas fa-shopping-cart"></i>
                <p>
                  Closing Penjualan
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="/penjualan/create" class="nav-link">
                    <i class="fas fa-plus-circle"></i>
                    <p>Input</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/penjualanindividu" class="nav-link">
                    <i class="fas fa-file-invoice"></i>
                    <p>Laporan</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/penjualan/shopee" class="nav-link">
                    <i class="fas fa-shopping-bag"></i>
                    <p>Pilih Shopee</p>
                  </a>
                </li>
              </ul>
            </li>
            @endif
             @if(auth()->user()->role == 'customerservice')
            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class=" fas fa-magnet"></i>
                <p>
                  Lead
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="/lead/create" class="nav-link">
                    <i class="fas fa-plus-circle"></i>
                    <p>Input</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/lead" class="nav-link">
                    <i class="fas fa-file-invoice"></i>
                    <p>Laporan</p>
                  </a>
                </li>
              </ul>
            </li>
            @endif
 @if(auth()->user()->role == 'admin')
            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class="fas fa-shopping-cart"></i>
                <p>
                  Closing Penjualan
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="/penjualanlaporan" class="nav-link">
                    <i class="fas fa-file-invoice"></i>
                    <p>Laporan</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/penjualan/lapshopee" class="nav-link">
                    <i class="fas fa-shopping-bag"></i>
                    <p>Shopee Batal</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/penjualan/pembagian" class="nav-link">
                    <i class="fas fa-money-check"></i>
                    <p>Pembagian Keuangan</p>
                  </a>
                </li>
              </ul>
            </li>
            @endif
               @if(auth()->user()->role == 'admin')
            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class=" fas fa-magnet"></i>
                <p>
                  Lead
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="/lead-laporan" class="nav-link">
                    <i class="fas fa-file-invoice"></i>
                    <p>Laporan</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/jenis-lead" class="nav-link">
                    <i class="fas fa-tag"></i>
                    <p>Jenis Lead</p>
                  </a>
                </li>
              </ul>
            </li>
            @endif

 @if(auth()->user()->role == 'editor')
            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class=" fas fa-briefcase"></i>
                <p>
                  Jobdesk
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="/editor/jobdesk" class="nav-link">
                    <i class="fas fa-plus-circle"></i>
                    <p>Ambil Jobdesk</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/editor/jobdesk/index" class="nav-link">
                    <i class="fas fa-clipboard-list"></i>
                    <p>Jobdesk Anda</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="/editor/jobdesk/done" class="nav-link">
                    <i class="fas fa-check-circle"></i>
                    <p>Jobdesk Selesai</p>
                  </a>
                </li>
              </ul>
            </li>
            @endif

@if(auth()->user()->role == 'admin')
            <li class="nav-item has-treeview">
              <a href="#" class="nav-link">
                <i class=" fas fa-briefcase"></i>
                <p>
                  Jobdesk
                  <i class="right fas fa-angle-left"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="/editor/laporan" class="nav-link">
                    <i class="fas fa-file-invoice"></i>
                    <p>Laporan</p>
                  </a>
                </li>
            @endif
          </ul>

          @if(auth()->user()->role == 'admin')
            <li class="nav-item">
              <a href="/penjualan/import" class="nav-link">
                <i class="fas fa-file-export"></i>
                <p>Import Penjualan</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="/lead/import" class="nav-link">
                <i class="fas fa-file-export"></i>
                <p>Import Lead</p>
              </a>
            </li>
            @endif
        </nav>
      </div>
    </aside>
  </div>

    <div class="content-wrapper">
      @yield('content')
    </div>
  </div>


  
<!-- Scripts -->
<script src="{{ asset('template/plugins/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('template/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('template/dist/js/adminlte.min.js') }}"></script>

  <script src="{{ asset('template/plugins/jquery-knob/jquery.knob.min.js') }}"></script>


  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/bundle.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function () {
    window.choicesInstances = {}; // simpan semua instance agar bisa akses nanti

    const selects = document.querySelectorAll('select');
    selects.forEach(select => {
        const instance = new Choices(select, {
            searchEnabled: true,
            itemSelectText: '',
            position: 'bottom',
        });

        // Simpan berdasarkan id
        if (select.id) {
            window.choicesInstances[select.id] = instance;
        }
    });
});


document.addEventListener('DOMContentLoaded', function () {
    const picker = new Litepicker({
        element: document.getElementById('daterange'),
        singleMode: false,
        autoApply: true,
        format: 'YYYY-MM-DD',
        dropdowns: {
            minYear: 2020,
            maxYear: new Date().getFullYear(),
            months: true,
            years: true
        },
        setup: (picker) => {
            picker.on('selected', (startDate, endDate) => {
                const formatted = startDate.format('YYYY-MM-DD') + ' - ' + endDate.format('YYYY-MM-DD');
                document.getElementById('daterange').value = formatted;
                document.getElementById('filterForm').submit();
            });
        }
    });
});
</script>

 
  @stack('scriptsdua')

  @yield('scripts')
</body>

</html>
