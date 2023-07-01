<?php
require_once URL_VIEWS_ADMIN . '/layout/header.php';
?>
	<div class="container">
		<form action="login" method="POST">
			<div class="mb-3 row">
			<label for="staticEmail" class="col-sm-2 col-form-label">Email</label>
			<div class="col-sm-10">
			  <input type="text" class="form-control"  name="username" value="admin">
			</div>
			</div>
			<div class="mb-3 row">
			<label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
			<div class="col-sm-10">
			  <input type="password" name="password" class="form-control" id="inputPassword" value="admin">
			</div>
			</div>
			<div class="col-auto">
			    <button type="submit" class="btn btn-primary mb-3">Confirm identity</button>
			  </div>
		</form>
	</div>
<?php
require_once URL_VIEWS_ADMIN . '/layout/footer.php';
?>