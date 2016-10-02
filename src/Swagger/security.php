<?php
/**
 * @SWG\SecurityScheme(
 *   securityDefinition="api_auth",
 *   type="oauth2",
 *   authorizationUrl="https://isso.nypl.org/oauth/authorize",
 *   tokenUrl="https://isso.nypl.org/oauth/token",
 *   flow="accessCode",
 *   scopes={
 *     "openid api": "General API access",
 *     "openid api patron:read": "Patron specific API access",
 *     "openid api staff:read": "Staff specific API access"
 *   }
 * )
 */
