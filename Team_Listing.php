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
                                                Team Name</th>
                                            <th
                                                class="px-6 py-3 text-sm font-semibold text-slate-700 dark:text-navy-100">
                                                Team Leader</th>
                                            <th
                                                class="px-6 py-3 text-sm font-semibold text-slate-700 dark:text-navy-100">
                                                No. of Members</th>
                                            <th
                                                class="px-6 py-3 text-sm font-semibold text-slate-700 dark:text-navy-100">
                                                No. of Sold Houses</th>
                                                <?php if ($_SESSION['auth_user']['role'] == '1') { ?>
                                                <th
                                                class="px-6 py-3 text-sm font-semibold text-slate-700 dark:text-navy-100">
                                                Action</th>
                                                <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 dark:divide-navy-700">
                                        <?php
                $team = $_SESSION['AgentTeam_ID'];
                if ($role == 1) {
                    $sql = "SELECT 
    t.Team_Name, 
    COUNT(DISTINCT CASE WHEN aa.role = 0 THEN a.Agent_infoID END) AS member_count,
    MAX(
        CASE 
            WHEN aa.role = 2 THEN CONCAT(tl.FN, ' ', tl.MN, '. ', tl.LN)
            ELSE NULL
        END
    ) AS Team_Leader_Name, 
    COUNT(hp.house_id) AS total_houses_sold,
    t.TeamID, 
    a.Agent_infoID 
FROM 
    tbl_team t
LEFT JOIN 
    tbl_agents_info a ON a.AgentTeam_ID = t.TeamID  
LEFT JOIN 
    tbl_acc_agent aa ON aa.AgentInfo_Id = a.Agent_infoID 
LEFT JOIN 
    tbl_agents_info tl ON a.Agent_infoID = tl.Agent_infoID AND aa.role = 2
LEFT JOIN 
    house_purchase hp ON a.Agent_infoID = hp.agentID 
WHERE 
    t.TeamID != 1  
GROUP BY 
    t.TeamID, t.Team_Name
ORDER BY 
    total_houses_sold DESC;
";
                } else {
                    $sql = "SELECT 
                                t.Team_Name, 
                                COUNT(DISTINCT CASE WHEN aa.role = 0 THEN a.Agent_infoID END) AS member_count,
                                MAX(CONCAT(tl.FN, ' ', tl.MN, '. ', tl.LN)) AS Team_Leader_Name, 
                                COUNT(hp.house_id) AS total_houses_sold,
                                t.TeamID,
                                 a.Agent_infoID  
                            FROM 
                                tbl_team t
                            LEFT JOIN 
                                tbl_agents_info a ON a.AgentTeam_ID = t.TeamID  
                            LEFT JOIN 
                                tbl_acc_agent aa ON aa.AgentInfo_Id = a.Agent_infoID 
                            LEFT JOIN 
                                tbl_agents_info tl ON tl.Agent_infoID = aa.AgentInfo_Id AND aa.role = 2 
                            LEFT JOIN 
                                house_purchase hp ON a.Agent_infoID = hp.agentID 
                            WHERE
                                t.TeamID != 1 AND t.TeamID = $team
                            GROUP BY 
                                t.TeamID, t.Team_Name";
                }

                $result = mysqli_query($con, $sql);

                if (mysqli_num_rows($result) > 0) {
                    $count = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        $total_houses_sold = (int)$row['total_houses_sold'];

                       
                ?>
                                        <tr class="hover:bg-slate-100 dark:hover:bg-navy-700">
                                            <td class="px-6 py-4"><?php echo $count++; ?></td>
                                            <td class="px-6 py-4">
    <a href="TeamMember.php?TeamID=<?php echo urlencode($row['TeamID']); ?>" class="text-blue-500 hover:underline">
        <?php echo htmlspecialchars($row['Team_Name']); ?>
    </a>
</td>

                                            </td>
                                            <td class="px-6 py-4">
                                                <?php echo htmlspecialchars($row['Team_Leader_Name']); ?></td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars($row['member_count']); ?>
                                            </td>
                                            <td class="px-6 py-4"><?php echo $total_houses_sold; ?></td>
                                            <?php if ($_SESSION['auth_user']['role'] == '1') { ?>       
                                            <td class="px-6 py-4 space-x-2">
    <button onclick="showEditModal(<?php echo $row['TeamID']; ?>, '<?php echo addslashes($row['Team_Name']); ?>', '<?php echo $row['Agent_infoID']; ?>')"
        class="btn size-8 p-1 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25 rounded-md">
        <i class="fa fa-edit"></i>
    </button>
    <button onclick="showDeleteModal(<?php echo $row['TeamID']; ?>, '<?php echo addslashes($row['Team_Name']); ?>')"
        class="btn size-8 p-1 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25 rounded-md">
        <i class="fa fa-trash-alt"></i>
    </button>
</td>
<?php } ?>
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

                    <!-- Edit Team Modal -->
<div id="editModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
    <div class="modal-container bg-white dark:bg-navy-800 w-11/12 rounded-lg shadow-lg z-50 overflow-y-auto max-h-[90vh] width-modal">
        <div class="modal-content py-6 text-left px-8">
            <div class="flex justify-between items-center pb-3">
                <p class="text-2xl font-bold text-slate-700 dark:text-navy-100">Edit Team Information</p>
                <div class="modal-close cursor-pointer z-50" onclick="closeModal('editModal')">
                    <svg class="fill-current text-black dark:text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                        <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                    </svg>
                </div>
            </div>

            <form method="POST" action="config/configuration/editTeam.php">
                <input type="hidden" id="editTeamId" name="team_id">

                <!-- Team Name -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2" for="editTeamName">Team Name</label>
                    <input type="text" id="editTeamName" name="team_name" class="form-input w-full rounded-lg border border-slate-300 dark:border-navy-600 px-3 py-2" required>
                </div>

                <!-- Team Leader -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2" for="editTeamLeader">Team Leader</label>
                    <select id="editTeamLeader" name="team_leader" class="form-select w-full rounded-lg border border-slate-300 dark:border-navy-600 px-3 py-2" required>
                        <option value="">Select Team Leader</option>
                        <?php
                        // Fetch available agents who can be team leaders
                        $leaderQuery = "SELECT a.Agent_infoID, CONCAT(a.FN, ' ', a.MN, '. ', a.LN) AS full_name 
                                       FROM tbl_agents_info a
                                       LEFT JOIN tbl_acc_agent aa ON aa.AgentInfo_Id = a.Agent_infoID
                                       "; // Agents who are not already leaders
                        $leaderResult = mysqli_query($con, $leaderQuery);
                        
                        while ($leader = mysqli_fetch_assoc($leaderResult)) {
                            echo '<option value="' . htmlspecialchars($leader['Agent_infoID']) . '">' . htmlspecialchars($leader['full_name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end pt-2">
                    <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-navy-100 bg-slate-200 hover:bg-slate-300 mr-2">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-success hover:bg-success-dark">Update Team</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Team Modal -->
<div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
    <div class="modal-container bg-white dark:bg-navy-800 w-11/12 md:max-w-xl mx-auto rounded shadow-lg z-50 overflow-y-auto">
        <div class="modal-content py-4 text-left px-6">
            <div class="flex justify-between items-center pb-3">
                <p class="text-2xl font-bold text-slate-700 dark:text-navy-100">Delete Team</p>
                <div class="modal-close cursor-pointer z-50" onclick="closeModal('deleteModal')">
                    <svg class="fill-current text-black dark:text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                        <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                    </svg>
                </div>
            </div>
            <form method="POST" action="config/configuration/deleteTeam.php">
                <input type="hidden" id="deleteTeamId" name="team_id">
                <p class="text-slate-700 dark:text-navy-100 mb-4">Are you sure you want to delete <span id="deleteTeamName" class="font-semibold"></span>? This action cannot be undone.</p>
                <div class="flex justify-end pt-2">
                    <button type="button" onclick="closeModal('deleteModal')" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-navy-100 bg-slate-200 dark:bg-navy-600 hover:bg-slate-300 dark:hover:bg-navy-500 mr-2">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-error hover:bg-error-dark">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Team Modal -->
<div id="createModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
    <div class="modal-container bg-white dark:bg-navy-800 w-11/12 rounded-lg shadow-lg z-50 overflow-y-auto max-h-[90vh] width-modal">
        <div class="modal-content py-6 text-left px-8">
            <div class="flex justify-between items-center pb-3">
                <p class="text-2xl font-bold text-slate-700 dark:text-navy-100">Create New Team</p>
                <div class="modal-close cursor-pointer z-50" onclick="closeModal('createModal')">
                    <svg class="fill-current text-black dark:text-white" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
                        <path d="M14.53 4.53l-1.06-1.06L9 7.94 4.53 3.47 3.47 4.53 7.94 9l-4.47 4.47 1.06 1.06L9 10.06l4.47 4.47 1.06-1.06L10.06 9z"></path>
                    </svg>
                </div>
            </div>
            <form method="POST" action="config/configuration/createTeam.php">
                <!-- Team Name -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2" for="createTeamName">Team Name</label>
                    <input type="text" id="createTeamName" name="team_name" class="form-input w-full rounded-lg border border-slate-300 dark:border-navy-600 bg-transparent px-3 py-2 placeholder:text-slate-400/70 dark:placeholder:text-navy-200" required>
                </div>

                <!-- Team Leader -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 dark:text-navy-100 mb-2" for="createTeamLeader">Team Leader</label>
                    <select id="createTeamLeader" name="team_leader" class="form-select w-full rounded-lg border border-slate-300 dark:border-navy-600 bg-transparent px-3 py-2 placeholder:text-slate-400/70 dark:placeholder:text-navy-200" required>
                        <option value="0">Select Team Leader</option>
                        <?php
                        // Fetch available agents who can be team leaders
                        $leaderQuery = "SELECT a.Agent_infoID, CONCAT(a.FN, ' ', a.MN, '. ', a.LN) AS full_name 
                                       FROM tbl_agents_info a
                                       LEFT JOIN tbl_acc_agent aa ON aa.AgentInfo_Id = a.Agent_infoID
                                       WHERE aa.role = 0 OR aa.role IS NULL"; // Agents who are not already leaders
                        $leaderResult = mysqli_query($con, $leaderQuery);
                        
                        while ($leader = mysqli_fetch_assoc($leaderResult)) {
                            echo '<option value="' . htmlspecialchars($leader['Agent_infoID']) . '">' . htmlspecialchars($leader['full_name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end pt-2">
                    <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-navy-100 bg-slate-200 dark:bg-navy-600 hover:bg-slate-300 dark:hover:bg-navy-500 mr-2">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium text-white bg-success hover:bg-success-dark">Create Team</button>
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
                                <?php if ($_SESSION['auth_user']['role'] == '1') { ?>
                            // Add Create New Account button
                            $('.create-btn').html(
                                '<button onclick="showCreateModal()" class="btn bg-primary text-white ml-4 px-4 py-2 rounded-lg hover:bg-primary-dark">Create New TEAM</button>'
                            );
                            <?php } ?>
                        });

                        function showEditModal(teamId, teamName, teamLeaderId) {
    document.getElementById('editTeamId').value = teamId;
    document.getElementById('editTeamName').value = teamName;
    
    // Set the selected team leader
    let leaderSelect = document.getElementById('editTeamLeader');
    for (let option of leaderSelect.options) {
        if (option.value == teamLeaderId) {
            option.selected = true;
            break;
        }
    }
    
    document.getElementById('editModal').classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function showDeleteModal(teamId, teamName) {
    document.getElementById('deleteTeamId').value = teamId;
    document.getElementById('deleteTeamName').textContent = teamName;
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