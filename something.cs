public async Task UploadSubmissions()
{
    IsBusy = true;
    try
    {
        bool accept = await Application.Current.MainPage.DisplayAlert("Confirm Action", "Are you sure you want to upload your comments for this assignment?", "Yes", "No");
        if (!accept)
        {
            return;
        }
        APISubmissionCommentData data = new APISubmissionCommentData();
        
        List<APIStaffSubmission> submissions = await DBService.GetStaffSubmissions(SelectedAssignment.AssignmentId);
        

        long[] submissionIds = (from s in submissions
                                select s.SubmissionId).Distinct().ToArray();
        var lists = Utility.SplitList(submissionIds.ToList(), 3);

        List<APIError> errorData = new List<APIError>();
        foreach (List<long> ids in lists)
        {
            List<APIStaffSubmission> dataSubmissions = new List<APIStaffSubmission>();
            List<APISubmissionComment> dataComments = new List<APISubmissionComment>();
            List<APIResponses> dataResponses = new List<APIResponses>();
            List<APIAssignmentQuestionResponse> dataGeneralResponses = new List<APIAssignmentQuestionResponse>();
            //get submissions
            dataSubmissions.AddRange(submissions.Where(s => ids.Contains(s.SubmissionId)));
            foreach (long id in ids)
            {

                //get comments for submissions
                dataComments.AddRange(await DBService.GetSubmissionComments(id));
                //get responses for submissions
                dataResponses.AddRange(await DBService.GetSubmissionResponses(id));
                //get general responses
                dataGeneralResponses.AddRange(await DBService.GetSubmissionGeneralResponses(id));
                foreach (APIAssignmentQuestionResponse r in dataGeneralResponses)
                {
                    dataResponses.Add(new APIResponses { CaseId = -1, QuestionId = r.QuestionId, ResponseText = r.ResponseText, SubmissionId = r.SubmissionId });
                }
            }
            //now send data for this list
            data.Submissions = dataSubmissions;
            data.Comments = dataComments;
            data.Responses = dataResponses;
            data.WebUsername = App.ApplicationUser.WebUsername;
            //now post
            List<APIError> messageList = await APIUserService.PostStaffSubmissionData(data);
            errorData.AddRange(messageList);

        }

        string[] infoMessages = (from m in errorData.Where(e => e.MessageType == "I")
                                    select m.ErrorMessage).ToArray();
        string[] errorMessages = (from m in errorData.Where(e => e.MessageType == "E")
                                    select m.ErrorMessage).ToArray();

        //await Utility.DisplayInfoMessage("Successfully uploaded submission comments:\n" + string.Join("\n", infoMessages));
        await Utility.DisplayInfoMessage("Successfully uploaded submission comments");

        if (errorMessages.Length > 0)
        {
            await Utility.DisplayErrorMessage(string.Join("\n", errorMessages));
        }


    }
    catch (Exception ex)
    {
        await Utility.DisplayErrorMessage(ex);
    }
    finally
    {
        IsBusy = false;
    }
}