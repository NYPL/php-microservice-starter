const spawn = require('child_process').spawn;

var listenerResult = {
    processed : false,
    success: null,
    message: ''
};

function setListenerResult(processed, success, message) {
    listenerResult = {
        processed : processed,
        success: success,
        message: message
    }
}

function logMessage(level, message) {
    console.log({
        level: level,
        message: message
    })
}

function initializeResult(result) {
    if (listenerResult.processed) {
        logMessage('NOTICE', 'Listener result was already set.');
        return false;
    }

    try {
        var parsedResult = JSON.parse(result);
    } catch (e) {
        setListenerResult(true, false, result);
        return false;
    }

    if (!parsedResult.processed) {
        logMessage('NOTICE', 'Processed key was not found in Listener result.');
    }

    listenerResult = parsedResult;
}

function getPhp() {
    if (process.env.LAMBDA_TASK_ROOT) {
        var headers = {
            LD_LIBRARY_PATH: process.env['LD_LIBRARY_PATH']
        };

        var options = {
            env: Object.assign(process.env, headers)
        };

        return spawn('./php', ['-n', '-d expose_php=Off', 'listener.php'], options);
    }

    return spawn('php', ['-d expose_php=Off', 'listener.php'], options);
}

exports.handler = function (event, context, callback) {
    var php = getPhp();

    php.stdin.setEncoding = 'utf-8';
    php.stdin.write(JSON.stringify(event));
    php.stdin.end();

    php.on('error', function (code) {
        const message = 'Lambda was unable to execute PHP (' + code + ')';
        logMessage('CRITICAL', message);
        callback(message);
        return false;
    });

    php.stdout.on('data', function (data) {
        initializeResult(data.toString());
    });

    php.stderr.on('data', function (data) {
        console.log(data.toString());
    });

    php.on('exit', function (code) {
        if (listenerResult.success) {
            callback(null, listenerResult.message);
            return true;
        }

        logMessage('CRITICAL', listenerResult.message);
        callback(listenerResult.message);
        return false;
    });
};
