<html>
<head>
<link rel="stylesheet" type="text/css" href="template.css"/>
</head>
<body>
<img src='background.png'/>
<div class="pos">
<?php
    $success = true;
	$db_conn = OCILogon("ora_m4s7", "a44406106", "ug");
	
	function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
	//echo "<br>running ".$cmdstr."<br>";
	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr); //There is a set of comments at the end of the file that describe some of the OCI specific functions and how they work

	if (!$statement) {
		echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
		$e = OCI_Error($db_conn); // For OCIParse errors pass the       
		// connection handle
		echo htmlentities($e['message']);
		$success = False;
	}

	$r = OCIExecute($statement, OCI_DEFAULT);
	if (!$r) {
		echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
		$e = oci_error($statement); // For OCIExecute errors pass the statementhandle
		echo htmlentities($e['message']);
		$success = False;
	} else {

	}
	return $statement;

	}

?>
<a href="Manager.php">Managers Login</a> <a href="Employee.php">Employees Login</a>  <a href="Customer.php">Customers Login</a>
<br>
<br>
<br>
  <form method="post" action="index.php?name" id="searchname">
    Looking for something?
    <input type="text" name="namesearch" value="Item Name" />
    <input type="submit" name="namesubmit" value="Go!" />
  </form>
  
  <form method="post" action="index.php?price" id="searchprice">
    Find all items less than $
	<input type="text" name="pricesearch" value="Price"/>
    <input type="submit" name="pricesubmit" value="Go!" /> 
  </form>

  <form method="post" action="index.php?used" id="searchused">
    Find the serial number and discount of all used
	<input type="text" name="genresearch" value="Genre"/>
	games
    <input type="submit" name="genresubmit" value="Go!" />
  </form>

  <form method="post" action="index.php?aggregation" id="searchaggregation">
Find the
<select name="aggregation">
<option value="Cheapest">Cheapest</option>
<option value="Most_Expensive">Most Expensive</option>
<option value="Average of all">Average Price Of All</option>
</select> Item(s)
<input type="submit" name ="aggregationsubmit" value="Go!"/>
</form>

  <form method="post" action="index.php?platformaggregation" id="platformaggregation">
Find the platform with the 
<select name="platformaggregation">
<option value="Highest">Highest</option>
<option value="Lowest">Lowest</option>
</select> average price of games.
<input type="submit" name="platformaggregationsubmit" value="Go!"/>
</form>

  <form method="post" action="index.php?genreaggregation" id="genreaggregation">
Find the game genre with the  
<select name="genreaggregation">
<option value="Highest">Highest</option>
<option value="Lowest">Lowest</option>
</select> average price.
<input type="submit" name="genreaggregationsubmit" value="Go!"/>
</form>

</br></br></br></br></br>

<form method="post" action="index.php?newgames" id="newgames">
<input type="submit" name="NewGameSubmit" value="New Games"/> 
</form>    
<form method="post" action="index.php?usedgames" id="usedgames">
<input type="submit" name="UsedGameSubmit" value="Used Games"/>
</form>
<form method="post" action="index.php?hardware" id="hardware">
<input type="submit" name="HardwareSubmit" value="Hardware"/>
</form>

  <?php
	
	
    if($db_conn) {
      if(isset($_POST['namesubmit'])) {
	    if(isset($_GET['name'])) {
	      if(preg_match("/[A-Z  | a-z]+/", $_POST['namesearch'])){
		    $name=$_POST['namesearch'];
		    echo "<br>".$name."<br>";
		  
		    $sql = "select i.pname, i.price, i.quantity from item i where i.pname LIKE '%".$name."%'";
		    $result = executePlainSQL($sql);
			
			echo "<br>Item search: ".$name."<br>";
			echo "<table>";
			echo "<tr><th>Name</th><th>Price</th><th>Quantity</th></tr>";

			while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
				echo "<tr><td>" . $row["PNAME"] . "</td><td>" . $row["PRICE"] . "</td><td>" . $row["QUANTITY"] . "</td></tr>"; //or just use "echo $row[0]" 
			}
			echo "</table>";		    
		  }
		  else {
			echo "<p>Invalid Entry. Please enter letters and numbers only <p>";
		  }
		  
	    }
	  }
      else if(isset($_POST['pricesubmit'])) {
	    if(isset($_GET['price'])) {
			if(preg_match("/^\d+(\.\d{2})?$/", $_POST['pricesearch'])){

				$price=$_POST['pricesearch'];
				$sql = "select * from item i where i.price<='".$price."'";
				$result = executePlainSQL($sql);
				
				echo "<br>Searching for items less than: ".$price."<br>";
				echo "<table>";
				echo "<tr><th>Serial#</th><th>Name</th><th>Price</th><th>Quantity</th></tr>";

				while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
					echo "<tr><td>" . $row["SERIAL_NUMBER"] . "</td><td>" . $row["PNAME"] . "</td><td>" . $row["PRICE"] . "</td><td>" . $row["QUANTITY"] . "</td></tr>"; //or just use "echo $row[0]" 
				}
				echo "</table>";
				
				
			}
			
			else{
				echo "<p>Invalid Price. Please enter only numberical values</p>";
			}			 
		}
	  }
      else if(isset($_POST['genresubmit'])) {
	    if(isset($_GET['used'])) {
  	        if(preg_match("/[A-Z  | a-z]+/", $_POST['genresearch'])){

				$genre=$_POST['genresearch'];
				$sql = "select g.serial_number, i.pname, u.discount from Game g, used_game u, item i where g.serial_number = u.serial_number AND i.serial_number = g.serial_number AND g.genre = '".$genre."'";
		
				$result = executePlainSQL($sql);
				
				echo "<br>Used game genre search for: ".$genre."<br>";
				echo "<table>";
				echo "<tr><th>Serial#</th><th>Game Name</th><th>Discount</th></tr>";

				while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
					echo "<tr><td>" . $row["SERIAL_NUMBER"] . "</td><td>" . $row["PNAME"] . "</td><td>" . $row["DISCOUNT"] . "</td></tr>"; //or just use "echo $row[0]" 
				}
				echo "</table>";
			}
			else {
			    echo "<p>Invalid Entry. Please enter letters only <p>";
			
			}
				
				

		}
	  }	  
	  
      else if(isset($_POST['aggregationsubmit'])) {
	    if(isset($_GET['aggregation'])) {
		    $option=$_POST['aggregation'];
		    echo "<br>".$option."<br>";
			$sql = "";
			if ( strcmp ( $option , "Cheapest" ) == 0) {
				$sql = "select Pname, price from item where price = (select MIN(price) from Item)";		
				$result = executePlainSQL($sql);
				
				echo "<br>Aggregation search</br>";
				echo "<table>";
				echo "<tr><th>Product Name</th><th>Min Price</th></tr>";

				while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
					echo "<tr><td>" . $row["PNAME"] . "</td><td>" . $row["PRICE"] . "</td></tr>"; //or just use "echo $row[0]" 
				}
				echo "</table>";	
		
			}
			else if ( strcmp ( $option , "Most_Expensive" ) == 0 ) {
				$sql = "select Pname, price from item where price = (select MAX(price) from Item)";		
				$result = executePlainSQL($sql);

				echo "<br>Aggregation search</br>";
				echo "<table>";
				echo "<tr><th>Product Name</th><th>Max Price</th></tr>";

				while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
					echo "<tr><td>" . $row["PNAME"] . "</td><td>" . $row["PRICE"] . "</td></tr>"; //or just use "echo $row[0]" 
				}
				echo "</table>";	
						
			}
			else if ( strcmp ( $option , "Average of all" ) == 0 ) {
				$sql = "select AVG(price) from item";
				$result = executePlainSQL($sql);
				echo "<br>Aggregation search</br>";
				echo "<table>";
				echo "<tr><th>Average of all items</th></tr>";

				while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
					echo "<tr><td>" . $row["AVG(PRICE)"] . "</td></tr>"; //or just use "echo $row[0]" 
				}
				echo "</table>";				
			
			}
					
			
		}
	  }
	    
	  
      else if(isset($_POST['platformaggregationsubmit'])) {
	    if(isset($_GET['platformaggregation'])) {
			$option=$_POST['platformaggregation'];
			$sqlcreateview = "create view PlatformAverage (platform, average) as 
							select g.platform, avg(i.price) as average
							from game g, item i
							where g.serial_number = i.serial_number
							group by g.platform
							having count(*) >= 1";
			$viewresult = executePlainSQL($sqlcreateview);			

			$sqlview = "select * from PlatformAverage";
			$viewresult = executePlainSQL($sqlview);
				
			echo "<br>Average Price of Each Platform</br>";
			echo "<table>";
			echo "<tr><th>Platform</th><th>Average</th></tr>";

			while ($row = OCI_Fetch_Array($viewresult, OCI_BOTH)) {
				echo "<tr><td>" . $row["PLATFORM"] . "</td><td>" . $row["AVERAGE"] . "</td></tr>"; //or just use "echo $row[0]" 
			}
			echo "</table>";	
			
			if ( strcmp ( $option , "Highest" ) == 0) {
									
				$sql = "select platform, average
						from PlatformAverage
						where average = (select MAX(average) from PlatformAverage)";
		
				$result = executePlainSQL($sql);
				
				echo "<br>Highest Average of Platforms</br>";



			}
			else if ( strcmp ( $option , "Lowest" ) == 0 ) {
			
				$sql = "select platform, average
						from PlatformAverage
						where average = (select MIN(average) from PlatformAverage)";
		
				$result = executePlainSQL($sql);
				
				echo "<br>Lowest Average of Platforms</br>";



			}
			echo "<table>";
			echo "<tr><th>Platform</th><th>Average</th></tr>";

			while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
				echo "<tr><td>" . $row["PLATFORM"] . "</td><td>" . $row["AVERAGE"] . "</td></tr>"; //or just use "echo $row[0]" 
			}
			echo "</table>";				
			
			$sqldrop = "drop view PlatformAverage";
			$viewdrop = executePlainSQL($sqldrop);			
			
			
		}
	  }	
      else if(isset($_POST['genreaggregationsubmit'])) {
	    if(isset($_GET['genreaggregation'])) {
			$option=$_POST['genreaggregation'];

			$sqlview = "create view GenreAverage (genre, average) as
						select g.genre, avg(i.price) as average
						from game g, item i
						where g.serial_number = i.serial_number
						group by g.genre
						having count(*) >= 1";
			$viewresult = executePlainSQL($sqlview);

			$sqlview = "select * from GenreAverage";
			$viewresult = executePlainSQL($sqlview);
				
			echo "<br>Average Price of Each Genre</br>";
			echo "<table>";
			echo "<tr><th>Genre</th><th>Average</th></tr>";

			while ($row = OCI_Fetch_Array($viewresult, OCI_BOTH)) {
				echo "<tr><td>" . $row["GENRE"] . "</td><td>" . $row["AVERAGE"] . "</td></tr>"; //or just use "echo $row[0]" 
			}
			echo "</table>";	
			
			if ( strcmp ( $option , "Highest" ) == 0) {
			
				$sql = "select genre, average
						from GenreAverage
						where average = (select MAX(average) from GenreAverage)";
		
				$result = executePlainSQL($sql);
				
				echo "<br>Highest Average of Genre</br>";


			}
			else if ( strcmp ( $option , "Lowest" ) == 0 ) {
			
				$sql = "select genre, average
						from GenreAverage
						where average = (select MIN(average) from GenreAverage)";
		
				$result = executePlainSQL($sql);
				
				echo "<br>Lowest Average of Genre</br>";

			}	
			
			echo "<table>";
			echo "<tr><th>Genre</th><th>Average</th></tr>";			
			while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
				echo "<tr><td>" . $row["GENRE"] . "</td><td>" . $row["AVERAGE"] . "</td></tr>"; //or just use "echo $row[0]" 
			}
			echo "</table>";	
			$sqldrop = "drop view GenreAverage";
			$viewdrop = executePlainSQL($sqldrop);			


		}
	  }


      else if(isset($_POST['NewGameSubmit'])) {
	    if(isset($_GET['newgames'])) {
			$sql = "select i.pname, g.genre, g.platform, i.price from New_Game n, Game g, Item i where n.serial_number=g.serial_number AND n.serial_number = i.serial_number";
			$result = executePlainSQL($sql);
			echo "<br>New Games</br>";
			echo "<table>";
			echo "<tr><th>Game</th><th>Genre</th><th>Platform</th><th>Price</th></tr>";
			while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
				echo "<tr><td>" . $row["PNAME"] . "</td><td>" . $row["GENRE"] . "</td><td>" . $row["PLATFORM"] . "</td><td>" . $row["PRICE"] . "</td></tr>"; //or just use "echo $row[0]" 
			}
			echo "</table>";	
		}
	  }

      else if(isset($_POST['UsedGameSubmit'])) {
	    if(isset($_GET['usedgames'])) {
			$sql = "select i.pname, g.genre, g.platform, i.price, u.discount from Used_Game u, Game g, Item i where u.serial_number=g.serial_number AND u.serial_number = i.serial_number";
			$result = executePlainSQL($sql);
			echo "<br>Used Games</br>";
			echo "<table>";
			echo "<tr><th>Game</th><th>Genre</th><th>Platform</th><th>Price</th><th>Discount</th></tr>";
			while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
				echo "<tr><td>" . $row["PNAME"] . "</td><td>" . $row["GENRE"] . "</td><td>" . $row["PLATFORM"] . "</td><td>" . $row["PRICE"] . "</td><td>" . $row["DISCOUNT"] . "</td></tr>"; //or just use "echo $row[0]" 
			}
			echo "</table>";	
		}
	  }
	  
      else if(isset($_POST['HardwareSubmit'])) {
	    if(isset($_GET['hardware'])) {
			$sql = "select i.pname, h.type, i.price from Hardware h, Item i where i.serial_number=h.serial_number";
			$result = executePlainSQL($sql);
			echo "<br>Hardware and Accessories</br>";
			echo "<table>";
			echo "<tr><th>Game</th><th>Type</th><th>Price</th></tr>";
			while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
				echo "<tr><td>" . $row["PNAME"] . "</td><td>" . $row["TYPE"] . "</td><td>" . $row["PRICE"] . "</td></tr>"; //or just use "echo $row[0]" 
			}
			echo "</table>";	
		}
	  }	  
	  
	  else {
	        echo "<p>Please enter search query</p>";
	  }		
	}
	


  ?>



</div>

</body>
</html>