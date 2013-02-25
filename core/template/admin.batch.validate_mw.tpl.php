<?php

require_once(__DIR__ . "/../3p/parsecsv/parsecsv.lib.php");

function parseMicroworkersData($data, $mw_salt) {

        $csv_head = "ROW,ID_TASK,ID_WORKER,TASK_RATING,FINISHED,IP,COUNTRY_CODE,PROOF,EMPLOYER_COMMENT";

        $result = strpos($data, $csv_head);

        if ($result === false) {

                die("Couldn't find the CSV head in your pasted stuff. <br/>I was looking for: '" . $csv_head . "'<br/>");
        }

        $mw_result = substr($data, $result);

        $csv = new parseCSV();

        $csv->parseCSV($mw_result);

        $return = array();

        $token_list = array();

	// Reverse the passed data (we want increasing time)
	$csv->data = array_reverse($csv->data);

        foreach ($csv->data AS $row) {

                $linedata = array();

                $linedata["worker_id"] = $row["ID_WORKER"];
                $linedata["proof_raw"] = $row["PROOF"];

		$linedata["token_shouldbe"] = md5($linedata["worker_id"] . $mw_salt);
		$linedata["proof_found"] = "";

	       // Look for a token in the submitted text
                if (!preg_match('/xy([a-f0-9]{32})yx/', $row["PROOF"], $matches)) {
 
		       $linedata["token_valid"] = "0";
                        $linedata["token_invalid_reason"] = "Couldn't not find a valid token!";
 
                } else {
       
			$linedata["proof_found"] = $matches[1];
 
                	// token found
                        if (in_array($matches[1], $token_list)) {

                        	// Token was used before
                                $linedata["token_valid"] = "0";
                                $linedata["token_invalid_reason"] = "Token $matches[1] was used before!";
                                        
                        } else {

			  // Check if token is valid with the current salt
			  if ($matches[1] == md5($linedata["worker_id"] . $mw_salt)) {
			  
                                $linedata["token_valid"] = "1";
                                $linedata["token"]       = $matches[1];
                                $token_list[] 	       = $matches[1];
			  
			  } else {
			  
			    $linedata["token_valid"] = "0";
                             $linedata["token_invalid_reason"] = "Token $matches[1] is not valid!";
			  
			  }
                        }
                }
		
                $linedata["worker_ip"]      = $row["IP"];
                $linedata["worker_country"] = $row["COUNTRY_CODE"];
		$linedata["timestamp_mw"]   = $row["FINISHED"];

                $return[] = $linedata;

        }

        return $return;
}

if ($mw_csvexport <> '') {
	
	$return = parseMicroworkersData($mw_csvexport, $mw_salt);

	foreach ($return as & $mw_worker) {

                $workerId = preg_replace("/[^a-zA-Z0-9-]/", "", $mw_worker["worker_id"]);

                $result = $batch->getWorker($workerId);

		// Apparently user did not do the test
		if (!$result) {
		
			// Check if the token is still valid
			if ($mw_worker["token_valid"])
			    $mw_worker["comment"] = "WARN! Token Valid, but user unknown!";
			else
			    $mw_worker["comment"] = "User did not do the test! (also, invalid token)";
			
			$mw_worker['finished'] = 0;
			
			continue;
		}

		// Check if user has finished the test
		if ($result['finished']) {

			// Check if token matches (if the worker specified a token)

			if ($mw_worker['token_valid'])
		                if ($mw_worker['token'] == $result['token'])
        		                $mw_worker['token_match'] = "1";
                		else {
                        		$mw_worker['token_match']   = "0";
					$mw_worker['token_should_be'] = $result['token'];
		                }
		}

		$mw_worker['timestamp'] = $result['timestamp'];
		$mw_worker['finished'] = $result['finished'];
	}

//	print_r($return);
}

?>

<form action="?" method="post">
	<fieldset>
		<legend>Batch Taken Evaluation</legend>
		<p>
		 <label for="mw_csvexport">Microworkers CSV export (including header etc)</label>
		 <textarea name="mw_csvexport" rows="20" cols="90"><? echo $mw_csvexport; ?></textarea>
		</p>
		<p>
		 <label for="mw_salt">Campaign MD5 salt</label>
		 <input name="mw_salt" size="32" value="<? echo $mw_salt; ?>"/>
		</p>
	</fieldset>
	
	<button>Search</button>
</form>


<?php if ($mw_csvexport <> ''): ?>
<h3>Result</h3>
<table class="meta">
	<?php if (is_array($return)): ?>

	<tr>
		<th>MW Worker ID</th>
		<th>Token Submitted</th>
		<th>Finished Batch?</th>
		<th>Token matches?</th>
		<th>Comment</th>
	</tr>

<?php
	foreach ($return AS $mw_worker) {
	
		echo("<tr>");
		echo("<td>".  $mw_worker["worker_id"] . "</td>");
	
                if ($mw_worker['token_valid'])
                        echo('<td>');
                else
                        echo('<td style="background-color: #ff0000">');
	
                echo(($mw_worker['token_valid'] ? (substr($mw_worker['token'],0,8).'...') : 'INVALID') . "</td>");

		if ($mw_worker['finished'])
			echo('<td>');
		else
			echo('<td style="background-color: #ff0000">');
                
		echo(($mw_worker['finished'] ? 'Yes' : 'No') . "</td>");

		if ($mw_worker['finished']) {

	                if ($mw_worker['token_match'])
        	                echo('<td>');
                	else
                        	echo('<td style="background-color: #ff0000">');

        	        echo(($mw_worker['token_match'] ? 'Yes' : ('No (should be '.$mw_worker['token_should_be'].')')) . "</td>");

		} else {
			
			echo('<td>-</td>');
		}

		if (isset($mw_worker["comment"])) {

			echo('<td>'. $mw_worker["comment"] .'</td>');
		} else {
			echo('<td></td>');
		}
		
		echo("<td></td>");
		echo("</tr>");

	}
?>
</table>

<h3>Token Only Check</h3>

<table class="meta">

        <tr>
                <th>MW Worker ID</th>
                <th>Token Sbumitted</th>
                <th>Token Calculated</th>
		<th>Match?</th>
        </tr>

<?php
        foreach ($return AS $mw_worker) {

                echo("<tr>");
                echo("<td>".  $mw_worker["worker_id"] . "</td>");

		echo("<td>". $mw_worker["proof_raw"]  ."</td>");

		echo("<td>". $mw_worker["token_shouldbe"]  ."</td>");

		
                if ($mw_worker["proof_found"] == $mw_worker["token_shouldbe"])
                        echo('<td>OK</td>');
                else
                        echo('<td style="background-color: #ff0000">INVALID</td>');

                echo("</tr>");

        }
?>

</table>
		
<?php else: ?>
	<tr>
		<th>Microworkers Export</th>
		<td><?= $query ?></td>
	</tr>
	<tr>
		<th>Result</th>
		<td>invalid file!</td>
	</tr>
<?php endif; ?>
</table>
<?php endif; ?>

