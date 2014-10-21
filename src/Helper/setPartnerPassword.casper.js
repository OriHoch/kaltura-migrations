
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

var params = getParams(['adminConsoleUrl', 'adminUser', 'adminPassword', 'partnerId', 'partnerEmail', 'newPassword']);

casper.test.begin('set partner password', 1, function suite(test) {
    casper.start()
        .thenOpen(params.adminConsoleUrl+'/index.php/partner/list')
        .waitForSelector('form.login').then(function(){
            casper.fill('form.login', {'email':params.adminUser, 'password':params.adminPassword});
            casper.evaluate(function(){
                $('#submit').click();
            });
        }).waitForSelector('#filter_type').then(function(){
            casper.evaluate(function(partnerId, partnerEmail, newPassword, adminConsoleUrl){
                var url = adminConsoleUrl+'/index.php/partner/reset-user-password/partner_id/'+partnerId+'/user_id/'+encodeURIComponent(partnerEmail);
                $('body').append('<form enctype="application/x-www-form-urlencoded" id="kalturaMigrationsSetPartnerPassword" method="post" action="'+url+'"><input type="hidden" name="newPassword" value="'+newPassword+'"/></form>');
                document.getElementById('kalturaMigrationsSetPartnerPassword').submit();
            }, params.partnerId, params.partnerEmail, params.newPassword, params.adminConsoleUrl);
        }).waitForUrl(
            params.adminConsoleUrl+'/index.php/partner/reset-user-password/partner_id/'+params.partnerId+'/user_id/'+params.partnerEmail,
            function(){
                test.assertTextDoesntExist('error');
            }, function(){
                test.fail('failed to get expected url. current url: '+casper.getCurrentUrl());
            }
        ).run(function(){
            test.done();
        })
    ;
});
