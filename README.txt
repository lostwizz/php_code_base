


REST =- https://www.codecademy.com/articles/what-is-rest
	https://medium.com/@sagar.mane006/understanding-rest-representational-state-transfer-85256b9424aa
	https://www.service-architecture.com/articles/web-services/representational_state_transfer_rest.html

SOAP Faults
	https://docs.oracle.com/cd/E23943_01/web.1111/e13734/faults.htm#WSADV627
	https://restfulapi.net/http-status-codes/


Use open
standards such as OpenID Connect and Open Authorization 2.0 (OAuth 2.0) for
RESTful APIs, and Security Assertion Markup Language 2.0 (SAML 2.0) for SOAP
APIs.

JSON Web Token (JWT) is required for RESTful API
interactions. WS-Security SAML Token Profile is required for SOAP APIs


Use consistent datetime format – International Organization for Standardization
8601 (ISO 8601), in Coordinated Universal Time (UTC), is the standard datetime
format for data and timestamp fields in APIs published across the GC and
externally. The date format is <yyyy-mm-dd> while timestamp format is <yyyy-mmdd>
T<hh:mm:ss>Z. Any other representation of time in the source system must be
converted to this format by the API.


Support official languages – All English or French content returned as data are
to be nested with BCP-47 language codes used as keys, specifically “en” and “fr”.
External facing APIs must reply with content in the requested language if the
backend data support it. APIs must interpret the ACCEPT-LANGUAGE HTTP
header and return the appropriate content. If the header is not set, then content



Version your APIs – Every API must be versioned. Every change to an API, no
matter how small, should be indicated by a new version. Follow the
v<Major>.<Minor>.<Patch> versioning structure whereby:
- Major = Significant release which is likely to break backwards
compatibility
- Minor = Addition of optional attributes or new functionality that is
backwards compatible, but should be tested
- Patch = Internal fix which should not impact the schema and/or contract
of the API
For example, going from v1.1.0 to v1.1.1 would allow a simple deploy-in-place
upgrade as it is a patch, while going from v1.1.0 to v2.0.0 would be a major
release and would require the legacy version to be kept while consumers test and
migrate to the new version.
The URL must reflect only the major version (e.g., v3). Versions must not passed
as a parameter or in the request header to force the API consumer to explicitly
identify the version and to avoid defaulting to an incompatible version. Minor and
patch versions do not need to be in the URL as they should not break backwards
compatibility, but they should be clearly identified in the contract, interface
documentation, and response message.
-




Use OpenAPI for RESTful – OpenAPI is a machine-readable interface
specification for RESTful APIs. There are open source tools (e.g., Swagger) which
can then generate human-readable documentation from this specification which
avoids the need to create and maintain separate documentation.
- Publish well-constructed WSDLs for SOAP – Each SOAP API must be
accompanied with a Web Services Description Language (WSDL) contract. The
WSDL is a machine-readable specification



Australian Government API Standardhttps://github.com/VictorianGovernment/api-designstandards/
blob/master/api-standards.md
Government of Canada API Standards:
https://www.canada.ca/en/government/publicservice/modernizing/government-canadastandards-
apis.html






-------------------------------------------------------
to run php server
P:
cd \Projects\MikesCommandAndControl2\src

php -S localhost:9999


-------------------------------------------------------
to run test suite:

cd P:\Projects\MikesCommandAndControl2\


	phpunit
   - or -
	phpunit --bootstrap vendor/autoload.php

-------------------------------------------------------


https://www.cloudways.com/blog/getting-started-with-unit-testing-php/


----------------
when copying to the PI - do this:
	sudo chmod 7777 /var/www/html/MikesCommandAndControl2/src/logs/*
