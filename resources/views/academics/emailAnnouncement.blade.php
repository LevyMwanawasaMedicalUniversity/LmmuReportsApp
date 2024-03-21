@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Send Email Announcement',
    'activePage' => 'academics',
    'activeNav' => '',
])


@section('content')
<script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
<div class="panel-header panel-header-sm">
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Send Email Announcement</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-9">
                            <form action="" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="emailSubject">Subject</label>
                                    <input type="text" class="form-control" id="emailSubject" name="subject" placeholder="Enter email subject">
                                </div>
                                <div class="form-group">
                                    <label for="emailBody">Body</label>
                                    <textarea class="form-control" id="emailBody" name="body" rows="3" placeholder="Enter email body"></textarea>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="emailSignature">Signature</label>
                                    <input type="text" class="form-control" id="emailSignature" name="signature" value="Regards, Registrar" readonly>
                                </div>
                                <button type="submit" class="btn btn-primary">Send Email</button>
                            </form> 
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="excelFile" style="font-weight: bold; font-size: 16px;">Choose Excel (xlsx) File</label>
                                <input type="file" name="excelFile" accept=".xlsx" class="form-control" id="excelFileInput" style="border: 2px solid #3498db; padding: 10px; border-radius: 5px; background-color: #f0f0f0; cursor: pointer;">
                            </div>
                            <div class="form-group">
                                <div id="filePreview" style="display: none;"></div>
                            </div>
                            <div class="loader" id="loader" style="display: none;">
                                <div id="percentage">0%</div>
                            <!-- You can add loading spinner or text here -->
                            Loading...
                            </div>
                        </div>
                        
                    </div>               
                </div>
            </div>
        </div>      
    </div>
</div>
<script>
    CKEDITOR.replace('emailBody');
</script>
<script>
    const excelFileInput = document.getElementById('excelFileInput');
    const filePreview = document.getElementById('filePreview');
    const loader = document.getElementById('loader');
    const percentage = document.getElementById('percentage');

    excelFileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = document.createElement('div');
                preview.innerHTML = `
                    <strong>File Preview:</strong><br>
                    File Name: ${file.name}<br>
                    File Type: ${file.type}<br>
                    File Size: ${formatBytes(file.size)}
                `;
                filePreview.innerHTML = '';
                filePreview.appendChild(preview);
                filePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            filePreview.style.display = 'none';
        }
    });

    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';

        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    document.querySelector('form').addEventListener('submit', (e) => {
        loader.style.display = 'block';
        const form = e.target;
        const formData = new FormData(form);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', form.action);

        // Progress event to update percentage
        xhr.upload.addEventListener('progress', (event) => {
        if (event.lengthComputable) {
            const percentComplete = (event.loaded / event.total) * 100;
            percentage.textContent = percentComplete.toFixed(2) + '%';
        }
    });

    xhr.send(formData);
    });
</script>

@endsection