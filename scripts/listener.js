const spawn = require('child_process').spawnSync;

function getListenerResult(processed, success, message) {
    return {
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
    try {
        var parsedResult = JSON.parse(result);
    } catch (e) {
        return getListenerResult(true, false, {result: result, error: e});
    }

    if (!parsedResult.processed) {
        return getListenerResult(true, false, 'Processed key was not found in Listener result');
    }

    return parsedResult;
}

function getPhp(event) {
    var headers = {
        LD_LIBRARY_PATH: process.env['LD_LIBRARY_PATH']
    };

    var options = {
        input: JSON.stringify(event),
        env: Object.assign(process.env, headers)
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
        logMessage('ERROR', message);
        callback(message);
        return false;
    }

    if (php.stderr) {
        php.stderr.toString().split("\n").map(function (message) {
            if (message.trim()) {
                console.log(message);
            }
        });
    }

    var listenerResult = initializeResult(php.stdout.toString());

    if (listenerResult.success) {
        callback(null, listenerResult.message);
        return true;
    }

    logMessage('ERROR', listenerResult.message);
    callback(listenerResult.message);
    return false;
};
