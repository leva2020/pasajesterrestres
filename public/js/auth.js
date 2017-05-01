function fbRedirect(code, client)
{
    window.location = window.location.protocol + '//' + window.location.hostname +'/social/facebook-login/' + client + '?code='+code;
}
function fbLogin(client)
{
    FB.login(function(response) {
         if (response.authResponse) {
             fbRedirect(response.authResponse.accessToken, client);
         }
       }, {scope:'email'});
}

function render(googleClientId) {
	gapi.signin.render('customBtn', {
	  'callback': 'signinGoogleCallback',
	  'clientid': googleClientId,
	  'redirecturi': 'postmessage',
	  'accesstype': 'offline',
	  'cookiepolicy': 'single_host_origin',
    'scope': 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email',
    // 'fetch_basic_profile': 'true',
	  // 'requestvisibleactions': 'http://schemas.google.com/AddActivity',
	});
}

function signinGoogleCallback (authResult) {

	if (authResult['code']) {
	    var code = authResult['code'];

		// Envía el código al servidor
		googleRedirect(code);

	} else if (authResult['error']) {
	// Se ha producido un error.
	// Posibles códigos de error:
	//   "access_denied": el usuario ha denegado el acceso a la aplicación.
	//   "immediate_failed": no se ha podido dar acceso al usuario de forma automática.
	// console.log('There was an error: ' + authResult['error']);
	}
}



  function startFlow(googleClientId){
    var options = {
    'callback' : signinGoogleCallback,
    'approvalprompt' : 'force',
    'clientid': googleClientId,
    'redirecturi': 'postmessage',
    'accesstype': 'offline',
    'cookiepolicy': 'single_host_origin',
    'requestvisibleactions' : 'http://schema.org/CommentAction http://schema.org/ReviewAction',
    'scope' : 'https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/plus.profile.emails.read',
  };
    gapi.auth.signIn(options);
  }