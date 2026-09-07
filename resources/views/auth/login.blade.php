<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Login Page 3 | Keenthemes</title>
		<meta name="description" content="Login page example" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<link rel="canonical" href="https://keenthemes.com/metronic" />
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<link href="{{ asset('assets/css/pages/login/classic/login-3.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/plugins/custom/prismjs/prismjs.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
	</head>
	<body id="kt_body" style="background-image: url({{ asset('assets/media/bg/bg-10.jpg') }})" class="quick-panel-right demo-panel-right offcanvas-right header-fixed subheader-enabled page-loading">
		<div class="d-flex flex-column flex-root">
			<div class="login login-3 login-signin-on d-flex flex-row-fluid" id="kt_login">
				<div class="d-flex flex-center bgi-size-cover bgi-no-repeat flex-row-fluid" style="background-image: url({{ asset('assets/media/bg/bg-13.png') }});">
					<div class="login-form text-center text-white p-7 position-relative overflow-hidden">
						<div class="d-flex flex-center mb-10">
							<a href="#">
								<img src="{{ asset('assets/media/logos/log-rsp-1.png') }}" class="max-h-85px" alt="" />
							</a>
						</div>
						<div class="login-signin">
							<div class="mb-15" style="color: #006a51;">
								<h3 class="font-weight-bolder">Sign In To Dashboard</h3>
								<p class="opacity-80 font-weight-bold">Enter your details to login to your account:</p>
							</div>
							<form class="form" id="kt_login_signin_form" method="POST" action="{{ route('login') }}">
                                @csrf
								<div class="form-group">
									<input class="form-control h-auto text-white placeholder-white bg-dark-o-70 rounded-pill border-0 py-4 px-8 mb-5 @error('email') is-invalid @enderror" type="text" placeholder="Email" name="email" value="{{ old('email') }}" autocomplete="off" />
                                    @error('email')
                                        <div class="text-white font-weight-bold mt-2" style="background:rgba(0,0,0,.35); border-radius: 8px; padding: 6px 12px;">{{ $message }}</div>
                                    @enderror
								</div>
								<div class="form-group">
									<input class="form-control h-auto text-white placeholder-white bg-dark-o-70 rounded-pill border-0 py-4 px-8 mb-5" type="password" placeholder="Password" name="password" />
								</div>
								<div class="form-group text-center">
									<div class="g-recaptcha d-inline-block" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
									@error('g-recaptcha-response')
										<div class="text-white font-weight-bold mt-2" style="background:rgba(0,0,0,.35); border-radius: 8px; padding: 6px 12px;">{{ $message }}</div>
									@enderror
								</div>
								<div class="form-group text-center mt-10">
									<button type="submit" id="kt_login_signin_submit" class=" btn btn-success btn-shadow font-weight-bold opacity-90 px-15 py-3">Sign In</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script src="https://www.google.com/recaptcha/api.js" async defer></script>
		<script>var HOST_URL = "https://preview.keenthemes.com/metronic/theme/html/tools/preview";</script>
		<script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1200 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#6993FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#F3F6F9", "dark": "#212121" }, "light": { "white": "#ffffff", "primary": "#E1E9FF", "secondary": "#ECF0F3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#212121", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#ECF0F3", "gray-300": "#E5EAEE", "gray-400": "#D6D6E0", "gray-500": "#B5B5C3", "gray-600": "#80808F", "gray-700": "#464E5F", "gray-800": "#1B283F", "gray-900": "#212121" } }, "font-family": "Poppins" };</script>
		<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
		<script src="{{ asset('assets/plugins/custom/prismjs/prismjs.bundle.js') }}"></script>
		<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
	</body>
</html>