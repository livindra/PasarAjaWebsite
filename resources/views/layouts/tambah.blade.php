<!DOCTYPE html>
<html lang="en">
<head>
      <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Skydash Admin</title>
  @include('include.style')
</head>
<body>
  <div class="container-scroller">
  @include('include.navbar')
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_settings-panel.html -->
      <div class="theme-setting-wrapper">
        <div id="settings-trigger"><i class="ti-settings"></i></div>
        <div id="theme-settings" class="settings-panel">
          <i class="settings-close ti-close"></i>
          <p class="settings-heading">SIDEBAR SKINS</p>
          <div class="sidebar-bg-options selected" id="sidebar-light-theme"><div class="img-ss rounded-circle bg-light border mr-3"></div>Light</div>
          <div class="sidebar-bg-options" id="sidebar-dark-theme"><div class="img-ss rounded-circle bg-dark border mr-3"></div>Dark</div>
          <p class="settings-heading mt-2">HEADER SKINS</p>
          <div class="color-tiles mx-0 px-4">
            <div class="tiles success"></div>
            <div class="tiles warning"></div>
            <div class="tiles danger"></div>
            <div class="tiles info"></div>
            <div class="tiles dark"></div>
            <div class="tiles default"></div>
          </div>
        </div>
      </div>
      <div id="right-sidebar" class="settings-panel">
        <i class="settings-close ti-close"></i>
        <ul class="nav nav-tabs border-top" id="setting-panel" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="todo-tab" data-toggle="tab" href="#todo-section" role="tab" aria-controls="todo-section" aria-expanded="true">TO DO LIST</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="chats-tab" data-toggle="tab" href="#chats-section" role="tab" aria-controls="chats-section">CHATS</a>
          </li>
        </ul>
        <div class="tab-content" id="setting-content">
          <div class="tab-pane fade show active scroll-wrapper" id="todo-section" role="tabpanel" aria-labelledby="todo-section">
            <div class="add-items d-flex px-3 mb-0">
              <form class="form w-100">
                <div class="form-group d-flex">
                  <input type="text" class="form-control todo-list-input" placeholder="Add To-do">
                  <button type="submit" class="add btn btn-primary todo-list-add-btn" id="add-task">Add</button>
                </div>
              </form>
            </div>
            <div class="list-wrapper px-3">
              <ul class="d-flex flex-column-reverse todo-list">
                <li>
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="checkbox" type="checkbox">
                      Team review meeting at 3.00 PM
                    </label>
                  </div>
                  <i class="remove ti-close"></i>
                </li>
                <li>
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="checkbox" type="checkbox">
                      Prepare for presentation
                    </label>
                  </div>
                  <i class="remove ti-close"></i>
                </li>
                <li>
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="checkbox" type="checkbox">
                      Resolve all the low priority tickets due today
                    </label>
                  </div>
                  <i class="remove ti-close"></i>
                </li>
                <li class="completed">
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="checkbox" type="checkbox" checked>
                      Schedule meeting for next week
                    </label>
                  </div>
                  <i class="remove ti-close"></i>
                </li>
                <li class="completed">
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="checkbox" type="checkbox" checked>
                      Project review
                    </label>
                  </div>
                  <i class="remove ti-close"></i>
                </li>
              </ul>
            </div>
            <h4 class="px-3 text-muted mt-5 font-weight-light mb-0">Events</h4>
            <div class="events pt-4 px-3">
              <div class="wrapper d-flex mb-2">
                <i class="ti-control-record text-primary mr-2"></i>
                <span>Feb 11 2018</span>
              </div>
              <p class="mb-0 font-weight-thin text-gray">Creating component page build a js</p>
              <p class="text-gray mb-0">The total number of sessions</p>
            </div>
            <div class="events pt-4 px-3">
              <div class="wrapper d-flex mb-2">
                <i class="ti-control-record text-primary mr-2"></i>
                <span>Feb 7 2018</span>
              </div>
              <p class="mb-0 font-weight-thin text-gray">Meeting with Alisa</p>
              <p class="text-gray mb-0 ">Call Sarah Graves</p>
            </div>
          </div>
          <!-- To do section tab ends -->
          <div class="tab-pane fade" id="chats-section" role="tabpanel" aria-labelledby="chats-section">
            <div class="d-flex align-items-center justify-content-between border-bottom">
              <p class="settings-heading border-top-0 mb-3 pl-3 pt-0 border-bottom-0 pb-0">Friends</p>
              <small class="settings-heading border-top-0 mb-3 pt-0 border-bottom-0 pb-0 pr-3 font-weight-normal">See All</small>
            </div>
            <ul class="chat-list">
              <li class="list active">
                <div class="profile"><img src="{{asset ('admin_asset/template/images/faces/face1.jpg')}}" alt="image"><span class="online"></span></div>
                <div class="info">
                  <p>Thomas Douglas</p>
                  <p>Available</p>
                </div>
                <small class="text-muted my-auto">19 min</small>
              </li>
              <li class="list">
                <div class="profile"><img src="{{asset ('admin_asset/template/images/faces/face2.jpg')}}" alt="image"><span class="offline"></span></div>
                <div class="info">
                  <div class="wrapper d-flex">
                    <p>Catherine</p>
                  </div>
                  <p>Away</p>
                </div>
                <div class="badge badge-success badge-pill my-auto mx-2">4</div>
                <small class="text-muted my-auto">23 min</small>
              </li>
              <li class="list">
                <div class="profile"><img src="{{asset ('admin_asset/template/images/faces/face3.jpg')}}" alt="image"><span class="online"></span></div>
                <div class="info">
                  <p>Daniel Russell</p>
                  <p>Available</p>
                </div>
                <small class="text-muted my-auto">14 min</small>
              </li>
              <li class="list">
                <div class="profile"><img src="{{asset ('admin_asset/template/images/faces/face4.jpg')}}" alt="image"><span class="offline"></span></div>
                <div class="info">
                  <p>James Richardson</p>
                  <p>Away</p>
                </div>
                <small class="text-muted my-auto">2 min</small>
              </li>
              <li class="list">
                <div class="profile"><img src="{{asset ('admin_asset/template/images/faces/face5.jpg')}}" alt="image"><span class="online"></span></div>
                <div class="info">
                  <p>Madeline Kennedy</p>
                  <p>Available</p>
                </div>
                <small class="text-muted my-auto">5 min</small>
              </li>
              <li class="list">
                <div class="profile"><img src="{{asset ('admin_asset/template/images/faces/face6.jpg')}}" alt="image"><span class="online"></span></div>
                <div class="info">
                  <p>Sarah Graves</p>
                  <p>Available</p>
                </div>
                <small class="text-muted my-auto">47 min</small>
              </li>
            </ul>
          </div>
          <!-- chat tab ends -->
        </div>
      </div>
      <!-- partial -->
      <!-- partial:partials/_sidebar.html -->
      @include('include.sidebar')
      <!-- partial -->
      <!-- <div class="container">

       

      </div> -->
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h2 class="font-weight-bold mt-2">Data Event</h2>
                </div>
                <div class="col-12 col-xl-4">
                 <div class="justify-content-end d-flex">
                  <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
                    <button class="btn btn-sm btn-light bg-white dropdown-toggle" type="button" id="dropdownMenuDate2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                     <i class="mdi mdi-calendar"></i> Today (10 Jan 2021)
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuDate2">
                      <a class="dropdown-item" href="#">January - March</a>
                      <a class="dropdown-item" href="#">March - June</a>
                      <a class="dropdown-item" href="#">June - August</a>
                      <a class="dropdown-item" href="#">August - November</a>
                    </div>
                  </div>
                 </div>
                </div>
              </div>
            </div>
          </div>
          <div class="container">
            <div class="row">
                <div class="table-responsive">
  <table class="table align-middle">
    <thead>
      <tr>
      <th>Image</th>
            <th>Title</th>
            <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        ...
      </tr>
      <tr class="align-bottom">
        ...
      </tr>
      <tr>
        <td>...</td>
        <td>...</td>
        <td class="align-top">This cell is aligned to the top.</td>
        <td>...</td>
      </tr>
    </tbody>
  </table>
</div>
            </div>
          </div>
            <!-- <div class="col-md-6 grid-margin transparent">
              <div class="row">
                <div class="col-md-6 mb-4 stretch-card transparent">
                <div class="card" style= "background-color: #0D4A31; color: white;">
                    <div class="card-body">
                      <p class="mb-4">Jumlah Toko</p>
                      <p class="fs-30 mb-2">20</p>
                      
                    </div>
                  </div>
                </div>
                <div class="col-md-6 mb-4 stretch-card transparent">
                  <div class="card" style= "background-color: #0D4A31; color: white;">
                    <div class="card-body">
                      <p class="mb-4">Jumlah Informasi</p>
                      <p class="fs-30 mb-2">5</p>               
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
                <div class="card" style= "background-color: #0D4A31; color: white;">
                    <div class="card-body">
                      <p class="mb-4">Jumplah Promo</p>
                      <p class="fs-30 mb-2">3</p>
                     
                    </div>
                  </div>
                </div>
                <div class="col-md-6 stretch-card transparent">
                <div class="card" style= "background-color: #0D4A31; color: white;">
                    <div class="card-body">
                      <p class="mb-4">Jumlah Event</p>
                      <p class="fs-30 mb-2">2</p>
                      
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div> -->
          <!-- <div class="row">
            <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
              <div class="card-body"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                 <div class="d-flex justify-content-between">
                  <p class="card-title">Analisis Pesanan</p>
                  <a href="#" class="text-info">Grafik 2024</a>
                 </div>
                  <p class="font-weight-500"></p>
                  <div id="sales-legend" class="chartjs-legend mt-4 mb-2"><ul class="1-legend"><li><span style="background-color: rgb(152, 189, 255);"></span>Offline Sales</li><li><span style="background-color: rgb(75, 73, 172);"></span>Online Sales</li></ul></div>
                  <canvas id="sales-chart" width="378" height="188" style="display: block; height: 151px; width: 303px;" class="chartjs-render-monitor"></canvas>
                </div>
              </div>
            </div>
            <div class="col-md-6 grid-margin stretch-card">
              <div class="card">
              <p class="card-title">Alur Pemesanan</p>
              <h5>Langkah-langkah</h5>
              <li class="nav-item">
              <i class="icon-mail menu-icon"></i><span class="menu-title" style="margin-left: 20px;">pembeli pesan barang di aplikasi mobile</span> <br>
              <li class="nav-item">
              <i class="icon-mail menu-icon"></i><span class="menu-title" style="margin-left: 20px;">Menyelesaikan transaksi</span> <br>
              <li class="nav-item">
              <i class="icon-mail menu-icon"></i><span class="menu-title" style="margin-left: 20px;">Penjual melakukan konfirmasi pesanan</span> <br>
              <li class="nav-item">
              <i class="icon-mail menu-icon"></i><span class="menu-title" style="margin-left: 20px;">Muncul notifikasi status pesanan pembeli</span> <br>
              <li class="nav-item">
              <i class="icon-mail menu-icon"></i><span class="menu-title" style="margin-left: 20px;">Barang diproses</span> <br>  
              <li class="nav-item">
              <i class="icon-mail menu-icon"></i><span class="menu-title" style="margin-left: 20px;">Pembeli mengambil barang dipasar</span> <br>         
            </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card position-relative">
                <div class="card-body">
                  <div id="detailedReports" class="carousel slide detailed-report-carousel position-static pt-2" data-ride="carousel">
                    <div class="carousel-inner">
                      <div class="carousel-item active">
                        <div class="row">
                          <div class="col-md-12 col-xl-3 d-flex flex-column justify-content-start">
                            <div class="ml-xl-4 mt-3">
                            <p class="card-title">Detailed Reports</p>
                              <h1 class="text-primary">$34040</h1>
                              <h3 class="font-weight-500 mb-xl-4 text-primary">North America</h3>
                              <p class="mb-2 mb-xl-0">The total number of sessions within the date range. It is the period time a user is actively engaged with your website, page or app, etc</p>
                            </div>  
                            </div>
                          <div class="col-md-12 col-xl-9">
                            <div class="row">
                              <div class="col-md-6 border-right">
                                <div class="table-responsive mb-3 mb-md-0 mt-3">
                                  <table class="table table-borderless report-table">
                                    <tbody><tr>
                                      <td class="text-muted">Illinois</td>
                                      <td class="w-100 px-0">
                                        <div class="progress progress-md mx-4">
                                          <div class="progress-bar bg-primary" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                      </td>
                                      <td><h5 class="font-weight-bold mb-0">713</h5></td>
                                    </tr>
                                    <tr>
                                      <td class="text-muted">Washington</td>
                                      <td class="w-100 px-0">
                                        <div class="progress progress-md mx-4">
                                          <div class="progress-bar bg-warning" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                      </td>
                                      <td><h5 class="font-weight-bold mb-0">583</h5></td>
                                    </tr>
                                    <tr>
                                      <td class="text-muted">Mississippi</td>
                                      <td class="w-100 px-0">
                                        <div class="progress progress-md mx-4">
                                          <div class="progress-bar bg-danger" role="progressbar" style="width: 95%" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                      </td>
                                      <td><h5 class="font-weight-bold mb-0">924</h5></td>
                                    </tr>
                                    <tr>
                                      <td class="text-muted">California</td>
                                      <td class="w-100 px-0">
                                        <div class="progress progress-md mx-4">
                                          <div class="progress-bar bg-info" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                      </td>
                                      <td><h5 class="font-weight-bold mb-0">664</h5></td>
                                    </tr>
                                    <tr>
                                      <td class="text-muted">Maryland</td>
                                      <td class="w-100 px-0">
                                        <div class="progress progress-md mx-4">
                                          <div class="progress-bar bg-primary" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                      </td>
                                      <td><h5 class="font-weight-bold mb-0">560</h5></td>
                                    </tr>
                                    <tr>
                                      <td class="text-muted">Alaska</td>
                                      <td class="w-100 px-0">
                                        <div class="progress progress-md mx-4">
                                          <div class="progress-bar bg-danger" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                      </td>
                                      <td><h5 class="font-weight-bold mb-0">793</h5></td>
                                    </tr>
                                  </tbody></table>
                                </div>
                              </div>
                              <div class="col-md-6 mt-3"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                                <canvas id="north-america-chart" width="403" height="201" style="display: block; height: 161px; width: 323px;" class="chartjs-render-monitor"></canvas>
                                <div id="north-america-legend"><div class="report-chart"><div class="d-flex justify-content-between mx-4 mx-xl-5 mt-3"><div class="d-flex align-items-center"><div class="mr-3" style="width:20px; height:20px; border-radius: 50%; background-color: #4B49AC"></div><p class="mb-0">Offline sales</p></div><p class="mb-0">88333</p></div><div class="d-flex justify-content-between mx-4 mx-xl-5 mt-3"><div class="d-flex align-items-center"><div class="mr-3" style="width:20px; height:20px; border-radius: 50%; background-color: #FFC100"></div><p class="mb-0">Online sales</p></div><p class="mb-0">66093</p></div><div class="d-flex justify-content-between mx-4 mx-xl-5 mt-3"><div class="d-flex align-items-center"><div class="mr-3" style="width:20px; height:20px; border-radius: 50%; background-color: #248AFD"></div><p class="mb-0">Returns</p></div><p class="mb-0">39836</p></div></div></div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="carousel-item">
                        <div class="row">
                          <div class="col-md-12 col-xl-3 d-flex flex-column justify-content-start">
                            <div class="ml-xl-4 mt-3">
                            <p class="card-title">Detailed Reports</p>
                              <h1 class="text-primary">$34040</h1>
                              <h3 class="font-weight-500 mb-xl-4 text-primary">North America</h3>
                              <p class="mb-2 mb-xl-0">The total number of sessions within the date range. It is the period time a user is actively engaged with your website, page or app, etc</p>
                            </div>  
                            </div>
                          <div class="col-md-12 col-xl-9">
                            <div class="row">
                              <div class="col-md-6 border-right">
                                <div class="table-responsive mb-3 mb-md-0 mt-3">
                                  <table class="table table-borderless report-table">
                                    <tbody><tr>
                                      <td class="text-muted">Illinois</td>
                                      <td class="w-100 px-0">
                                        <div class="progress progress-md mx-4">
                                          <div class="progress-bar bg-primary" role="progressbar" style="width: 70%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                      </td>
                                      <td><h5 class="font-weight-bold mb-0">713</h5></td>
                                    </tr>
                                    <tr>
                                      <td class="text-muted">Washington</td>
                                      <td class="w-100 px-0">
                                        <div class="progress progress-md mx-4">
                                          <div class="progress-bar bg-warning" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                      </td>
                                      <td><h5 class="font-weight-bold mb-0">583</h5></td>
                                    </tr>
                                    <tr>
                                      <td class="text-muted">Mississippi</td>
                                      <td class="w-100 px-0">
                                        <div class="progress progress-md mx-4">
                                          <div class="progress-bar bg-danger" role="progressbar" style="width: 95%" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                      </td>
                                      <td><h5 class="font-weight-bold mb-0">924</h5></td>
                                    </tr>
                                    <tr>
                                      <td class="text-muted">California</td>
                                      <td class="w-100 px-0">
                                        <div class="progress progress-md mx-4">
                                          <div class="progress-bar bg-info" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                      </td>
                                      <td><h5 class="font-weight-bold mb-0">664</h5></td>
                                    </tr>
                                    <tr>
                                      <td class="text-muted">Maryland</td>
                                      <td class="w-100 px-0">
                                        <div class="progress progress-md mx-4">
                                          <div class="progress-bar bg-primary" role="progressbar" style="width: 40%" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                      </td>
                                      <td><h5 class="font-weight-bold mb-0">560</h5></td>
                                    </tr>
                                    <tr>
                                      <td class="text-muted">Alaska</td>
                                      <td class="w-100 px-0">
                                        <div class="progress progress-md mx-4">
                                          <div class="progress-bar bg-danger" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                      </td>
                                      <td><h5 class="font-weight-bold mb-0">793</h5></td>
                                    </tr>
                                  </tbody></table>
                                </div>
                              </div>
                              <div class="col-md-6 mt-3"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                                <canvas id="south-america-chart" height="201" width="403" class="chartjs-render-monitor" style="display: block; height: 161px; width: 323px;"></canvas>
                                <div id="south-america-legend"><div class="report-chart"><div class="d-flex justify-content-between mx-4 mx-xl-5 mt-3"><div class="d-flex align-items-center"><div class="mr-3" style="width:20px; height:20px; border-radius: 50%; background-color: #4B49AC"></div><p class="mb-0">Offline sales</p></div><p class="mb-0">495343</p></div><div class="d-flex justify-content-between mx-4 mx-xl-5 mt-3"><div class="d-flex align-items-center"><div class="mr-3" style="width:20px; height:20px; border-radius: 50%; background-color: #FFC100"></div><p class="mb-0">Online sales</p></div><p class="mb-0">630983</p></div><div class="d-flex justify-content-between mx-4 mx-xl-5 mt-3"><div class="d-flex align-items-center"><div class="mr-3" style="width:20px; height:20px; border-radius: 50%; background-color: #248AFD"></div><p class="mb-0">Returns</p></div><p class="mb-0">290831</p></div></div></div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <a class="carousel-control-prev" href="#detailedReports" role="button" data-slide="prev">
                      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                      <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#detailedReports" role="button" data-slide="next">
                      <span class="carousel-control-next-icon" aria-hidden="true"></span>
                      <span class="sr-only">Next</span>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div> -->
          <div class="row">
            <div class="col-md-7 grid-margin stretch-car">
              <div class="card">
                <div class="card-body">
                  <p class="card-title mb-0">Tambah Data</p>

                  <form action="{{ route('upload.proses') }}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}

                    <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">ID User</label>
                    <input type="text" class="form-control" id="id_user" placeholder="Masukan id user" name="id_user" autofocus required>
                    </div>
                    <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Nama Toko</label>
                    <input type="text" class="form-control" id="nama_toko" placeholder="Masukan nama toko" name="nama_toko" autofocus required>
                    </div>
                    <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Titik Lokasi</label>
                    <input type="text" class="form-control" id="id_user" placeholder="Masukan titik lokasi" name="id_user" autofocus required>
                    </div>
                    <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">No.Handphone</label>
                    <input type="text" class="form-control" id="no_handphone" placeholder="Masukan no.handphone" name="no_handphone" autofocus required>
                    </div>
                    <div class="mb-3">
                    <label for="formFile" class="form-label">Upload Gambar</label>
                    <input class="form-control" type="file" id="gambar" name="gambar"required>
                    </div>
                    <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="deskripsi" rows="3" name="deskripsi" required></textarea>
                </div>
                    <a href="#" class="btn mt-5" id="btn-detail">Tambah Event</a>
                  <!-- <div class="table-responsive">
                    <table class="table table-striped table-borderless">
                    
                      <thead>
                        <tr>
                          <th>Product</th>
                          <th>Price</th>
                          <th>Date</th>
                          <th>Status</th>
                        </tr>  
                      </thead>
                      <tbody>
                        <tr>
                          <td>Search Engine Marketing</td>
                          <td class="font-weight-bold">$362</td>
                          <td>21 Sep 2018</td>
                          <td class="font-weight-medium"><div class="badge badge-success">Completed</div></td>
                        </tr>
                        <tr>
                          <td>Search Engine Optimization</td>
                          <td class="font-weight-bold">$116</td>
                          <td>13 Jun 2018</td>
                          <td class="font-weight-medium"><div class="badge badge-success">Completed</div></td>
                        </tr>
                        <tr>
                          <td>Display Advertising</td>
                          <td class="font-weight-bold">$551</td>
                          <td>28 Sep 2018</td>
                          <td class="font-weight-medium"><div class="badge badge-warning">Pending</div></td>
                        </tr>
                        <tr>
                          <td>Pay Per Click Advertising</td>
                          <td class="font-weight-bold">$523</td>
                          <td>30 Jun 2018</td>
                          <td class="font-weight-medium"><div class="badge badge-warning">Pending</div></td>
                        </tr>
                        <tr>
                          <td>E-Mail Marketing</td>
                          <td class="font-weight-bold">$781</td>
                          <td>01 Nov 2018</td>
                          <td class="font-weight-medium"><div class="badge badge-danger">Cancelled</div></td>
                        </tr>
                        <tr>
                          <td>Referral Marketing</td>
                          <td class="font-weight-bold">$283</td>
                          <td>20 Mar 2018</td>
                          <td class="font-weight-medium"><div class="badge badge-warning">Pending</div></td>
                        </tr>
                        <tr>
                          <td>Social media marketing</td>
                          <td class="font-weight-bold">$897</td>
                          <td>26 Oct 2018</td>
                          <td class="font-weight-medium"><div class="badge badge-success">Completed</div></td>
                        </tr>
                      </tbody>
                    </table>
                  </div> -->
                </div>
              </div>
            </div>
            <!-- <div class="col-md-5 grid-margin stretch-card">
							<div class="card">
								<div class="card-body">
									<h4 class="card-title"></h4>
									<div class="list-wrapper pt-2">
										<ul class="d-flex flex-column-reverse todo-list todo-list-custom">
											<li>
												<div class="form-check form-check-flat">
													<label class="form-check-label">
														<input class="checkbox" type="checkbox">
														Meeting with Urban Team
													<i class="input-helper"></i></label>
												</div>
												<i class="remove ti-close"></i>
											</li>
											<li class="completed">
												<div class="form-check form-check-flat">
													<label class="form-check-label">
														<input class="checkbox" type="checkbox" checked="">
														Duplicate a project for new customer
													<i class="input-helper"></i></label>
												</div>
												<i class="remove ti-close"></i>
											</li>
											<li>
												<div class="form-check form-check-flat">
													<label class="form-check-label">
														<input class="checkbox" type="checkbox">
														Project meeting with CEO
													<i class="input-helper"></i></label>
												</div>
												<i class="remove ti-close"></i>
											</li>
											<li class="completed">
												<div class="form-check form-check-flat">
													<label class="form-check-label">
														<input class="checkbox" type="checkbox" checked="">
														Follow up of team zilla
													<i class="input-helper"></i></label>
												</div>
												<i class="remove ti-close"></i>
											</li>
											<li>
												<div class="form-check form-check-flat">
													<label class="form-check-label">
														<input class="checkbox" type="checkbox">
														Level up for Antony
													<i class="input-helper"></i></label>
												</div>
												<i class="remove ti-close"></i>
											</li>
										</ul>
                  </div>
                  <div class="add-items d-flex mb-0 mt-2">
										<input type="text" class="form-control todo-list-input" placeholder="Add new task">
										<button class="add btn btn-icon text-primary todo-list-add-btn bg-transparent"><i class="icon-circle-plus"></i></button>
									</div>
								</div>
							</div>
            </div> -->
          </div>
          <!-- <div class="row">
            <div class="col-md-4 stretch-card grid-margin">
              <div class="card">
                <div class="card-body">
                  <p class="card-title mb-0">Projects</p>
                  <div class="table-responsive">
                    <table class="table table-borderless">
                      <thead>
                        <tr>
                          <th class="pl-0  pb-2 border-bottom">Places</th>
                          <th class="border-bottom pb-2">Orders</th>
                          <th class="border-bottom pb-2">Users</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="pl-0">Kentucky</td>
                          <td><p class="mb-0"><span class="font-weight-bold mr-2">65</span>(2.15%)</p></td>
                          <td class="text-muted">65</td>
                        </tr>
                        <tr>
                          <td class="pl-0">Ohio</td>
                          <td><p class="mb-0"><span class="font-weight-bold mr-2">54</span>(3.25%)</p></td>
                          <td class="text-muted">51</td>
                        </tr>
                        <tr>
                          <td class="pl-0">Nevada</td>
                          <td><p class="mb-0"><span class="font-weight-bold mr-2">22</span>(2.22%)</p></td>
                          <td class="text-muted">32</td>
                        </tr>
                        <tr>
                          <td class="pl-0">North Carolina</td>
                          <td><p class="mb-0"><span class="font-weight-bold mr-2">46</span>(3.27%)</p></td>
                          <td class="text-muted">15</td>
                        </tr>
                        <tr>
                          <td class="pl-0">Montana</td>
                          <td><p class="mb-0"><span class="font-weight-bold mr-2">17</span>(1.25%)</p></td>
                          <td class="text-muted">25</td>
                        </tr>
                        <tr>
                          <td class="pl-0">Nevada</td>
                          <td><p class="mb-0"><span class="font-weight-bold mr-2">52</span>(3.11%)</p></td>
                          <td class="text-muted">71</td>
                        </tr>
                        <tr>
                          <td class="pl-0 pb-0">Louisiana</td>
                          <td class="pb-0"><p class="mb-0"><span class="font-weight-bold mr-2">25</span>(1.32%)</p></td>
                          <td class="pb-0">14</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4 stretch-card grid-margin">
              <div class="row">
                <div class="col-md-12 grid-margin stretch-card">
                  <div class="card">
                    <div class="card-body">
                      <p class="card-title">Charts</p>
                      <div class="charts-data">
                        <div class="mt-3">
                          <p class="mb-0">Data 1</p>
                          <div class="d-flex justify-content-between align-items-center">
                            <div class="progress progress-md flex-grow-1 mr-4">
                              <div class="progress-bar bg-inf0" role="progressbar" style="width: 95%" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <p class="mb-0">5k</p>
                          </div>
                        </div>
                        <div class="mt-3">
                          <p class="mb-0">Data 2</p>
                          <div class="d-flex justify-content-between align-items-center">
                            <div class="progress progress-md flex-grow-1 mr-4">
                              <div class="progress-bar bg-info" role="progressbar" style="width: 35%" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <p class="mb-0">1k</p>
                          </div>
                        </div>
                        <div class="mt-3">
                          <p class="mb-0">Data 3</p>
                          <div class="d-flex justify-content-between align-items-center">
                            <div class="progress progress-md flex-grow-1 mr-4">
                              <div class="progress-bar bg-info" role="progressbar" style="width: 48%" aria-valuenow="48" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <p class="mb-0">992</p>
                          </div>
                        </div>
                        <div class="mt-3">
                          <p class="mb-0">Data 4</p>
                          <div class="d-flex justify-content-between align-items-center">
                            <div class="progress progress-md flex-grow-1 mr-4">
                              <div class="progress-bar bg-info" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <p class="mb-0">687</p>
                          </div>
                        </div>
                      </div>  
                    </div>
                  </div>
                </div>
                <div class="col-md-12 stretch-card grid-margin grid-margin-md-0">
                  <div class="card data-icon-card-primary">
                    <div class="card-body">
                      <p class="card-title text-white">Number of Meetings</p>                      
                      <div class="row">
                        <div class="col-8 text-white">
                          <h3>34040</h3>
                          <p class="text-white font-weight-500 mb-0">The total number of sessions within the date range.It is calculated as the sum . </p>
                        </div>
                        <div class="col-4 background-icon">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4 stretch-card grid-margin">
              <div class="card">
                <div class="card-body">
                  <p class="card-title">Notifications</p>
                  <ul class="icon-data-list">
                    <li>
                      <div class="d-flex">
                        <img src="{{asset('admin_asset/template/images/faces/face1.jpg')}}" alt="user">
                        <div>
                          <p class="text-info mb-1">Isabella Becker</p>
                          <p class="mb-0">Sales dashboard have been created</p>
                          <small>9:30 am</small>
                        </div>
                      </div>
                    </li>
                    <li>
                      <div class="d-flex">
                        <img src="images/faces/face2.jpg" alt="user">
                        <div>
                          <p class="text-info mb-1">Adam Warren</p>
                          <p class="mb-0">You have done a great job #TW111</p>
                          <small>10:30 am</small>
                        </div>
                      </div>
                    </li>
                    <li>
                      <div class="d-flex">
                      <img src="images/faces/face3.jpg" alt="user">
                     <div>
                      <p class="text-info mb-1">Leonard Thornton</p>
                      <p class="mb-0">Sales dashboard have been created</p>
                      <small>11:30 am</small>
                     </div>
                      </div>
                    </li>
                    <li>
                      <div class="d-flex">
                        <img src="images/faces/face4.jpg" alt="user">
                        <div>
                          <p class="text-info mb-1">George Morrison</p>
                          <p class="mb-0">Sales dashboard have been created</p>
                          <small>8:50 am</small>
                        </div>
                      </div>
                    </li>
                    <li>
                      <div class="d-flex">
                        <img src="images/faces/face5.jpg" alt="user">
                        <div>
                        <p class="text-info mb-1">Ryan Cortez</p>
                        <p class="mb-0">Herbs are fun and easy to grow.</p>
                        <small>9:00 am</small>
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div> -->
          <!-- <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title">Advanced Table</p>
                  <div class="row">
                    <div class="col-12">
                      <div class="table-responsive">
                        <div id="example_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer"><div class="row"><div class="col-sm-12 col-md-6"></div><div class="col-sm-12 col-md-6"></div></div><div class="row"><div class="col-sm-12"><table id="example" class="display expandable-table dataTable no-footer" style="width: 100%;" role="grid">
                          <thead>
                            <tr role="row"><th class="select-checkbox sorting_disabled" rowspan="1" colspan="1" aria-label="Quote#" style="width: 70px;">Quote#</th><th class="sorting_asc" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Product: activate to sort column descending" aria-sort="ascending" style="width: 51px;">Product</th><th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Business type: activate to sort column ascending" style="width: 58px;">Business type</th><th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Policy holder: activate to sort column ascending" style="width: 43px;">Policy holder</th><th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Premium: activate to sort column ascending" style="width: 59px;">Premium</th><th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Status: activate to sort column ascending" style="width: 43px;">Status</th><th class="sorting" tabindex="0" aria-controls="example" rowspan="1" colspan="1" aria-label="Updated at: activate to sort column ascending" style="width: 64px;">Updated at</th><th class="details-control sorting_disabled" rowspan="1" colspan="1" aria-label="" style="width: 22px;"></th></tr>
                          </thead>
                      <tbody><tr class="odd"><td class=" select-checkbox">Incs234</td><td class="sorting_1">Car insurance</td><td>Business type 1</td><td>Jesse Thomas</td><td>$1200</td><td>In progress</td><td>25/04/2020</td><td class=" details-control"></td></tr><tr class="even"><td class=" select-checkbox">Incs235</td><td class="sorting_1">Car insurance</td><td>Business type 2</td><td>Jesse Thomas</td><td>$1200</td><td>Active</td><td>25/04/2020</td><td class=" details-control"></td></tr><tr class="odd"><td class=" select-checkbox">Incs235</td><td class="sorting_1">Car insurance</td><td>Business type 2</td><td>Jesse Thomas</td><td>$1200</td><td>Expired</td><td>25/04/2020</td><td class=" details-control"></td></tr><tr class="even"><td class=" select-checkbox">Incs235</td><td class="sorting_1">Car insurance</td><td>Business type 2</td><td>Jesse Thomas</td><td>$1200</td><td>In progress</td><td>25/04/2020</td><td class=" details-control"></td></tr><tr class="odd"><td class=" select-checkbox">Incs235</td><td class="sorting_1">Car insurance</td><td>Business type 2</td><td>Jesse Thomas</td><td>$1200</td><td>Active</td><td>25/04/2020</td><td class=" details-control"></td></tr><tr class="even"><td class=" select-checkbox">Incs235</td><td class="sorting_1">Car insurance</td><td>Business type 2</td><td>Jesse Thomas</td><td>$1200</td><td>Active</td><td>25/04/2020</td><td class=" details-control"></td></tr><tr class="odd"><td class=" select-checkbox">Incs235</td><td class="sorting_1">Car insurance</td><td>Business type 2</td><td>Jesse Thomas</td><td>$1200</td><td>Active</td><td>25/04/2020</td><td class=" details-control"></td></tr><tr class="even"><td class=" select-checkbox">Incs235</td><td class="sorting_1">Car insurance</td><td>Business type 2</td><td>Jesse Thomas</td><td>$1200</td><td>Expired</td><td>25/04/2020</td><td class=" details-control"></td></tr><tr class="odd"><td class=" select-checkbox">Incs235</td><td class="sorting_1">Car insurance</td><td>Business type 2</td><td>Jesse Thomas</td><td>$1200</td><td>Active</td><td>25/04/2020</td><td class=" details-control"></td></tr><tr class="even"><td class=" select-checkbox">Incs235</td><td class="sorting_1">Car insurance</td><td>Business type 2</td><td>Jesse Thomas</td><td>$1200</td><td>In progress</td><td>25/04/2020</td><td class=" details-control"></td></tr></tbody></table></div></div><div class="row"><div class="col-sm-12 col-md-5"></div><div class="col-sm-12 col-md-7"></div></div></div>
                      </div>
                    </div>
                  </div>
                  </div>
                </div>
              </div>
            </div>
        </div> -->
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2021.  Premium <a href="https://www.bootstrapdash.com/" target="_blank">Bootstrap admin template</a> from BootstrapDash. All rights reserved.</span>
            <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted &amp; made with <i class="ti-heart text-danger ml-1"></i></span>
          </div>
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Distributed by <a href="https://www.themewagon.com/" target="_blank">Themewagon</a></span> 
          </div>
        </footer> 
        <!-- partial -->
      </div>

      <!-- partial -->
    </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  @include('include.scripct')
 
</body>
</html>