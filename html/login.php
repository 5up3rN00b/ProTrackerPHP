<?php
require '../templates/header.php';
session_start();

$db = setupDb();
if (!$db) {
    //die("Database could not load!");
}

if (isset($_POST['username'])) {
    $sth = $db->prepare("SELECT `user_id`, `password` FROM `users` WHERE `email`=?");
    $sth->execute([$_POST['username']]);
    $passArr = $sth->fetchAll();

    if (hash("sha256", $_POST['password']) == $passArr[0]['password']) {
        $_SESSION['user_id'] = $passArr[0]['user_id'];
        redirect('index');
    }
}
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
            <button type="submit" class="registerbtn" value="Login">Sign In</button>
        </div>

        <div class="container signin">
            <p>Please sign up for an account if you do not have one <a href="register.php">Sign up</a>.</p>
        </div>
</form>
<?php
require '../templates/footer.php';
