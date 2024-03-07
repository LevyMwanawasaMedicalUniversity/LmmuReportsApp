$(document).ready(function() {
    $('.registerButton').click(function(e) {
        e.preventDefault();

        var index = $(this).attr('id').replace('registerButton', '');
        var registrationFee = registrationFeesArray[index];
        var totalFee = totalFeesArray[index];
        // var payments2024 = parseFloat($('#payments2024').text().replace('K', ''));
        // Store the courses in a variable
        var courses = [];
        $('input[id^="course' + index + '"]:checked').each(function() {
            courses.push($(this).val());
        });
        console.log(courses);
        console.log(registrationFee);
        console.log(payments2024);
        console.log(totalFee);

        // Show the modal
        if (registrationFee <= payments2024) {
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

$(document).ready(function() {
    $('.registerButtonRepeat').click(function(e) {
        e.preventDefault();

        var index = $(this).attr('id').replace('registerButtonRepeat', '');
        
        var registrationFee = registrationFeesRepeatArray[index];
        var totalFee = totalFeeArrayRepeat[index];
        // var payments2024 = parseFloat($('#payments2024').text().replace('K', ''));
        // Store the courses in a variable
        var coursesRepeat = [];
        $('input[id^="courseRepeat' + index + '"]:checked').each(function() {
            coursesRepeat.push($(this).val());
        });
        console.log(coursesRepeat);
        console.log(registrationFee);
        console.log(totalFee);
        console.log(payments2024);

        // Show the modal
        if (registrationFee <= payments2024) {
            $('#eligibleModal').modal('show');

            // Populate the modal with the courses
            var courseList = '';
            for (var i = 0; i < coursesRepeat.length; i++) {
                courseList += '<p>' + coursesRepeat[i] + '</p>';
            }
            var courseListText = '<p>You are submitting the following course for registration. Click "Yes" to proceed and "No" to cancel</p><br>' + courseList + '<br><p>Your Total Invoice is: K ' + totalFee + '</p>';
            $('#eligibleModal .modal-body').html(courseListText);

            // Update the hidden input field with the selected courses
            $('#coursesInput').val(coursesRepeat.join(','));
        } else {
            var shortfall = registrationFee - payments2024;
            $('#ineligibleModal .modal-body').html('<p style="color:red;">You are short of registration by: K ' + shortfall + '</p><br><p>Kindly make a payment to proceed with the registration</p>');
            $('#ineligibleModal').modal('show');
        }
    });
});