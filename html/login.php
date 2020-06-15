<?php
require '../templates/header.php';
?>
<form action="" method="post">
    <div class="container">
        <h1>Sign In</h1>
        <p>Please fill in this form to sign in</p>
        <hr>

        <div class="form-group">
            <label for="email"><b>Email</b></label>
            <input type="text" placeholder="Enter Email" name="username" value="" required>
        </div>

        <div class="form-group">
            <label for="psw"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="password" required>
        </div>

        <div class="form-group">
            <label for="terms"><input type="checkbox" id="terms" name="terms" value="terms"> I Agree to Terms & Conditions</label><br>
        </div>


        <div class="form-group">
            <button type="submit" class="registerbtn" value="Login">Sign In</button>
        </div>

        <div class="container signin">
            <p>Please sign-up for an account? <a href="register.php">Sign up</a>.</p>
        </div>
</form>
<?php
require '../templates/footer.php';
