<header id="header" class="header d-flex align-items-center fixed-top">
   <div class="container-fluid container-xl position-relative d-flex align-items-center">
 
     <a href="index.html" class="logo d-flex align-items-center me-auto">
       <img src="assets/img/logo.png" alt="">
       <h1 class="sitename">E-GROWTH</h1>
     </a>
 
     <nav id="navmenu" class="navmenu">
       <ul>
         {{-- <li><a href="{{ url('/') }}" class="active">Home</a></li> --}}
         {{-- <li><a href="{{ url('/tentang') }}">Tentang</a></li> --}}
         <li><a href="{{ url('/') }}">Home</a></li>
         {{-- <li><a href="{{ url('/kontak') }}">Kontak</a></li> --}}
       </ul>
       <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
     </nav>
 
     <a class="btn-getstarted" href="{{ route('login') }}">Login</a>
 
   </div>
 </header>
 