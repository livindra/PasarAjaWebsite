<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Upload File Laravel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .heading {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="file"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }

        textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
            resize: none;
        }

        .btn-primary {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2 class="heading">Upload File Dengan Laravel</h2>
        <form action="{{ route('upload.proses') }}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}

            <div class="form-group">
                <label for="file">File Gambar</label>
                <input type="file" name="file" id="file">
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="4"></textarea>
            </div>
            <input type="submit" value="Upload" class="btn-primary">
        </form>
    </div>

</body>

</html>
