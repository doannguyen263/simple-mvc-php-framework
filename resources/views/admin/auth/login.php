<?php
use App\Helpers\FlashMessage;
use App\Helpers\CSRFTokenTrait;
require_once PATH_VIEWS_ADMIN . '/layout/header.php';
// Tao pass
// $password_hash = password_hash('admin', PASSWORD_DEFAULT);
// print_r($password_hash);
?>
<div class="hold-transition login-page">
	<div class="login-box">
		<div class="login-logo">
			<a href="<?= SITE_URL ?>"><b>Admin Login</a>
		</div>

		<div class="card">
			<div class="card-body login-card-body">
				<p class="login-box-msg">Sign in to start your session</p>
				<form action="login" method="post" class="mb-3">
					<div class="input-group mb-3">
						<input type="text" name="username" class="form-control" placeholder="Username" value="admin">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-envelope"></span>
							</div>
						</div>
					</div>
					<div class="input-group mb-3">
						<input type="password" name="password" class="form-control" placeholder="Password" value="admin">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-lock"></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-7">
							<div class="icheck-primary">
								<input type="checkbox" id="remember" name="remember">
								<label for="remember">
									Ghi nhớ
								</label>
							</div>
						</div>

						<div class="col-5">
							<button type="submit" class="btn btn-primary btn-block">Đăng nhập</button>
							<?= CSRFTokenTrait::generateCSRFTokenInput('csrf_login_token') ?>
						</div>
					</div>
				</form>

				<?php FlashMessage::displayFlashMessage();?>

			</div>
		</div>
	</div>
</div>
<?php
require_once PATH_VIEWS_ADMIN . '/layout/footer.php';
?>