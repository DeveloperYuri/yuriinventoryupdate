 <!-- ======= Sidebar ======= -->
 <aside id="sidebar" class="sidebar">

     <ul class="sidebar-nav" id="sidebar-nav">

         <li class="nav-item">
             <a class="nav-link {{ request()->routeIs('indexdashboard') ? 'active' : 'collapsed' }}"
                 href="{{ route('indexdashboard') }}">
                 <i class="bi bi-grid"></i>
                 <span>Dashboard</span>
             </a>
         </li><!-- End Dashboard Nav -->

         <!-- Start Spare Part Sidebar -->
         @if (Auth::user()->is_role == 0 || Auth::user()->is_role == 1 || Auth::user()->is_role == 2)
             @php
                 $isSparePartActive =
                     request()->routeIs('spare-parts.*') ||
                     request()->routeIs('stock-in.*') ||
                     request()->routeIs('stock-out.*') ||
                     request()->routeIs('sparepartinmultiple.*') ||
                     request()->routeIs('sparepartoutmultiple.*') ||
                     request()->routeIs('suratpermintaansparepart.*') ||
                     request()->routeIs('suratpesanan.*') ||
                     request()->routeIs('sparepart.history');
             @endphp

             <li class="nav-item">
                 <a class="nav-link {{ $isSparePartActive ? '' : 'collapsed' }}" data-bs-target="#sparepart-nav"
                     data-bs-toggle="collapse" href="#">
                     <i class="bi bi-wrench"></i><span>Spare Part</span><i class="bi bi-chevron-down ms-auto"></i>
                 </a>
                 <ul id="sparepart-nav" class="nav-content collapse {{ $isSparePartActive ? 'show' : '' }}"
                     data-bs-parent="#sidebar-nav">

                     @if (Auth::user()->is_role == 2)
                         <li>
                             <a href="{{ route('dashboardsparepart.index') }}"
                                 class="{{ request()->routeIs('dashboardsparepart.index.*') ? 'active' : '' }}">
                                 <i class="bi bi-circle"></i><span>Dashboard Spare Part</span>
                             </a>
                         </li>
                     @endif

                     <li>
                         <a href="{{ route('spare-parts.index') }}"
                             class="{{ request()->routeIs('spare-parts.*') ? 'active' : '' }}">
                             <i class="bi bi-circle"></i><span>Daftar Spare Part</span>
                         </a>
                     </li>

                     <li>
                         <a href="{{ route('sparepartinmultiple.index') }}"
                             class="{{ request()->routeIs('sparepartinmultiple.*') ? 'active' : '' }}">
                             <i class="bi bi-circle"></i><span>Spare Part Masuk</span>
                         </a>
                     </li>

                     @if (Auth::user()->is_role == 2)
                         <li>
                             <a href="{{ route('sparepartinmultiple.index') }}"
                                 class="{{ request()->routeIs('sparepartinmultiple.*') ? 'active' : '' }}">
                                 <i class="bi bi-circle"></i><span>Spare Part Masuk Baru</span>
                             </a>
                         </li>
                     @endif

                     <li>
                         <a href="{{ route('sparepartoutmultiple.index') }}"
                             class="{{ request()->routeIs('sparepartoutmultiple.*') ? 'active' : '' }}">
                             <i class="bi bi-circle"></i><span>Spare Part Keluar</span>
                         </a>
                     </li>

                     <li>
                         <a href="{{ route('sparepart.history') }}"
                             class="{{ request()->routeIs('sparepart.history') ? 'active' : '' }}">
                             <i class="bi bi-circle"></i><span>Riwayat Spare Part Masuk/Keluar</span>
                         </a>
                     </li>

                     @if (Auth::user()->is_role == 0 || Auth::user()->is_role == 1 || Auth::user()->is_role == 2)
                         <li>
                             <a href="{{ route('suratpermintaansparepart.index') }}"
                                 class="{{ request()->routeIs('suratpermintaansparepart.index') ? 'active' : '' }}">
                                 <i class="bi bi-circle"></i><span>Surat Permintaan</span>
                             </a>
                         </li>
                     @endif

                     @if (Auth::user()->is_role == 0 || Auth::user()->is_role == 1 || Auth::user()->is_role == 2)
                         <li>
                             <a href="{{ route('suratpesanan.index') }}"
                                 class="{{ request()->routeIs('suratpesanan.index') ? 'active' : '' }}">
                                 <i class="bi bi-circle"></i><span>Surat Pesanan</span>
                             </a>
                         </li>
                     @endif

                     @if (Auth::user()->is_role == 2)
                         <li>
                             <a href="{{ route('suratpesanan.index') }}"
                                 class="{{ request()->routeIs('suratpesanan.index') ? 'active' : '' }}">
                                 <i class="bi bi-circle"></i><span>Surat Pesanan Spare Part Baru</span>
                             </a>
                         </li>
                     @endif
                 </ul>
             </li>
         @endif
         <!-- End Spare Part Sidebar -->

         <!-- Start Asset Sidebar -->
         @if (Auth::user()->is_role == 0 || Auth::user()->is_role == 1 || Auth::user()->is_role == 2)
             @php
                 $isAssetToolsActive =
                     request()->routeIs('asset-tools.*') ||
                     request()->routeIs('asset-in.*') ||
                     request()->routeIs('asset-out.*') ||
                     request()->routeIs('assettools.history');
             @endphp

             <li class="nav-item">
                 <a class="nav-link {{ $isAssetToolsActive ? '' : 'collapsed' }}" data-bs-target="#tools-nav"
                     data-bs-toggle="collapse" href="#">
                     <i class="bi bi-tools"></i><span>Asset Tools</span><i class="bi bi-chevron-down ms-auto"></i>
                 </a>
                 <ul id="tools-nav" class="nav-content collapse {{ $isAssetToolsActive ? 'show' : '' }}"
                     data-bs-parent="#sidebar-nav">
                     <li>
                         <a href="{{ route('asset-tools.index') }}"
                             class="{{ request()->routeIs('asset-tools.*') ? 'active' : '' }}">
                             <i class="bi bi-circle"></i><span>Daftar Asset Tools</span>
                         </a>
                     </li>
                     <li>
                         <a href="{{ route('asset-in.index') }}"
                             class="{{ request()->routeIs('asset-in.*') ? 'active' : '' }}">
                             <i class="bi bi-circle"></i><span>Asset Tools Masuk</span>
                         </a>
                     </li>
                     <li>
                         <a href="{{ route('asset-out.index') }}"
                             class="{{ request()->routeIs('asset-out.*') ? 'active' : '' }}">
                             <i class="bi bi-circle"></i><span>Asset Tools Keluar</span>
                         </a>
                     </li>
                     <li>
                         <a href="{{ route('assettools.history') }}"
                             class="{{ request()->routeIs('assettools.history') ? 'active' : '' }}">
                             <i class="bi bi-circle"></i><span>Riwayat Asset Tools Masuk/Keluar</span>
                         </a>
                     </li>
                 </ul>
             </li>
         @endif
         <!-- End Asset Sidebar -->

         <!-- Start Asset IT Sidebar -->
         @if (Auth::user()->is_role == 4 || Auth::user()->is_role == 2)
             @php
                 $isAssetITActive =
                     request()->routeIs('asset-it.*') ||
                     request()->routeIs('perbaikanasset-it.*') ||
                     request()->routeIs('peminjamanasset-it.*');
             @endphp

             <li class="nav-item">
                 <a class="nav-link {{ $isAssetITActive ? '' : 'collapsed' }}" data-bs-target="#assetit-nav"
                     data-bs-toggle="collapse" href="#">
                     <i class="bi bi-laptop"></i><span>Asset IT</span><i class="bi bi-chevron-down ms-auto"></i>
                 </a>
                 <ul id="assetit-nav" class="nav-content collapse {{ $isAssetITActive ? 'show' : '' }}"
                     data-bs-parent="#sidebar-nav">
                     <li>
                         <a href="{{ route('asset-it.index') }}"
                             class="{{ request()->routeIs('asset-it.*') ? 'active' : '' }}">
                             <i class="bi bi-circle"></i><span>Daftar Asset IT</span>
                         </a>
                     </li>
                     <li>
                         <a href="{{ route('peminjamanasset-it.index') }}"
                             class="{{ request()->routeIs('peminjamanasset-it.*') ? 'active' : '' }}">
                             <i class="bi bi-circle"></i><span>Riwayat Peminjaman Asset IT</span>
                         </a>
                     </li>
                     <li>
                         <a href="{{ route('perbaikanasset-it.index') }}"
                             class="{{ request()->routeIs('perbaikanasset-it.*') ? 'active' : '' }}">
                             <i class="bi bi-circle"></i><span>Riwayat Perbaikan Asset IT</span>
                         </a>
                     </li>
                 </ul>
             </li>
         @endif
         <!-- End Asset IT Sidebar -->

         <!-- Start Spare Part IT Sidebar -->
         @if (Auth::user()->is_role == 4 || Auth::user()->is_role == 2)
             @php
                 $isSparepartITActive =
                     request()->routeIs('sparepart-it.*') ||
                     request()->routeIs('sparepartitinmultiple.*') ||
                     request()->routeIs('sparepartithistory.*');
             @endphp

             <li class="nav-item">
                 <a class="nav-link {{ $isSparepartITActive ? '' : 'collapsed' }}" data-bs-target="#sparepartit-nav"
                     data-bs-toggle="collapse" href="#">
                     <i class="bi bi-laptop"></i><span>Spare Part IT</span><i class="bi bi-chevron-down ms-auto"></i>
                 </a>
                 <ul id="sparepartit-nav" class="nav-content collapse {{ $isSparepartITActive ? 'show' : '' }}"
                     data-bs-parent="#sidebar-nav">
                     <li>
                         <a href="{{ route('sparepart-it.index') }}"
                             class="{{ request()->routeIs('sparepart-it.*') ? 'active' : '' }}">
                             <i class="bi bi-circle"></i><span>Daftar Spare Part IT</span>
                         </a>
                     </li>
                     <li>
                         <a href="{{ route('sparepartitinmultiple.index') }}"
                             class="{{ request()->routeIs('sparepartitinmultiple.*') ? 'active' : '' }}">
                             <i class="bi bi-circle"></i><span>Spare Part IT Masuk</span>
                         </a>
                     </li>

                     <li>
                         <a href="{{ route('sparepartithistory.index') }}"
                             class="{{ request()->routeIs('sparepartithistory.*') ? 'active' : '' }}">
                             <i class="bi bi-circle"></i><span>Riwayat Spare Part IT</span>
                         </a>
                     </li>
                 </ul>
             </li>
         @endif
         <!-- End Asset IT Sidebar -->

         <!-- Start ATK Sidebar -->
         @if (Auth::user()->is_role == 3 || Auth::user()->is_role == 2)
             @php
                 $isAtkActive =
                     request()->routeIs('atk.index') ||
                     request()->routeIs('atkmasuk.*') ||
                     request()->routeIs('atk-keluar.*') ||
                     request()->routeIs('atk.history') ||
                     request()->routeIs('suratpesanan-atk.*');
             @endphp

             <li class="nav-item">
                 <a class="nav-link {{ $isAtkActive ? '' : 'collapsed' }}" data-bs-target="#atk-nav"
                     data-bs-toggle="collapse" href="#">
                     <i class="bi bi-journal-check"></i><span>ATK</span><i class="bi bi-chevron-down ms-auto"></i>
                 </a>

                 <ul id="atk-nav" class="nav-content collapse {{ $isAtkActive ? 'show' : '' }}"
                     data-bs-parent="#sidebar-nav">

                     {{-- Daftar ATK --}}
                     <li>
                         <a href="{{ route('atk.index') }}"
                             class="{{ request()->routeIs('atk.index') ? 'active' : '' }}">
                             <i class="bi bi-circle"></i><span>Daftar ATK</span>
                         </a>
                     </li>

                     {{-- ATK - Masuk --}}
                     <li>
                         <a href="{{ route('atkmasuk.index') }}"
                             class="{{ request()->routeIs('atkmasuk.*') ? 'active' : '' }}">
                             <i class="bi bi-circle"></i><span>ATK - Masuk</span>
                         </a>
                     </li>

                     {{-- ATK - Keluar --}}
                     <li>
                         <a href="{{ route('atk-keluar.index') }}"
                             class="{{ request()->routeIs('atk-keluar.*') ? 'active' : '' }}">
                             <i class="bi bi-circle"></i><span>ATK - Keluar</span>
                         </a>
                     </li>

                     {{-- History --}}
                     <li>
                         <a href="{{ route('atk.history') }}"
                             class="{{ request()->routeIs('atk.history') ? 'active' : '' }}">
                             <i class="bi bi-circle"></i><span>Riwayat ATK Masuk/Keluar</span>
                         </a>
                     </li>

                     {{-- Buat Surat Pesanan --}}
                     <li>
                         <a href="{{ route('suratpesanan-atk.index') }}"
                             class="{{ request()->routeIs('suratpesanan-atk.*') ? 'active' : '' }}">
                             <i class="bi bi-circle"></i><span>Buat Surat Pesanan ATK</span>
                         </a>
                     </li>

                 </ul>
             </li>
         @endif

         <!-- End ATK Sidebar -->

         <!-- Start Surat Pesanan Baru -->
         @if (Auth::user()->is_role == 2)
             <li class="nav-item">
                 <a class="nav-link {{ request()->routeIs('suratpesananbaru.index') ? 'active' : 'collapsed' }}"
                     href="{{ route('suratpesananbaru.index') }}">
                     <i class="bi bi-truck"></i>
                     <span>Surat Pesanan Baru</span>
                 </a>
             </li>
         @endif

         <!-- Start Supplier Sidebar -->
         <li class="nav-item">
             <a class="nav-link {{ request()->routeIs('indexsupplier') ? 'active' : 'collapsed' }}"
                 href="{{ route('indexsupplier') }}">
                 <i class="bi bi-truck"></i>
                 <span>Supplier</span>
             </a>
         </li>
         <!-- End Supplier Sidebar -->

         @if (Auth::user()->is_role == 2)
             <!-- Start Riwayat Mesin -->
             <li class="nav-item">
                 <a class="nav-link {{ request()->routeIs('index.riwayatmesin') ? 'active' : 'collapsed' }}"
                     href="{{ route('index.riwayatmesin') }}">
                     <i class="bi bi-gear-fill"></i>
                     <span>Riwayat Mesin</span>
                 </a>
             </li>
         @endif
         <!-- End Riwayat Mesin Sidebar -->

         <!-- Start Users Sidebar -->
         @if (Auth::user()->is_role == 2)
             <li class="nav-item">
                 <a class="nav-link {{ request()->routeIs('indexusers') ? 'active' : 'collapsed' }}"
                     href="{{ route('indexusers') }}">
                     <i class="bi bi-person-circle"></i>
                     <span>Users</span>
                 </a>
             </li>
         @endif
         <!-- End Users Sidebar -->

         <!-- Start Spare Part Sidebar -->

         @php
             $isConfigActive =
                 request()->routeIs('indexbrand') ||
                 request()->routeIs('indexwarehouse') ||
                 request()->routeIs('indexlocations') ||
                 request()->routeIs('indexcategory') ||
                 request()->routeIs('indexsubcategory') ||
                 request()->routeIs('index.satuan') ||
                 request()->routeIs('index.department') ||
                 request()->routeIs('produkstatus.index') ||
                 request()->routeIs('indexprofile');
         @endphp

         <li class="nav-item">
             <a class="nav-link {{ $isConfigActive ? '' : 'collapsed' }}" data-bs-target="#configuration-nav"
                 data-bs-toggle="collapse" href="#">
                 <i class="bi bi-gear"></i><span>Configuration</span><i class="bi bi-chevron-down ms-auto"></i>
             </a>
             <ul id="configuration-nav" class="nav-content collapse {{ $isConfigActive ? 'show' : '' }}"
                 data-bs-parent="#sidebar-nav">
                 <li>
                     <a href="{{ route('indexbrand') }}"
                         class="{{ request()->routeIs('indexbrand') ? 'active' : '' }}">
                         <i class="bi bi-circle"></i><span>Brand</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('indexwarehouse') }}"
                         class="{{ request()->routeIs('indexwarehouse') ? 'active' : '' }}">
                         <i class="bi bi-circle"></i><span>Warehouse</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('indexlocations') }}"
                         class="{{ request()->routeIs('indexlocations') ? 'active' : '' }}">
                         <i class="bi bi-circle"></i><span>Lokasi</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('indexcategory') }}"
                         class="{{ request()->routeIs('indexcategory') ? 'active' : '' }}">
                         <i class="bi bi-circle"></i><span>Category</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('indexsubcategory') }}"
                         class="{{ request()->routeIs('indexsubcategory') ? 'active' : '' }}">
                         <i class="bi bi-circle"></i><span>Sub Category</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('index.satuan') }}"
                         class="{{ request()->routeIs('index.satuan') ? 'active' : '' }}">
                         <i class="bi bi-circle"></i><span>Satuan</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('index.department') }}"
                         class="{{ request()->routeIs('index.department') ? 'active' : '' }}">
                         <i class="bi bi-circle"></i><span>Department</span>
                     </a>
                 </li>
                 @if (Auth::user()->is_role == 2)
                     <li>
                         <a href="{{ route('produkstatus.index') }}"
                             class="{{ request()->routeIs('produkstatus.index') ? 'active' : '' }}">
                             <i class="bi bi-circle"></i><span>Produk Status</span>
                         </a>
                     </li>
                 @endif
                 <li>
                     <a href="{{ route('indexprofile') }}"
                         class="{{ request()->routeIs('indexprofile') ? 'active' : '' }}">
                         <i class="bi bi-circle"></i><span>Profile</span>
                     </a>
                 </li>
             </ul>
         </li>
         <!-- End Spare Part Sidebar -->

     </ul>

 </aside><!-- End Sidebar-->
