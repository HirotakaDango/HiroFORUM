    <div class="container">
      <div class="d-flex flex-column flex-md-row align-items-center">
        <a href="index.php" class="link-body-emphasis text-decoration-none fw-bold fs-3 mt-4">
          HiroFORUM
        </a>

        <nav class="d-inline-flex mt-md-0 ms-md-auto mt-2 pt-3 gap-2">
          <a class="btn btn-outline-light border-0 fw-bold rounded <?php if(basename($_SERVER['PHP_SELF']) == 'index.php') echo 'active' ?>" href="index.php"><small>home</small></a>
          <a class="btn btn-outline-light border-0 fw-bold rounded <?php if(basename($_SERVER['PHP_SELF']) == 'upload.php') echo 'active' ?>" href="upload.php"><small>upload</small></a>
          <a class="btn btn-outline-light border-0 fw-bold rounded <?php if(basename($_SERVER['PHP_SELF']) == 'settings.php') echo 'active' ?>" href="settings.php"><small>settings</small></a>
          <?php
            // Check if the user is logged in
            $loggedInUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

            if (isset($loggedInUserId)) {
              // If the user is logged in, show the profile button
              $isProfileActive = (basename($_SERVER['PHP_SELF']) == 'user.php' && isset($_GET['id']) && $_GET['id'] == $loggedInUserId);
              echo '<a class="btn btn-outline-light border-0 fw-bold rounded';
              echo $isProfileActive ? ' active' : '';
              echo '" href="user.php?id=' . $loggedInUserId . '"><small>profile</small></a>';
            } else {
              // If the user is not logged in, show a login button
              echo '<a class="btn btn-outline-light border-0 fw-bold rounded" href="session.php"><small>login</small></a>';
            }
          ?>
          <a class="d-none d-md-block btn btn-outline-light border-0 fw-bold rounded <?php if(basename($_SERVER['PHP_SELF']) == 'search.php') echo 'active' ?>" href="#" data-bs-toggle="modal" data-bs-target="#searchModal"><small>search</small></a>
          <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content bg-transparent border-0">
                <div class="modal-body">
                  <form class="input-group" role="search" action="search.php">
                    <input class="form-control rounded-start-4 border-0 bg-body-tertiary focus-ring focus-ring-dark" name="q" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn rounded-end-4 border-0 bg-body-tertiary" type="submit"><i class="bi bi-search"></i></button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </nav>
      </div>
      <div class="d-md-none mt-3">
        <form class="input-group" role="search" action="search.php">
          <input class="form-control rounded-start-4 border-0 bg-body-tertiary focus-ring focus-ring-dark" name="q" type="search" placeholder="Search" aria-label="Search">
          <button class="btn rounded-end-4 border-0 bg-body-tertiary" type="submit"><i class="bi bi-search"></i></button>
        </form>
      </div>
    </div>
    <button type="button" class="z-3 btn bg-dark-subtle rounded-pill border-0 btn-sm position-fixed end-0 bottom-0 m-2 fw-medium" data-bs-toggle="modal" data-bs-target="#infoModal"><i class="bi bi-question-circle"></i> info</button>
    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
          <div class="modal-header border-0">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Help</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>If you want to upload image, use image link address, only support jpg, jpeg, png, and gif.</p>
            <p>example: https://i.imgur.com/8e3UNUk.png</p>
          </div>
        </div>
      </div>
    </div>