@extends('layouts.app', [
    'class' => 'sidebar-mini ',
    'namePage' => 'Course Registration',
    'activePage' => 'studentCourseRegistration',
    'activeNav' => '',
])

@section('content')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<div class="panel-header panel-header-sm">
</div>
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title no-print">NMCZ Course Registration</h4>

                    <div class="col-md-12">
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
                </div>
                <div class="card-body">
                    @include('allStudents.components.finacialInformation')
                    @include('allStudents.components.studentCoursesForNMCZRegistration')           
                </div>               
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.registerButtonNMCZ').click(function(e) {
        e.preventDefault();

        
        var registrationFee = registrationFeesNMCZ;
        var totalFee = totalFeeNMCZ;
        // var payments2024 = parseFloat($('#payments2024').text().replace('K', ''));
        // Store the courses in a variable
        var courses = [];
        $('input[id^="coursesnmcz"]:checked').each(function() {
            courses.push($(this).val());
        });
        var studentBalance = balance * -1;
        console.log(courses);
        console.log(registrationFee);
        console.log(payments2024);
        console.log(totalFee);

        // Show the modal
        if (registrationFee <= studentBalance) {
            $('#eligibleModal').modal('show');

            // Populate the modal with the courses
            var courseList = '';
            for (var i = 0; i < courses.length; i++) {
                courseList += '<p>' + courses[i] + '</p>';
            }
            var courseListText = '<p>You are submitting the following course for registration. Click "Yes" to proceed and "No" to cancel</p><br>' + courseList + '<br><p>Your Total Invoice is: K ' + totalFee + '</p>';
            $('#eligibleModal .modal-body').html(courseListText);

            // Update the hidden input field with the selected courses
            $('#coursesInput').val(courses.join(','));
        } else {
            var shortfall = registrationFee - payments2024;
            $('#ineligibleModal .modal-body').html('<p style="color:red;">You are short of registration by: K ' + shortfall + '</p><br><p>Kindly make a payment to proceed with the registration</p>');
            $('#ineligibleModal').modal('show');
        }
    });
});
</script>
@include('allStudents.components.registrationModalsNMCZ')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection