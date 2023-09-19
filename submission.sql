CREATE procedure [dbo].[spGT_APIElogbookSubmissionsInsert]

@SubmissionIdT uniqueidentifier,
@MentorId int,
@HospitalId	int,
@AssignmentId	int,
@UpdatedBy	varchar(100),
@IsPublished bit
	
AS
declare @StudentId	int,@InstitutionId int, @SubmissionId bigint
select @StudentId=StudentId,@InstitutionId=InstitutionId from GT_Students where WebUsername=@UpdatedBy
if not exists (select * from GT_ElogbookSubmissions where StudentId=@StudentId and AssignmentId=@AssignmentId)
begin
insert into GT_ElogbookSubmissions

(
SubmissionIdT,
StudentId,
MentorId,
HospitalId,
AssignmentId,
CreatedBy,
CreatedOn,
UpdatedBy,
UpdatedOn,
InstitutionId,
Status,
IsPublished

)
values(
@SubmissionIdT,
@StudentId,
@MentorId,
@HospitalId,
@AssignmentId,
@UpdatedBy,
GETDATE(),
@UpdatedBy,
GETDATE(),
@InstitutionId,
'Pending',
@IsPublished

)

set @SubmissionId = SCOPE_IDENTITY()
end
else
begin
update GT_ElogbookSubmissions
set 

SubmissionIdT=@SubmissionIdT,
MentorId=@MentorId,
HospitalId=@HospitalId,
UpdatedBy=@UpdatedBy,
UpdatedOn=GETDATE(),
InstitutionId=@InstitutionId,
IsPublished=@IsPublished
where StudentId=@StudentId and AssignmentId=@AssignmentId

select @SubmissionId=SubmissionId from GT_ElogbookSubmissions where StudentId=@StudentId and AssignmentId=@AssignmentId
end
--delete all cases for submission
delete from GT_ElogbookCases where SubmissionId=@SubmissionId
delete from GT_ElogbookResponses where SubmissionId=@SubmissionId

select @SubmissionId
