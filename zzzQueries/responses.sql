CREATE procedure [dbo].[spGT_ElogbookResponsesInsert]

@CaseId	bigint,
@QuestionId	int,
@ResponseText	varchar(MAX),
@UpdatedBy	varchar(100),
@InstitutionId	int,
@SubmissionId	bigint
	
AS

declare @StudentId	int
set @StudentId	 = (select top 1 StudentId from GT_Students where WebUsername=@UpdatedBy)

if @CaseId<=0
begin
set @CaseId = null
end


begin

if not exists (select * from GT_ElogbookResponses where QuestionId=@QuestionId  and SubmissionId=@SubmissionId and (@CaseId is null or CaseId=@CaseId))
begin
insert into GT_ElogbookResponses
(

CaseId,
QuestionId,
ResponseText,
CreatedBy,
CreatedOn,
UpdatedBy,
UpdatedOn,
StudentId,
InstitutionId,
SubmissionId

)
values
(
@CaseId,
@QuestionId,
@ResponseText,
@UpdatedBy,
GETDATE(),
@UpdatedBy,
GETDATE(),
@StudentId,
@InstitutionId,
@SubmissionId
)
end
else 
begin
update GT_ElogbookResponses
set 
ResponseText = @ResponseText,
UpdatedBy = @UpdatedBy,
UpdatedOn = GETDATE()
where QuestionId=@QuestionId  and SubmissionId=@SubmissionId and (@CaseId is null or CaseId=@CaseId)
end

end
