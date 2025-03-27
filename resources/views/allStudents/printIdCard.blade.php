<html>
	<head></head>
	<body style="margin: 0px; padding: 0px;">
				<div style=" height: 280px; break-inside: avoid; text-align:center; width: 395pt;">
			
				<!-- TOP BAR -->
				<div class="university" style="font-size: 16pt; margin-right:0px; margin-bottom: 5px;  padding: 2pt; text-align: center; font-weight:bold; color: #FFF; font-family: arial; background-color: #58121a;"> 
					Levy Mwanawasa Medical University
				</div>
				
				<!-- LOGO -->
				<div style="width: 100px; float:left; margin-left: 20px; text-align: center;  font-family: arial; padding-top: 15px;">
					<img width="92" src="https://edurole.lmmu.ac.zm/templates/mobile/images/logonobg.png">
				</div>
				
				
				<!-- CARD TYPE-->
				<div class="subtitle" style="text-align: center; margin-right:27px; float:left; width: 300px; padding-top: 20px; ">
					<span style="font-size: 18pt; font-weight:900; margin-right:37px; color:#58121a; text-align: center; font-family: arial; padding: 2pt; padding-bottom: 3px; border-bottom: 1px solid #000; font-color: #58121a;"> STUDENT ID </span>   
				</div>
				
				<!-- PHOTO -->	  
				<div style=" position: relative; clear: both; margin-top: 100px; margin-left: 20px;   width: 110px; height: 138px; text-align: center; overflow: hidden; border: 2px solid black;">
					<img width="120" src="//edurole.lmmu.ac.zm/datastore/identities/pictures/{{ $studentInformation->StudentID }}.png">
				</div>
				
				<!-- FIELDS -->	  
				<div style="position: absolute; top: 135px; float: left; width: 330px; height: 300px; left: 180px;">
				
						<div class="id" style=" font-family: arial; color:black; float:left;  font-size: 12pt; width: 40pt; text-align: left; height: 18pt;"> <b>Name </b>  </div>  
						<div style="width: 20pt; float: left;  height: 18pt;">:</div>
						<div class="studentid" style="width: 200px; font-size: 12pt; color:black; font-family: arial; font-weight: bold; float:left; text-align: left;  height: 18pt;">{{ $studentInformation->FirstName }}  {{ $studentInformation->Surname }}</div>
					
						<div class="id" style=" font-family: arial; color:black; float:left;  font-size: 12pt; width: 40pt; text-align: left;  height: 18pt;"> <b>ID</b></div>
						<div style="width: 20pt; float: left;  height: 18pt;">:</div>
						<div class="studentid" style="width: 200px; font-size: 12pt; color:black; font-family: arial; font-weight: bold; float:left; text-align: left; height: 18pt; ">{{ $studentInformation->StudentID }} </div>
					
						<div class="date" style=" font-family: arial;  color:black; float:left; font-size: 12pt; width: 40pt; text-align: left;  height: 18pt;"> <b>NRC</b>  </div>
						<div style="width: 20pt; float: left;  height: 18pt;">:</div>
						<div class="studentid" style="font-size: 12pt; color:black; font-family: arial; font-weight: bold; width: 200px; float:left; text-align: left;  height: 18pt;">{{ $studentInformation->GovernmentID }} </div>
						
						<!-- <div class="date" style=" font-family: arial;  color:black; float:left; font-size: 12pt; width: 40pt; text-align: left;  height: 18pt;"> <b>Exp</b>  </div>
						<div  style="width: 20pt; float: left;  height: 18pt;">:</div>
						<div class="studentid" style=" font-size: 12pt; color:black;  font-family: arial; font-weight: bold;  float:left; width: 200px; text-align: left;  height: 18pt;">06-Mar-2028 </div> -->


						<div class="date" style=" font-family: arial;  color:black; float:left; font-size: 12pt; width: 40pt; text-align: left;  height: 18pt;"> <b>Prog</b>  </div>.
						<div style="width: 20pt; float: left;  height: 18pt;">:</div>
						<div class="studentid" style=" font-size: 12pt; color:black;  font-family: arial; font-weight: bold;  float:left; width: 200px; text-align: left;  height: 18pt;">{{ $studentInformation->Name }} </div>
						

				</div>
				
				<!-- BOTTOM BAR -->
				<div class="university" style="position: relative; margin-top: 5px; font-size: 16pt; margin-right:0px; margin-bottom: 5px;  padding: 2pt; text-align: center; font-weight:bold; color: #FFF; font-family: arial; background-color: #58121a;"> 
					2025
				</div>
				
			</div>
			<div style="page-break-before: always; color:#2e164e; text-align: center; width: 350pt; padding-top: 5px;">
				<br><br>
				<div style="font-size: 12pt;  font-family: arial;  color:#000; padding-bottom: 0px;">
					This Card is the property of<br>
						Levy Mwanawasa Medical University,<br>
						P.O.Box 33991, Lusaka
				<br>
				<img src="https://portal.edenuniversity.edu.zm/barcode.php?ID={{ $studentInformation->StudentID }}" height="50px;" style="margin: 10px;"> <br>
				If found return it to the above address<br>
				or take it to the nearest Police Station<br>
				Issuing Authority<br>
				<img src="https://edurole.lmmu.ac.zm/templates/mobile/images/signature.png" height="40px;"> <br><div style="font-size: 15pt;"> Registrar</div><br>
				</div>
			</div>
		</body>
</html>