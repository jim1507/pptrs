<?php 
include 'template/header.php'; 
?>

<body x-data="" class="is-header-blur" x-bind="$store.global.documentBody">
    <!-- Success Message Handler -->
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

    <!-- App preloader-->
    <div class="app-preloader fixed z-50 grid h-full w-full place-content-center bg-slate-50 dark:bg-navy-900">
        <div class="app-preloader-inner relative inline-block size-48"></div>
    </div>

    <!-- Page Wrapper -->
    <div id="root" class="min-h-100vh flex grow bg-slate-50 dark:bg-navy-900" x-cloak="">
        <?php include 'template/sidebar.php'; ?>
        <?php include 'template/navbar.php'; ?>

        <!-- Main Content Wrapper -->
        <main class="main-content w-full px-[var(--margin-x)] pb-8">
            <div class="flex items-center space-x-4 py-5 lg:py-6">
                <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                    Parakalan System
                </h2>
                <div class="hidden h-full py-1 sm:flex">
                    <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
                </div>
                <ul class="hidden flex-wrap items-center space-x-2 sm:flex">
                    <li>User Management</li>
                </ul>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:gap-6">
                <!-- GridJS Advanced Example -->
                <div class="card pb-4">
                    <div>
                        <!-- Fixed Table Start -->
                        <div class="overflow-x-auto px-4 lg:px-8 py-3">
                            <div
                                class="bg-white dark:bg-navy-800 shadow-md rounded-lg overflow-hidden border border-slate-200 dark:border-navy-700">
                                <table id="teamTable"
                                    class="table-auto w-full text-left border-collapse border border-slate-200 dark:border-navy-700">
                                    <thead class="bg-slate-100 dark:bg-navy-800">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-sm font-semibold text-slate-700 dark:text-navy-100">
                                                #</th>
                                            <th
                                                class="px-6 py-3 text-sm font-semibold text-slate-700 dark:text-navy-100">
                                                Image</th>
                                            <th
                                                class="px-6 py-3 text-sm font-semibold text-slate-700 dark:text-navy-100">
                                                House Name</th>
                                            <th
                                                class="px-6 py-3 text-sm font-semibold text-slate-700 dark:text-navy-100">
                                                Available Unit</th>
                                            <th
                                                class="px-6 py-3 text-sm font-semibold text-slate-700 dark:text-navy-100">
                                                Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 dark:divide-navy-700">
                                        <?php
                            $sql = "SELECT * FROM tbl_house";
                            $result = mysqli_query($con, $sql);

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $count= 1;
                                    $logoPath = !empty($row['Image']) 
                                        ? "parakalan/uploads/" . $row['Image'] 
                                        : "admin/uploads/default.png";
                                    ?>


                                        <tr class="hover:bg-slate-100 dark:hover:bg-navy-700">
                                            <td class="px-6 py-4"><?php echo $count++; ?></td>
                                            <td class="px-6 py-4">
                                                <img src="<?php echo $logoPath; ?>" alt="Logo"
                                                    class="w-10 h-10 rounded-full">
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php echo htmlspecialchars($row['HouseName']); ?></td>
                                            <td class="px-6 py-4">
                                                <?php echo htmlspecialchars($row['unit_available']); ?>
                                            </td>

                                            <td class="px-6 py-4 space-x-2">
    <button data-house-id="<?php echo $row['HouseID']; ?>"
        class="edit-house-btn btn size-8 p-1 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25 rounded-md">
        <i class="fa fa-edit"></i>
    </button>
    <button onclick="showDeleteModal(<?php echo $row['HouseID']; ?>, '<?php echo addslashes($row['HouseName']); ?>')"
        class="btn size-8 p-1 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25 rounded-md">
        <i class="fa fa-trash-alt"></i>
    </button>
</td>
                                        </tr>
                                        <?php
                                            }
                                        } else {
                                            echo "<tr><td colspan='7' class='text-center px-6 py-4 text-slate-500'>No data available</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Fixed Table End -->
                        <!-- DataTable Script -->
                        <link rel="stylesheet" type="text/css"
                            href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
                        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
                        <script type="text/javascript"
                            src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

                        <!-- Edit House Modal -->
                        <div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
                            <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
                            <div
                                class="modal-container bg-white dark:bg-navy-800 w-11/12 rounded-lg shadow-lg z-50 overflow-y-auto max-h-[90vh] width-modal">
                                <div class="modal-content py-6 text-left px-8">
                                    <div class="flex justify-between items-center pb-3">
                                        <p class="text-2xl font-bold text-slate-700 dark:text-navy-100">Edit House
                                            Information</p>
                                        <div class="modal-close cursor-pointer z-50" onclick="closeModal('editModal')">
                                            <svg class="fill-current text-black dark:text-white"
                                                xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                viewBox="0 0 18 18">
                                                <path
                                                    d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>

                                    <form method="POST" action="config/configuration/editHouse.php"
                                        enctype="multipart/form-data">
                                        <input type="hidden" id="editHouseId" name="house_id">

                                        <!-- House Name -->
                                        <div class="mb-4">
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2"
                                                for="editHouseName">House Name</label>
                                            <input type="text" id="editHouseName" name="house_name"
                                                class="form-input w-full rounded-lg border border-slate-300 dark:border-navy-600 px-3 py-2"
                                                required>
                                        </div>

                                        <!-- Price -->
                                        <div class="mb-4">
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2"
                                                for="editPrice">Price</label>
                                            <input type="number" id="editPrice" name="price"
                                                class="form-input w-full rounded-lg border border-slate-300 dark:border-navy-600 px-3 py-2"
                                                required>
                                        </div>

                                        <!-- Down Payment -->
                                        <div class="mb-4">
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2"
                                                for="editDownPayment">Down Payment</label>
                                            <input type="number" id="editDownPayment" name="down_payment"
                                                class="form-input w-full rounded-lg border border-slate-300 dark:border-navy-600 px-3 py-2"
                                                required>
                                        </div>

                                        <!-- House Condition -->
                                        <div class="mb-4">
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2"
                                                for="editHouseCondition">House Condition</label>
                                                <select id="editHouseCondition" name="house_condition" class="form-select w-full rounded-lg border border-slate-300 dark:border-navy-600 px-3 py-2" required>
    <option value="">Select the condition</option>
    <option value="turnover">Turn-Over</option>
    <option value="ready for occupacy">Ready for Occupacy(RFO)</option>
</select>
                                        </div>

                                        <!-- Description -->
                                        <div class="mb-4">
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2"
                                                for="editDescription">Description</label>
                                            <textarea id="editDescription" name="description"
                                                class="form-textarea w-full rounded-lg border border-slate-300 dark:border-navy-600 px-3 py-2"
                                                rows="3"></textarea>
                                        </div>

                                        <!-- House Type -->
                                        <div class="mb-4">
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2"
                                                for="editHouseType" id=>House Type</label>
                                                <select id="editHouseType" name="house_type" class="form-select w-full rounded-lg border border-slate-300 dark:border-navy-600 px-3 py-2" required>
    <option value="">Select the category</option>
    <option value="TownHouse">TownHouse with Garage</option>
    <option value="TownHouse without Garage">TownHouse without Garage</option>
    <option value="Single Attached">Single Attached with Garage</option>
    <option value="Single Attached without Garage">Single Attached without Garage</option>
    <option value="Single Detached">Single Detached with Garage</option>
    <option value="Single Detached without Garage">Single Detached without Garage</option>
    <option value="Duplex">Duplex Unit with Garage</option>
    <option value="Duplex without Garage">Duplex Unit without Garage</option>
</select>
                                               
                                        </div>

                                        <!-- House Size -->
                                        <div class="mb-4">
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2"
                                                for="editHouseSize">House Size (sqm)</label>
                                            <input type="number" id="editHouseSize" name="house_size"
                                                class="form-input w-full rounded-lg border border-slate-300 dark:border-navy-600 px-3 py-2"
                                                required>
                                        </div>

                                        <!-- Bathroom Number -->
                                        <div class="mb-4">
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2"
                                                for="editBathNum">Number of Bathrooms</label>
                                            <input type="number" id="editBathNum" name="bath_num"
                                                class="form-input w-full rounded-lg border border-slate-300 dark:border-navy-600 px-3 py-2"
                                                required>
                                        </div>

                                        <!-- Room Number -->
                                        <div class="mb-4">
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2"
                                                for="editRoomNum">Number of Rooms</label>
                                            <input type="number" id="editRoomNum" name="room_num"
                                                class="form-input w-full rounded-lg border border-slate-300 dark:border-navy-600 px-3 py-2"
                                                required>
                                        </div>

                                        <!-- Location -->
                                        <div class="mb-4">
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2"
                                                for="editLocation">Location</label>
                                            <input type="text" id="editLocation" name="location"
                                                class="form-input w-full rounded-lg border border-slate-300 dark:border-navy-600 px-3 py-2"
                                                required>
                                        </div>

                                        <!-- Available Units -->
                                        <div class="mb-4">
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2"
                                                for="editUnitAvailable">Available Units</label>
                                            <input type="number" id="editUnitAvailable" name="unit_available"
                                                class="form-input w-full rounded-lg border border-slate-300 dark:border-navy-600 px-3 py-2"
                                                required>
                                        </div>

                                        <!-- Current Image Preview -->
                                        <div class="mb-4">
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2">Current
                                                Image</label>
                                            <img id="currentImagePreview" src="" alt="Current House Image"
                                                class="w-32 h-32 object-cover rounded-lg">
                                        </div>

                                        <!-- New Image Upload -->
                                        <div class="mb-4">
                                            <label
                                                class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2"
                                                for="editImage">Change Image (Optional)</label>
                                            <input type="file" id="editImage" name="image"
                                                class="form-input w-full rounded-lg border border-slate-300 dark:border-navy-600 px-3 py-2"
                                                accept="image/*">
                                        </div>

                                        <!-- Buttons -->
                                        <div class="flex justify-end pt-2">
                                            <button type="button" onclick="closeModal('editModal')"
                                                class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-navy-100 bg-slate-200 hover:bg-slate-300 mr-2">Cancel</button>
                                            <button type="submit"
                                                class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-success hover:bg-success-dark">Update
                                                House</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Delete Team Modal -->
                        <div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
                            <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
                            <div
                                class="modal-container bg-white dark:bg-navy-800 w-11/12 md:max-w-xl mx-auto rounded shadow-lg z-50 overflow-y-auto">
                                <div class="modal-content py-4 text-left px-6">
                                    <div class="flex justify-between items-center pb-3">
                                        <p class="text-2xl font-bold text-slate-700 dark:text-navy-100">Delete Team</p>
                                        <div class="modal-close cursor-pointer z-50"
                                            onclick="closeModal('deleteModal')">
                                            <svg class="fill-current text-black dark:text-white"
                                                xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                viewBox="0 0 18 18">
                                                <path
                                                    d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                    <form method="POST" action="config/configuration/deleteHouse.php">
                                        <input type="hidden" id="deleteTeamId" name="house_id">
                                        <p class="text-slate-700 dark:text-navy-100 mb-4">Are you sure you want to
                                            delete <span id="deleteTeamName" class="font-semibold"></span>? This action
                                            cannot be undone.</p>
                                        <div class="flex justify-end pt-2">
                                            <button type="button" onclick="closeModal('deleteModal')"
                                                class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-navy-100 bg-slate-200 dark:bg-navy-600 hover:bg-slate-300 dark:hover:bg-navy-500 mr-2">Cancel</button>
                                            <button type="submit"
                                                class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-error hover:bg-error-dark">Delete</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Success Modal -->
                        <!-- Success Modal -->
                        <div id="successModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
                            <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
                            <div
                                class="modal-container bg-white dark:bg-navy-800 w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
                                <div class="modal-content py-4 text-left px-6">
                                    <div class="flex justify-between items-center pb-3">
                                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">Success</p>
                                        <div class="modal-close cursor-pointer z-50"
                                            onclick="closeModal('successModal')">
                                            <svg class="fill-current text-black dark:text-white"
                                                xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                viewBox="0 0 18 18">
                                                <path
                                                    d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <p class="text-green-600 dark:text-green-400" id="successMessage"></p>
                                    </div>
                                    <div class="flex justify-end pt-2">
                                        <button type="button" onclick="closeModal('successModal')"
                                            class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700">OK</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Error Modal -->
                        <div id="errorModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
                            <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
                            <div
                                class="modal-container bg-white dark:bg-navy-800 w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
                                <div class="modal-content py-4 text-left px-6">
                                    <div class="flex justify-between items-center pb-3">
                                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">Error</p>
                                        <div class="modal-close cursor-pointer z-50" onclick="closeModal('errorModal')">
                                            <svg class="fill-current text-black dark:text-white"
                                                xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                viewBox="0 0 18 18">
                                                <path
                                                    d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <p class="text-red-600 dark:text-red-400" id="errorMessage"></p>
                                    </div>
                                    <div class="flex justify-end pt-2">
                                        <button type="button" onclick="closeModal('errorModal')"
                                            class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700">OK</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Custom Styling -->
                        <style>
                        .modal-container {
                            max-width: 95vw;
                            max-height: 90vh;
                            background-color: #f9fafb;
                        }

                        .dark .modal-container {
                            background-color: #1f2937;
                        }

                        .modal-content {
                            background-color: #ffffff;
                            max-height: 85vh;
                            overflow-y: auto;
                        }

                        .dark .modal-content {
                            background-color: #2d3748;
                        }

                        .modal-overlay {
                            background-color: rgba(0, 0, 0, 0.6);
                        }

                        .btn-gray {
                            background-color: #f1f5f9;
                            color: #374151;
                        }

                        .dark .btn-gray {
                            background-color: #4b5563;
                            color: #e5e7eb;
                        }

                        .btn-gray:hover {
                            background-color: #e5e7eb;
                        }

                        .dark .btn-gray:hover {
                            background-color: #374151;
                        }

                        .width-modal {
                            width: 7in;
                        }
                        </style>

                        <script>
                        // Initialize DataTable
                        $(document).ready(function() {
                            $('#teamTable').DataTable({
                                responsive: true,
                                pageLength: 10,
                                lengthMenu: [10, 25, 50, 100],
                                language: {
                                    search: "",
                                    searchPlaceholder: "Search agents..."
                                },
                                order: [
                                    [0, 'asc']
                                ],
                                dom: '<"flex items-center justify-between mb-4"<"flex items-center"<"search-container"f>><"create-btn">><"overflow-x-auto"t><"flex items-center justify-between mt-4"lp>',
                                pagingType: "simple_numbers"
                            });

                            // Add form-control to the search input
                            $('#teamTable_filter input')
                                .addClass('form-control')
                                .attr('placeholder', 'Search agents...')
                                .css('margin-bottom', '0');

                            $('.create-btn').html(
                                '<a href="HouseListing.php" class="btn bg-primary text-white ml-4 px-4 py-2 rounded-lg hover:bg-primary-dark">Create New Account</a>'
                            );
                             // Add event listener for edit buttons
    $(document).on('click', '.edit-house-btn', function() {
        const houseId = $(this).data('house-id');
        showEditModal(houseId);
    });

                        });

                        function showEditModal(houseId) {
    // Make an AJAX call to get the house details
    fetch(`config/configuration/getHouseDetails.php?houseId=${houseId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const house = data.house;
                document.getElementById('editHouseId').value = house.HouseID;
                document.getElementById('editHouseName').value = house.HouseName;
                document.getElementById('editPrice').value = house.Price;
                document.getElementById('editDownPayment').value = house.DownPayment;
                
                // Set House Condition dropdown
                const conditionSelect = document.getElementById('editHouseCondition');
                for (let i = 0; i < conditionSelect.options.length; i++) {
                    if (conditionSelect.options[i].value === house.HouseCondition) {
                        conditionSelect.selectedIndex = i;
                        break;
                    }
                }
                
                document.getElementById('editDescription').value = house.Description;
                
                // Set House Type dropdown
                const typeSelect = document.getElementById('editHouseType');
                for (let i = 0; i < typeSelect.options.length; i++) {
                    if (typeSelect.options[i].value === house.HouseType) {
                        typeSelect.selectedIndex = i;
                        break;
                    }
                }
                
                document.getElementById('editHouseSize').value = house.HouseSize;
                document.getElementById('editBathNum').value = house.BathNum;
                document.getElementById('editRoomNum').value = house.RoomNum;
                document.getElementById('editLocation').value = house.Location;
                document.getElementById('editUnitAvailable').value = house.unit_available;
                
                // Set current image preview
                const imagePath = house.Image ? "parakalan/uploads/" + house.Image : "admin/uploads/default.png";
                document.getElementById('currentImagePreview').src = imagePath;
                
                document.getElementById('editModal').classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            } else {
                alert('Error fetching house details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error fetching house details');
        });
}
                        function showDeleteModal(HouseID, HouseName) {
                            document.getElementById('deleteTeamId').value = HouseID;
                            document.getElementById('deleteTeamName').textContent = HouseName;
                            document.getElementById('deleteModal').classList.remove('hidden');
                            document.body.classList.add('overflow-hidden');
                        }

                        function showCreateModal() {
                            document.getElementById('createModal').classList.remove('hidden');
                            document.body.classList.add('overflow-hidden');
                        }

                        // Open Modal
                        function openModal(modalId) {
                            document.getElementById(modalId).classList.remove('hidden');
                            document.body.classList.add('overflow-hidden');
                        }

                        // Close Modal
                        function closeModal(modalId) {
                            document.getElementById(modalId).classList.add('hidden');
                            document.body.classList.remove('overflow-hidden');
                        }

                        // Close modals when clicking outside
                        window.onclick = function(event) {
                            if (event.target.classList.contains('modal-overlay')) {
                                document.querySelectorAll('[id$="Modal"]').forEach(modal => {
                                    modal.classList.add('hidden');
                                    document.body.classList.remove('overflow-hidden');
                                });
                            }
                        }

                        // Handle form submissions
                        document.getElementById('editForm').addEventListener('submit', function(e) {
                            e.preventDefault();
                            // Here you would typically make an AJAX request to update the user
                            alert('User updated successfully!');
                            closeModal('editModal');
                            // Then you might refresh the table or update the row directly
                        });

                        document.getElementById('deleteForm').addEventListener('submit', function(e) {
                            e.preventDefault();
                            // Here you would typically make an AJAX request to delete the user
                            alert('User deleted successfully!');
                            closeModal('deleteModal');
                            // Then you might refresh the table or remove the row directly
                        });

                        // Check for success/error messages on page load
                        document.addEventListener('DOMContentLoaded', function() {
                            // Success Message Handler
                            <?php if (isset($_SESSION['message'])): ?>
                            document.getElementById('successMessage').textContent =
                                "<?php echo $_SESSION['message']; ?>";
                            openModal('successModal');

                            // Auto-close after 5 seconds
                            setTimeout(function() {
                                closeModal('successModal');
                            }, 5000);
                            <?php unset($_SESSION['message']); ?>
                            <?php endif; ?>

                            // Error Message Handler
                            <?php if (isset($_SESSION['error'])): ?>
                            document.getElementById('errorMessage').textContent =
                                "<?php echo $_SESSION['error']; ?>";
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
                            document.getElementById('successMessage').textContent =
                                "<?php echo $_SESSION['message']; ?>";
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
                        </script>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Alpine.js Teleport Feature -->
    <script>
    window.addEventListener("DOMContentLoaded", () => Alpine.start());
    </script>
</body>

</html>