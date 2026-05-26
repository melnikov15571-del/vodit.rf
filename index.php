<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'header.php';
?>
<div class="slideshow-container">
    <div class="mySlides fade">
        <img src="images/яхта.jpg" style="width:100%">
        <div class="text">Вождение яхт</div>
    </div>
    <div class="mySlides fade">
        <img src="images/катер.jpg" style="width:100%">
        <div class="text">Вождение катеров</div>
    </div>
    <div class="mySlides fade">
        <img src="images/круизныйлайнер.jpg" style="width:100%">
        <div class="text">Вождение круизного лайнера</div>
    </div>
    <a class="prev" onclick="plusSlides(-1)">❮</a>
    <a class="next" onclick="plusSlides(1)">❯</a>
</div>
<div class="dot-container">
    <span class="dot active" onclick="currentSlide(1)"></span> 
    <span class="dot" onclick="currentSlide(2)"></span> 
    <span class="dot" onclick="currentSlide(3)"></span> 
</div>
<script src='script/script.js'></script>
<?php include 'footer.php'; ?>