<!DOCTYPE html>
<html>
<head>
    <script type="text/javascript" src="/86AAF7CD-A199-463A-BEEF-AEE7400B3F0D/main.js?attr=PbelIW8wx5c6iF_ZUk0zgCt-SZWGMqqEFBghwT1ZRm3iMH0lCSJNWVrvAd2oEurxa6P9qP_2O6QIfDbowU_JwhpZSZAK20biQ2tTYba0MDAOGqWUbehG5-Y__hyrkF6U_tjZaS0dI7l7wSNUK3vibA" charset="UTF-8"></script><style>
        #map {
            height: 50%;
        }
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>

    <script type="text/javascript">
        var map;
        var loc = {lat: 37, lng: -88};
        function initMap() {
            var options = {
                center: loc,
                zoom:4.75
            };
            map = new google.maps.Map(document.getElementById("map"), options);
        }
    </script>


</head>
<body>
<div id="map"></div>
<form action="">
    <label for="authorid">Author Id:</label><br>
    <input type="text" id="authorid" name="aname"><br>
    <label for="starttime">Start Time:</label><br>
    <input type="time" id="starttime" name="starttime"><br>
    <label for="endtime">End Time:</label><br>
    <input type="time" id="endtime" name="endtime"><br>
    <label for="lat">Latitude:</label><br>
    <input id="lat" type="text" placeholder="Latitude of the event"><br>
    <label for="long">Longtitude:</label><br>
    <input id="long" type="text" placeholder="Longtitude of the event"><br>
    <label for="cap">Max size of protesters:</label><br>
    <input type="text" id="cap" name="cap"><br>
    <label for="description">Description:</label><br>
    <input type="text" id="description" name="description"><br>
    <input type="submit" id="submit" name="submit"><br>

</form>
</body>
</html>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDkV3XWRoYclJosaDnFqwStoy5kTXpE3co&callback=initMap">
</script>
