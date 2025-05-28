<!DOCTYPE html>
<html>
<head>
    <title>Upload Ảnh với Cloudinary</title>
</head>
<body>
    <h2>Upload ảnh lên Cloudinaryaaaaaaa</h2>
    <form action="{{ url('/api/upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="image" required>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
