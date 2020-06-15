<?php
require '../templates/header.php';
session_start();

$db = setupDb();
if (!$db) {
    //die("Database could not load!");
}

if (isset($_POST['startingTime'])) {
    $sth = $db->prepare("INSERT INTO `protests` (`author_id`, `starting_time`, `ending_time`, `date`, `latitude`, `longitude`, `description`, `cap`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $sth->execute([$_SESSION['user'], $_POST['startingTime'], $_POST['endingTime'], $_POST['latitude'], $_POST['longitude'], $_POST['description'], $_POST['cap']]);
}
?>
<div id="mapId" style="height: 500px"></div>
<script>
    let map = L.map('mapId').setView([39, -98], 5);

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
<form id="geocodeForm" method="get">
    Street:
    <input class="geocodeForm" name="street" type="text"> <br>
    City:
    <input class="geocodeForm" name="city" type="text"> <br>
    County:
    <input class="geocodeForm" name="county" type="text"> <br>
    State:
    <input class="geocodeForm" name="state" type="text"> <br>
    Country:
    <input class="geocodeForm" name="country" type="text"> <br>
    <input name="format" value="json" hidden>
    <input name="limit" value="1" hidden>
</form>
<p id="upload_process" style="display: none">Checking <img src="media/loader.gif" width="20" height="20" /></p>
<p id="dialogDiv"></p>
<form action="index.php" method="post">
    Date:<br>
    <input type="date" name="date"><br>
    <label for="startingTime">Start Time:</label><br>
    <input type="time" id="startingTime" name="startingTime"><br>
    <label for="endingTime">End Time:</label><br>
    <input type="time" id="endingTime" name="endingTime"><br>
    <label for="cap">Max size of protesters:</label><br>
    <input type="text" id="cap" name="cap"><br>
    <label for="description">Description:</label><br>
    <input type="text" id="description" name="description"><br>
    <input type="submit" id="submit" name="submit"><br>
    <input id="latitude" name="latitude" hidden>
    <input id="longitude" name="longitude" hidden>
</form>
<?php
require '../templates/footer.php';