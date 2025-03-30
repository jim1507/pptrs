
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
      <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
            Parakalan System
          </h2>
          <div class="hidden h-full py-1 sm:flex">
            <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
          </div>
          <ul class="hidden flex-wrap items-center space-x-2 sm:flex">
            <li class="flex items-center space-x-2">
              <a class="text-primary transition-colors hover:text-primary-focus dark:text-accent-light dark:hover:text-accent" href="#">Profile Settings</a>
              <svg x-ignore="" xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewbox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </li>
            <li>Update Profile</li>
          </ul>
        </div>

        <?php
   $agent = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

                            $sql = "SELECT * FROM `tbl_agents_info` INNER JOIN tbl_acc_agent ON tbl_agents_info.Agent_infoID = tbl_acc_agent.AgentInfo_Id LEFT JOIN tbl_team ON tbl_agents_info.AgentTeam_ID = tbl_team.TeamID WHERE tbl_acc_agent.Agent_accID = '$agent'";
                            $result = mysqli_query($con, $sql);

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $count= 1;
                                    $logoPath = !empty($row['IMAGEID']) 
                                        ? "parakalan/uploads/" . $row['IMAGEID'] 
                                        : "admin/uploads/default.png";

                                        $agentName = $row['FN'] . ' ' . $row['MN'] . ' ' . $row['LN'];
                                    ?>

        <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6">
          <div class="col-span-12 lg:col-span-4">
            <div class="card p-4 sm:p-5">
              <div class="flex items-center space-x-4">
                <div class="avatar size-14">
                  <img class="rounded-full" src="<?php echo $logoPath ?>" alt="avatar">
                </div>
                <div>
                  <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                  <?php echo $agentName ?>
                  </h3>
                  <p class="text-xs+"> <?php echo $row['Team_Name']?></p>
                </div>
              </div>
              <ul class="mt-6 space-y-1.5 font-inter font-medium">
                <li>
                  <a class="flex items-center space-x-2 rounded-lg bg-primary px-4 py-2.5 tracking-wide text-white outline-none transition-all dark:bg-accent" href="#">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewbox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Account</span>
                  </a>
                </li>
               
              </ul>
            </div>
          </div>
          <div class="col-span-12 lg:col-span-8">
            <div class="card">
              <div class="flex flex-col items-center space-y-4 border-b border-slate-200 p-4 dark:border-navy-500 sm:flex-row sm:justify-between sm:space-y-0 sm:px-5">
                <h2 class="text-lg font-medium tracking-wide text-slate-700 dark:text-navy-100">
                  Account Setting
                </h2>
                <div class="flex justify-center space-x-2">
                  <button class="btn min-w-[7rem] rounded-full border border-slate-300 font-medium text-slate-700 hover:bg-slate-150 focus:bg-slate-150 active:bg-slate-150/80 dark:border-navy-450 dark:text-navy-100 dark:hover:bg-navy-500 dark:focus:bg-navy-500 dark:active:bg-navy-500/90">
                    Cancel
                  </button>

                  <form action="config/updateAcc.php" method="POST" enctype="multipart/form-data">
                  <button class="btn min-w-[7rem] rounded-full bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                    Save
                  </button>
                </div>
              </div>
              <div class="p-4 sm:p-5">
              <div class="flex flex-col">
    <span class="text-base font-medium text-slate-600 dark:text-navy-100">Avatar</span>
    <div class="avatar mt-1.5 size-20 relative">
        <!-- Image preview - will be updated when user selects a file -->
        <img id="avatar-preview" class="mask is-squircle" src="<?php echo $logoPath ?>" alt="avatar">
        
        <!-- Hidden file input -->
        <input 
            type="file" 
            id="avatar-upload" 
            accept="image/*" 
            class="hidden" 
            name="avatar"
        >
        
        <!-- Upload button -->
        <div class="absolute bottom-0 right-0 flex items-center justify-center rounded-full bg-white dark:bg-navy-700">
            <button 
                type="button"
                onclick="document.getElementById('avatar-upload').click()"
                class="btn size-6 rounded-full border border-slate-200 p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:border-navy-500 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" viewbox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
document.getElementById('avatar-upload').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('avatar-preview').src = event.target.result;
            
            // Optional: You can also store the image data temporarily
            // localStorage.setItem('tempAvatar', event.target.result);
        };
        reader.readAsDataURL(file);
    }
});
</script>
                <div class="my-7 h-px bg-slate-200 dark:bg-navy-500"></div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                  <label class="block">
                    <span>Agent Name </span>
                    <span class="relative mt-1.5 flex">
                      <input class="form-input peer w-full rounded-full border border-slate-300 bg-transparent px-3 py-2 pl-9 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent" value="<?php echo $agentName ?>" type="text" readonly>
                      <span class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
                        <i class="fa-regular fa-user text-base" aria-readonly=""></i>
                      </span>
                    </span>
                  </label>
                  <label class="block">
                    <span>Team </span>
                    <span class="relative mt-1.5 flex">
                      <input class="form-input peer w-full rounded-full border border-slate-300 bg-transparent px-3 py-2 pl-9 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent" value="<?php echo $row['Team_Name']?>" type="text" readonly>
                      <span class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
                        <i class="fa-regular fa-user text-base"></i>
                      </span>
                    </span>
                  </label>
                  <label class="block">
                    <span>Email Address </span>
                    <span class="relative mt-1.5 flex">
                      <input class="form-input peer w-full rounded-full border border-slate-300 bg-transparent px-3 py-2 pl-9 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent" value="<?php echo $row['username'] ?>" type="text">
                      <span class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
                        <i class="fa-regular fa-envelope text-base"></i>
                      </span>
                    </span>
                  </label>
                  <label class="block">
                    <span>Password</span>
                    <span class="relative mt-1.5 flex">
                      <input class="form-input peer w-full rounded-full border border-slate-300 bg-transparent px-3 py-2 pl-9 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent" placeholder="Enter passwword" type="text">
                      <span class="pointer-events-none absolute flex h-full w-10 items-center justify-center text-slate-400 peer-focus:text-primary dark:text-navy-300 dark:peer-focus:text-accent">
                        <i class="fa fa-phone"></i>
                      </span>
                    </span>
                  </label>
                </div>
                <div class="my-7 h-px bg-slate-200 dark:bg-navy-500"></div>
           
              </div>
            </div>
</form>
            <?php
                                }
                            } else {
                                echo '<div class="px-[var(--margin-x)]">No teams found</div>';
                            }
                            ?>
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
      window.addEventListener("DOMContentLoaded", () => Alpine.start());
    </script>
  </body>
</html>
