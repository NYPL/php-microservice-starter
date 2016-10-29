<?php
namespace NYPL\Service;

class Config
{
    const MESSAGE_BROKER = "";

    const DB_CONNECT_STRING = "pgsql:host=apiservice.cicyc5fazypj.us-east-1.rds.amazonaws.com;dbname=nypl";
    const DB_USERNAME = "";
    const DB_PASSWORD = "";

    const IDENTITY_HEADER = 'X-NYPL-Identity';

    const OAUTH_REDIRECT_URI = "oauth";
    const OAUTH_CLIENT_ID = "";
    const OAUTH_CLIENT_SECRET = "";
    const OAUTH_AUTHORIZE_URI = "https://nypl-sierra-test.iii.com/iii/sierra-api/authorize";
    const OAUTH_TOKEN_URI = "https://nypl-sierra-test.iii.com/iii/sierra-api/token";
    const OAUTH_USER_INFO_URI = null;
    const OAUTH_TIME_ZONE = "America/Los_Angeles";

    const BASE_SIERRA_API_URL = 'https://nypl-sierra-test.iii.com/iii/sierra-api/v3';

    const SLACK_TOKEN = "";
    const SLACK_CHANNEL = "service-logging";
    const SLACK_USERNAME = "general-services";
}
