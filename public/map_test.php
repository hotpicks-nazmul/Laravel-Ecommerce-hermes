<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Map Diagnostic — which embed works?</title>
<style>
body { font-family: sans-serif; background: #f5f5f5; margin: 20px; }
.box { background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 16px; margin-bottom: 24px; }
h2 { margin-top: 0; font-size: 16px; }
iframe { width: 100%; max-width: 600px; height: 350px; border: 0; }
</style>
</head>
<body>
<h1>Map Embed Diagnostic — open each, tell Haku which show a map</h1>

<div class="box">
<h2>A) Place-ID embed (current on contact page) — HAMKO J &amp; J Tower pin</h2>
<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3677.548137724079!2d89.55057870666296!3d22.81920141159502!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39ff91184efada9f%3A0x62c81bf9e9e89d57!2sHAMKO%20Industries%20Ltd.%20(J%20%26%20J%20Tower)!5e0!3m2!1sen!2sbd!4v1786249069317!5m2!1sen!2sbd" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
</div>

<div class="box">
<h2>B) Coordinate embed (the one that WAS showing before) — no pin</h2>
<iframe src="https://www.google.com/maps?q=22.81920141,89.55057871&z=16&output=embed" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
</div>

<div class="box">
<h2>C) Coordinate pin embed — red pin at store, no label</h2>
<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3690.82!2d89.55057871!3d22.81920141!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjIuODE5MjAxNDEsODkuNTUwNTc4NzE!5e0!3m2!1sen!2sbd!4v1635000000000!5m2!1sen!2sbd" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
</div>

</body>
</html>
