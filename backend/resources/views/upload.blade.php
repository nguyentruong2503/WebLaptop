<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Ảnh | Hệ thống Quản lý Sản phẩm</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-hover: #3a56d4;
            --success-color: #2ecc71;
            --error-color: #e74c3c;
            --text-color: #2d3748;
            --light-gray: #f7fafc;
            --border-color: #e2e8f0;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f5f7fa;
            color: var(--text-color);
            line-height: 1.6;
            padding: 0;
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .upload-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 500px;
            padding: 2.5rem;
            margin: 1rem;
            text-align: center;
        }
        
        .upload-header {
            margin-bottom: 2rem;
        }
        
        .upload-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .upload-header p {
            color: #718096;
            font-size: 0.9rem;
        }
        
        .upload-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .upload-area {
            border: 2px dashed var(--border-color);
            border-radius: 8px;
            padding: 2rem 1rem;
            margin-bottom: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            background-color: var(--light-gray);
        }
        
        .upload-area:hover {
            border-color: var(--primary-color);
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .upload-area.active {
            border-color: var(--primary-color);
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .file-input {
            display: none;
        }
        
        .upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .upload-label i {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .upload-label h3 {
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .upload-label p {
            font-size: 0.85rem;
            color: #718096;
        }
        
        .preview-container {
            margin-top: 1.5rem;
            display: none;
        }
        
        .image-preview {
            max-width: 100%;
            max-height: 250px;
            border-radius: 6px;
            object-fit: contain;
            box-shadow: var(--shadow);
            margin-bottom: 1rem;
        }
        
        .btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }
        
        .btn:disabled {
            background-color: #cbd5e0;
            cursor: not-allowed;
            transform: none;
        }
        
        .btn i {
            font-size: 1rem;
        }
        
        .status-message {
            margin-top: 1.5rem;
            padding: 0.75rem;
            border-radius: 6px;
            font-size: 0.9rem;
            display: none;
        }
        
        .status-message.success {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success-color);
            display: block;
        }
        
        .status-message.error {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--error-color);
            display: block;
        }
        
        .loading {
            display: none;
            margin: 1rem auto;
        }
        
        .spinner {
            border: 3px solid rgba(67, 97, 238, 0.1);
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 576px) {
            .upload-container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="upload-container">
        <div class="upload-header">
            <div class="upload-icon">
                <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <h2>Upload Ảnh Sản Phẩm</h2>
            <p>Chọn hình ảnh từ thiết bị của bạn hoặc kéo thả vào vùng bên dưới</p>
        </div>
        
        <form id="uploadForm" enctype="multipart/form-data">
            @csrf
            <div class="upload-area" id="uploadArea">
                <input type="file" id="fileInput" class="file-input" name="image" accept="image/*" required>
                <label for="fileInput" class="upload-label">
                    <i class="fas fa-images"></i>
                    <h3>Chọn hình ảnh</h3>
                    <p>JPG, PNG hoặc GIF (Tối đa 5MB)</p>
                </label>
            </div>
            
            <div class="preview-container" id="previewContainer">
                <img id="imagePreview" class="image-preview" src="" alt="Xem trước ảnh">
            </div>
            
            <button type="submit" class="btn" id="uploadBtn" disabled>
                <i class="fas fa-upload"></i> Upload Ảnh
            </button>
            
            <div class="loading" id="loading">
                <div class="spinner"></div>
            </div>
            
            <div class="status-message" id="statusMessage"></div>
        </form>
    </div>

    <script>
        // DOM Elements
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const previewContainer = document.getElementById('previewContainer');
        const imagePreview = document.getElementById('imagePreview');
        const uploadBtn = document.getElementById('uploadBtn');
        const uploadForm = document.getElementById('uploadForm');
        const loading = document.getElementById('loading');
        const statusMessage = document.getElementById('statusMessage');
        
        // Drag and drop events
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('active');
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('active');
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('active');
            
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                handleFileSelect();
            }
        });
        
        // File input change event
        fileInput.addEventListener('change', handleFileSelect);
        
        function handleFileSelect() {
            const file = fileInput.files[0];
            
            if (file) {
                // Validate file type and size
                const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                const maxSize = 5 * 1024 * 1024; // 5MB
                
                if (!validTypes.includes(file.type)) {
                    showStatus('Vui lòng chọn file ảnh (JPEG, PNG hoặc GIF)', 'error');
                    resetForm();
                    return;
                }
                
                if (file.size > maxSize) {
                    showStatus('File ảnh quá lớn (Tối đa 5MB)', 'error');
                    resetForm();
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    previewContainer.style.display = 'block';
                    uploadBtn.disabled = false;
                    statusMessage.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        }
        
        // Form submission
        uploadForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const file = fileInput.files[0];
            if (!file) return;
            
            // Disable button and show loading
            uploadBtn.disabled = true;
            loading.style.display = 'block';
            statusMessage.style.display = 'none';
            
            try {
                const formData = new FormData();
                formData.append('image', file);
                formData.append('_token', document.querySelector('input[name="_token"]').value);
                
                const response = await fetch('{{ url("/api/upload") }}', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (!response.ok) {
                    throw new Error(result.error || 'Lỗi khi upload ảnh');
                }
                
                // Show success message
                showStatus('Upload ảnh thành công!', 'success');
                
                // Send URL back to parent window and close after delay
                window.opener.postMessage({ 
                    imageUrl: result.url,
                    message: 'Upload ảnh thành công'
                }, '*');
                
                // Close window after 1.5 seconds
                setTimeout(() => {
                    window.close();
                }, 1500);
                
            } catch (error) {
                showStatus('Lỗi: ' + error.message, 'error');
                uploadBtn.disabled = false;
                console.error('Upload error:', error);
            } finally {
                loading.style.display = 'none';
            }
        });
        
        // Helper functions
        function showStatus(message, type) {
            statusMessage.textContent = message;
            statusMessage.className = `status-message ${type}`;
        }
        
        function resetForm() {
            fileInput.value = '';
            previewContainer.style.display = 'none';
            uploadBtn.disabled = true;
        }
    </script>
</body>
</html>