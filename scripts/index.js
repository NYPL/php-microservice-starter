const spawn = require('child_process').spawnSync;
const parser = require('./parser');
const querystring = require('querystring');

exports.handler = function(event, context, callback) {
    if (!event.requestContext) {
        console.log('No request context in event.');
        return false;
    }

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

        if (event.headers['Content-type']) {
            headers['CONTENT_TYPE'] = event.headers['Content-type'];
        }
    }

    var options = {
        input: event.body,
        env: Object.assign(process.env, headers)
    };

    if (process.env.LAMBDA_TASK_ROOT) {
        var php = spawn(
            process.env.LAMBDA_TASK_ROOT + '/php-cgi',
            [
                '-n',
                '-d expose_php=Off',
                '-d zend_extension=' + process.env.LAMBDA_TASK_ROOT + '/lib/opcache.so',
                '-d opcache.file_cache=/tmp',
                'index.php'
            ],
            options
        );
    } else {
        var php = spawn('php-cgi', ['-d expose_php=Off', 'index.php'], options);
    }

    if (php.error) {
        callback(php.error);
        return false;
    }

    if (php.stderr) {
        php.stderr.toString().split("\n").map(function (message) {
            if (message.trim().length) console.log(message);
        });
    }

    var parsedResponse = parser.parseResponse(php.stdout.toString());

    callback(null, {
        statusCode: parsedResponse.statusCode || 200,
        headers: parsedResponse.headers,
        body: parsedResponse.body
    });
};
