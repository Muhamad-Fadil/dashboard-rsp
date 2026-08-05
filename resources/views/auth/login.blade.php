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
				<div class="d-flex flex-center bgi-size-cover bgi-no-repeat flex-row-fluid" style="background-image: url({{ asset('assets/media/bg/bg-1.jpg') }});">
					<div class="login-form text-center text-white p-7 position-relative overflow-hidden">
						<div class="d-flex flex-center mb-15">
							<a href="#">
								<img src="{{ asset('assets/media/logos/logo-letter-9.png') }}" class="max-h-100px" alt="" />
							</a>
						</div>
						<div class="login-signin">
							<div class="mb-20">
								<h3>Sign In To Admin</h3>
								<p class="opacity-60 font-weight-bold">Enter your details to login to your account:</p>
							</div>
							<form class="form" id="kt_login_signin_form" method="POST" action="{{ route('login') }}">
                                @csrf
								<div class="form-group">
									<input class="form-control h-auto text-white placeholder-white opacity-70 bg-dark-o-70 rounded-pill border-0 py-4 px-8 mb-5 @error('email') is-invalid @enderror" type="text" placeholder="Email" name="email" value="{{ old('email') }}" autocomplete="off" />
                                    @error('email')
                                        <div class="text-white font-weight-bold mt-2" style="background:rgba(0,0,0,.35); border-radius: 8px; padding: 6px 12px;">{{ $message }}</div>
                                    @enderror
								</div>
								<div class="form-group">
									<input class="form-control h-auto text-white placeholder-white opacity-70 bg-dark-o-70 rounded-pill border-0 py-4 px-8 mb-5" type="password" placeholder="Password" name="password" />
								</div>
								<div class="form-group d-flex flex-wrap justify-content-between align-items-center px-8">
									<div class="checkbox-inline">
										<label class="checkbox checkbox-outline checkbox-white text-white m-0">
										<input type="checkbox" name="remember" />
										<span></span>Remember me</label>
									</div>
									<a href="javascript:;" id="kt_login_forgot" class="text-white font-weight-bold">Forget Password ?</a>
								</div>
								<div class="form-group text-center mt-10">
									<button type="submit" id="kt_login_signin_submit" class="btn btn-pill btn-outline-white font-weight-bold opacity-90 px-15 py-3">Sign In</button>
								</div>
							</form>
							<div class="mt-10">
								<span class="opacity-70 mr-4">Don't have an account yet?</span>
								<a href="javascript:;" id="kt_login_signup" class="text-white font-weight-bold">Sign Up</a>
							</div>
						</div>
						<div class="login-signup">
							<div class="mb-20">
								<h3>Sign Up</h3>
								<p class="opacity-60">Enter your details to create your account</p>
							</div>
							<form class="form text-center" id="kt_login_signup_form">
								<div class="form-group">
									<input class="form-control h-auto text-white placeholder-white opacity-70 bg-dark-o-70 rounded-pill border-0 py-4 px-8" type="text" placeholder="Fullname" name="fullname" />
								</div>
								<div class="form-group">
									<input class="form-control h-auto text-white placeholder-white opacity-70 bg-dark-o-70 rounded-pill border-0 py-4 px-8" type="text" placeholder="Email" name="email" autocomplete="off" />
								</div>
								<div class="form-group">
									<input class="form-control h-auto text-white placeholder-white opacity-70 bg-dark-o-70 rounded-pill border-0 py-4 px-8" type="password" placeholder="Password" name="password" />
								</div>
								<div class="form-group">
									<input class="form-control h-auto text-white placeholder-white opacity-70 bg-dark-o-70 rounded-pill border-0 py-4 px-8" type="password" placeholder="Confirm Password" name="cpassword" />
								</div>
								<div class="form-group text-left px-8">
									<div class="checkbox-inline">
										<label class="checkbox checkbox-outline checkbox-white text-white m-0">
										<input type="checkbox" name="agree" />
										<span></span>I Agree the
										<a href="#" class="text-white font-weight-bold ml-1">terms and conditions</a>.</label>
									</div>
									<div class="form-text text-muted text-center"></div>
								</div>
								<div class="form-group">
									<button id="kt_login_signup_submit" class="btn btn-pill btn-outline-white font-weight-bold opacity-90 px-15 py-3 m-2">Sign Up</button>
									<button id="kt_login_signup_cancel" class="btn btn-pill btn-outline-white font-weight-bold opacity-70 px-15 py-3 m-2">Cancel</button>
								</div>
							</form>
						</div>
						<div class="login-forgot">
							<div class="mb-20">
								<h3>Forgotten Password ?</h3>
								<p class="opacity-60">Enter your email to reset your password</p>
							</div>
							<form class="form" id="kt_login_forgot_form">
								<div class="form-group mb-10">
									<input class="form-control h-auto text-white placeholder-white opacity-70 bg-dark-o-70 rounded-pill border-0 py-4 px-8" type="text" placeholder="Email" name="email" autocomplete="off" />
								</div>
								<div class="form-group">
									<button id="kt_login_forgot_submit" class="btn btn-pill btn-outline-white font-weight-bold opacity-90 px-15 py-3 m-2">Request</button>
									<button id="kt_login_forgot_cancel" class="btn btn-pill btn-outline-white font-weight-bold opacity-70 px-15 py-3 m-2">Cancel</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>var HOST_URL = "https://preview.keenthemes.com/metronic/theme/html/tools/preview";</script>
		<script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1200 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#6993FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#F3F6F9", "dark": "#212121" }, "light": { "white": "#ffffff", "primary": "#E1E9FF", "secondary": "#ECF0F3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#212121", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#ECF0F3", "gray-300": "#E5EAEE", "gray-400": "#D6D6E0", "gray-500": "#B5B5C3", "gray-600": "#80808F", "gray-700": "#464E5F", "gray-800": "#1B283F", "gray-900": "#212121" } }, "font-family": "Poppins" };</script>
		<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
		<script src="{{ asset('assets/plugins/custom/prismjs/prismjs.bundle.js') }}"></script>
		<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
	</body>
</html>
