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
         @php
             $isSparePartActive =
                 request()->routeIs('spare-parts.*') ||
                 request()->routeIs('stock-in.*') ||
                 request()->routeIs('stock-out.*') ||
                 request()->routeIs('sparepartinmultiple.*') ||
                 request()->routeIs('sparepartoutmultiple.*') ||
                 request()->routeIs('sparepart.history');
         @endphp

         <li class="nav-item">
             <a class="nav-link {{ $isSparePartActive ? '' : 'collapsed' }}" data-bs-target="#sparepart-nav"
                 data-bs-toggle="collapse" href="#">
                 <i class="bi bi-wrench"></i><span>Spare Part</span><i class="bi bi-chevron-down ms-auto"></i>
             </a>
             <ul id="sparepart-nav" class="nav-content collapse {{ $isSparePartActive ? 'show' : '' }}"
                 data-bs-parent="#sidebar-nav">
                 <li>
                     <a href="{{ route('spare-parts.index') }}"
                         class="{{ request()->routeIs('spare-parts.*') ? 'active' : '' }}">
                         <i class="bi bi-circle"></i><span>List Spare Part</span>
                     </a>
                 </li>

                 <li>
                     <a href="{{ route('sparepartinmultiple.index') }}"
                         class="{{ request()->routeIs('sparepartinmultiple.*') ? 'active' : '' }}">
                         <i class="bi bi-circle"></i><span>Spare Part In</span>
                     </a>
                 </li>

                 <li>
                     <a href="{{ route('sparepartoutmultiple.index') }}"
                         class="{{ request()->routeIs('sparepartoutmultiple.*') ? 'active' : '' }}">
                         <i class="bi bi-circle"></i><span>Spare Part Out</span>
                     </a>
                 </li>

                 <li>
                     <a href="{{ route('sparepart.history') }}"
                         class="{{ request()->routeIs('sparepart.history') ? 'active' : '' }}">
                         <i class="bi bi-circle"></i><span>History In/Out</span>
                     </a>
                 </li>

                 @if (Auth::user()->is_role == 0 || Auth::user()->is_role == 2)
                     <li>
                         <a href="{{ route('suratpesanan.index') }}"
                             class="{{ request()->routeIs('suratpesanan.index') ? 'active' : '' }}">
                             <i class="bi bi-circle"></i><span>Create Surat Pesanan</span>
                         </a>
                     </li>
                 @endif
             </ul>
         </li>
         <!-- End Spare Part Sidebar -->

         <!-- Start Asset Sidebar -->

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
                         <i class="bi bi-circle"></i><span>List Asset Tools</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('asset-in.index') }}"
                         class="{{ request()->routeIs('asset-in.*') ? 'active' : '' }}">
                         <i class="bi bi-circle"></i><span>Asset Tools In</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('asset-out.index') }}"
                         class="{{ request()->routeIs('asset-out.*') ? 'active' : '' }}">
                         <i class="bi bi-circle"></i><span>Asset Tools Out</span>
                     </a>
                 </li>
                 <li>
                     <a href="{{ route('assettools.history') }}"
                         class="{{ request()->routeIs('assettools.history') ? 'active' : '' }}">
                         <i class="bi bi-circle"></i><span>Asset History In/Out</span>
                     </a>
                 </li>
             </ul>
         </li>
         <!-- End Asset Sidebar -->

         <!-- Start ATK Sidebar -->
         @if (Auth::user()->is_role == 1 || Auth::user()->is_role == 2)
         @php
             $isAtkActive =
                 request()->routeIs('atk.*') ||
                 request()->routeIs('atkmasuk.*') ||
                 request()->routeIs('atk-keluar.*')||
                 request()->routeIs('riwayat-atk.*')||
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
                     <a href="{{ route('atk.index') }}" class="{{ request()->routeIs('atk.*') ? 'active' : '' }}">
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
