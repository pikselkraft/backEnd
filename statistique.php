<<<<<<< HEAD
<?php

	require('includes/header.php');
  
  $test  = 10;
  $test2 = 15;
  $test3 = 100;
  $test4 = 5;
?>

<div class="row">
    <div class="small-6 large-centered columns">
      <h3>Vision globale</h3>
      <canvas id="canvas" height="350" width="500"></canvas>
    </div>
</div>
<br>
<div class="row">
    <div class="small-6 large-centered columns">
      <h3>Vision globale</h3>
      <canvas id="canvas2" height="300" width="300"></canvas>
    </div>
</div>


  <script src="scripts/Chart.js"></script>
  <script>

    var barChartData = {
      labels : ["January","February","March","April","May","June","July"],
      datasets : [
        {
          fillColor : "rgba(220,220,220,0.5)",
          strokeColor : "rgba(220,220,220,1)",
          data : [<?= $test; ?>,<?= $test4; ?>,<?= $test2; ?>,<?= $test3; ?>,56,55,40]
        },
        {
          fillColor : "rgba(151,187,205,0.5)",
          strokeColor : "rgba(151,187,205,1)",
          data : [<?= $test; ?>,<?= $test4; ?>,<?= $test2; ?>,<?= $test3; ?>,96,27,100]
        }
      ]
      
    }

  var myLine = new Chart(document.getElementById("canvas").getContext("2d")).Bar(barChartData);

  var pieData = [
        {
          value: 30,
          color:"#F38630"
        },
        {
          value : 50,
          color : "#E0E4CC"
        },
        {
          value : 100,
          color : "#69D2E7"
        }
      
      ];

  var myPie = new Chart(document.getElementById("canvas2").getContext("2d")).Pie(pieData);
  
  </script>


=======
<?php

	require('includes/header.php');
  
  $test  = 10;
  $test2 = 15;
  $test3 = 100;
  $test4 = 5;
?>

<div class="row">
    <div class="small-6 large-centered columns">
      <h3>Vision globale</h3>
      <canvas id="canvas" height="350" width="500"></canvas>
    </div>
</div>
<br>
<div class="row">
    <div class="small-6 large-centered columns">
      <h3>Vision globale</h3>
      <canvas id="canvas2" height="300" width="300"></canvas>
    </div>
</div>


  <script src="scripts/Chart.js"></script>
  <script>

    var barChartData = {
      labels : ["January","February","March","April","May","June","July"],
      datasets : [
        {
          fillColor : "rgba(220,220,220,0.5)",
          strokeColor : "rgba(220,220,220,1)",
          data : [<?= $test; ?>,<?= $test4; ?>,<?= $test2; ?>,<?= $test3; ?>,56,55,40]
        },
        {
          fillColor : "rgba(151,187,205,0.5)",
          strokeColor : "rgba(151,187,205,1)",
          data : [<?= $test; ?>,<?= $test4; ?>,<?= $test2; ?>,<?= $test3; ?>,96,27,100]
        }
      ]
      
    }

  var myLine = new Chart(document.getElementById("canvas").getContext("2d")).Bar(barChartData);

  var pieData = [
        {
          value: 30,
          color:"#F38630"
        },
        {
          value : 50,
          color : "#E0E4CC"
        },
        {
          value : 100,
          color : "#69D2E7"
        }
      
      ];

  var myPie = new Chart(document.getElementById("canvas2").getContext("2d")).Pie(pieData);
  
  </script>


>>>>>>> 1b1dc0d30f435543a713d0fb3963e6e1d43f9639
<?php require('includes/footer.php'); ?>