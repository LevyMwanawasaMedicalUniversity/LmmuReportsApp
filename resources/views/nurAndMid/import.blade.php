@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Docket',
    'activePage' => 'nurAndMid.import',
    'activeNav' => '',
])

@section('content')
<div class="panel-header panel-header-sm">
</div>

<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Import Nursing And Midwifery Affiliate Students</h4>

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <form action="{{ route('nurAndMid.uploadStudents') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="excelFile" style="font-weight: bold; font-size: 16px;">Choose Excel (xlsx) File</label>
                            <input type="file" name="excelFile" accept=".xlsx, .xls, .csv" class="form-control" id="excelFileInput" style="border: 2px solid #3498db; padding: 10px; border-radius: 5px; background-color: #f0f0f0; cursor: pointer;">
                        </div>
                        <div class="form-group">
                            <div id="filePreview" style="display: none;"></div>
                        </div>
                        <div class="loader" id="loader" style="display: none;">
                            <div id="percentage">0%</div>
                            Loading...
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="academicYear" style="font-weight: bold; font-size: 16px;">Academic Year</label>
                                    <select name="academicYear" class="form-control">                                    
                                        <option value="2024">2024</option>
                                        <option value="2025">2025</option>
                                        <option value="2026">2026</option>
                                        <option value="2027">2027</option>
                                        <option value="2028">2028</option>
                                        <option value="2029">2029</option>
                                    </select>
                                </div>
                            </div>  
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="programme" style="font-weight: bold; font-size: 16px;">Programme</label>
                                    <select name="programme" class="form-control">                                    
                                        <option value="BScCCN">BACHELOR OF SCIENCE IN CRITICAL CARE NURSING</option>                                    
                                    </select>
                                </div>
                            </div>                                             
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

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
