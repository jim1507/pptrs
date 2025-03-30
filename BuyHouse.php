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

        <?php
        // Check if TeamID is set
        if (isset($_GET['HouseID']) && !empty($_GET['HouseID'])) {
            // Sanitize the input to prevent XSS and SQL injection
            $houseid = htmlspecialchars($_GET['HouseID']);

            // SQL query to fetch team members
            $sql = $sql = "SELECT * FROM tbl_house WHERE HouseID =  $houseid";

            $result = mysqli_query($con, $sql);

            // Check if records exist
            if (mysqli_num_rows($result) > 0) {
                // Get team name and reset pointer for looping
                $houseInfo = mysqli_fetch_assoc($result);
                $houseID = htmlspecialchars($houseInfo['HouseID']);
                $houseName = htmlspecialchars($houseInfo['HouseName']);
                $price = htmlspecialchars($houseInfo['Price']);
                $downPayment = htmlspecialchars($houseInfo['DownPayment']);
                $location = htmlspecialchars($houseInfo['Location']);
                $unitAvailable = htmlspecialchars($houseInfo['unit_available']);


                mysqli_data_seek($result, 0); // Reset pointer for loop

                while ($row = mysqli_fetch_assoc($result)) {
                   
                }

                // Reset pointer again for displaying cards
                mysqli_data_seek($result, 0);
                ?>


        <!-- Main Content Wrapper -->
        <main class="main-content w-full px-[var(--margin-x)] pb-8">
            <div class="flex flex-col items-center justify-between space-y-4 py-5 sm:flex-row sm:space-y-0 lg:py-6">
                <div class="flex items-center space-x-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewbox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <h2 class="text-xl font-medium text-slate-700 line-clamp-1 dark:text-navy-50">
                        New House Listing
                    </h2>
                </div>
                <div class="flex justify-center space-x-2">

                </div>
            </div>
            <div class="grid grid-cols-12 gap-4 sm:gap-5 lg:gap-6">
                <div class="col-span-12 lg:col-span-8">
                    <div class="card">
                        <div class="tabs flex flex-col">
                            <form action="config/configuration/buyhouse.php" method="post"
                                enctype="multipart/form-data">
                                <div class="tab-content p-4 sm:p-5">
                                    <!-- Buyer's Information Label -->
                                    <div class="mb-4">
                                        <span class="font-medium text-slate-600 dark:text-navy-100 text-lg">Buyer's
                                            Information</span>
                                    </div>
                                    <input
                                        class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                        type="hidden" name="house_id" value="<?php echo  $houseID ?>" required>
                                    <!-- Grid for 3 Input Fields Per Row -->
                                    <div class="grid grid-cols-3 gap-4">
                                        <!-- First Name -->
                                        <input
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Enter First Name" type="text" name="fname" required>

                                        <!-- Middle Name -->
                                        <input
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Enter Middle Name" type="text" name="mname">

                                        <!-- Last Name -->
                                        <input
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Enter Last Name" type="text" name="lname" required>
                                    </div>

                                    <!-- Second Row -->
                                    <div class="grid grid-cols-3 gap-4 mt-4">
                                        <!-- Birthday -->
                                        <input
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Enter Birthday" type="date" name="dob">

                                        <!-- Civil Status -->
                                        <input
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Enter Civil Status" type="text" name="civil_status">

                                        <!-- Nationality -->
                                        <input
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Enter Nationality" type="text" name="nationality">
                                    </div>

                                    <!-- Third Row -->
                                    <div class="grid grid-cols-2 gap-4 mt-4">
                                        <!-- Contact Number with +639 Prefix -->
                                        <div
                                            class="flex items-center w-full rounded-lg border border-slate-300 bg-transparent hover:border-slate-400 focus-within:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus-within:border-accent">
                                            <span
                                                class="text-slate-600 bg-slate-100 dark:text-slate-300 dark:bg-navy-700 px-3 py-2 rounded-l-lg border border-r-0 border-slate-300 dark:border-navy-450">
                                                +639
                                            </span>
                                            <input
                                                class="form-input flex-1 bg-transparent focus:outline-none placeholder:text-slate-400/70 border-0 rounded-r-lg"
                                                placeholder="Enter Contact Number" type="text" name="contact_number"
                                                maxlength="9">
                                        </div>

                                        <!-- Address Input on the Right Side -->
                                        <div
                                            class="flex items-center w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 hover:border-slate-400 focus-within:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus-within:border-accent">
                                            <input
                                                class="form-input flex-1 bg-transparent focus:outline-none placeholder:text-slate-400/70"
                                                placeholder="Enter Address" type="text" name="address">
                                        </div>
                                    </div>
                                    <br>
                                    <!-- Buyer's Information Label -->
                                    <div class="mb-4">
                                        <span class="font-medium text-slate-600 dark:text-navy-100 text-lg">Employment &
                                            Financial Information</span>
                                    </div>

                                    <!-- Grid for 3 Input Fields Per Row -->
                                    <div class="grid grid-cols-3 gap-4">
                                        <!-- First Name -->
                                        <input
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Enter Occupation / Job Title" type="text" name="occupation">

                                        <!-- Middle Name -->
                                        <input
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Enter Employer / Business Name" type="text" name="employer">

                                        <!-- Last Name -->
                                        <input
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Enter Salary" type="text" name="salary">
                                    </div>

                                    <br>
                                    <div class="mb-4">
                                        <span class="font-medium text-slate-600 dark:text-navy-100 text-lg">Spouse /
                                            Co-Borrower / Co-Owner Information (if applicable)</span>
                                    </div>

                                    <!-- Grid for 3 Input Fields Per Row -->
                                    <div class="grid grid-cols-3 gap-4">
                                        <!-- First Name -->
                                        <input
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Enter Full Name" type="text" name="co_borrower_name">

                                        <!-- Middle Name -->
                                        <input
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Enter Relationship to Buyer" type="text" name="relationship">

                                        <!-- Last Name -->
                                        <input
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Enter Occupation" type="text" name="co_borrower_name_work">
                                    </div>

                                    <!-- Second Row -->
                                    <div class="grid grid-cols-3 gap-4 mt-4">
                                        <!-- Birthday -->


                                        <!-- Civil Status -->
                                        <input
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Enter Employer/ Business Name" type="text"
                                            name="co_borrower_company">

                                        <!-- Nationality -->
                                        <input
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Enter Salary" type="text" name="co_borrower_salary">
                                    </div>

                                    <br>
                                    <div class="mb-4">
                                        <span class="font-medium text-slate-600 dark:text-navy-100 text-lg">Legal
                                            Documents & Requirements</span>
                                    </div>

                                    <!-- Grid for 3 Input Fields Per Row -->
                                    <div class="grid grid-cols-3 gap-4">
                                        <!-- First Name -->
                                        <input
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Enter Tax Identification Number (TIN)" type="text" name="tin">

                                        <!-- Middle Name -->
                                        <input
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Marriage Certificate (if married)" type="file"
                                            name="marriage_certificate">

                                        <!-- Last Name -->
                                        <input
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Enter Proof of Billing" type="file" name="proof_of_billing">
                                    </div>

                                </div>
                        </div>
                    </div>
                </div>
                <!-- Right Column - House Details -->
                <div class="col-span-12 lg:col-span-4">
                    <div class="card sticky top-4">
                        <div class="p-4 sm:p-5">
                            <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100 mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5 mr-2 text-primary dark:text-accent" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2H5a1 1 0 010-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z"
                                        clip-rule="evenodd" />
                                </svg>
                                House Details
                            </h3>

                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-slate-600 dark:text-navy-100">House Name:</span>
                                    <span class="font-semibold"><?php echo $houseName; ?></span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-slate-600 dark:text-navy-100">Price:</span>
                                    <span
                                        class="font-semibold text-primary dark:text-accent">₱<?php echo number_format($price, 2); ?></span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-slate-600 dark:text-navy-100">Reservation Fee:</span>
                                    <span class="font-semibold">₱<?php echo number_format($downPayment, 2); ?></span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-slate-600 dark:text-navy-100">Location:</span>
                                    <span class="text-right"><?php echo $location; ?></span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="font-medium text-slate-600 dark:text-navy-100">Available Units:</span>
                                    <span class="font-semibold"><?php echo $unitAvailable; ?></span>
                                </div>

                                <div class="pt-4 border-t border-slate-200 dark:border-navy-500">
                                    <h4 class="font-medium text-slate-700 dark:text-navy-100 mb-2">Payment Terms</h4>
                                    <ul class="space-y-2 text-sm">
                                        <li class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4 mr-2 text-primary dark:text-accent" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            20% Downpayment
                                        </li>
                                        <li class="flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4 mr-2 text-primary dark:text-accent" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            80% Balance payable in 10 years
                                        </li>
                                    </ul>
                                </div>

                                <div class="pt-4">
                                    <button type="submit"
                                        class="btn w-full bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                        Submit Reservation
                                    </button>
                                </div>
                            </div>
              </form>
                        </div>
                    </div>
                </div>
                <?php
            } else {
                echo "<div class='col-span-12 p-4 text-center text-error'>House not found!</div>";
            }
        } else {
            echo "<div class='col-span-12 p-4 text-center text-error'>Invalid House ID!</div>";
        }
        ?>
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