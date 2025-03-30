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
                <main class="main-content w-full pb-8">

                    <div class="mt-4 grid grid-cols-12 gap-4 px-[var(--margin-x)] transition-all duration-[.25s] sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
                    <div class="card col-span-12 mt-12 bg-gradient-to-r from-blue-500 to-blue-600 p-5 sm:col-span-12 sm:mt-0 sm:flex-row">
              <div class="flex justify-center sm:order-last">
                <img class="-mt-16 h-40 sm:mt-0" src="images/illustrations/agent.png" alt="image">
              </div>
            <div class="mt-2 flex-1 pt-2 text-center text-white sm:mt-0 sm:text-left">
  <span class="text-xl" id="greeting">
    Good Morning
  </span>, <span class="text-xl font-semibold">
    <?php echo $_SESSION['auth_user']['NameOfUser']; ?>
  </span>
  <p class="mt-2 leading-relaxed">This is the summary of your performance. Have a nice day!</p>
</div>
</div>
            <script>
  // Function to update the greeting based on the time of day
  function updateGreeting() {
    const hour = new Date().getHours(); // Get the current hour (0-23)
    const greetingElement = document.getElementById('greeting');

    if (hour >= 5 && hour < 12) {
      greetingElement.textContent = 'Good Morning';
    } else if (hour >= 12 && hour < 18) {
      greetingElement.textContent = 'Good Afternoon';
    } else {
      greetingElement.textContent = 'Good Evening';
    }
  }

  // Call the function to set the greeting when the page loads
  updateGreeting();
</script>

<?php

if ($role == 1) {
  $query = "
  SELECT 
      -- Total houses sold
      (SELECT COUNT(hp.house_id) FROM `house_purchase` hp) AS total_sold,
      
      -- Total available houses
      (SELECT SUM(h.unit_available) FROM tbl_house h WHERE h.unit_available > 0) AS total_available,
      
      -- Total available houses updated in the last 30 days
      (SELECT SUM(h.unit_available) FROM tbl_house h WHERE h.unit_available > 0 AND h.date_updated >= DATE_SUB(NOW(), INTERVAL 30 DAY)) AS total_available_prev,
      
      -- Total sold today
      (SELECT COUNT(hp.house_id) FROM `house_purchase` hp WHERE DATE(hp.date_sold) = CURDATE()) AS total_sold_today,
      
      -- Total agents
      (SELECT COUNT(ag.Agent_accID) FROM tbl_acc_agent ag) AS total_agents,
      
      -- Total teams
      (SELECT COUNT(DISTINCT ai.AgentTeam_ID) FROM tbl_agents_info ai) AS total_teams,
      
      -- Total customers
      (SELECT COUNT(c.customer_id) FROM tbl_customer c) AS total_customers,
      
      -- New members in the last 30 days
      (SELECT COUNT(DISTINCT hp.customer_id) FROM house_purchase hp WHERE hp.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) AS new_members
  ";
} elseif ($role == 2) {
  $query = "
  SELECT 
  -- Total houses sold by agents with role = 2 or filtered by TeamID for role 2
  (SELECT COUNT(hp.house_id)
   FROM `house_purchase` hp
   INNER JOIN `tbl_acc_agent` ag ON ag.Agent_accID = hp.agentID
   LEFT JOIN tbl_agents_info ai ON ag.AgentInfo_Id = ai.Agent_infoID
   WHERE ag.role = 2 AND (
       ai.AgentTeam_ID = (
           SELECT ai.AgentTeam_ID 
           FROM tbl_agents_info ai
           WHERE ai.Agent_infoID = $agent_id
       )
       OR ai.AgentTeam_ID IS NOT NULL
   )
  ) AS total_sold,
  
  -- Total available houses
  (SELECT SUM(h.unit_available) 
   FROM tbl_house h 
   WHERE h.unit_available > 0) AS total_available,
  
  -- Total available houses updated in the last 30 days
  (SELECT SUM(h.unit_available) 
   FROM tbl_house h 
   WHERE h.unit_available > 0 
     AND h.date_updated >= DATE_SUB(NOW(), INTERVAL 30 DAY)) AS total_available_prev,
  
  -- Total houses sold today (filtered by role and team if role = 2)
  (SELECT COUNT(hp.house_id)
   FROM `house_purchase` hp
   INNER JOIN `tbl_acc_agent` ag ON ag.Agent_accID = hp.agentID
   LEFT JOIN tbl_agents_info ai ON ag.AgentInfo_Id = ai.Agent_infoID
   WHERE DATE(hp.date_sold) = CURDATE()
   AND ag.role = 2 AND (
       ai.AgentTeam_ID = (
           SELECT ai.AgentTeam_ID
           FROM tbl_agents_info ai
           WHERE ai.Agent_infoID = $agent_id
       )
       OR ai.AgentTeam_ID IS NOT NULL
   )
  ) AS total_sold_today,
  
  -- Total houses sold by the agent's team
  (SELECT COUNT(hp.house_id)
   FROM `house_purchase` hp
   INNER JOIN `tbl_acc_agent` ag ON ag.Agent_accID = hp.agentID
   LEFT JOIN tbl_agents_info ai ON ag.AgentInfo_Id = ai.Agent_infoID
   WHERE ai.AgentTeam_ID = (
       SELECT ai.AgentTeam_ID
       FROM tbl_agents_info ai
       WHERE ai.Agent_infoID = $agent_id
   )
  ) AS total_team_sold

  ";
} elseif ($role == 0) {
  // Role = 0: Fetch data for the current agent only
  $query = "
  SELECT 
      -- Total houses sold by the current agent
      (SELECT COUNT(hp.house_id)
       FROM `house_purchase` hp
       WHERE hp.agentID = $agent_id
      ) AS total_sold,
       
      -- Total available houses
      (SELECT SUM(h.unit_available) 
       FROM tbl_house h 
       WHERE h.unit_available > 0) AS total_available,
      
      -- Total available houses updated in the last 30 days
      (SELECT SUM(h.unit_available) 
       FROM tbl_house h 
       WHERE h.unit_available > 0 
         AND h.date_updated >= DATE_SUB(NOW(), INTERVAL 30 DAY)) AS total_available_prev,
      
      -- Total houses sold today by the current agent
      (SELECT COUNT(hp.house_id)
       FROM `house_purchase` hp
       WHERE DATE(hp.date_sold) = CURDATE()
         AND hp.agentID = $agent_id
      ) AS total_sold_today,
      
      -- Total houses sold in the last 30 days by the current agent
      (SELECT COUNT(hp.house_id)
       FROM `house_purchase` hp
       WHERE hp.date_sold >= DATE_SUB(NOW(), INTERVAL 30 DAY)
         AND hp.agentID = $agent_id
      ) AS total_sold_prev,
      
      -- Total houses sold yesterday by the current agent
      (SELECT COUNT(hp.house_id)
       FROM `house_purchase` hp
       WHERE DATE(hp.date_sold) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
         AND hp.agentID = $agent_id
      ) AS total_sold_today_prev
  ";
} else {
  // Default to zero if role is not recognized
  $total_sold = 0;
  $total_available = 0;
  $total_available_prev = 0;
  $total_sold_today = 0;
  $total_agents = 0;
  $total_teams = 0;
  $total_customers = 0;
  $new_members = 0;
}

// Execute the query if valid role
if (isset($query)) {
  $result = mysqli_query($con, $query);
  $data = mysqli_fetch_assoc($result);

  $total_sold = $data['total_sold'] ?? 0;
  $total_available = $data['total_available'] ?? 0;
  $total_available_prev = $data['total_available_prev'] ?? 0;
  $total_sold_today = $data['total_sold_today'] ?? 0;
  $total_agents = $data['total_agents'] ?? 0;
  $total_teams = $data['total_teams'] ?? 0;
  $total_customers = $data['total_customers'] ?? 0;
  $new_members = $data['new_members'] ?? 0;
  $total_team_sold = $data['total_team_sold'] ?? 0;
}



// Function to calculate percentage change
function getPercentageChange($current, $previous) {
if ($previous == 0) {
    return $current > 0 ? 100 : 0; // Avoid division by zero
}
return round((($current - $previous) / $previous) * 100, 2);
}

// Set default values if not defined to avoid warnings
$total_sold_prev = isset($total_sold_prev) ? $total_sold_prev : 0;
$total_sold_today_prev = isset($total_sold_today_prev) ? $total_sold_today_prev : 0;
$total_team_sold_prev = isset($total_team_sold_prev) ? $total_team_sold_prev : 0;
$total_available_prev = isset($total_available_prev) ? $total_available_prev : 0;

// ✅ Apply conditional percentage logic for Total Sold
$change_sold = getPercentageChange($total_sold, $total_sold_prev);
$color_sold = $change_sold >= 50 ? 'text-success' : ($change_sold >= 10 ? 'text-warning' : 'text-danger');

// ✅ Apply conditional percentage logic for Total Sold Today
$change_sold_today = getPercentageChange($total_sold_today, $total_sold_today_prev);
$color_sold_today = $change_sold_today >= 20 ? 'text-success' : ($change_sold_today >= 10 ? 'text-warning' : 'text-danger');

// ✅ Apply conditional percentage logic for Total Team Sold
$change_sold_team = getPercentageChange($total_team_sold, $total_team_sold_prev);
$color_sold_team = $change_sold_team >= 50 ? 'text-success' : ($change_sold_team >= 10 ? 'text-warning' : 'text-danger');

// ✅ Apply conditional percentage logic for Total Available
$change_available = getPercentageChange($total_available, $total_available_prev);
$color_available = $change_available < 0 ? 'text-danger' : ($change_available > 0 ? 'text-success' : 'text-warning');

?>

                  
                          <div class="col-span-12 lg:col-span-12">
      <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-5 lg:grid-cols-4">
          <!-- Total Sold -->
          <div class="rounded-lg bg-slate-150 p-4 dark:bg-navy-700">
              <div class="flex justify-between items-center">
                  <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                  <?php echo $total_sold; ?>
                  </p>
                  <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-primary dark:text-accent" fill="none"
                  viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
  </svg>

              </div>
              <p class="mt-1 text-xs+">Total Sold</p>
          </div>

          <!-- Total House Available -->
          <div class="rounded-lg bg-slate-150 p-4 dark:bg-navy-700">
              <div class="flex justify-between items-center">
                  <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                  <?php echo $total_available; ?>
                  </p>
                  <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-success" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 9.75L12 3l9 6.75v9.75a1.5 1.5 0 01-1.5 1.5h-15A1.5 1.5 0 013 19.5V9.75z" />
                  </svg>
              </div>
              <p class="mt-1 text-xs+">Total House Available</p>
          </div>

          <!-- Total Sold Today -->
          <div class="rounded-lg bg-slate-150 p-4 dark:bg-navy-700">
              <div class="flex justify-between items-center">
                  <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                  <?php echo $total_sold_today; ?>
                  </p>
                  <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-warning" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                  </svg>
              </div>
              <p class="mt-1 text-xs+">Total Sold Today</p>
          </div>

          <!-- Total Agents -->
          <div class="rounded-lg bg-slate-150 p-4 dark:bg-navy-700">
              <div class="flex justify-between items-center">
                  <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                  <?php echo isset($total_agents) && $total_agents != "" ? $total_agents : "0"; ?>
                  </p>
                  <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-info" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                  </svg>
              </div>
              <p class="mt-1 text-xs+">Total Agents</p>
          </div>

          <!-- Total Teams -->
          <div class="rounded-lg bg-slate-150 p-4 dark:bg-navy-700">
              <div class="flex justify-between items-center">
                  <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                  <?php echo isset($total_teams) && $total_teams != "" ? $total_teams : "0"; ?>
                  </p>
                  <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-secondary" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                  </svg>
              </div>
              <p class="mt-1 text-xs+">Total Teams</p>
          </div>

          <!-- New Members -->
          <div class="rounded-lg bg-slate-150 p-4 dark:bg-navy-700">
              <div class="flex justify-between items-center">
                  <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                  <?php echo isset($new_members) && $new_members != "" ? $new_members : "0"; ?>
                  </p>
                  <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-error" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                  </svg>
              </div>
              <p class="mt-1 text-xs+">New Members</p>
          </div>

          <!-- Total Customers -->
          <div class="rounded-lg bg-slate-150 p-4 dark:bg-navy-700">
              <div class="flex justify-between items-center">
                  <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                  <?php echo $total_customers; ?>
                  </p>
                  <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-error" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                  </svg>
              </div>
              <p class="mt-1 text-xs+">Total Customers</p>
          </div>
      </div>
  </div>


  <div class="card col-span-12 lg:col-span-8">
  <div class="flex items-center justify-between py-3 px-4">
    <h2 class="font-medium tracking-wide text-slate-700 dark:text-navy-100" id="monthYear"></h2>
    <h5 class="card-subtitle card-subtitle-dash">Number of Houses Sold This Week</h5>
  </div>
  <!-- Line Chart Canvas -->
  <div class="grid grid-cols-1 gap-y-4 pb-3">
    <canvas id="performanceLine" width="100%" height="300"></canvas>
  </div>

</div>
                        <?php
// Skip the first row (Superadmin) and get top-performing teams
$query = "
SELECT 
    agt.Team_Name,
    COUNT(hp.house_id) AS total_sold
FROM tbl_team agt
LEFT JOIN tbl_agents_info ai ON agt.TeamID = ai.AgentTeam_ID
LEFT JOIN tbl_acc_agent ag ON ai.Agent_infoID = ag.Agent_accID
LEFT JOIN house_purchase hp ON ag.Agent_accID = hp.agentID
WHERE agt.Team_Name != 'Superadmin' -- Exclude Superadmin team
GROUP BY agt.Team_Name
ORDER BY total_sold DESC
LIMIT 100 OFFSET 0; -- Skip the first row if needed
";

$result = mysqli_query($con, $query);

// Prepare data for teams
$teams = [];
while ($row = mysqli_fetch_assoc($result)) {
    $teams[] = $row;
}

// Identify top-performing team
$top_team = $teams[0] ?? null; // Top team (1st rank)
$min_sold_count = min(array_column($teams, 'total_sold')); // Get minimum sold count

// Identify teams with the lowest sold count
$lowest_teams = array_filter($teams, function ($team) use ($min_sold_count) {
    return $team['total_sold'] == $min_sold_count;
});
?>

<div class="col-span-12 lg:col-span-4">
    <div class="flex items-center justify-between">
        <h2 class="font-medium tracking-wide text-slate-700 dark:text-navy-100 py-3 px-4">
            Top Performing Team
        </h2>
    </div>

    <div class="is-scrollbar-hidden min-w-full overflow-x-auto">
        <table class="is-zebra w-full text-left">
            <thead>
                <tr class="border border-transparent border-b-slate-200 dark:border-b-navy-500">
                    <th class="whitespace-nowrap px-3 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">
                        Rank
                    </th>
                    <th class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                        Team Name
                    </th>
                    <th class="whitespace-nowrap px-4 py-3 font-semibold uppercase text-slate-800 dark:text-navy-100 lg:px-5">
                        Total Sold
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($teams)) {
                    $rank = 1;
                    foreach ($teams as $team) {
                        // Apply different background colors for the entire row
                        $rowClass = '';
                        if ($rank == 1) {
                            $rowClass = 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'; // Top team (green)
                        } elseif ($team['total_sold'] == $min_sold_count) {
                            $rowClass = 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'; // Lowest team (red)
                        } else {
                            $rowClass = 'bg-white dark:bg-navy-700'; // Normal rows
                        }
                        ?>
                        <tr class="<?= $rowClass; ?>"> <!-- Entire Row Highlighted -->
                            <td class="whitespace-nowrap rounded-l-lg px-4 py-3 sm:px-5"><?= $rank++; ?></td>
                            <td class="whitespace-nowrap px-4 py-3 sm:px-5"><?= htmlspecialchars($team['Team_Name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="whitespace-nowrap px-4 py-3 sm:px-5"><?= htmlspecialchars($team['total_sold'], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="3" class="text-center px-4 py-3">No data available</td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

                    </div>

                    <?php
// Fetch all agents with sold count (0 for agents without sales)
$query = "
SELECT 
    ag.Agent_accID,
    ai.IMAGEID,
    t.Team_Name,
    CONCAT(ai.FN, ' ', ai.LN) AS agent_name,
    COUNT(hp.house_id) AS total_sold
FROM tbl_acc_agent ag
LEFT JOIN tbl_agents_info ai ON ag.Agent_accID = ai.Agent_infoID
LEFT JOIN house_purchase hp ON ag.Agent_accID = hp.agentID
LEFT JOIN tbl_team t ON ai.AgentTeam_ID = t.TeamID
WHERE ai.AgentTeam_ID != 1 -- Exclude agents with TeamID = 1
GROUP BY ag.Agent_accID
ORDER BY total_sold DESC
";

$result = mysqli_query($con, $query);

// Prepare data for top, middle, and lowest agents
$agents = [];
while ($row = mysqli_fetch_assoc($result)) {
    $agents[] = $row;
}

// Identify top and lowest agents
$top_agent = $agents[0] ?? null; // Agent with highest sales
$min_sold_count = min(array_column($agents, 'total_sold')); // Get minimum sold count

// Identify agents with the lowest sold count (including 0 sales)
$lowest_agents = array_filter($agents, function ($agent) use ($min_sold_count) {
    return $agent['total_sold'] == $min_sold_count;
});
?>

<div class="mt-4 grid grid-cols-12 gap-4 bg-slate-150 py-5 dark:bg-navy-800 sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
    <div class="col-span-12 flex flex-col px-[var(--margin-x)] transition-all duration-[.25s] lg:col-span-3 lg:pr-0">
        <h2 class="text-base font-medium tracking-wide text-slate-700 line-clamp-1 dark:text-navy-100 lg:text-xl">
            Top Performing Agents
        </h2>
        <p class="mt-3 grow">
            The top-performing agents are ranked based on the total number of properties sold.
        </p>
    </div>

    <!-- Cards for Agents -->
    <div class="is-scrollbar-hidden col-span-12 flex space-x-4 overflow-x-auto px-[var(--margin-x)] transition-all duration-[.25s] lg:col-span-9 lg:pl-0">
        <?php
        if (!empty($agents)) {
            $rank = 1;
            foreach ($agents as $agent) {
                // Determine background color for top and lowest agents
                $cardClass = '';
                if ($rank == 1) {
                    $cardClass = 'bg-green-100 dark:bg-green-900'; // Top agent (green)
                } elseif ($agent['total_sold'] == $min_sold_count) {
                    $cardClass = 'bg-red-100 dark:bg-red-900'; // Lowest agent (red)
                } else {
                    $cardClass = 'bg-white dark:bg-navy-700'; // Normal agent
                }

                // Agent image or default avatar if IMAGEID is empty
                $avatar_path = !empty($agent['IMAGEID']) ? "parakalan_system/uploads/{$agent['IMAGEID']}" : "images/avatar/avatar-1.jpg";
                ?>
                <div class="card w-72 shrink-0 space-y-6 rounded-xl p-4 sm:px-5 <?= $cardClass; ?>">
                    <div class="flex items-center justify-between space-x-2">
                        <div class="flex items-center space-x-3">
                            <div class="avatar">
                                <img class="mask is-squircle size-12" src="<?= htmlspecialchars($avatar_path); ?>" alt="Agent Image">
                            </div>
                            <div>
                                <p class="font-medium text-slate-700 line-clamp-1 dark:text-navy-100">
                                    <?= htmlspecialchars($agent['agent_name'], ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                                <p class="text-xs text-slate-400 dark:text-navy-300">
                                    <?= htmlspecialchars($agent['Team_Name'], ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                            </div>
                        </div>

                        <!-- Rank Badge -->
                        <div class="relative cursor-pointer">
                            <button class="btn h-7 w-7 bg-primary/10 p-0 text-primary hover:bg-primary/20 focus:bg-primary/20 active:bg-primary/25 dark:bg-accent-light/10 dark:text-accent-light dark:hover:bg-accent-light/20 dark:focus:bg-accent-light/20 dark:active:bg-accent-light/25">
                                <img x-tooltip="'Rank <?= $rank; ?>'" class="size-6" src="images/awards/award-<?= ($rank <= 3) ? $rank : '4'; ?>.svg" alt="Rank">
                            </button>
                            <div class="absolute top-0 right-0 -m-1 flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-primary px-1 text-tiny font-medium leading-none text-white dark:bg-accent">
                                <?= $rank++; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Total Sold Count -->
                    <div class="flex justify-start">
                        <div>
                            <p class="text-xs+">Total Number of Sold</p>
                            <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                <?= number_format($agent['total_sold']); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="card w-72 shrink-0 space-y-6 rounded-xl p-4 sm:px-5 bg-white dark:bg-navy-700">
                <p class="text-center text-slate-700 dark:text-navy-100">No data available</p>
            </div>
            <?php
        }
        ?>
    </div>
</div>

                    
                </main>
            </div>
      
            <div id="x-teleport-target"></div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>    
<!-- Add this in the <head> or before your closing </body> tag -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
            window.addEventListener("DOMContentLoaded", () => Alpine.start());

</script>



<script>
  // Ensure charts only load once
  let lineChart, houseSoldChart;

  // DOM ready event
  $(document).ready(function () {
    loadSalesData(); // Load line chart data
    loadHouseSoldData(); // Load bar chart data
    displayMonthYear(); // Show current month and year
  });

  // ✅ Function to load and display weekly sales data (Line Chart)
  function loadSalesData() {
    $.ajax({
      url: 'config/management/daySoldHouses.php',
      type: 'GET',
      dataType: 'json',
      success: function (data) {
        createLineChart(data); // Create line chart after data fetch
      },
      error: function (xhr, status, error) {
        console.error("Error fetching data:", error);
      }
    });
  }

  // ✅ Function to create the Line Chart
  function createLineChart(data) {
    const daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    const salesData = new Array(7).fill(0);

    // Map fetched data to the correct day
    data.forEach(item => {
      const saleDate = new Date(item.date);
      const dayOfWeek = saleDate.getDay(); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
      const index = (dayOfWeek === 0) ? 6 : dayOfWeek - 1;
      salesData[index] += parseInt(item.num_of_sales);
    });

    // Get context and check for existing chart
    var ctx = document.getElementById('performanceLine').getContext('2d');
    if (lineChart) {
      lineChart.destroy();
    }

    // Create new line chart
    lineChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: daysOfWeek,
        datasets: [{
          label: 'Houses Sold',
          data: salesData,
          fill: false,
          borderColor: 'rgb(75, 192, 192)',
          tension: 0.1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  }

  
  

  // ✅ Function to display the current month and year
  function displayMonthYear() {
    const now = new Date();
    const options = { year: 'numeric', month: 'long' };
    const monthYearString = now.toLocaleDateString('en-US', options);
    document.getElementById('monthYear').textContent = monthYearString;
  }
</script>
</body>

</html>