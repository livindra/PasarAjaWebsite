<<<<<<< HEAD
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{asset('admin_asset/template/css/vertical-layout-light/dropzone.css')}}">
    <title>Document</title>
</head>
<body>
<div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h6>Tambah Detailing</h6>
        </div>
        <div class="card-body px-4 pt-0 pb-2">
            <form action="/service-submit" method="POST" id="formDropzone" class="dropzone" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="exampleInputPrice">Tipe Layanan <span style="color: red;">*</span></label>
                    <select name="type" class="form-control" id="exampleInputPrice" placeholder="Masukkan tipe layanan">
                        <option value="" disabled selected>Pilih Tipe Layanan</option>
                        <option value="Detailing Interior">Detailing Interior</option>
                        <option value="Detailing Eksterior">Detailing Eksterior</option>
                        <option value="Detailing Kaca Mobil">Detailing Kaca Mobil</option>
                        <option value="Detailing Engine Bay">Detailing Engine Bay</option>
                        <option value="Detailing Velg & Ban">Detailing Velg & Ban</option>
                    </select>  
                    @error('type')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror              
                </div>
                <div class="form-group">
                    <label for="exampleInputSparepart">Suku Cadang <span style="color: red;">*</span></label>
                    <select name="sparepart" class="form-control" id="exampleInputSparepart" placeholder="Masukkan suku cadang">
                        <option value="" disabled selected>Pilih Suku Cadang</option>
                        <option value="Microfiber">Kain Mikrofiber</option>
                        <option value="Car Shampoo">Sampo Mobil</option>
                        <option value="Wax or Sealant">Pengilap atau Sealant</option>
                        <option value="Detailing Brushes">Sikat Detailing</option>
                        <option value="Clay Bar">Bar Clay</option>
                        <option value="Interior Cleaners">Pembersih Interior</option>
                        <option value="Glass Cleaners">Pembersih Kaca</option>
                        <option value="Tar Remover">Tar Remover</option>
                        <option value="Metal Polish">Metal Polish</option>
                        <option value="Wheel Cleaner">Wheel Cleaner</option>
                        <option value="Tire Brush">Tire Brush</option>
                    </select>    
                    @error('sparepart')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror  
                </div>
            
                <div class="form-group">
                    <label for="exampleInputQty">Jumlah Suku Cadang <span style="color: red;">*</span></label>
                    <input name="qty" type="number" class="form-control" id="exampleInputQty" aria-describedby="emailHelp" placeholder="Masukkan Jumlah Suku Cadang">
                    @error('qty')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror  
                </div>
            
                <div class="form-group">
                    <label for="exampleInputPrice">Harga <span style="color: red;">*</span></label>
                    <input name="price" type="number" class="form-control" id="exampleInputPrice" aria-describedby="emailHelp" placeholder="Masukkan Harga">
                    @error('price')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror  
                </div>  
            
                <div class="form-group mb-4">
                    <label class="form-label text-muted opacity-75 fw-medium" for="formImage">Foto Sparepart <span style="color: red;">*</span></label>
                    <div class="dropzone-drag-area form-control" id="previews">
                        <div class="dz-message text-muted opacity-50" data-dz-message>
                            <span>Drag file here to upload</span>
                        </div>    
                    </div>
                    @error('file')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>             
            
                <button class="btn form-control" id="search-btn" type="submit">
                    <span class="spinner-border spinner-border-sm d-none me-2" aria-hidden="true"></span>
                    Simpan
                </button>
            
            </form>
            
            <!-- Scripts -->
            <script src="{{ asset('admin_asset/template/js/jquery.js') }}"></script>
            <script src="{{ asset('admin_asset/template/js/dropzone.js') }}"></script>
            <script>
                Dropzone.autoDiscover = false;
            
                $('#formDropzone').dropzone({
                    addRemoveLinks: true,
                    autoProcessQueue: false,       
                    uploadMultiple: false,
                    parallelUploads: 1,
                    maxFiles: 1,
                    acceptedFiles: '.jpeg, .jpg, .png, .gif',
                    previewsContainer: "#previews",
                    init: function() 
                    {
                        myDropzone = this;
            
                        // when file is dragged in
                        this.on('addedfile', function(file) { 
                            $('.dropzone-drag-area').removeClass('is-invalid').next('.invalid-feedback').hide();
                        });
                    },
                    success: function(file, response) 
                    {
                        // hide form and show success message
                        $('#formDropzone').fadeOut(600);
                        setTimeout(function() {
                            $('#successMessage').removeClass('d-none');
                            window.location.href = '/service-index';
                        }, 600);
                    }
                });
            
                $('#search-btn').on('click', function(event) {
                    event.preventDefault();
                    var $this = $(this);
                    
                    // show submit button spinner
                    $this.children('.spinner-border').removeClass('d-none');
                    
                    // validate form & submit if valid
                    if ($('#formDropzone')[0].checkValidity() === false) {
                        event.stopPropagation();
            
                        // show error messages & hide button spinner    
                        $('#formDropzone').addClass('was-validated'); 
                        $this.children('.spinner-border').addClass('d-none');
    
                        // if dropzone is empty show error message
                        if (!myDropzone.getQueuedFiles().length > 0) {                        
                            $('.dropzone-drag-area').addClass('is-invalid').next('.invalid-feedback').show();
                        }
                    } else {
    
                        // if everything is ok, submit the form
                        myDropzone.processQueue();
                    }
                });
    
            </script>
              
                     
    
        </div>
    </div>
</div>
</body>
</html>

<div class="card-body"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                  <p class="card-title">Penilaian pembeli dalam bulan ini</p>
                  <p class="font-weight-500"></p>
                  <div class="d-flex flex-wrap mb-5">
                    <div class="mr-5 mt-3">
                      <p class="text-muted">Order value</p>
                      <h3 class="text-primary fs-30 font-weight-medium">12.3k</h3>
                    </div>
                    <div class="mr-5 mt-3">
                      <p class="text-muted">Orders</p>
                      <h3 class="text-primary fs-30 font-weight-medium">14k</h3>
                    </div>
                    <div class="mr-5 mt-3">
                      <p class="text-muted">Users</p>
                      <h3 class="text-primary fs-30 font-weight-medium">71.56%</h3>
                    </div>
                    <div class="mt-3">
                      <p class="text-muted">Downloads</p>
                      <h3 class="text-primary fs-30 font-weight-medium">34040</h3>
                    </div> 
                  </div>
                  <canvas id="order-chart" width="378" height="188" style="display: block; height: 151px; width: 303px;" class="chartjs-render-monitor"></canvas>
                </div>
=======
<html>

<head>
    <title>Dropzone Image Upload in Laravel</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.js"> </script>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center">Dropzone Image Upload in Laravel</h1>

                <form action="{{ route('dropzone.store') }}" method="post" name="file" files="true"
                    enctype="multipart/form-data" class="dropzone" id="image-upload">
                    @csrf
                    <div>
                        <h3 class="text-center">Upload Multiple Images</h3>
                    </div>
                </form>
                <button type="button" id="button" class="btn btn-primary">Upload</button>
            </div>
        </div>
    </div>
</body>

</html>
>>>>>>> c2f027fc5289960da3c87fadc3ece38105046125
