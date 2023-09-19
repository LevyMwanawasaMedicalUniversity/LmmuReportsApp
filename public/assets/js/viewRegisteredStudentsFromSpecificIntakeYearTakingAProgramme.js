$(document).ready(function() {

    $('#yearOfStudy,#academicYear, #schoolName, #programmeName').on('change', function() {
        var yearOfStudy = $('#yearOfStudy').val();
        var academicYear = $('#academicYear').val();
        var school = $('#schoolName').val();
        var programme = $('#programmeName').val();

        if (yearOfStudy){
            $('#academicYear').prop('disabled', false);
        }else{
            $('#academicYear').prop('disabled', true);
        }

        if (yearOfStudy && academicYear){
            $('#schoolName').prop('disabled', false);
        }else{
            $('#schoolName').prop('disabled', true);
        }

        if (yearOfStudy && academicYear && school){
            $('#programmeName').prop('disabled', false);
        }else{
            $('#programmeName').prop('disabled', true);
        }

        if (yearOfStudy && school && programme && programme ) {
            $('#viewResultsBtn').prop('disabled', false);
        } else {
            $('#viewResultsBtn').prop('disabled', true);
        }
    });
    $('#yearOfStudy').on('change', function() {
        var yearOfStudy = $(this).val();

        if (yearOfStudy) {
            $('#academicYear').prop('disabled', false);
        } else {
            $('#academicYear').prop('disabled', false);
            $('#schoolName').prop('disabled', true);
            $('#programmeName').prop('disabled', true);
            $('#programmeName').empty();
        }
    });

    $('#schoolName').on('click', function() {
        var schoolName = $(this).val();

        if (schoolName) {
            $('#programmeName').prop('disabled', false);
            getProgrammesBySchool(schoolName);
        } else {
            $('#programmeName').prop('disabled', true);
            $('#programmeName').empty();
        }
    });

    function getProgrammesBySchool(schoolName) {
        $.ajax({
            url: "{{ route('getProgrammesBySchoolDynamicForm') }}",
            type: 'GET',
            data: { schoolName: schoolName },
            dataType: 'json',
            success: function(data) {
                $('#programmeName').empty();
                console.log(data);
                $('#programmeName').append('<option value="">--Select Programme--</option>');
                $.each(data, function(key, value) {
                    $('#programmeName').append('<option value="' + value.ShortName + '">' + value.Name + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    }
});