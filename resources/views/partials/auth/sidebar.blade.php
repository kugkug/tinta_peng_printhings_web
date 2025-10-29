<div class="nk-sidebar">           
    <div class="nk-nav-scroll">
        <ul class="metismenu" id="menu">
            <li>
                <a href="{{ route('home') }}" aria-expanded="false">
                    <i class="fa-solid fa-home menu-icon fa-action"></i><span class="nav-text">Home</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('inventory.list') }}" aria-expanded="false">
                    <i class="fa-solid fa-box menu-icon fa-action"></i><span class="nav-text">Inventory</span>
                </a>
            </li>

            <li>
                <a href="{{ route('products.list') }}" aria-expanded="false">
                    <i class="fa-solid fa-cubes menu-icon fa-action"></i><span class="nav-text">Products</span>
                </a>
            </li>

            <li>
                <a href="{{ route('settings.index') }}" aria-expanded="false">
                    <i class="fa-solid fa-gear menu-icon fa-action"></i><span class="nav-text">Settings</span>
                </a>
            </li>
        </ul>
    </div>
</div>