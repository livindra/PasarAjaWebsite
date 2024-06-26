<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Pasar Aja loo kii</title>
        <!-- Favicon-->
        <link href="./vendor/jqvmap/css/jqvmap.min.css" rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700" rel="stylesheet" type="text/css" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="{{asset ('boot/css/styles.css')}}" rel="stylesheet" />
        <link href="{{asset ('boot/css/ganti.css')}}" rel="stylesheet" />
</head>
<body>
<section class="vh-100">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6 px-0 d-none d-sm-block">
        <img src="{{asset('img/forgot1.png')}}" alt="Login image" class="w-100 vh-100" style="object-fit: cover; object-position: left;">
      </div>

      <div class="col-sm-6 text-black">
        <div class="text-center">
          <h2 class="text-judul">Ganti Password</h2>
          <p class="text-1">Masukan password yang baru, gunakan password yang rumit namun mudah di ingat</p>
        </div>

        <div class="d-flex align-items-center h-custom-2 px-5 ms-xl-4 mt-5 pt-5 pt-xl-0 mt-xl-n5">
          <form style="width: 23rem;">
            <h3 class="fw-normal mb-3 pb-3" id="text-log">Ganti Password</h3>
            <div class="form-outline mb-4">
              <label class="form-label" for="form2Example28">Password</label>
              <input type="password" id="form2Example28" class="form-control form-control-lg" />
            </div>

            <div class="form-outline mb-4">
              <label class="form-label" for="form2Example28">Konfirmasi Password</label>
              <input type="password" id="form2Example28" class="form-control form-control-lg" />
            </div>

            <div class="pt-1 mb-4">
              <button class="btn btn-info btn-lg btn-block" id="btn-detail" type="button">Ganti Password</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

</body>
</html>