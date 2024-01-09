    <div class="container">
      <div class="d-flex flex-column flex-md-row align-items-center">
        <a href="index.php" class="link-body-emphasis text-decoration-none fw-bold fs-3 mt-4">
          HiroFORUM
        </a>

        <nav class="d-inline-flex mt-md-0 ms-md-auto mt-2 pt-3 gap-4">
          <a class="btn btn-outline-light border-0 fw-bold rounded <?php if(basename($_SERVER['PHP_SELF']) == 'index.php') echo 'active' ?>" href="index.php"><small>home</small></a>
          <a class="btn btn-outline-light border-0 fw-bold rounded <?php if(basename($_SERVER['PHP_SELF']) == 'upload.php') echo 'active' ?>" href="upload.php"><small>upload</small></a>
          <a class="btn btn-outline-light border-0 fw-bold rounded <?php if(basename($_SERVER['PHP_SELF']) == 'settings.php') echo 'active' ?>" href="settings.php"><small>settings</small></a>
        </nav>
      </div>
    </div>