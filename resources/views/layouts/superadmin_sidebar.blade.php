<li class="nav-item">
    <a class="nav-link" href="{{ route('users') }}">{{ __('User') }}</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('roles') }}">{{ __('Role') }}</a>
</li>
<li class="nav-item dropdown">
    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
        {{ __('Products') }}
    </a>

    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
        <a class="nav-link" href="{{ route('products') }}">{{ __('Products') }}</a>
        <a class="nav-link" href="{{ route('product_type') }}">{{ __('Product Type') }}</a>
        <a class="nav-link" href="{{ route('product_family') }}">{{ __('Product Family') }}</a>
        <a class="nav-link" href="{{ route('categories') }}">{{ __('Category') }}</a>
        <a class="nav-link" href="{{ route('brands') }}">{{ __('Brand') }}</a>
        <a class="nav-link" href="{{ route('colors') }}">{{ __('Color') }}</a>
        <a class="nav-link" href="{{ route('sizes') }}">{{ __('Sizes') }}</a>
    </div>
</li>