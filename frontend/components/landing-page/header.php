<header>
        <nav class="bg-slate-900 text-white shadow-md flex justify-between items-center px-10 py-5">
            <div class="flex items-center gap-4">
                <img src="./frontend/assets/images/Logo.webp" alt="Barangay Logo" class="h-10 w-10 rounded-full">
                <span class="font-bold text-md">BIS</span>
            </div>
            <!-- Desktop Navbar -->
            <ul class="hidden lg:flex space-x-6 font-medium text-sm">
                <?php foreach ($navbarLinks as $nav):
                    $isActive = ($current_page === basename($nav['path']))
                        ? 'text-white font-bold bg-green-500 rounded-full px-4 py-2'
                        : 'hover:bg-green-500 hover:text-white rounded-full px-4 py-2';
                    ?>
                    <li>
                        <a href="<?= $nav['path'] ?>" class="<?= $isActive ?> hover:text-gray-300">
                            <?= $nav['label'] ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                <li>
                    <a href="./frontend/pages/login.php"
                        class="px-4 py-2 rounded-full bg-slate-600 text-white shadow-md hover:bg-green-500 hover:text-white">
                        Login
                    </a>
                </li>
            </ul>
            <!-- Mobile Toggle Button -->
            <button class="lg:hidden text-white focus:outline-none"
                onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                <i class="fa fa-bars" aria-hidden="true"></i>
            </button>
        </nav>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden bg-slate-900 text-white hov p-4 lg:hidden">
            <?php foreach ($navbarLinks as $nav):
                $isActive = ($current_page === basename($nav['path']))
                    ? 'text-white font-bold bg-green-500 rounded-full px-4 py-2'
                    : 'hover:bg-green-500 hover:text-white rounded-full px-4 py-2';
                ?>
                <a href="<?= $nav['path'] ?>" class="block py-2 px-4 <?= $isActive ?>">
                    <?= $nav['label'] ?>
                </a>
            <?php endforeach; ?>
            <a href="./frontend/pages/login.php"
                class="px-4 py-2 rounded-full bg-slate-600 text-white shadow-md hover:bg-green-500 hover:text-white  mx-2">
                Login
            </a>
        </div>
    </header>