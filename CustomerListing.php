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
                                            <th class="px-6 py-3 text-sm font-semibold text-slate-700 dark:text-navy-100">House Name</th>
                                            <th class="px-6 py-3 text-sm font-semibold text-slate-700 dark:text-navy-100">Customer Name</th>      
                                            <th class="px-6 py-3 text-sm font-semibold text-slate-700 dark:text-navy-100">Agent Name</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-200 dark:divide-navy-700">
<?php
// Check if TeamID is set
if (isset($_GET['team_id']) && !empty($_GET['team_id'])) {
    // Sanitize input
    $teamID = intval($_GET['team_id']); // use intval to sanitize as integer

    // Updated query to show one row per customer
    $sql = "SELECT 
                h.HouseName, 
                CONCAT(c.FN, ' ', c.MN, '. ', c.LN) AS customerName,
                CONCAT(ai.FN, ' ', ai.MN, '. ', ai.LN) AS agentName
            FROM 
                house_purchase hp
            INNER JOIN 
                tbl_customer c ON hp.customer_id = c.customer_id
            LEFT JOIN 
                tbl_house h ON hp.house_id = h.HouseID
            LEFT JOIN 
                tbl_acc_agent ac ON hp.agentID = ac.Agent_accID
            LEFT JOIN 
                tbl_agents_info ai ON ac.AgentInfo_Id = ai.Agent_infoID
            LEFT JOIN 
                tbl_team t ON ai.AgentTeam_ID = t.TeamID
            WHERE ai.AgentTeam_ID = $teamID";

    $result = mysqli_query($con, $sql);
    $count = 1;

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $customername = htmlspecialchars($row['customerName'] ?? 'N/A');
            $agentname = htmlspecialchars($row['agentName'] ?? 'N/A');
            $housename = htmlspecialchars($row['HouseName'] ?? 'N/A');
?>
<tr class="hover:bg-slate-100 dark:hover:bg-navy-700">
    <td class="px-6 py-4"><?php echo $count++; ?></td>
    <td class="px-6 py-4"><?php echo $housename; ?></td>
    <td class="px-6 py-4"><?php echo $customername; ?></td>
    <td class="px-6 py-4"><?php echo $agentname; ?></td>
</tr>
<?php
        }
    } else {
        echo "<tr><td colspan='4' class='text-center px-6 py-4 text-slate-500'>No data available</td></tr>";
    }
} else {
    echo "<tr><td colspan='4' class='text-center px-6 py-4 text-slate-500'>No team selected</td></tr>";
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
                                    searchPlaceholder: "Search records..."
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
                                .attr('placeholder', 'Search records...')
                                .css('margin-bottom', '0');
                        });

                        function openModal(modalId) {
                            document.getElementById(modalId).classList.remove('hidden');
                            document.body.classList.add('overflow-hidden');
                        }

                        function closeModal(modalId) {
                            document.getElementById(modalId).classList.add('hidden');
                            document.body.classList.remove('overflow-hidden');
                        }

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