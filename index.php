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
			<div class="login-box">
				<div style="display: flex;justify-content: center;align-items: center;"><img src='img\hrms.png' alt="" style="height: 50px;"></div>
				<a href="#" class="login-logo">
					<h3 style="color: #3e3f42; font-weight: bolder;">HRMS SOFTWARE</h3>
				</a>
				<span class="text-danger" id="cinnocheck">
				</span>
				<h5>Welcome back,<br />Please Login to your Account.</h5>
				<div class="form-group mt-4">
					<label for="lusername">User Name</label>
					<input type="text" name="lusername" id="lusername" tabindex="1" class="form-control" value="" placeholder="Enter Email" style="padding: 10px;border-radius:6px;" />
					<span id="usernamecheck" class="text-danger" style="display:none">Enter Email</span>
				</div>
				<div class="form-group mt-4">
					<label for="lpassword">Password</label>
					<input type="password" name="lpassword" id="lpassword" tabindex="2" class="form-control" value="" placeholder="Enter Password" style="padding: 10px;border-radius:6px;" />
					<span id="passwordcheck" class="text-danger" style="display:none">Enter Password</span>
				</div>

				<div class="actions" style="padding-top: 40px;">
					<button type="submit" id="lbutton" tabindex="6" name="lbutton" class="form-control btn btn-primary" style="font-size: 1rem;font-weight: bolder;color: white;padding: 10px;border-radius:6px;">Login</button>
				</div>
				<div id="portal">
					<div class="portal-content">
						<h1 style="color: black;">ACCESS GRANTED</h1>
						<p style="color: black;">Loading HRMS Dashboard...</p>
					</div>
				</div>

			</div>
		</div>
	</form>

</body>


<style>
	#portal {
		position: fixed;
		width: 0;
		height: 0;
		border-radius: 50%;
		background: #c1ecff;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		z-index: 99999;
		overflow: hidden;
		transition: all 1s ease-in-out;
	}

	#portal.open {
		width: 3000px;
		height: 3000px;
	}

	.portal-content {
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		color: #fff;
		text-align: center;
		opacity: 0;
	}

	#portal.open .portal-content {
		opacity: 1;
		transition: opacity .10s ease 0.5s;
	}
</style>
<?php include "include/common/dashboardfooter.php" ?>
<script src="jsd/index.js"></script>