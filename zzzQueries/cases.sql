CREATE procedure [dbo].[spGT_ElogbookCasesInsert]
@Patient	varchar(50),
@SubmissionId	bigint,
@InstitutionId	int,
@UpdatedBy	varchar(100)
	
AS
declare @StudentId	int
set @StudentId	 = (Select top 1 StudentId from GT_Students where WebUsername=@UpdatedBy)

if not exists (select * from GT_ElogbookCases where Patient=@Patient and SubmissionId=@SubmissionId)
begin
insert into GT_ElogbookCases
(

Patient,
SubmissionId,
InstitutionId,
StudentId,
CreatedBy,
CreatedOn,
UpdatedBy,
UpdatedOn

)
values
(

@Patient,
@SubmissionId,
@InstitutionId,
@StudentId,
@UpdatedBy,
GETDATE(),
@UpdatedBy,
GETDATE()

)

select SCOPE_IDENTITY() as CaseId
end
else
begin
update GT_ElogbookCases
set UpdatedBy = @UpdatedBy,
UpdatedOn = GETDATE()
where Patient=@Patient and SubmissionId=@SubmissionId
Select top 1 CaseId from  GT_ElogbookCases where Patient=@Patient and SubmissionId=@SubmissionId
end
