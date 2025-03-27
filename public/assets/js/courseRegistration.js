// Individual courses registration function
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

        // The eligibility check and modal handling has been moved to Blade template
        // We're keeping this code for reference but it's disabled now
        
        // Just collect the courses for potential manual processing
        var courseList = '';
        for (var i = 0; i < courses.length; i++) {
            courseList += '<p>' + courses[i] + '</p>';
        }
        
        // Still update the hidden input field with selected courses for JS-enabled forms
        $('#coursesInput').val(courses.join(','));
    });
});

// Individual repeat courses registration function
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

        // The eligibility check and modal handling has been moved to Blade template
        // We're keeping this code for reference but it's disabled now
        
        // Just collect the courses for potential manual processing
        var courseList = '';
        for (var i = 0; i < coursesRepeat.length; i++) {
            courseList += '<p>' + coursesRepeat[i] + '</p>';
        }
        
        // Still update the hidden input field with selected courses for JS-enabled forms
        $('#coursesInput').val(coursesRepeat.join(','));
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
        
        // Get payment values from hidden inputs - only care about actualBalance
        var actualBalance = parseFloat($('#actualBalance').val().replace('K', '').replace(/,/g, ''));
        
        // Calculate separate fees for carry-over and current courses
        var carryOverFees = 0;
        var currentCourseFees = 0;
        var carryOverCourses = [];
        var currentCourses = [];
        
        // Store the courses in a variable and calculate fees
        var combinedCourses = [];
        $('input.courseRepeat:checked').each(function() {
            var courseCode = $(this).val();
            combinedCourses.push(courseCode);
            
            // Check if this is a carry-over course
            var courseRow = $(this).closest('tr');
            var courseType = courseRow.find('td:nth-child(5) span').text();
            
            if (courseType === 'Repeat') {
                carryOverCourses.push(courseCode);
            } else {
                currentCourses.push(courseCode);
            }
        });
        
        console.log("Registration Fee:", registrationFee);
        console.log("Total Fee:", totalFee);
        console.log("Actual Balance:", actualBalance);
        console.log("Selected courses:", combinedCourses);
        console.log("Carry-over courses:", carryOverCourses);
        console.log("Current courses:", currentCourses);

        // The eligibility check and modal handling has been moved to Blade template
        // We're keeping this code for reference but it's disabled now
        
        // Just collect the courses for potential manual processing
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
        
        // Still update the hidden input field with selected courses for JS-enabled forms
        $('#combinedCoursesInput').val(combinedCourses.join(','));
        $('#combinedCoursesFormInput').val(JSON.stringify(combinedCourses));
    });
});