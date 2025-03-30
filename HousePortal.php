<?php include 'template/header.php'; ?>

<body x-data="" class="is-header-blur" x-bind="$store.global.documentBody">
    <?php if (isset($_SESSION['message'])): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('successMessage').textContent = "<?php echo $_SESSION['message']; ?>";
        document.getElementById('successModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        // Auto-close after 5 seconds
        setTimeout(function() {
            closeModal('successModal');
        }, 5000);
    });
    </script>
    <?php 
        unset($_SESSION['message']); // Clear the message after displaying
    endif; 
    ?>
    <div class="app-preloader fixed z-50 grid h-full w-full place-content-center bg-slate-50 dark:bg-navy-900">
        <div class="app-preloader-inner relative inline-block size-48"></div>
    </div>

    <!-- Page Wrapper -->
    <div id="root" class="min-h-100vh flex grow bg-slate-50 dark:bg-navy-900" x-cloak="">
        <?php include 'template/sidebar.php'; ?>
        <?php include 'template/navbar.php'; ?>

        <!-- Main Content Wrapper -->
        <main class="main-content w-full pb-8">
            <?php
        include('config/db.php');

        // SQL query to fetch data from the database
        $sql = "SELECT * FROM tbl_house WHERE unit_available != 0";
        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
            $logoPath = !empty($row['Image']) ? "parakalan/uploads/" . $row['Image'] : "admin/uploads/default.png";
        ?>
            <div class="mt-4 pl-[var(--margin-x)] transition-all duration-[.25s] sm:mt-5 lg:mt-6">
                <div class="rounded-l-lg bg-slate-150 pt-4 pb-1 dark:bg-navy-800">

                    <div class="mt-4 grid grid-cols-1 gap-4 px-4 pb-4 sm:grid-cols-2 sm:px-5 lg:grid-cols-4">
                        <div class="flex flex-col">
                            <img class="h-48 w-full rounded-2xl object-cover object-center"
                                src="<?php echo htmlspecialchars($logoPath); ?>" alt="image">

                            <div class="card mx-2 -mt-8 grow rounded-2xl p-3.5">
                                <div class="flex space-x-2">
                                    <div class="badge rounded-full bg-info py-1 uppercase text-white">
                                        Avail
                                    </div>
                                    <div class="flex flex-wrap items-center font-inter text-xs uppercase">
                                        <p> <?php echo htmlspecialchars($row['HouseSize']); ?> sqft</p>
                                        <div class="mx-2 my-1 w-px self-stretch bg-slate-200 dark:bg-navy-500"></div>
                                        <p> <?php echo htmlspecialchars($row['RoomNum']); ?> Beds</p>
                                        <div class="mx-2 my-1 w-px self-stretch bg-slate-200 dark:bg-navy-500"></div>
                                        <p> <?php echo htmlspecialchars($row['BathNum']); ?> Bath </p>

                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="#"
                                        class="text-sm+ font-medium text-slate-700 line-clamp-1 hover:text-primary focus:text-primary dark:text-navy-100 dark:hover:text-accent-light dark:focus:text-accent-light">Emerald
                                        Bay Inn.</a>
                                </div>
                                <div class="flex items-end justify-between">
                                    <p class="mt-2">
                                        <span class="text-base font-medium text-slate-700 dark:text-navy-100">
                                            ₱<?php echo htmlspecialchars($row['DownPayment']); ?></span>
                                        <span class="text-xs text-slate-400 dark:text-navy-300">/Reservation fee</span>
                                    </p>
                                    <p class="flex shrink-0 items-center space-x-1">
                                    <div x-data="{showModal:false, modalData: {}}">
                                        <button @click="modalData = {
        houseId: '<?php echo htmlspecialchars($row['HouseID']); ?>',
        houseName: '<?php echo htmlspecialchars($row['HouseName']); ?>',
        price: '<?php echo htmlspecialchars($row['Price']); ?>',
        logoPath: '<?php echo htmlspecialchars($logoPath); ?>',
        location: '<?php echo htmlspecialchars($row['Location']); ?>',
        houseSize: '<?php echo htmlspecialchars($row['HouseSize']); ?>',
        roomNum: '<?php echo htmlspecialchars($row['RoomNum']); ?>',
        bathNum: '<?php echo htmlspecialchars($row['BathNum']); ?>',
        downPayment: '<?php echo htmlspecialchars($row['DownPayment']); ?>',
        description: '<?php echo htmlspecialchars($row['Description']); ?>'
    }; showModal = true" class="btn size-8 p-1 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25 rounded-md">
                                            <i class="fa fa-eye"></i>
                                        </button>

                                        <template x-teleport="#x-teleport-target">
                                            <div class="fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden px-4 py-6 sm:px-5"
                                                x-show="showModal" role="dialog" aria-modal="true"
                                                @keydown.window.escape="showModal = false">
                                                <!-- Modal Background -->
                                                <div class="absolute inset-0 bg-slate-900/60 transition-opacity duration-300"
                                                    @click="showModal = false" x-show="showModal"
                                                    x-transition:enter="ease-out" x-transition:enter-start="opacity-0"
                                                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in"
                                                    x-transition:leave-start="opacity-100"
                                                    x-transition:leave-end="opacity-0"></div>

                                                <!-- Modal Content -->
                                                <div class="relative w-full max-w-2xl origin-bottom rounded-lg bg-white pb-4 transition-all duration-300 dark:bg-navy-700"
                                                    x-show="showModal" x-transition:enter="ease-out"
                                                    x-transition:enter-start="opacity-0 scale-95"
                                                    x-transition:enter-end="opacity-100 scale-100"
                                                    x-transition:leave="ease-in"
                                                    x-transition:leave-start="opacity-100 scale-100"
                                                    x-transition:leave-end="opacity-0 scale-95">
                                                    <!-- Modal Header -->
                                                    <div
                                                        class="flex justify-between rounded-t-lg bg-slate-200 px-4 py-3 dark:bg-navy-800 sm:px-5">
                                                        <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100"
                                                            x-text="modalData.houseName"></h3>
                                                        <button @click="showModal = false"
                                                            class="btn -mr-1.5 size-7 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                                stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                        </button>
                                                    </div>

                                                    <!-- Modal Body -->
                                                    <div class="px-4 py-4 sm:px-5">
                                                        <div class="flex flex-col space-y-4">
                                                            <!-- House Image & Description -->
                                                            <div class="flex">
                                                                <div class="w-1/2 pr-2">
                                                                    <img class="h-48 w-full rounded-2xl object-cover object-center"
                                                                        :src="modalData.logoPath"
                                                                        :alt="modalData.houseName" />
                                                                </div>

                                                                <div class="w-1/2 pl-2">
                                                                    <div class="grid grid-cols-2 gap-4">
                                                                        <div class="flex items-center space-x-2">
                                                                            <i
                                                                                class="fa fa-dollar-sign text-slate-500"></i>
                                                                            <input type="text"
                                                                                class="form-input w-full rounded-md border-slate-300 bg-transparent px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-600 dark:text-navy-100"
                                                                                :value="modalData.price" readonly />
                                                                        </div>

                                                                        <div class="flex items-center space-x-2">
                                                                            <i
                                                                                class="fa fa-map-marker-alt text-slate-500"></i>
                                                                            <input type="text"
                                                                                class="form-input w-full rounded-md border-slate-300 bg-transparent px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-600 dark:text-navy-100"
                                                                                :value="modalData.location" readonly />
                                                                        </div>
                                                                    </div>
                                                                    <h4
                                                                        class="text-md font-semibold text-slate-700 dark:text-navy-100">
                                                                        Description
                                                                    </h4>
                                                                    <p class="mt-1 text-sm text-slate-600 dark:text-navy-100"
                                                                        x-text="modalData.description"></p>
                                                                </div>
                                                            </div>

                                                            <!-- House Information - Form Input Controls -->
                                                            <div class="mt-4">
                                                                <div class="flex space-x-4">
                                                                    <!-- Size -->
                                                                    <div class="flex items-center space-x-2 w-1/4">
                                                                        <i
                                                                            class="fa fa-ruler-combined text-slate-500"></i>
                                                                        <input type="text"
                                                                            class="form-input w-full rounded-md border-slate-300 bg-transparent px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-600 dark:text-navy-100"
                                                                            :value="modalData.houseSize + ' sqft'"
                                                                            readonly />
                                                                    </div>

                                                                    <!-- Rooms -->
                                                                    <div class="flex items-center space-x-2 w-1/4">
                                                                        <i class="fa fa-door-closed text-slate-500"></i>
                                                                        <input type="text"
                                                                            class="form-input w-full rounded-md border-slate-300 bg-transparent px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-600 dark:text-navy-100"
                                                                            :value="modalData.roomNum + ' Rooms'"
                                                                            readonly />
                                                                    </div>

                                                                    <!-- Bathrooms -->
                                                                    <div class="flex items-center space-x-2 w-1/4">
                                                                        <i class="fa fa-bath text-slate-500"></i>
                                                                        <input type="text"
                                                                            class="form-input w-full rounded-md border-slate-300 bg-transparent px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-600 dark:text-navy-100"
                                                                            :value="modalData.bathNum + ' Bathrooms'"
                                                                            readonly />
                                                                    </div>

                                                                    <!-- Available Units -->
                                                                    <div class="flex items-center space-x-2 w-1/4">
                                                                        <i class="fa fa-home text-slate-500"></i>
                                                                        <input type="text"
                                                                            class="form-input w-full rounded-md border-slate-300 bg-transparent px-3 py-2 text-sm focus:border-primary focus:outline-none dark:border-navy-450 dark:bg-navy-600 dark:text-navy-100"
                                                                            :value="modalData.availableUnits + ' Units Available'"
                                                                            readonly />
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>

                                                    <!-- Modal Footer - Reserve Button -->
                                                    <div class="text-center">
                                                        <a href="BuyHouse.php?HouseID=<?php echo urlencode($row['HouseID']); ?>"
                                                            class="btn mt-4 border border-primary/30 bg-primary/10 font-medium text-primary hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:border-accent-light/30 dark:bg-accent-light/10 dark:text-accent-light dark:hover:bg-accent-light/20 dark:focus:bg-accent-light/20 dark:active:bg-accent-light/25">
                                                            Reserve Now
                                                        </a>
                                                    </div>

                                                </div>
                                            </div>
                                        </template>
<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
    <div class="modal-container bg-white dark:bg-navy-800 w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
        <div class="modal-content py-4 text-left px-6">
            <div class="flex justify-between items-center pb-3">
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">Success</p>
                <div class="modal-close cursor-pointer z-50" onclick="closeModal('successModal')">
                    <svg class="fill-current text-black dark:text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                        <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                    </svg>
                </div>
            </div>
            <div class="mb-4">
                <p class="text-green-600 dark:text-green-400" id="successMessage"></p>
            </div>
            <div class="flex justify-end pt-2">
                <button type="button" onclick="closeModal('successModal')" class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="errorModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
    <div class="modal-container bg-white dark:bg-navy-800 w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
        <div class="modal-content py-4 text-left px-6">
            <div class="flex justify-between items-center pb-3">
                <p class="text-2xl font-bold text-red-600 dark:text-red-400">Error</p>
                <div class="modal-close cursor-pointer z-50" onclick="closeModal('errorModal')">
                    <svg class="fill-current text-black dark:text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                        <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                    </svg>
                </div>
            </div>
            <div class="mb-4">
                <p class="text-red-600 dark:text-red-400" id="errorMessage"></p>
            </div>
            <div class="flex justify-end pt-2">
                <button type="button" onclick="closeModal('errorModal')" class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700">OK</button>
            </div>
        </div>
    </div>
</div>
                                    </div>

                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php
          }
        } else {
          echo "<div class='col-12 text-center'><p>No houses available.</p></div>";
        }
        ?>

                    </div>
                </div>
            </div>
        </main>
    </div>



    <!-- 
        This is a place for Alpine.js Teleport feature 
        @see https://alpinejs.dev/directives/teleport
      -->
    <div id="x-teleport-target"></div>
    <script>
// Check for success/error messages on page load
document.addEventListener('DOMContentLoaded', function() {
    // Success Message Handler
    <?php if (isset($_SESSION['message'])): ?>
        document.getElementById('successMessage').textContent = "<?php echo $_SESSION['message']; ?>";
        openModal('successModal');
        
        // Auto-close after 5 seconds
        setTimeout(function() {
            closeModal('successModal');
        }, 5000);
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    
    // Error Message Handler
    <?php if (isset($_SESSION['error'])): ?>
        document.getElementById('errorMessage').textContent = "<?php echo $_SESSION['error']; ?>";
        openModal('errorModal');
        
        // Auto-close after 5 seconds
        setTimeout(function() {
            closeModal('errorModal');
        }, 5000);
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
});

// Success Message Handler from initial script
<?php if (isset($_SESSION['message'])): ?>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('successMessage').textContent = "<?php echo $_SESSION['message']; ?>";
    document.getElementById('successModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    
    // Auto-close after 5 seconds
    setTimeout(function() {
        closeModal('successModal');
    }, 5000);
});
<?php 
    unset($_SESSION['message']); // Clear the message after displaying
endif; 
?>


    window.addEventListener("DOMContentLoaded", () => Alpine.start());
    </script>
</body>

</html>