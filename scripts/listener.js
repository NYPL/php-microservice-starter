const spawn = require('child_process').spawnSync;

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

function getPhp(event) {
    var options = {
        input: JSON.stringify(event),
        env: Object.assign(process.env, headers)
    };

    var headers = {
        LD_LIBRARY_PATH: process.env['LD_LIBRARY_PATH']
    };

    if (process.env.LAMBDA_TASK_ROOT) {
        return spawn(
            process.env.LAMBDA_TASK_ROOT + '/php',
            ['-n', '-d expose_php=Off', '-d opcache.file_cache=/tmp', '-d zend_extension=' + process.env.LAMBDA_TASK_ROOT + '/lib/opcache.so', 'listener.php'],
            options
        );
    }

    return spawn(
        'php',
        ['-d expose_php=Off', 'listener.php'],
        options
    );
}

exports.handler = function (event, context, callback) {
    var php = getPhp(event);

    if (php.error) {
        const message = 'Lambda was unable to execute PHP (' + php.error + ')';
        logMessage('CRITICAL', message);
        callback(message);
        return false;
    }

    if (php.stderr) {
        php.stderr.toString().split("\n").map(function (message) {
            message = message.trim();

            if (message) {
                console.log(message);
            }
        });
    }

    initializeResult(php.stdout.toString());

    if (listenerResult.success) {
        callback(null, listenerResult.message);
        return true;
    }

    logMessage('CRITICAL', listenerResult.message);
    callback(listenerResult.message);
    return false;
};
