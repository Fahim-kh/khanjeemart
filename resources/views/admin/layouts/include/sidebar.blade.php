<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="{{ route('dashboard') }}" class="sidebar-logo">
            <img src="{{ asset('admin') }}/assets/images/khanjee_logo.png" alt="site logo" class="light-logo">
            <img src="{{ asset('admin') }}/assets/images/khanjee_logo.png" alt="site logo" class="dark-logo">
            <img src="{{ asset('admin') }}/assets/images/khanjee_icon.png" alt="site logo" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
      <ul class="sidebar-menu" id="sidebar-menu">
        @php
          $modules = Auth::user()->getmodules();
        @endphp
        @foreach($modules as $module)
          @if($module->is_group_title)
            <li class="sidebar-menu-group-title">{{ $module->name }}</li>
          @else
            @if($module->childs->count())
              <li class="dropdown">
                <a href="{{ ($module->route != '#')? $module->route : '#' }}">
                  @if($module->icon_type === 'html')
                    <iconify-icon icon="{{ $module->icon }}" class="menu-icon"></iconify-icon>
                  @else
                    <i class="{{ $module->icon }} text-xl me-14 d-flex w-auto"></i>
                  @endif
                  <span>{{ $module->name }}</span>
                </a>
                <ul class="sidebar-submenu">
                  @foreach($module->childs as $child)
                    <li>
                      <a href="{{ $child->route !== '#' ? route($child->route) : $child->route }}">
                        <i class="ri-circle-fill circle-icon text-{{ $child->color ?? 'primary-600' }} w-auto"></i>
                        {{ $child->name }}
                      </a>
                    </li>
                  @endforeach
                </ul>
              </li>
            @else
              <li>
                <a href="{{ $module->route !== '#' ? route($module->route) : $module->route }}">
                  @if($module->icon_type === 'html')
                    <iconify-icon icon="{{ $module->icon }}" class="menu-icon"></iconify-icon>
                  @else
                    <i class="{{ $module->icon }} text-xl me-14 d-flex w-auto"></i>
                  @endif
                  <span>{{ $module->name }}</span>
                </a>
              </li>
            @endif
          @endif
        @endforeach
      </ul>
    </div>
</aside>
