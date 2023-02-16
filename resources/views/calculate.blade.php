<!DOCTYPE html>
<html>

<head>

	<!-- cdn for jQuery -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.3/jquery.min.js" integrity="sha512-STof4xm1wgkfm7heWqFJVn58Hm3EtS31XFaagaa8VMReCXAkQnJZ+jEy8PCC/iT18dFy95WcExNHFTqLyp72eQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

	<!-- cdn for bootstrap css -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

	<!-- for styling -->
	<style>
		table {
			border: 1px solid black;
			margin-left: auto;
			margin-right: auto;
		}

		input[type="button"] {
			width: 100%;
			padding: 20px 40px;
			background-color: green;
			color: white;
			font-size: 24px;
			font-weight: bold;
			border: none;
			border-radius: 5px;
		}

		input[type="text"] {
			padding: 20px 30px;
			font-size: 24px;
			font-weight: bold;
			border: none;
			border-radius: 5px;
			border: 2px solid black;
		}
	</style>
</head>
<!-- create table -->

<body>
	<table id="calcu">
		<tr>
			<td colspan="3"><input type="text" id="result"></td>
			<!-- clr() function will call clr to clear all value -->
			<td><input type="button" value="c" onclick="clr()" /> </td>
		</tr>
		<tr>
			<!-- create button and assign value to each button -->
			<!-- dis("1") will call function dis to display value -->
			<td><input type="button" value="1" onclick="dis('1')"> </td>
			<td><input type="button" value="2" onclick="dis('2')"> </td>
			<td><input type="button" value="3" onclick="dis('3')"> </td>
			<td><input type="button" value="/" onclick="dis('/')"> </td>
		</tr>
		<tr>
			<td><input type="button" value="4" onclick="dis('4')"> </td>
			<td><input type="button" value="5" onclick="dis('5')"> </td>
			<td><input type="button" value="6" onclick="dis('6')"> </td>
			<td><input type="button" value="*" onclick="dis('*')"> </td>
		</tr>
		<tr>
			<td><input type="button" value="7" onclick="dis('7')"> </td>
			<td><input type="button" value="8" onclick="dis('8')"> </td>
			<td><input type="button" value="9" onclick="dis('9')"> </td>
			<td><input type="button" value="-" onclick="dis('-')"> </td>
		</tr>
		<tr>
			<td><input type="button" value="0" onclick="dis('0')"> </td>
			<td><input type="button" value="." onclick="dis('.')"> </td>
			<!-- solve function call function solve to evaluate value -->
			<td><input type="button" value="=" onclick="solve()"> </td>

			<td><input type="button" value="+" onclick="dis('+')"> </td>
		</tr>
		<tr>
			<td><input type="button" id="saveBtn" value="Save"></td>
			<td><input type="button" id="HistoryBtn" value="History"></td>
			<td colspan="2"><input type="button" onclick="deleteRecord('all')" value="Clear History"></td>
		</tr>
	</table>

	<div id="historyContainer" class="p-5 col-7" style="display: none; margin-left: auto; margin-right: auto;">

		<table class="table">
			<thead class="thead-dark">
				<tr>
					<th scope="col">Equation</th>
					<th scope="col">Result</th>
					<th scope="col">Delete</th>
				</tr>
			</thead>
			<tbody id="historyDataBody">
			
			</tbody>
		</table>

	</div>

	<script>
		var previous_calculated_equation = null;

		// Function that display value
		function dis(val) {
			document.getElementById("result").value += val
		}

		//Enter key press event
		var cal = $('#calcu').val();
		cal.onkeyup = function(event) {
			if (event.keyCode === 13) {
				solve();
			}
		}

		//history btn event listner
		$('#HistoryBtn').click(function() {

			//getting dat if history is not shown
			if ($('#historyContainer').is(":hidden")) {
				getHistory()
			} else {

				$('#historyContainer').hide();
			}
		});

		//function to get history
		function getHistory() {
			$.ajax({
				method: 'get',
				url: 'api/getHistory',
				async: true,
				success: function(res) {

					if (res.status == 200) {

						historyLoad(res.data);
						$('#historyContainer').show();
					} else {

						// error acur
						alert(res.errorMessage);
					}
				},
				error: function(req, msg, obj) {

					// exception while runing api
					console.log('An error occured while executing a request');
					console.log('Error: ' + msg);
				}
			});
		}

		// save btn eventlistner
		$('#saveBtn').click(function() {

			if ($('#result').val()) {

				//api call to save the given equation
				$.ajax({
					method: 'POST',
					url: 'api/save',
					data: {
						'previousEquation': previous_calculated_equation,
						'userInputEquation': $('#result').val()
					},
					async: true,
					success: function(res) {

						if (res.status == 200) {

							if ($('#historyContainer').is(":visible")) {

								getHistory();
							}

							clr();
						} else {
							// error acur
							alert(res.errorMessage);

						}
					},
					error: function(req, msg, obj) {
						// exception while runing api
						console.log('An error occured while executing a request');
						console.log('Error: ' + msg);

					}
				});

			} else {

			}
		});

		// to load and show histoy body
		function historyLoad(data) {

			html = '<td colspan="2"><h3 class=" d-flex justify-content-center text-danger">No Record Found</h3></td>';
			if (data.length) {
				html = '';
				data.forEach(element => {
					equation_and_result = JSON.parse(element.value);
					html += `<tr>`;
					html += `<td>${equation_and_result.equation}</td>`;
					html += `<td>${equation_and_result.result}</td>`;
					html += `<td><button class='btn btn-outline-danger' onclick='deleteRecord(${element.id})'>Delete</button></td>`;
					html += `</tr>`;
				});
			}
			$('#historyDataBody').html(html);
		}

		//function to delete individual record
		function deleteRecord(id) {
			$.ajax({
				method: 'get',
				url: 'api/delete/' + id,
				async: true,
				success: function(res) {

					if (res.status == 200) {

						if ($('#historyContainer').is(":visible")) {

							getHistory();
						}

						$('#historyContainer').show();
					} else {

						// error acur
						alert(res.errorMessage);
					}
				},
				error: function(req, msg, obj) {

					// exception while runing api
					console.log('An error occured while executing a request');
					console.log('Error: ' + msg);
				}
			});
		}

		// Function that evaluates the digit and return result
		function solve() {
			let x = $('#result').val();

			//api call to solve the given equation
			$.ajax({
				method: 'POST',
				url: 'api/solve',
				data: {
					'equation': x
				},
				async: true,
				success: function(res) {

					if (res.status == 200) {

						//storing the current equation 
						previous_calculated_equation = x;
						//output of equation
						$('#result').val(res.data);

						if ($('#historyContainer').is(":visible")) {

							getHistory();
						}

					} else {
						// error acur
						alert(res.errorMessage);

					}
				},
				error: function(req, msg, obj) {
					// exception while runing api
					console.log('An error occured while executing a request');
					console.log('Error: ' + msg);

				}
			});

		}

		// Function that clear the display
		function clr() {
			$('#result').val('');
		}
	</script>
</body>

</html>