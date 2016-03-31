
$(window).load(function() {

	$("#pageonloading").hide();


	//Form Submit ...
	$('form').submit(function(){

		var login = _qs('input[name=login]');
	    var password = _qs('input[name=password]');

	    //Gerando e encriptando os dados
	    var js = JSON.stringify({login:login.value, password:password.value, asskey:_passw.gen(40)})
	    _qs('input[name=key]').value = RSA.encrypt(js, RSA.getPublicKey(KEY));

	    login.remove();
	    password.remove();

	});


});