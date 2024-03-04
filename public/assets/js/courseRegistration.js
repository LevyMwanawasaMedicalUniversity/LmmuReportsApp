$(document).ready(function() {
    $('.registerButton').click(function(e) {
        e.preventDefault();

        var index = $(this).attr('id').replace('registerButton', '');
        var registrationFeeText = $('#registrationFee' + index).text();
        var registrationFee = parseFloat(registrationFeeText.replace(/[^0-9\.]/g, ''));
        var payments2024 = parseFloat($('#payments2024').text().replace('K', ''));
    // Store the courses in a variable
        var courses = [];
        $('input[id^="course' + index + '"]:checked').each(function() {
            courses.push($(this).val());
        });
        console.log(courses);
        console.log(registrationFee);
        console.log(payments2024);

    // Show the modal
        if (registrationFee <= payments2024) {
            $('#eligibleModal').modal('show');

      // Populate the modal with the courses
        var courseList = '';
        for (var i = 0; i < courses.length; i++) {
                courseList += '<p>' + courses[i] + '</p>';
        }
        $('#eligibleModal .modal-body').html(courseList);
        } else {
            $('#ineligibleModal').modal('show');
        }
    });
});

$(document).ready(function() {
    $('.registerButtonRepeat').click(function(e) {
        e.preventDefault();

        var index = $(this).attr('id').replace('registerButtonRepeat', '');
        var registrationFeeText = $('#registrationFeeRepeat' + index).text();
        var registrationFee = parseFloat(registrationFeeText.replace(/[^0-9\.]/g, ''));
        var payments2024 = parseFloat($('#payments2024').text().replace('K', ''));
    // Store the courses in a variable
        var coursesRepeat = [];
        $('input[id^="courseRepeat' + index + '"]:checked').each(function() {
            coursesRepeat.push($(this).val());
        });
        console.log(coursesRepeat);
        console.log(registrationFee);
        console.log(payments2024);

    // Show the modal
        if (registrationFee <= payments2024) {
            $('#eligibleModal').modal('show');

      // Populate the modal with the courses
        var courseList = '';
        for (var i = 0; i < coursesRepeat.length; i++) {
                courseList += '<p>' + coursesRepeat[i] + '</p>';
        }
        $('#eligibleModal .modal-body').html(courseList);
        } else {
            $('#ineligibleModal').modal('show');
        }
    });
});