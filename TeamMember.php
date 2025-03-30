<?php include 'template/header.php'; ?>

<body x-data="" class="is-header-blur" x-bind="$store.global.documentBody">
    <!-- App preloader -->
    <div class="app-preloader fixed z-50 grid h-full w-full place-content-center bg-slate-50 dark:bg-navy-900">
        <div class="app-preloader-inner relative inline-block size-48"></div>
    </div>

    <!-- Page Wrapper -->
    <div id="root" class="min-h-100vh flex grow bg-slate-50 dark:bg-navy-900" x-cloak="">
        <?php include 'template/sidebar.php'; ?>
        <?php include 'template/navbar.php'; ?>

        <?php
        // Check if TeamID is set
        if (isset($_GET['TeamID']) && !empty($_GET['TeamID'])) {
            // Sanitize the input to prevent XSS and SQL injection
            $teamID = htmlspecialchars($_GET['TeamID']);

            // SQL query to fetch team members
            $sql = "
            SELECT 
                t.TeamID,
                t.Team_Name,
                a.Agent_infoID,
                CONCAT(a.FN, ' ', a.MN, '. ', a.LN) AS NameOfUser,
                aa.role,
                a.IMAGEID,
                COUNT(hp.house_id) AS Total_Sold,
                RANK() OVER (PARTITION BY t.TeamID ORDER BY COUNT(hp.house_id) DESC) AS SalesRank
            FROM 
                tbl_team t
            LEFT JOIN 
                tbl_agents_info a ON a.AgentTeam_ID = t.TeamID
            LEFT JOIN 
                tbl_acc_agent aa ON aa.AgentInfo_Id = a.Agent_infoID
            LEFT JOIN 
                house_purchase hp ON a.Agent_infoID = hp.agentID
            WHERE 
                t.TeamID = $teamID
            GROUP BY 
                t.TeamID, t.Team_Name, a.Agent_infoID, aa.role, a.FN, a.MN, a.LN, a.IMAGEID
            ORDER BY 
                CASE 
                    WHEN aa.role = 2 THEN 0  -- Place leader (role = 2) at the top
                    ELSE 1
                END,
                SalesRank ASC;
            ";

            $result = mysqli_query($con, $sql);

            // Check if records exist
            if (mysqli_num_rows($result) > 0) {
                // Get team name and reset pointer for looping
                $teamInfo = mysqli_fetch_assoc($result);
                $teamName = htmlspecialchars($teamInfo['Team_Name']);
                mysqli_data_seek($result, 0); // Reset pointer for loop

                // Find the Top Seller dynamically
                $topSellerID = null;
                $highestSales = 0;

                while ($row = mysqli_fetch_assoc($result)) {
                    if ($row['Total_Sold'] > $highestSales) {
                        $highestSales = $row['Total_Sold'];
                        $topSellerID = $row['Agent_infoID'];
                    }
                }

                // Reset pointer again for displaying cards
                mysqli_data_seek($result, 0);
                ?>

                <!-- Main Content Wrapper -->
                <main class="main-content w-full px-[var(--margin-x)] pb-8">
                    <div class="flex items-center justify-between py-5 lg:py-6">
                        <div class="flex items-center space-x-1">
                            <h2 class="text-xl font-medium text-slate-700 line-clamp-1 dark:text-navy-50 lg:text-2xl">
                                Team: <?php echo $teamName; ?>
                            </h2>
                        </div>

                        <!-- Search Bar -->
                        <div class="flex items-center space-x-2">
                            <label class="relative hidden sm:flex">
                                <input id="searchInput" onkeyup="filterCards()"
                                    class="form-input peer h-9 w-full rounded-full border border-slate-300 bg-transparent px-3 py-2 pl-9 text-xs+ placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                    placeholder="Search member..." type="text">
                                <span
                                    class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 transition-colors duration-200"
                                        fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M3.316 13.781l.73-.171-.73.171zm0-5.457l.73.171-.73-.171zm15.473 0l.73-.171-.73.171zm0 5.457l.73.171-.73-.171zm-5.008 5.008l-.171-.73.171.73zm-5.457 0l-.171.73.171-.73zm0-15.473l-.171-.73.171.73zm5.457 0l.171-.73-.171.73zM20.47 21.53a.75.75 0 101.06-1.06l-1.06 1.06zM4.046 13.61a11.198 11.198 0 010-5.115l-1.46-.342a12.698 12.698 0 000 5.8l1.46-.343zm14.013-5.115a11.196 11.196 0 010 5.115l1.46.342a12.698 12.698 0 000-5.8l-1.46.343zm-4.45 9.564a11.196 11.196 0 01-5.114 0l-.342 1.46c1.907.448 3.892.448 5.8 0l-.343-1.46zM8.496 4.046a11.198 11.198 0 015.115 0l.342-1.46a12.698 12.698 0 00-5.8 0l.343 1.46zm0 14.013a5.97 5.97 0 01-4.45-4.45l-1.46.343a7.47 7.47 0 005.568 5.568l.342-1.46zm5.457 1.46a7.47 7.47 0 005.568-5.567l-1.46-.342a5.97 5.97 0 01-4.45 4.45l.342 1.46zM13.61 4.046a5.97 5.97 0 014.45 4.45l1.46-.343a7.47 7.47 0 00-5.568-5.567l-.342 1.46zm-5.457-1.46a7.47 7.47 0 00-5.567 5.567l1.46.342a5.97 5.97 0 014.45-4.45l-.343-1.46zm8.652 15.28l3.665 3.664 1.06-1.06-3.665-3.665-1.06 1.06z">
                                        </path>
                                    </svg>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div id="cardContainer" class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 sm:gap-5 lg:gap-6">
                        <?php
                        // Display all agents dynamically with "Top Seller" for the highest seller
                        while ($row = mysqli_fetch_assoc($result)) {
                            $name = htmlspecialchars($row['NameOfUser']);
                            $role = ($row['role'] == 2) ? "Team Leader" : "Agent";
                            $totalSold = $row['Total_Sold'];
                            $isTopSeller = ($row['Agent_infoID'] == $topSellerID);

                            // Correctly set image path inside the loop
                            $logoPath = !empty($row['IMAGEID']) ? "parakalan/uploads/" . htmlspecialchars($row['IMAGEID']) : "admin/uploads/default.png";
                            $logo = "<img src='$logoPath' alt='Image' class='rounded-full' style='width: 50px; height: 50px; object-fit: cover;'>";

                            // Agent card with dynamic data
                            ?>

                            <!-- Agent Card -->
                            <div class="card items-center justify-between lg:flex-row agent-card"
                                data-name="<?php echo strtolower($name); ?>"
                                data-role="<?php echo strtolower($role); ?>"
                                data-sales="<?php echo $totalSold; ?>">
                                <div
                                    class="flex flex-col items-center p-4 text-center sm:p-5 lg:flex-row lg:space-x-4 lg:text-left">
                                    <div class="avatar size-18 lg:h-12 lg:w-12">
                                        <?php echo $logo; ?>
                                    </div>
                                    <div class="mt-2 lg:mt-0">
                                        <div class="flex items-center justify-center space-x-1">
                                            <h4 class="text-base font-medium text-slate-700 line-clamp-1 dark:text-navy-100">
                                                <?php echo $name; ?>
                                            </h4>
                                            <!-- Show "Top Seller" only for highest sales -->
                                            <?php if ($isTopSeller) { ?>
                                                <button
                                                    class="btn hidden h-6 rounded-full px-2 text-xs font-medium text-white lg:inline-flex"
                                                    style="background: linear-gradient(to right, #f59e0b, #ef4444); hover:opacity-80;">
                                                    🏆 Top Seller of the Month
                                                </button>
                                            <?php } ?>
                                        </div>
                                        <p class="text-xs+"><?php echo $role; ?> | Total Sales: <?php echo $totalSold; ?></p>
                                    </div>
                                </div>
                            </div>

                        <?php
                        }
                        ?>
                    </div>
                </main>
                <?php
            } else {
                echo "<div class='text-center px-6 py-4 text-slate-500'>No members found in this team!</div>";
            }
        } else {
            echo "<div class='text-center px-6 py-4 text-red-500'>Invalid Team ID!</div>";
        }
        ?>
    </div>

    <!-- Alpine.js Teleport -->
    <div id="x-teleport-target"></div>
    <script>
        window.addEventListener("DOMContentLoaded", () => Alpine.start());

        // ✅ JavaScript to filter agent cards dynamically
        function filterCards() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let cards = document.querySelectorAll(".agent-card");

            cards.forEach(function (card) {
                let name = card.getAttribute("data-name");
                let role = card.getAttribute("data-role");
                let sales = card.getAttribute("data-sales");

                // Check if input matches any field
                if (name.includes(input) || role.includes(input) || sales.includes(input)) {
                    card.style.display = "block"; // Show matching cards
                } else {
                    card.style.display = "none"; // Hide unmatched cards
                }
            });
        }
    </script>
</body>

</html>
