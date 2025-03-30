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
                            <div class="bg-white dark:bg-navy-800 shadow-md rounded-lg overflow-hidden border border-slate-200 dark:border-navy-700">
                                <table id="teamTable" class="table-auto w-full text-left border-collapse border border-slate-200 dark:border-navy-700">
                                    <thead class="bg-slate-100 dark:bg-navy-800">
                                        <tr>
                                            <th class="px-6 py-3 text-sm font-semibold text-slate-700 dark:text-navy-100">#</th>
                                            <th class="px-6 py-3 text-sm font-semibold text-slate-700 dark:text-navy-100">Profile</th>
                                            <th class="px-6 py-3 text-sm font-semibold text-slate-700 dark:text-navy-100">Agent Fullname</th>
                                            <th class="px-6 py-3 text-sm font-semibold text-slate-700 dark:text-navy-100">Team Group</th>
                                            <th class="px-6 py-3 text-sm font-semibold text-slate-700 dark:text-navy-100">Username</th>
                                            <th class="px-6 py-3 text-sm font-semibold text-slate-700 dark:text-navy-100">Password</th>
                                            <th class="px-6 py-3 text-sm font-semibold text-slate-700 dark:text-navy-100">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 dark:divide-navy-700">
                                        <?php
                                        // SQL query to fetch data from the database
                                        $sql = "SELECT * FROM `tbl_acc_agent` 
                                            INNER JOIN tbl_agents_info ON tbl_agents_info.Agent_infoID = tbl_acc_agent.AgentInfo_Id
                                            LEFT JOIN tbl_team ON tbl_team.TeamID = tbl_agents_info.AgentTeam_ID";
                                        $result = mysqli_query($con, $sql);
                                        $count = 1; // Initialize counter

                                        if (mysqli_num_rows($result) > 0) {
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                // Image path
                                                $logoPath = !empty($row['IMAGEID']) ? "parakalan/uploads/" . $row['IMAGEID'] : "admin/uploads/default.png";
                                                $logo = "<img src='" . htmlspecialchars($logoPath) . "' alt='Image' class='rounded-full' style='width: 50px; height: 50px; object-fit: cover;'>";
                                                // Full name
                                                $name = htmlspecialchars($row['FN']) . ' ' . htmlspecialchars($row['MN']) . '. ' . htmlspecialchars($row['LN']);
                                        ?>
                                        <tr class="hover:bg-slate-100 dark:hover:bg-navy-700">
                                            <td class="px-6 py-4"><?php echo $count++; ?></td>
                                            <td class="px-6 py-4"><?php echo $logo ?></td>
                                            <td class="px-6 py-4"><?php echo $name; ?></td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars($row['Team_Name']); ?></td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars($row['username']); ?></td>
                                            <td class="px-6 py-4">*******</td>
                                            <td class="px-6 py-4 space-x-2">
                                            <button onclick="showEditModal(<?php echo $row['Agent_accID']; ?>, 
                                '<?php echo addslashes($row['FN']); ?>',
                                '<?php echo addslashes($row['MN']); ?>',
                                '<?php echo addslashes($row['LN']); ?>',
                                '<?php echo addslashes($row['username']); ?>',
                                '<?php echo htmlspecialchars($row['Team_Name']); ?>',
                                '<?php echo $row['role']; ?>')"
        class="btn size-8 p-1 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25 rounded-md">
    <i class="fa fa-edit"></i>
</button>

                                                <button onclick="showDeleteModal(<?php echo $row['Agent_accID']; ?>, '<?php echo $name; ?>')"
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
                        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
                        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
                        <script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                        
                        <div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
    <div class="modal-container bg-white dark:bg-navy-800 w-11/12 rounded-lg shadow-lg z-50 overflow-y-auto max-h-[90vh] width-modal">
        <div class="modal-content py-6 text-left px-8">
            <div class="flex justify-between items-center pb-3">
                <p class="text-2xl font-bold text-slate-700 dark:text-navy-100">Edit Account Information</p>
                <div class="modal-close cursor-pointer z-50" onclick="closeModal('editModal')">
                    <svg class="fill-current text-black dark:text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                        <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                    </svg>
                </div>
            </div>

            <!-- Corrected form method and action -->
            <form method="POST" action="config/configuration/editUserMGT.php" enctype="multipart/form-data">
                <input type="hidden" id="editUserId" name="user_id">

                <!-- Row with 3 input fields -->
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2" for="editFirstName">First Name</label>
                        <input type="text" id="editFirstName" name="first_name" class="form-input w-full rounded-lg border border-slate-300 dark:border-navy-600 px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2" for="editMiddleName">Middle Name</label>
                        <input type="text" id="editMiddleName" name="middle_name" class="form-input w-full rounded-lg border border-slate-300 dark:border-navy-600 px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2" for="editLastName">Last Name</label>
                        <input type="text" id="editLastName" name="last_name" class="form-input w-full rounded-lg border border-slate-300 dark:border-navy-600 px-3 py-2" required>
                    </div>
                </div>

                <!-- Row with 3 input fields in a single row -->
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2" for="editUsername">Username</label>
                        <input type="text" id="editUsername" name="username" class="form-input w-full rounded-lg border border-slate-300 dark:border-navy-600 px-3 py-2" required>
                    </div>
                    <div>
    <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2" for="editTeam">Team</label>
    <select id="editTeam" name="team_name" class="form-select w-full rounded-lg border border-slate-300 dark:border-navy-600 px-3 py-2" required>
    <option value="">Select Team</option>
    <?php
    // Fetch teams from database
    $teamQuery = "SELECT * FROM tbl_team";
    $teamResult = mysqli_query($con, $teamQuery);

    // Get selected team name from the database (modify as needed)
    $selectedTeamName = isset($row['Team_Name']) ? htmlspecialchars($row['Team_Name']) : '';

    // Display the selected team but hide it from the dropdown
    if (!empty($selectedTeamName)) {
        echo '<option value="' . $selectedTeamName . '" selected hidden>' . $selectedTeamName . '</option>';
    }

    // Loop through available teams
    while ($team = mysqli_fetch_assoc($teamResult)) {
        if ($team['Team_Name'] !== $selectedTeamName) { // Exclude the already selected team from the dropdown
            echo '<option value="' . htmlspecialchars($team['Team_Name']) . '">' . htmlspecialchars($team['Team_Name']) . '</option>';
        }
    }
    ?>
</select>

</div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2" for="editRole">Role</label>
                        <select id="editRole" name="role" class="form-select w-full rounded-lg border border-slate-300 dark:border-navy-600 px-3 py-2" required>
                            <option value="">Select Role</option>
                            <option value="1">Superadmin</option>
                            <option value="0">Agent</option>
                        </select>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end pt-2">
                    <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-navy-100 bg-slate-200 hover:bg-slate-300 mr-2">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-success hover:bg-success-dark">Update Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

                        <!-- Delete Modal -->
                        <div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
                            <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
                            <div class="modal-container bg-white dark:bg-navy-800 w-11/12 md:max-w-xl mx-auto rounded shadow-lg z-50 overflow-y-auto">
                                <div class="modal-content py-4 text-left px-6">
                                    <div class="flex justify-between items-center pb-3">
                                        <p class="text-2xl font-bold text-slate-700 dark:text-navy-100">Delete User</p>
                                        <div class="modal-close cursor-pointer z-50" onclick="closeModal('deleteModal')">
                                            <svg class="fill-current text-black dark:text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                                                <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <form method="POST" action="config/configuration/deleteUserAcc.php">
                                        <input type="text" id="deleteUserId" name="id">
                                        <p class="text-slate-700 dark:text-navy-100 mb-4">Are you sure you want to delete <span id="deleteUserName" class="font-semibold"></span>? This action cannot be undone.</p>
                                        <div class="flex justify-end pt-2">
                                            <button type="button" onclick="closeModal('deleteModal')" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-navy-100 bg-slate-200 dark:bg-navy-600 hover:bg-slate-300 dark:hover:bg-navy-500 mr-2">Cancel</button>
                                            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-error hover:bg-error-dark">Delete</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Create Modal -->
                        <div id="createModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
                            <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
                            <div class="modal-container bg-white dark:bg-navy-800 w-11/12 rounded-lg shadow-lg z-50 overflow-y-auto max-h-[90vh] width-modal">
                                <div class="modal-content py-6 text-left px-8">
                                    <div class="flex justify-between items-center pb-3">
                                        <p class="text-2xl font-bold text-slate-700 dark:text-navy-100">Create New Account</p>
                                        <div class="modal-close cursor-pointer z-50" onclick="closeModal('createModal')">
                                            <svg class="fill-current text-black dark:text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                                                <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <form method="POST" action="config/configuration/createUserAcc.php" enctype="multipart/form-data">
                                        <!-- Row with 3 input fields -->
                                        <div class="grid grid-cols-3 gap-4 mb-4">
                                            <!-- First Name -->
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2" for="createFirstName">First Name</label>
                                                <input type="text" id="createFirstName" name="first_name" class="form-input w-full rounded-lg border border-slate-300 dark:border-navy-600 bg-transparent px-3 py-2 placeholder:text-slate-400/70 dark:placeholder:text-navy-200" required>
                                            </div>
                                            <!-- Middle Name -->
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2" for="createMiddleName">Middle Name</label>
                                                <input type="text" id="createMiddleName" name="middle_name" class="form-input w-full rounded-lg border border-slate-300 dark:border-navy-600 bg-transparent px-3 py-2 placeholder:text-slate-400/70 dark:placeholder:text-navy-200">
                                            </div>
                                            <!-- Last Name -->
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2" for="createLastName">Last Name</label>
                                                <input type="text" id="createLastName" name="last_name" class="form-input w-full rounded-lg border border-slate-300 dark:border-navy-600 bg-transparent px-3 py-2 placeholder:text-slate-400/70 dark:placeholder:text-navy-200" required>
                                            </div>
                                        </div>

                                        <!-- Row with 3 input fields in a single row -->
                                        <div class="grid grid-cols-3 gap-4 mb-4">
                                            <!-- Username -->
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2" for="createUsername">Username</label>
                                                <input type="text" id="createUsername" name="username" class="form-input w-full rounded-lg border border-slate-300 dark:border-navy-600 bg-transparent px-3 py-2 placeholder:text-slate-400/70 dark:placeholder:text-navy-200" required>
                                            </div>
                                            <!-- Password -->
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2" for="createPassword">Password</label>
                                                <input type="password" id="createPassword" name="password" class="form-input w-full rounded-lg border border-slate-300 dark:border-navy-600 bg-transparent px-3 py-2 placeholder:text-slate-400/70 dark:placeholder:text-navy-200" required>
                                            </div>
                                            <!-- Role -->
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2" for="createRole">Role</label>
                                                <select id="createRole" name="role" class="form-select w-full rounded-lg border border-slate-300 dark:border-navy-600 bg-transparent px-3 py-2 placeholder:text-slate-400/70 dark:placeholder:text-navy-200" required>
                                                    <option value="">Select Role</option>
                                                    <option value="1">Superadmin</option>
                                                    <option value="0">Agent</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-3 gap-4 mb-4">
                                            <!-- Team -->
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2" for="createTeam">Team</label>
                                                <select id="createTeam" name="team_name" class="form-select w-full rounded-lg border border-slate-300 dark:border-navy-600 bg-transparent px-3 py-2 placeholder:text-slate-400/70 dark:placeholder:text-navy-200" required>
                                                    <option value="">Select Team</option>
                                                    <?php
                                                    // Fetch teams from database
                                                    $teamQuery = "SELECT * FROM tbl_team";
                                                    $teamResult = mysqli_query($con, $teamQuery);
                                                    while ($team = mysqli_fetch_assoc($teamResult)) {
                                                        echo '<option value="' . htmlspecialchars($team['TeamID']) . '">' . htmlspecialchars($team['Team_Name']) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <!-- Upload Image Field -->
                                            <div>
                                                <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2 form-control" for="createProfileImage">Upload Profile Image</label>
                                                <input type="file" id="createProfileImage" name="logo" accept="image/*" class="form-input w-full rounded-lg border border-slate-300 dark:border-navy-600 bg-transparent px-3 py-2 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 dark:file:bg-navy-600 dark:file:text-navy-100 dark:hover:file:bg-navy-500">
                                            </div>
                                        </div>
                                        <!-- Buttons -->
                                        <div class="flex justify-end pt-2">
                                            <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-navy-100 bg-slate-200 dark:bg-navy-600 hover:bg-slate-300 dark:hover:bg-navy-500 mr-2">Cancel</button>
                                            <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-success hover:bg-success-dark">Create Account</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                       <!-- Success Modal -->
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

    // Add Create New Account button
    $('.create-btn').html(
        '<button onclick="showCreateModal()" class="btn bg-primary text-white ml-4 px-4 py-2 rounded-lg hover:bg-primary-dark">Create New Account</button>'
    );
});

function showEditModal(id, firstName, middleName, lastName, username, team, role) {
    document.getElementById('editUserId').value = id;
    document.getElementById('editFirstName').value = firstName;
    document.getElementById('editMiddleName').value = middleName;
    document.getElementById('editLastName').value = lastName;
    document.getElementById('editUsername').value = username;

    // Wait until the select options are loaded
    setTimeout(() => {
        let teamSelect = document.getElementById('editTeam');
        let roleSelect = document.getElementById('editRole');

        if (teamSelect) {
            for (let option of teamSelect.options) {
                if (option.value == team) {
                    option.selected = true;
                    break;
                }
            }
        }

        if (roleSelect) {
            for (let option of roleSelect.options) {
                if (option.value == role) {
                    option.selected = true;
                    break;
                }
            }
        }
    }, 100); // Small delay to ensure options exist

    document.getElementById('editModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}


// Show Delete Modal
function showDeleteModal(id, fullName) {
    document.getElementById('deleteUserId').value = id;
    document.getElementById('deleteUserName').textContent = fullName;

    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

// Show Create Modal
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