
SELECT TOP (1000) [UserId]
      ,[app]
      ,[method]
      ,[username]
      ,[password]
      ,[PrimaryRoleName]
      ,[ip]
      ,[last_logon_time]
  FROM [Mikes_Application_Store].[dbo].[Users]

  
SELECT TOP (1000) [id]
      ,[UserId]
      ,[AttributeName]
      ,[AttributeValue]
  FROM [Mikes_Application_Store].[dbo].[UserAttributes]

SELECT TOP (1000) [RoleId]
      ,[name]
  FROM [Mikes_Application_Store].[dbo].[Roles]


SELECT TOP (1000) [id]
      ,[roleId]
      ,[model]
      ,[task]
      ,[field]
      ,[right]
  FROM [Mikes_Application_Store].[dbo].[RolePermissions]
