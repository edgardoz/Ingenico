<?php

?>

<style>
/* The Modal (background) */
.modal-p {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content/Box */
.modal-content-p {
  background-color: #fefefe;
  margin: 1% auto; /* 15% from the top and centered */
  padding: 10px;
  border: 1px solid #888;
  width: 50%; /* Could be more or less, depending on screen size */
  height: 92%;
}

/* The Close Button */
.close-p {
  color: #aaa;
  float: right;
  font-size: 20px;
  font-weight: bold;
  margin: 0% auto;
}

.close-p:hover,
.close-p:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}

 iframe {
			margin: 0% auto;
            width: 100%;
            height: 100%;
			border: 0px solid #888;
			
        }
.button {
      background-color: #77a434; /* Green */
      border: none;
      color: white;
      padding: 10px 28px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 16px;
      

    }

</style>


<!-- Trigger/Open The Modal -->

<!-- The Modal -->
<div id="myModal" class="modal-p">

  <!-- Modal content -->
  <div class="modal-content-p">
    <span class="close-p">&times;</span>  
	
  	<iframe border="0" id="pantalla"> </iframe>

  </div>

</div>


<script>
var modal = document.getElementById('myModal');
var span = document.getElementsByClassName("close-p")[0];
span.onclick = function() {
  modal.style.display = "none";
}

window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}

function lanzador(row){
  $("tr.itemRow").each(function() {
        var id = $(this).attr("rel");
        if(id==row[0]){
          $(this).attr("rel","R"+row[0]);                    
        }
        
    });
 
  document.getElementById('pantalla').src = "plantilla/plantilla.php?RES_ID="+row[1]+"&UID="+row[0];  
  modal.style.display = "block";
}
//3333
/*
function lanzador(row){
      $("tr.itemRow").each(function() {
        var id = $(this).attr("rel");
        if(id=="3333"){
          $(this).attr("rel","R3333");
          var id2 = $(this).attr("rel");
          alert("lo encontre es: "+id+" a "+id2);
        }
        
    });

}
*/
</script>