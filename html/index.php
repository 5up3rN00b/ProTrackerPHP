<?php
require '../templates/header.php';
session_start();

$db = setupDb();
if (!$db) {
    //die("Database could not load!");
}

if (isset($_POST['join']) && isset($_SESSION['user_id'])) {
    $sth = $db->prepare("INSERT INTO `participations` (`user_id`, `protest_id`) VALUES (?, ?)");
    $sth->execute([$_SESSION['user_id'], $_POST['join']]);

    $sth = $db->prepare("SELECT `user_id` FROM `participations` WHERE `protest_id`=?");
    $sth->execute([$_POST['join']]);
    $passArr = $sth->fetchAll();
    $protestCheckLen = sizeof($passArr);

    $sth = $db->prepare("SELECT `cap` FROM `protests` WHERE `protest_id`=?");
    $sth->execute([$_POST['join']]);
    $passArr = $sth->fetchAll();

    if ($passArr[0]['cap'] <= $protestCheckLen) {
        $sth = $db->prepare("UPDATE `protests` SET `full`=1 WHERE `protest_id`=?");
        $sth->execute([$_POST['join']]);
    }
}

if (isset($_POST['logout'])) {
    unset($_SESSION['user_id']);
}

if (isset($_POST['startingTime']) && isset($_SESSION['user_id'])) {
    $sth = $db->prepare("INSERT INTO `protests` (`author_id`, `starting_time`, `ending_time`, `date`, `latitude`, `longitude`, `description`, `cap`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $sth->execute([$_POST['author_id'], $_POST['startingTime'], $_POST['endingTime'], $_POST['date'], $_POST['latitude'], $_POST['longitude'], $_POST['description'], $_POST['cap']]);

    if ($sth) {
        redirect('index');
    }
}

$sth = $db->prepare("SELECT `protest_id`, `starting_time`, `ending_time`, `date`, `latitude`, `longitude`, `description` FROM `protests` WHERE (`full`=0 OR `author_id`=?)");
$sth->execute([$_SESSION['user_id']]);
$passArr = $sth->fetchAll();

$len = sizeof($passArr);

$sth = $db->prepare("SELECT `protest_id` FROM `participations` WHERE `user_id`=?");
$sth->execute([$_SESSION['user_id']]);
$joinedProtests = $sth->fetchAll();
$protestLen = sizeof($joinedProtests);
?>
<div class="main">
    <div id="mapId" style="height: 100%; width: 85%; position: absolute;"></div>
    <script>
        let map = L.map('mapId').setView([39, -98], 5);

        let markers = [];

        <?php
        for ($i = 0; $i < $len; $i++) {
            echo "markers[$i] = L.marker([" . $passArr[$i]['latitude'] . ',' . $passArr[$i]['longitude'] . "]).addTo(map);";
            echo "markers[$i].bindPopup('" . "<b>Date: </b>" . $passArr[$i]['date'] . "<br><b>Starting Time: </b>" . $passArr[$i]['starting_time'] . "<br><b>Ending Time: </b>" . $passArr[$i]['ending_time'] . "<br>" . $passArr[$i]['description'] . "<br>";

            $joined = false;
            for ($j = 0; $j < $protestLen; $j++) {
                if ($passArr[$i]['protest_id'] == $joinedProtests[$j]['protest_id']) {
                    $joined = true;
                    break;
                }
            }

            if ($joined) {
                echo "Already joined this protest!<br>";
            } else {
                echo "<form action=\'index.php\' method=\'post\'><input name=\'join\' value=\'" . $passArr[$i]['protest_id'] . "\' hidden><input type=\'submit\' value=\'Join\'></form><br>";
            }


            $sth = $db->prepare("SELECT `user_id` FROM `participations` WHERE `protest_id`=?");
            $sth->execute([$passArr[$i]['protest_id']]);
            $protestNum = $sth->fetchAll();
            $protestPeopleLen = sizeof($protestNum);
            echo "<b>" . $protestPeopleLen . "</b> attending this protest!');";
        }
        ?>

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        $(function() {
            $("form#geocodeForm").submit(function(e) {
                e.preventDefault();
                var formData = $("form#geocodeForm").serialize();

                $("#dialogDiv").empty();

                $.ajax({
                    url: "https://nominatim.openstreetmap.org/search",
                    type: 'GET',
                    data: formData,
                    success: function(data) {
                        item = data[0];
                        $("#address").val(item.lat + "," + item.lon);
                        $("#dialogDiv").append(item.display_name);
                    },
                    error: function() {
                        $("#geocodeForm").html("Error happened");
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            });

            var emailTypingTimer, geocodeTypingTimer;
            var doneTypingInterval = 1000;

            $('#email').keyup(function(){
                clearTimeout(emailTypingTimer);
                if ($('#email').val()) {
                    emailTypingTimer = setTimeout(emailDoneTyping, doneTypingInterval);
                }
            });

            $('.geocodeForm').each(function() {
                $(this).keyup(function() {
                    clearTimeout(geocodeTypingTimer);
                    if ($(this).val()) {
                        $('#upload_process').css("display", "none");
                        geocodeTypingTimer = setTimeout(geocodeDoneTyping, doneTypingInterval * 2);
                    }
                })
            })

            function emailDoneTyping() {
                $.ajax({
                    url: "ajax/check.php",
                    type: "GET",
                    data: {'email': $('#email').val()},
                    success: function(data) {
                        if (data == '0') {
                            $("#email")[0].setCustomValidity('');
                        } else {
                            $("#email")[0].setCustomValidity('This email is already taken.');
                        }
                    }
                });
            }

            function geocodeDoneTyping() {
                var formData = $("form#geocodeForm").serialize();

                $("#dialogDiv").empty();
                $('#upload_process').css("display", "inline");

                $.ajax({
                    url: "https://nominatim.openstreetmap.org/search",
                    type: 'GET',
                    data: formData,
                    success: function(data) {
                        let item = data[0];
                        $("#latitude").val(item.lat);
                        $("#longitude").val(item.lon);
                        $("#dialogDiv").append(item.display_name);
                        $('#upload_process').css("display", "none");
                    },
                    error: function() {
                        $("#geocodeForm").html("Error happened");
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            }
        });
    </script>
</div>
<div class="sidenav">
    <?php
    if (!isset($_SESSION['user_id'])) {
        echo "<b>" . "Please login" . "<a href='login.php'>here</a> </b>";
    } else {?>
    <form method="post" action="index.php">
        <input name="logout" value="true" hidden>
        <input class="button" type="submit" value="Logout">
    </form>
    <b>Create Protest</b> <br>
    <form id="geocodeForm" method="get">
        Street:
        <input class="geocodeForm protestInput" name="street" type="text"> <br>
        City:
        <input class="geocodeForm protestInput" name="city" type="text"> <br>
        County:
        <input class="geocodeForm protestInput" name="county" type="text"> <br>
        State:
        <input class="geocodeForm protestInput" name="state" type="text"> <br>
        Country:
        <input class="geocodeForm protestInput" name="country" type="text"> <br>
        <input name="format" value="json" hidden>
        <input name="limit" value="1" hidden>
    </form>
    <p id="upload_process" style="display: none">Checking <img src="media/loader.gif" width="20" height="20" /></p>
    <p id="dialogDiv"></p>
    <form method="post" action="index.php">
        Date:<br>
        <input type="date" name="date"><br>
        <label for="startingTime">Start Time:</label><br>
        <input type="time" id="startingTime" name="startingTime"><br>
        <label for="endingTime">End Time:</label><br>
        <input type="time" id="endingTime" name="endingTime"><br>
        <label for="cap">Max size of protesters:</label><br>
        <input class="protestInput" type="text" id="cap" name="cap"><br>
        <label for="description">Description:</label><br>
        <input class="protestInput" type="text" id="description" name="description"><br>
        <input class="button" type="submit" id="submit" name="submit"><br>
        <input id="latitude" name="latitude" hidden>
        <input id="longitude" name="longitude" hidden>
        <input name="author_id" value="<?php echo $_SESSION['user_id'] ?>" hidden>
    </form>
    <?php
    }
    ?>
</div>
<?php
require '../templates/footer.php';