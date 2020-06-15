<?php
require '../templates/header.php';
session_start();

$db = setupDb();
if (!$db) {
    //die("Database could not load!");
}

if (isset($_POST['username'])) {
    $hashedpw = hash("sha256", $_POST['password']);
    $sth = $db->prepare("INSERT INTO `users` (`email`, `password`) VALUES (?, ?)");
    $sth->execute([$_POST['username'], $hashedpw]);
    redirect('login');
}
?>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="container">
        <h1>Sign Up</h1>
        <p>Please fill in this form to create an account.</p>
        <hr>

        <div class="form-group">
            <label for="email"><b>Email</b></label>
            <input type="text" placeholder="Enter Email" name="username" value="" required>
            <span class="help-block"></span>
        </div>

        <div class="form-group ">
            <label for="psw"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="password" value="" required>
            <span class="help-block"></span>
        </div>

        <div class="form-group ">
            <label for="psw-repeat"><b>Repeat Password</b></label>
            <input type="password" placeholder="Repeat Password" name="confirm_password" value="" required>
            <span class="help-block"></span>
        </div>
        <div class="form-group ">
            <label for="terms"><input type="checkbox" id="terms" name="terms" value="terms"> I Agree to Terms & Conditions</label><br>
            <span class="help-block"></span>
        </div>


        <button type="submit" class="registerbtn">Register</button>
    </div>

    <div class="container signin">
        <p>Already have an account? <a href="login.php">Sign in</a>.</p>
    </div>
</form>
<?php
require '../templates/footer.php';
