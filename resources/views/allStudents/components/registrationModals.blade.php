<!-- Modal for eligible registration -->
<div class="modal" id="eligibleModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">The following are the courses you have selected for reigtration</h5>
            </div>
            <div class="modal-body">
                <p>You are eligible to register. Do you want to proceed?</p>
            </div>
            <div class="modal-footer">
                <form method="POST" action="{{ auth()->user()->hasAnyRole(['Administrator', 'Developer']) ? route('sumbitRegistration.student') : route('student.submitCourseRegistration') }}">                   
                    @csrf
                    <input type="hidden" name="courses" id="coursesInput">
                    <input type="hidden" name="studentNumber" value="{{ $studentId }}">
                    <button type="submit" class="btn btn-primary">Yes</button>
                </form>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for ineligible registration -->
<div class="modal" id="ineligibleModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Not Eligible for Registration</h5>
            </div>
            <div class="modal-body">
                <p>You do not have enough for registration.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    
</script>