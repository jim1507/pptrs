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
        <div class="flex flex-col items-center justify-between space-y-4 py-5 sm:flex-row sm:space-y-0 lg:py-6">
          <div class="flex items-center space-x-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewbox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
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
                <form action="config/configuration/editHouse.php" method="post" enctype="multipart/form-data">
                <div class="tab-content p-4 sm:p-5">
                  <div class="space-y-5">
                    <label class="block">
                      <span class="font-medium text-slate-600 dark:text-navy-100">House Name</span>
                      <input class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent" placeholder="Enter House Name" type="text" name="house_name">
                    </label>
                    <div class="flex gap-4"> <!-- Use flex and add gap for spacing -->
  <!-- House Price -->
  <label class="block w-1/2"> <!-- w-1/2 makes each take half width -->
    <span class="font-medium text-slate-600 dark:text-navy-100">House Price</span>
    <input class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent" 
      placeholder="Enter house price" 
      type="text" name ="price">
  </label>

  <!-- House Reservation Fee -->
  <label class="block w-1/2">
    <span class="font-medium text-slate-600 dark:text-navy-100">House Reservation Fee</span>
    <input class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent" 
      placeholder="Enter reservation fee" 
      type="text" name="downpayment">
  </label>
</div>

                    <div>
                      <span class="font-medium text-slate-600 dark:text-navy-100">House Description</span>
                      <div class="mt-1.5 w-full">
                        <div class="h-48" x-init="$el._x_quill = new Quill($el,{
                            modules : {
                              toolbar: [
                                ['bold', 'italic', 'underline', 'strike'], // toggled buttons
                                ['blockquote', 'code-block'],
                                [{ header: 1 }, { header: 2 }], // custom button values
                                [{ list: 'ordered' }, { list: 'bullet' }],
                                [{ script: 'sub' }, { script: 'super' }], // superscript/subscript
                                [{ indent: '-1' }, { indent: '+1' }], // outdent/indent
                                [{ direction: 'rtl' }], // text direction
                                [{ size: ['small', false, 'large', 'huge'] }], // custom dropdown
                                [{ header: [1, 2, 3, 4, 5, 6, false] }],
                                [{ color: [] }, { background: [] }], // dropdown with defaults from theme
                                [{ font: [] }],
                                [{ align: [] }],
                                ['clean'], // remove formatting button
                              ],
                            },
                            placeholder: 'Enter house description...',
                            theme: 'snow', name: 'description',
                          })"></div>
                      </div>
                    </div>
                    <label class="block">
                      <span class="font-medium text-slate-600 dark:text-navy-100">Location</span>
                      <input class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent" placeholder="Enter House Location" type="text" name="location">
                    </label>
                    <div>
                      <span class="font-medium text-slate-600 dark:text-navy-100">Post Images</span>
                      <div class="filepond fp-bordered fp-grid mt-1.5 [--fp-grid:2]">
                      <input type="file" name="logo"/>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-span-12 lg:col-span-4">
            <div class="card space-y-5 p-4 sm:p-5">
            
              <label class="block">
                <span class="font-medium text-slate-600 dark:text-navy-100"> House Category</span>
                <select class="mt-1.5 w-full" x-init="$el._x_tom = new Tom($el,{create: false,sortField: {field: 'text',direction: 'asc'}})" name="house_type">
                  <option value="">Select the category</option>
                  <option value="TownHouse">TownHouse with Garage</option>
                  <option value="TownHouse without Garage">TownHouse without Garage</option>
                  <option value="Single Attached">Single Attached with Garage</option>
                  <option value="Single Attached without Garage">Single Attached without Garage</option>
                  <option value="Single Detached">Single Detached with Garage</option>
                  <option value="Single Detached without Garage">Single Detached without Garage</option>
                  <option value="Duplex ">Duplex Unit with Garage</option>
                  <option value="Duplex without Garage">Duplex Unit without Garage</option>
                </select>
              </label>

              <label class="block">
                <span class="font-medium text-slate-600 dark:text-navy-100">Pre- Selling Condition</span>
                <select class="mt-1.5 w-full" x-init="$el._x_tom = new Tom($el,{create: false,sortField: {field: 'text',direction: 'asc'}})" name="property_condition">
                  <option value="">Select the condition</option>
                  <option value="turnover">Turn-Over</option>
                  <option value="ready for occupacy">Ready for Occupacy(RFO)</option>
                </select>
              </label>
              <label class="block">
                <span class="font-medium text-slate-600 dark:text-navy-100">House Floor Area (sqm)</span>
                <input class="mt-1.5 w-full" placeholder="Enter house area" x-init="$el._x_tom = new Tom($el,{create: true})" name="floor_area">
              </label>
              <label class="block">
                <span class="font-medium text-slate-600 dark:text-navy-100">Number of Bedrooms</span>
                <input class="mt-1.5 w-full" placeholder="Enter number of bedrooms" x-init="$el._x_tom = new Tom($el,{create: true})" name="bedrooms">
              </label>
              <label class="block">
                <span class="font-medium text-slate-600 dark:text-navy-100">Number of Bathrooms</span>
                <input class="mt-1.5 w-full" placeholder="Enter number of bathrooms" x-init="$el._x_tom = new Tom($el,{create: true})" name="bathrooms">
              </label>
              <label class="block">
                <span class="font-medium text-slate-600 dark:text-navy-100">Total Unit Available</span>
                <input class="mt-1.5 w-full" placeholder="Enter total unit available" x-init="$el._x_tom = new Tom($el,{create: true})" name="avail">
              </label>
              <button class="btn min-w-[7rem] bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
              Save
            </button>
            </form>
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
      window.addEventListener("DOMContentLoaded", () => Alpine.start());
    </script>
  </body>
</html>
