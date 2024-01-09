    <div class="container">
      <div class="d-flex flex-column flex-md-row align-items-center">
        <a href="index.php" class="d-flex align-items-center link-body-emphasis text-decoration-none">
          <h1 class="fw-bold text-center mb-4 mt-5">HiroFORUM</h1>
        </a>

        <nav class="d-inline-flex mt-md-0 ms-md-auto gap-4">
          <a class="btn btn-outline-light border-0 fw-bold rounded <?php if(basename($_SERVER['PHP_SELF']) == 'index.php') echo 'active' ?>" href="index.php"><small>home</small></a>
          <a class="btn btn-outline-light border-0 fw-bold rounded <?php if(basename($_SERVER['PHP_SELF']) == 'upload.php') echo 'active' ?>" href="upload.php"><small>upload</small></a>
          <a class="btn btn-outline-light border-0 fw-bold rounded <?php if(basename($_SERVER['PHP_SELF']) == 'settings.php') echo 'active' ?>" href="settings.php"><small>settings</small></a>
        </nav>
      </div>
    </div>