USE [Mikes_Application_Store]
GO
/****** Object:  Table [dbo].[Application_Data_Log]    Script Date: 30-Dec-2019 2:42:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Application_Data_Log](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[timestamp] [datetime] NULL,
	[channel] [varchar](50) NULL,
	[machine_id] [varchar](50) NULL,
	[App] [varchar](50) NULL,
	[Level] [varchar](20) NULL,
	[operation] [varchar](max) NULL,
	[message] [varchar](max) NULL,
 CONSTRAINT [PK_Application_Data_Log] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, FILLFACTOR = 100) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Application_Log]    Script Date: 30-Dec-2019 2:42:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Application_Log](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[timestamp] [datetime] NULL,
	[channel] [varchar](50) NULL,
	[machine_id] [varchar](50) NULL,
	[App] [varchar](50) NULL,
	[Level] [varchar](20) NULL,
	[operation] [varchar](max) NULL,
	[caller] [varchar](max) NULL,
	[caller_of_caller] [varchar](max) NULL,
	[threadNum] [int] NULL,
	[Tracer] [varchar](50) NULL,
	[message] [varchar](max) NULL,
 CONSTRAINT [PK_Application_Log] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, FILLFACTOR = 100) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[MenuDefinitions]    Script Date: 30-Dec-2019 2:42:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[MenuDefinitions](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[app] [varchar](50) NULL,
	[item_number] [varchar](50) NULL,
	[Name] [varchar](50) NULL,
	[Role_Name_Required] [varchar](50) NULL,
	[Process_Permission_Required] [varchar](50) NULL,
	[Task_Permission_Required] [varchar](50) NULL,
	[Action_Permission_Required] [varchar](50) NULL,
	[Field_Permmission_Required] [varchar](50) NULL,
	[Permission_Required] [varchar](10) NULL,
 CONSTRAINT [PK_MenuDefinitions] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, FILLFACTOR = 100) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[RolePermissions]    Script Date: 30-Dec-2019 2:42:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[RolePermissions](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[roleId] [int] NOT NULL,
	[process] [varchar](50) NOT NULL,
	[task] [varchar](50) NOT NULL,
	[action] [varchar](50) NULL,
	[field] [varchar](50) NOT NULL,
	[Permission] [varchar](10) NOT NULL,
 CONSTRAINT [PK_PermissionManagement] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, FILLFACTOR = 100) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Roles]    Script Date: 30-Dec-2019 2:42:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Roles](
	[RoleId] [int] IDENTITY(1,1) NOT NULL,
	[name] [nvarchar](50) NOT NULL,
 CONSTRAINT [PK_RoleManagement] PRIMARY KEY CLUSTERED 
(
	[RoleId] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, FILLFACTOR = 100) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Settings]    Script Date: 30-Dec-2019 2:42:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Settings](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[App] [varchar](50) NOT NULL,
	[SettingName] [varchar](50) NULL,
	[SettingValue] [varchar](max) NULL,
	[SettingTypeHint] [varchar](50) NULL,
	[Category] [varchar](50) NULL,
	[TimeStamp] [datetime] NULL,
	[is_active] [int] NULL,
 CONSTRAINT [PK_Settings] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, FILLFACTOR = 100) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[TestApp_Security_log]    Script Date: 30-Dec-2019 2:42:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[TestApp_Security_log](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[timestamp] [datetime] NULL,
	[channel] [varchar](50) NULL,
	[machine_id] [varchar](50) NULL,
	[App] [varchar](50) NULL,
	[Level] [varchar](20) NULL,
	[operation] [varchar](max) NULL,
	[message] [varchar](max) NULL,
 CONSTRAINT [PK_TestApp_Security_Log] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, FILLFACTOR = 100) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[UserAttributes]    Script Date: 30-Dec-2019 2:42:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[UserAttributes](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[UserId] [int] NOT NULL,
	[AttributeName] [nvarchar](50) NULL,
	[AttributeValue] [varchar](max) NULL,
 CONSTRAINT [PK_UserAttributeManagment] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, FILLFACTOR = 100) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[Users]    Script Date: 30-Dec-2019 2:42:49 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Users](
	[UserId] [int] IDENTITY(1,1) NOT NULL,
	[app] [varchar](50) NOT NULL,
	[method] [varchar](10) NOT NULL,
	[username] [varchar](20) NOT NULL,
	[password] [varchar](255) NULL,
	[PrimaryRoleName] [varchar](50) NULL,
	[ip] [varchar](40) NULL,
	[last_logon_time] [datetime2](0) NULL,
 CONSTRAINT [PK_UserManagement] PRIMARY KEY CLUSTERED 
(
	[UserId] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, FILLFACTOR = 100) ON [PRIMARY]
) ON [PRIMARY]
GO
SET IDENTITY_INSERT [dbo].[MenuDefinitions] ON 

INSERT [dbo].[MenuDefinitions] ([id], [app], [item_number], [Name], [Role_Name_Required], [Process_Permission_Required], [Task_Permission_Required], [Action_Permission_Required], [Field_Permmission_Required], [Permission_Required]) VALUES (1, N'TestApp', N'1', N'Home', N'StandardUser', NULL, NULL, NULL, NULL, NULL)
INSERT [dbo].[MenuDefinitions] ([id], [app], [item_number], [Name], [Role_Name_Required], [Process_Permission_Required], [Task_Permission_Required], [Action_Permission_Required], [Field_Permmission_Required], [Permission_Required]) VALUES (2, N'TestApp', N'1.1', N'Home1.1', N'StandardUser', NULL, NULL, NULL, NULL, NULL)
INSERT [dbo].[MenuDefinitions] ([id], [app], [item_number], [Name], [Role_Name_Required], [Process_Permission_Required], [Task_Permission_Required], [Action_Permission_Required], [Field_Permmission_Required], [Permission_Required]) VALUES (3, N'TestApp', N'1.2', N'Home1.2', N'StandardUser', NULL, NULL, NULL, NULL, NULL)
INSERT [dbo].[MenuDefinitions] ([id], [app], [item_number], [Name], [Role_Name_Required], [Process_Permission_Required], [Task_Permission_Required], [Action_Permission_Required], [Field_Permmission_Required], [Permission_Required]) VALUES (4, N'TestApp', N'1.3', N'Home1.3', N'StandardUser', NULL, NULL, NULL, NULL, NULL)
INSERT [dbo].[MenuDefinitions] ([id], [app], [item_number], [Name], [Role_Name_Required], [Process_Permission_Required], [Task_Permission_Required], [Action_Permission_Required], [Field_Permmission_Required], [Permission_Required]) VALUES (5, N'TestApp', N'2', N'Program2', N'StandardUser', NULL, NULL, NULL, NULL, NULL)
INSERT [dbo].[MenuDefinitions] ([id], [app], [item_number], [Name], [Role_Name_Required], [Process_Permission_Required], [Task_Permission_Required], [Action_Permission_Required], [Field_Permmission_Required], [Permission_Required]) VALUES (6, N'TestApp', N'2.1', N'Program2.1', N'StandardUser', NULL, NULL, NULL, NULL, NULL)
INSERT [dbo].[MenuDefinitions] ([id], [app], [item_number], [Name], [Role_Name_Required], [Process_Permission_Required], [Task_Permission_Required], [Action_Permission_Required], [Field_Permmission_Required], [Permission_Required]) VALUES (7, N'TestApp', N'2.2', N'Program2.2', N'StandardUser', NULL, NULL, NULL, NULL, NULL)
INSERT [dbo].[MenuDefinitions] ([id], [app], [item_number], [Name], [Role_Name_Required], [Process_Permission_Required], [Task_Permission_Required], [Action_Permission_Required], [Field_Permmission_Required], [Permission_Required]) VALUES (8, N'TestApp', N'2.3', N'Program2.3', N'StandardUser', NULL, NULL, NULL, NULL, NULL)
INSERT [dbo].[MenuDefinitions] ([id], [app], [item_number], [Name], [Role_Name_Required], [Process_Permission_Required], [Task_Permission_Required], [Action_Permission_Required], [Field_Permmission_Required], [Permission_Required]) VALUES (9, N'TestApp', N'2.4', N'Program2.4', N'StandardUser', NULL, NULL, NULL, NULL, NULL)
INSERT [dbo].[MenuDefinitions] ([id], [app], [item_number], [Name], [Role_Name_Required], [Process_Permission_Required], [Task_Permission_Required], [Action_Permission_Required], [Field_Permmission_Required], [Permission_Required]) VALUES (10, N'TestApp', N'3', N'DBA', N'DBA', NULL, NULL, NULL, NULL, NULL)
INSERT [dbo].[MenuDefinitions] ([id], [app], [item_number], [Name], [Role_Name_Required], [Process_Permission_Required], [Task_Permission_Required], [Action_Permission_Required], [Field_Permmission_Required], [Permission_Required]) VALUES (11, N'TestApp', N'3.1', N'DBA3.1', N'DBA', NULL, NULL, NULL, NULL, NULL)
INSERT [dbo].[MenuDefinitions] ([id], [app], [item_number], [Name], [Role_Name_Required], [Process_Permission_Required], [Task_Permission_Required], [Action_Permission_Required], [Field_Permmission_Required], [Permission_Required]) VALUES (12, N'TestApp', N'3.2', N'DBA3.2', N'DBA', NULL, NULL, NULL, NULL, NULL)
INSERT [dbo].[MenuDefinitions] ([id], [app], [item_number], [Name], [Role_Name_Required], [Process_Permission_Required], [Task_Permission_Required], [Action_Permission_Required], [Field_Permmission_Required], [Permission_Required]) VALUES (13, N'TestApp', N'3.4', N'DBA3.4', N'DBA', NULL, NULL, NULL, NULL, NULL)
INSERT [dbo].[MenuDefinitions] ([id], [app], [item_number], [Name], [Role_Name_Required], [Process_Permission_Required], [Task_Permission_Required], [Action_Permission_Required], [Field_Permmission_Required], [Permission_Required]) VALUES (14, N'TestApp', N'3.3', N'DBA3.3', N'DBA', NULL, NULL, NULL, NULL, NULL)
INSERT [dbo].[MenuDefinitions] ([id], [app], [item_number], [Name], [Role_Name_Required], [Process_Permission_Required], [Task_Permission_Required], [Action_Permission_Required], [Field_Permmission_Required], [Permission_Required]) VALUES (15, N'TestApp', N'3.2.1', N'DBA3.2.1', N'DBA', NULL, NULL, NULL, NULL, NULL)
INSERT [dbo].[MenuDefinitions] ([id], [app], [item_number], [Name], [Role_Name_Required], [Process_Permission_Required], [Task_Permission_Required], [Action_Permission_Required], [Field_Permmission_Required], [Permission_Required]) VALUES (16, N'TestApp', N'3.2.2', N'DBA3.2.2', N'DBA', NULL, NULL, NULL, NULL, NULL)
SET IDENTITY_INSERT [dbo].[MenuDefinitions] OFF
SET IDENTITY_INSERT [dbo].[RolePermissions] ON 

INSERT [dbo].[RolePermissions] ([id], [roleId], [process], [task], [action], [field], [Permission]) VALUES (1, 2, N'Change_Password', N'Read_Old_Password', N'*', N'Password', N'Write')
INSERT [dbo].[RolePermissions] ([id], [roleId], [process], [task], [action], [field], [Permission]) VALUES (2, 2, N'Add_Something', N'doSomething', N'*', N'SomeField', N'Write')
INSERT [dbo].[RolePermissions] ([id], [roleId], [process], [task], [action], [field], [Permission]) VALUES (3, 4, N'FREDDBA', N'*', N'*', N'*', N'DBA')
INSERT [dbo].[RolePermissions] ([id], [roleId], [process], [task], [action], [field], [Permission]) VALUES (4, 2, N'Read_Something', N'*', N'*', N'*', N'Read')
INSERT [dbo].[RolePermissions] ([id], [roleId], [process], [task], [action], [field], [Permission]) VALUES (5, 2, N'test', N'doWork', N'*', N'*', N'Write')
INSERT [dbo].[RolePermissions] ([id], [roleId], [process], [task], [action], [field], [Permission]) VALUES (6, 5, N'*', N'*', N'*', N'*', N'GOD')
INSERT [dbo].[RolePermissions] ([id], [roleId], [process], [task], [action], [field], [Permission]) VALUES (7, 6, N'*', N'*', N'*', N'*', N'Read')
INSERT [dbo].[RolePermissions] ([id], [roleId], [process], [task], [action], [field], [Permission]) VALUES (8, 3, N'*', N'*', N'*', N'*', N'Read')
INSERT [dbo].[RolePermissions] ([id], [roleId], [process], [task], [action], [field], [Permission]) VALUES (9, 1, N'*', N'*', N'*', N'*', N'Read')
INSERT [dbo].[RolePermissions] ([id], [roleId], [process], [task], [action], [field], [Permission]) VALUES (10, 1, N'StandardWrite', N'*', N'*', N'*', N'Write')
SET IDENTITY_INSERT [dbo].[RolePermissions] OFF
SET IDENTITY_INSERT [dbo].[Roles] ON 

INSERT [dbo].[Roles] ([RoleId], [name]) VALUES (2, N'Clerk')
INSERT [dbo].[Roles] ([RoleId], [name]) VALUES (4, N'DBA')
INSERT [dbo].[Roles] ([RoleId], [name]) VALUES (5, N'GOD')
INSERT [dbo].[Roles] ([RoleId], [name]) VALUES (6, N'guest')
INSERT [dbo].[Roles] ([RoleId], [name]) VALUES (1, N'StandardUser')
INSERT [dbo].[Roles] ([RoleId], [name]) VALUES (3, N'ViewOnly')
SET IDENTITY_INSERT [dbo].[Roles] OFF
SET IDENTITY_INSERT [dbo].[Settings] ON 

INSERT [dbo].[Settings] ([id], [App], [SettingName], [SettingValue], [SettingTypeHint], [Category], [TimeStamp], [is_active]) VALUES (1, N'TestApp', N'Fred was here', N'a:2:{s:4:"fred";s:6:"johnny";s:3:"bob";s:5:"house";}', N'array', N'Public', CAST(N'2019-11-04T12:57:58.797' AS DateTime), 1)
INSERT [dbo].[Settings] ([id], [App], [SettingName], [SettingValue], [SettingTypeHint], [Category], [TimeStamp], [is_active]) VALUES (2, N'TestApp', N'Tony has left the building', N'Sam stayed', N'string', N'Protected', CAST(N'2019-11-04T12:58:08.610' AS DateTime), 1)
INSERT [dbo].[Settings] ([id], [App], [SettingName], [SettingValue], [SettingTypeHint], [Category], [TimeStamp], [is_active]) VALUES (3, N'TestApp', N'BOBBY', N'-True-', N'bool', N'Public', CAST(N'2019-11-04T13:33:47.617' AS DateTime), 1)
SET IDENTITY_INSERT [dbo].[Settings] OFF
SET IDENTITY_INSERT [dbo].[UserAttributes] ON 

INSERT [dbo].[UserAttributes] ([id], [UserId], [AttributeName], [AttributeValue]) VALUES (1, 1, N'GivenName', N'Mike')
INSERT [dbo].[UserAttributes] ([id], [UserId], [AttributeName], [AttributeValue]) VALUES (3, 1, N'eMailAddress', N'mike.merrett@whitehorse.ca')
INSERT [dbo].[UserAttributes] ([id], [UserId], [AttributeName], [AttributeValue]) VALUES (4, 1, N'PrimaryRole', N'DBA')
INSERT [dbo].[UserAttributes] ([id], [UserId], [AttributeName], [AttributeValue]) VALUES (5, 1, N'SecondaryRole', N'Clerk')
INSERT [dbo].[UserAttributes] ([id], [UserId], [AttributeName], [AttributeValue]) VALUES (6, 1, N'SecondaryRole', N'ViewOnly')
INSERT [dbo].[UserAttributes] ([id], [UserId], [AttributeName], [AttributeValue]) VALUES (7, 1, N'SNAILMail', N'7 here and there')
INSERT [dbo].[UserAttributes] ([id], [UserId], [AttributeName], [AttributeValue]) VALUES (8, 1, N'PhoneNum', N'334-2104')
INSERT [dbo].[UserAttributes] ([id], [UserId], [AttributeName], [AttributeValue]) VALUES (9, 1, N'Title', N'GIS / Database Specialist')
INSERT [dbo].[UserAttributes] ([id], [UserId], [AttributeName], [AttributeValue]) VALUES (10, 1, N'Department', N'Business and Technology Systems')
INSERT [dbo].[UserAttributes] ([id], [UserId], [AttributeName], [AttributeValue]) VALUES (11, 1, N'CellNum', N'334-2104')
INSERT [dbo].[UserAttributes] ([id], [UserId], [AttributeName], [AttributeValue]) VALUES (12, 15, N'eMailAddress', N'mike.merrett@whitehorse.ca')
INSERT [dbo].[UserAttributes] ([id], [UserId], [AttributeName], [AttributeValue]) VALUES (13, 15, N'GivenName', N'adminmerrem')
INSERT [dbo].[UserAttributes] ([id], [UserId], [AttributeName], [AttributeValue]) VALUES (14, 15, N'Surname', N'Merrett')
INSERT [dbo].[UserAttributes] ([id], [UserId], [AttributeName], [AttributeValue]) VALUES (15, 15, N'PhoneNum', N'334-2104')
INSERT [dbo].[UserAttributes] ([id], [UserId], [AttributeName], [AttributeValue]) VALUES (18, 1, N'Surname', N'Merrett')
INSERT [dbo].[UserAttributes] ([id], [UserId], [AttributeName], [AttributeValue]) VALUES (19, 15, N'Title', N'')
INSERT [dbo].[UserAttributes] ([id], [UserId], [AttributeName], [AttributeValue]) VALUES (20, 15, N'Department', N'')
INSERT [dbo].[UserAttributes] ([id], [UserId], [AttributeName], [AttributeValue]) VALUES (21, 15, N'CellNum', N'')
SET IDENTITY_INSERT [dbo].[UserAttributes] OFF
SET IDENTITY_INSERT [dbo].[Users] ON 

INSERT [dbo].[Users] ([UserId], [app], [method], [username], [password], [PrimaryRoleName], [ip], [last_logon_time]) VALUES (1, N'TestApp', N'LDAP', N'merrem', N'$2y$10$vQxh2e4/LZ3X5PhDM3bvv.K21H0hUzJ8BalBugR74aVUeF4SRN6fS', N'DBA', N'::1', CAST(N'2019-12-30T01:40:06.0000000' AS DateTime2))
INSERT [dbo].[Users] ([UserId], [app], [method], [username], [password], [PrimaryRoleName], [ip], [last_logon_time]) VALUES (2, N'TestApp', N'DB_Table', N'merremtest', NULL, N'Clerk', NULL, NULL)
INSERT [dbo].[Users] ([UserId], [app], [method], [username], [password], [PrimaryRoleName], [ip], [last_logon_time]) VALUES (4, N'TestApp', N'DB_Table', N'sam', N'$2y$10$UlROVQ78GDZxL8rR7IhXRuK.oLsBRIzoBPowuVk4YYa3MZpr8jnxW', N'ViewOnly', NULL, NULL)
INSERT [dbo].[Users] ([UserId], [app], [method], [username], [password], [PrimaryRoleName], [ip], [last_logon_time]) VALUES (5, N'TestApp', N'DB_Table', N'fred', NULL, N'guest', NULL, NULL)
INSERT [dbo].[Users] ([UserId], [app], [method], [username], [password], [PrimaryRoleName], [ip], [last_logon_time]) VALUES (6, N'TestApp', N'LDAP', N'bob', NULL, N'StandardUser', NULL, NULL)
INSERT [dbo].[Users] ([UserId], [app], [method], [username], [password], [PrimaryRoleName], [ip], [last_logon_time]) VALUES (9, N'TestApp', N'DB_Table', N'tony', N't', NULL, NULL, NULL)
INSERT [dbo].[Users] ([UserId], [app], [method], [username], [password], [PrimaryRoleName], [ip], [last_logon_time]) VALUES (10, N'TestApp', N'DB_Table', N'm', N'$2y$10$zofEoreltPwAUzVTjQLWFeRVmUpfrqUXfMmCB1qW3Sw.Xl1bAxr26', N'StandardUser', N'::1', CAST(N'2019-12-30T12:49:35.0000000' AS DateTime2))
INSERT [dbo].[Users] ([UserId], [app], [method], [username], [password], [PrimaryRoleName], [ip], [last_logon_time]) VALUES (11, N'TestApp', N'DB_Table', N'gord', N'$2y$10$UhmCcTi5mGbAUgxWx1wFKu6MIUvvgx5YR04.Lykt41PZbSjU8XoHi', NULL, NULL, NULL)
INSERT [dbo].[Users] ([UserId], [app], [method], [username], [password], [PrimaryRoleName], [ip], [last_logon_time]) VALUES (12, N'TestApp', N'DB_Table', N'gord2', N'$2y$10$wB.ocPFUS4WcgxEl1HLu1eBs3wvx9lrx5Juz73TzdT.F5jdh0p.yW', NULL, NULL, NULL)
INSERT [dbo].[Users] ([UserId], [app], [method], [username], [password], [PrimaryRoleName], [ip], [last_logon_time]) VALUES (13, N'TestApp2', N'DB_Table', N'merrem', N'$2y$10$vQxh2e4/LZ3X5PhDM3bvv.K21H0hUzJ8BalBugR74aVUeF4SRN6fS', N'DBA', NULL, NULL)
INSERT [dbo].[Users] ([UserId], [app], [method], [username], [password], [PrimaryRoleName], [ip], [last_logon_time]) VALUES (14, N'TestApp', N'DB_Table', N'georgey', N'$2y$10$l8NfbSJhDwjTlcvkB6WsFeuBPBxqnmlYi7sK4G9k1FVOfqufkd/K6', NULL, NULL, NULL)
INSERT [dbo].[Users] ([UserId], [app], [method], [username], [password], [PrimaryRoleName], [ip], [last_logon_time]) VALUES (15, N'TestApp', N'LDAP', N'adminmerrem', NULL, N'DBA', N'::1', CAST(N'2019-11-29T01:55:32.0000000' AS DateTime2))
INSERT [dbo].[Users] ([UserId], [app], [method], [username], [password], [PrimaryRoleName], [ip], [last_logon_time]) VALUES (16, N'TestApp', N'HARDCoded', N'harry', N'$2y$10$8HoPqNBlIvc1JR140QmnI.OCCwsXPxlP8M6.eFS8taIaMwdhgmtP.', NULL, N'::1', CAST(N'2019-11-29T04:05:49.0000000' AS DateTime2))
SET IDENTITY_INSERT [dbo].[Users] OFF
ALTER TABLE [dbo].[Settings] ADD  CONSTRAINT [DF_Table_1_timestamp]  DEFAULT (getdate()) FOR [TimeStamp]
GO
ALTER TABLE [dbo].[Users] ADD  CONSTRAINT [DF_Users_PrimaryRoleName]  DEFAULT ('ViewOnly') FOR [PrimaryRoleName]
GO
