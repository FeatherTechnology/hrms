<?php
//this screen is only for login page*****

//below code is for redirecting user to dashboard if already logged in, even directly changes url
session_start();
$userid  = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : "";
if ($userid != "") {
	echo "<script>location.href='home.php'</script>";
}

include "include/common/dashboardhead.php";
?>
<script>
	localStorage.setItem('currentPage', 'home_page');
</script>


<body class="authentication">

	<form id="loginform" name="loginform" action="" method="post">
		<div class="login-screen">
			<div class="row">
				<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12 left-panel" style="display: flex;justify-content:center;align-items:center; border-right: 0.01px solid black;">

					<div class="left-content">
						<img src="img/login_image.png" alt="HRMS" class="hrms-image">

						<h4 style="color: white;">Onboarding New Talent with Digital HRMS</h4>

						<p class="sub-text" style="color: #cecece;">
							Everything you need in an easily customizable dashboard.
						</p>

					</div>

				</div>
				<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
					<div class="login-box">
						<div style="display: flex;justify-content: center;align-items: center;"><img src='img\hrms_text.png' alt="" style="height: 50px;"></div>
						<a href="#" class="login-logo">
							<h3 style="color: #ffffff; font-weight: bolder;">Welcome Back..!</h3>
						</a>
						<span class="text-danger" id="cinnocheck">
						</span>
						<h5 style="color: #cecece;">Please Login to your Account.</h5>
						<div class="form-group mt-4">
							<label for="lusername" style="color: #cecece;">User Name</label>
							<input type="text" name="lusername" id="lusername" tabindex="1" class="form-control" value="" placeholder="Enter Email" style="padding: 10px;border-radius:6px;" />
							<span id="usernamecheck" class="text-danger" style="display:none">Enter Email</span>
						</div>
						<div class="form-group mt-4">
							<label for="lpassword" style="color: #cecece;">Password</label>
							<input type="password" name="lpassword" id="lpassword" tabindex="2" class="form-control" value="" placeholder="Enter Password" style="padding: 10px;border-radius:6px;" />
							<span id="passwordcheck" class="text-danger" style="display:none">Enter Password</span>
						</div>

						<div class="actions" style="padding-top: 40px;">
							<button type="submit" id="lbutton" tabindex="6" name="lbutton" class="form-control btn btn-primary" style="font-size: 1rem;font-weight: bolder;color: white;padding: 10px;border-radius:6px; background-color: #f26b35;">Login</button>
						</div>
					</div>
				</div>

			</div>

		</div>
		<div id="loginSuccessPopup" class="success-popup">
			<div class="success-icon">✓</div>
			<h3>Login Successful</h3>
			<p>Redirecting to Home Page...</p>
		</div>
	</form>

</body>


<style>
	.login-screen.blur {
    filter: blur(6px);
    transition: filter 0.4s ease;
    pointer-events: none; /* optional: prevent clicks */
}
	.success-popup {
		position: fixed;
		left: 50%;
		top: 50%;
		transform: translate(-50%, -50%) scale(0);
		background: #fff;
		padding: 50px 80px;
		min-width: 500px;
		border-radius: 24px;
		text-align: center;
		box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
		z-index: 9999;
		opacity: 0;
		visibility: hidden;
	}

	.success-popup.show {
		visibility: visible;
		animation: popupFromButton 1.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
	}

	.success-icon {
		width: 100px;
		height: 100px;
		margin: 0 auto 20px;
		border-radius: 50%;
		background: #22c55e;
		color: #fff;
		font-size: 55px;
		font-weight: bold;
		line-height: 100px;
	}

	.success-popup h3 {
		margin: 0 0 10px;
		font-size: 32px;
		color: #222;
		font-weight: 700;
	}

	.success-popup p {
		margin: 0;
		font-size: 18px;
		color: #666;
	}

	@keyframes popupFromButton {
		0% {
			opacity: 0;
			transform: translate(-50%, 300px) scale(0.2);
		}

		60% {
			opacity: 1;
		}

		100% {
			opacity: 1;
			transform: translate(-50%, -50%) scale(1);
		}
	}
</style>
<?php include "include/common/dashboardfooter.php" ?>
<script src="jsd/index.js"></script>