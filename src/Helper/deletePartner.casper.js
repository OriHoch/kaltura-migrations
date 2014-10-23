
function getParams(paramNames)
{
    var params = {};
    for (var i in paramNames) {
        var paramName = paramNames[i];
        if (casper.cli.has(paramName)) {
            params[paramName] = casper.cli.get(paramName);
        } else {
            throw 'param '+paramName+' must be provided';
        }
    }
    return params;
}

casper.options.waitTimeout = 5000;

var params = getParams(['serviceUrl', 'user', 'password', 'partnerId']);

casper.test.begin('set partner password', 1, function suite(test) {
    casper.start()
        .thenOpen('http://'+params.serviceUrl+'/admin_console/index.php/partner/list')
        .waitForSelector('form.login').then(function(){
            casper.fill('form.login', {'email':params.user, 'password':params.password});
            casper.evaluate(function(){
                $('#submit').click();
            });
        }).waitForSelector('#filter_type').then(function(){
            casper.evaluate(function(partnerId){
                window.KMIG_REMOVED_PARTNER = false;
                changeStatus( partnerId, 3, 'NULL', function(){window.KMIG_REMOVED_PARTNER=true} );
            }, params.partnerId);
        }).waitFor(function(){
            return casper.evaluate(function(){
                return window.KMIG_REMOVED_PARTNER;
            })
        }).then(function(){
            test.pass('OK!');
        }).run(function(){
            test.done();
        })
    ;
});
