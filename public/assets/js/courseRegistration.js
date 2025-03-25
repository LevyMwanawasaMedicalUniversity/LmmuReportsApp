$(document).ready(function() {
    $('.registerButton').click(function(e) {
        $.ajaxSetup({ cache: false });
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
        // console.log(courses);
        // console.log(registrationFee);
        // console.log(payments2024);
        // console.log(totalFee);
        // console.log("Actual" + actualBalance);

        // Show the modal
        if ((registrationFee <= payments2024) && (actualBalance <= 0)) {
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
        } else if (registrationFee > payments2024) {
            var shortfall = registrationFee - payments2024;
            $('#ineligibleModal .modal-body').html('<p style="color:red;">You are short of registration by: K ' + shortfall + '</p><br><p>Kindly make a payment to proceed with the registration</p>');
            $('#ineligibleModal').modal('show');
        } else if (actualBalance > 0) {
            $('#ineligibleModal .modal-body').html('<p style="color:red;">You currently have a balance on your account of : K ' + actualBalance + '</p><br><p>Kindly clear your balance to proceed with registration</p>');
            $('#ineligibleModal').modal('show');
        }
    });
});

$(document).ready(function() {
    $('.registerButtonRepeat').click(function(e) {
        $.ajaxSetup({ cache: false });
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
        // console.log(coursesRepeat);
        // console.log(registrationFee);
        // console.log(totalFee);
        // console.log(payments2024);
        // console.log("Actual" + actualBalance);

        // Show the modal
        if ((registrationFee <= payments2024) && (actualBalance <= 0)) {
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
        } else if (registrationFee > payments2024) {
            var shortfall = registrationFee - payments2024;
            $('#ineligibleModal .modal-body').html('<p style="color:red;">You are short of registration by: K ' + shortfall + '</p><br><p>Kindly make a payment to proceed with the registration</p>');
            $('#ineligibleModal').modal('show');
        } else if (actualBalance > 0) {
            $('#ineligibleModal .modal-body').html('<p style="color:red;">You currently have a balance on your account of : K ' + actualBalance + '</p><br><p>Kindly clear your balance to proceed with registration</p>');
            $('#ineligibleModal').modal('show');
        }
    });
});

// Combined courses registration function
$(document).ready(function() {
    $('.registerButtonCombined').click(function(e) {
        $.ajaxSetup({ cache: false });
        e.preventDefault();

        var studentId = $(this).attr('id').replace('registerButtonCombined', '');
        
        // Get the registration fee and total fee from the accordion header
        var registrationFee = parseFloat($('#registrationFeeRepeatCombined' + studentId).text().replace('Registration Fee = K', '').replace(/,/g, ''));
        var totalFee = parseFloat($('#totalFeeRepeatCombined' + studentId).text().replace('Total Invoice = K', '').replace(/,/g, ''));
        var payments2024 = parseFloat($('#payments2024').val().replace('K', '').replace(/,/g, ''));
        var actualBalance = parseFloat($('#actualBalance').val().replace('K', '').replace(/,/g, ''));
        
        console.log("Registration Fee:", registrationFee);
        console.log("Total Fee:", totalFee);
        console.log("Payments:", payments2024);
        console.log("Balance:", actualBalance);
        
        // Store the courses in a variable
        var combinedCourses = [];
        $('input.courseRepeat:checked').each(function() {
            combinedCourses.push($(this).val());
        });
        
        console.log("Selected courses:", combinedCourses);

        // Show the modal based on eligibility
        if ((registrationFee <= payments2024) && (actualBalance <= 0)) {
            // Populate the modal with the courses
            var courseListHtml = '';
            $('input.courseRepeat:checked').each(function() {
                var courseCode = $(this).val();
                var courseRow = $(this).closest('tr');
                var courseName = courseRow.find('td:nth-child(3)').text();
                var courseType = courseRow.find('td:nth-child(5) span').text();
                var badgeClass = courseType === 'Repeat' ? 'bg-warning' : 'bg-primary';
                
                courseListHtml += '<li>' + courseCode + ' - ' + courseName + ' <span class="badge ' + badgeClass + '">' + courseType + '</span></li>';
            });
            
            $('#combinedCoursesList').html(courseListHtml);
            $('#combinedTotalFee').text(totalFee.toFixed(2));
            
            // Update the hidden input field with the selected courses
            $('#combinedCoursesInput').val(combinedCourses.join(','));
            $('#combinedCoursesFormInput').val(JSON.stringify(combinedCourses));
            
            // Show the modal
            $('#eligibleModalRepeatCombined').modal('show');
        } else if (registrationFee > payments2024) {
            var shortfall = registrationFee - payments2024;
            $('#ineligibleModalRepeatCombined .modal-body p:first').html('You are short of registration by: K ' + shortfall.toFixed(2));
            $('#ineligibleModalRepeatCombined').modal('show');
        } else if (actualBalance > 0) {
            $('#balanceModalRepeatCombined .modal-body p:first').html('You currently have a balance on your account of: K ' + actualBalance.toFixed(2));
            $('#balanceModalRepeatCombined').modal('show');
        }
    });
});