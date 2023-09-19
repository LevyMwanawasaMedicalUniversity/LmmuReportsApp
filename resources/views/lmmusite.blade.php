<script>
// Set the date we're counting down to
var countDownDate;
var message = "Graduation Countdown";
var graduation = new Date("Sep 21, 2023 17:00:00").getTime();
// var countDownDate1 = new Date("Jan 3, 2023 17:00:00").getTime();


// Update the count down every 1 second
var x = setInterval(function() {

  // Get today's date and time
  var now = new Date().getTime();
  
  // Find the distance between now and the count down date
  var distance = graduation - now;

  // Time calculations for days, hours, minutes and seconds
  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
  var seconds = Math.floor((distance % (1000 * 60)) / 1000);

  // Display the result in the element with id="demo"
  document.getElementById("demo").innerHTML = days + "d " + hours + "h "
  + minutes + "m " + seconds + "s ";
  
  //Display Message in the element with id="message"
  document.getElementById("message").innerHTML = message;

  // If the count down is finished, write some text
  if (distance < 0) {
    clearInterval(x);
    document.getElementById("demo").innerHTML = "Welcome";
  }
}, 1000);
</script>
<div class="graduation">
<h1 align="center" id="message" style="color:#3a6412">&nbsp;</h1>

<p>&nbsp;</p>

<h1 align="center" id="demo" style="color:#ecc311">&nbsp;</h1>

<p>&nbsp;</p>
</div>
<style>

.graduation {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      /* min-height: 100vh; Ensure the content takes at least the full viewport height */
      margin: 0;
      background-color: #fff; /* Set background to white */
    }

    h1 {
      font-size: 24px;
      text-align: center;
      color: #3a6412;
    }
  </style>