const spawn = require('child_process').spawnSync;
const parser = require('http-string-parser');
const querystring = require('querystring');

exports.handler = function(event, context) {
    var serverName = event.headers ? event.headers.Host : '';
    var requestMethod = event.httpMethod || 'GET';

    var headers = {
        SCRIPT_FILENAME: 'index.php',
        SCRIPT_NAME: '/index.php',
        PATH_INFO: '/',
        SERVER_PROTOCOL: 'HTTP/1.1',
        HTTPS: 'on',
        SERVER_PORT: '443',
        REDIRECT_STATUS: 200,
        GATEWAY_INTERFACE: 'CGI/1.1',
        SERVER_NAME: serverName,
        REQUEST_METHOD: requestMethod
    };

    var requestUri = event.path || '';
    var queryString = '';

    if (event.queryStringParameters) {
        queryString = querystring.stringify(event.queryStringParameters);

        headers['QUERY_STRING'] = queryString;
        requestUri += '?' + queryString;
    }

    headers['REQUEST_URI'] = requestUri;

    if (event.headers) {
        Object.keys(event.headers).map(function (key) {
            headers['HTTP_' + key.toUpperCase().replace(/-/g, '_')] = event.headers[key];
        });
    }

    headers['HTTP_X_NYPL_LOG_STREAM_NAME'] = context.logStreamName;
    headers['HTTP_X_NYPL_REQUEST_ID'] = context.awsRequestId;

    if (event.requestContext && event.requestContext.authorizer && event.requestContext.authorizer.principalId) {
        headers['HTTP_X_NYPL_IDENTITY'] = event.requestContext.authorizer.principalId;
    }

    if (event.body) {
        headers['CONTENT_LENGTH'] = Buffer.byteLength(event.body, 'utf8');

        if (event.headers['Content-Type']) {
            headers['CONTENT_TYPE'] = event.headers['Content-Type'];
        }
    }

    var options = {
        input: event.body,
        env: Object.assign(process.env, headers)
    };

    const php = spawn('./php-cgi', ['index.php'], options);

    if (php.stderr.length) {
        php.stderr.toString().split("\n").map(function (message) {
            if (message.trim().length) console.log(message);
        });
    }

    var parsedResponse = parser.parseResponse(php.stdout.toString());

    context.succeed({
        statusCode: parsedResponse.statusCode || 200,
        headers: parsedResponse.headers,
        body: parsedResponse.body
    });
};
