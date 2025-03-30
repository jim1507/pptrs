<?php include 'template/header.php'; ?>

<body x-data="" class="is-header-blur" x-bind="$store.global.documentBody">
    <!-- App preloader-->
    <div class="app-preloader fixed z-50 grid h-full w-full place-content-center bg-slate-50 dark:bg-navy-900">
        <div class="app-preloader-inner relative inline-block size-48"></div>
    </div>

    <!-- Page Wrapper -->
    <div id="root" class="min-h-100vh flex grow bg-slate-50 dark:bg-navy-900" x-cloak="">
        <?php include 'template/sidebar.php'; ?>
        <?php include 'template/navbar.php'; ?>

      <!-- Main Content Wrapper -->
      <main class="main-content filemanager-app w-full pb-6">
        <div class="flex items-center justify-between space-x-2 px-[var(--margin-x)] pb-4 pt-5 transition-all duration-[.25s]">
          <div class="flex items-center space-x-1">
            <h3 class="text-lg font-medium text-slate-700 line-clamp-1 dark:text-navy-50">
             Customer Management
            </h3>
          </div>
        </div>
        
        <div class="flex flex-col" x-data="{activeTab:'tab-recent'}">
          <div>
            <div x-transition:enter="transition-all duration-500 easy-in-out" x-transition:enter-start="opacity-0 [transform:translate3d(1rem,0,0)]" x-transition:enter-end="opacity-100 [transform:translate3d(0,0,0)]" x-init="$nextTick(()=>new Swiper($el,{slidesPerView: 'auto', spaceBetween: 20}))" class="swiper px-[var(--margin-x)] pt-4 transition-all duration-[.25s]">
              <div class="swiper-wrapper">
                <?php
                $team = $_SESSION['AgentTeam_ID'];
                if ($role == 1) {
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
                                tbl_agents_info tl ON a.Agent_infoID = tl.Agent_infoID AND aa.role = 2 
                            LEFT JOIN 
                                house_purchase hp ON a.Agent_infoID = hp.agentID 
                            WHERE 
                                t.TeamID != 1  
                            GROUP BY 
                                t.TeamID, t.Team_Name
                            ORDER BY 
                                total_houses_sold DESC;";
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
                    $colors = ['primary', 'warning', 'info', 'secondary', 'success', 'error'];
                    $color_index = 0;
                    
                    while ($row = mysqli_fetch_assoc($result)) {
                        $team_name = $row['Team_Name'];
                        $total_houses_sold = (int)$row['total_houses_sold'];
                        $member_count = (int)$row['member_count'];
                        $color_class = $colors[$color_index % count($colors)];
                        $color_index++;
                ?>
                <div class="card swiper-slide w-56 shrink-0 p-3 pt-4">
    <a href="CustomerListing.php?team_id=<?php echo $row['TeamID']; ?>" class="block hover:cursor-pointer">
        <div class="flex items-center justify-between">
            <img class="w-14" src="images/folders/folder-<?php echo $color_class; ?>.svg" alt="folder">
        </div>
        <div class="pt-5 text-base font-medium tracking-wide text-<?php echo $color_class; ?> dark:text-<?php echo ($color_class == 'secondary' ? 'secondary-light' : ($color_class == 'primary' ? 'accent-light' : '')); ?>">
            <?php echo htmlspecialchars($team_name); ?>
        </div>
        <div class="mt-1.5 flex items-center justify-between">
            <p class="text-slate-400 text-xs+ dark:text-navy-300">
                <?php echo $member_count; ?> members
            </p>
            <p class="font-medium text-slate-600 dark:text-navy-100">
                <?php echo $total_houses_sold; ?> sold
            </p>
        </div>
    </a>
</div>

                <?php
                    }
                } else {
                    echo '<div class="px-[var(--margin-x)]">No teams found</div>';
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
    
    <div id="x-teleport-target"></div>
    <script>
      window.addEventListener("DOMContentLoaded", () => Alpine.start());
    </script>
</body>
</html>