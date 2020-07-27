<html>
	<head>
		<title>Test Task</title>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<head>
	<body>

<?php
	class DateParser {
		function __construct(int $time) {
			$this->day = date("d.m.Y", $time);
			$this->hour = date("H", $time);
			$this->minute = date("i", $time);
			$this->timestamp = $time;
		}

		public $day;
		public $hour;
		public $minute;
		public $timestamp;

		public function toString() {

			return  "{$this->day} {$this->hour} год {$this->minute} хв";
		}

		public function dateDifference(DateParser $date) {
			$date1 = new DateTime();
			$date1->setTimestamp($this->timestamp);
			$date2 =  new DateTime();
			$date2->setTimestamp($date->timestamp);
			$interval = date_diff($date1, $date2);

			return $interval->format('%h год %i хв %s сек');
		} 

	}

	class App {
		function __construct() {}

		public static function render() {
			if( isset($_POST['submit']) ) {
				return self::renderResponse();
			} else {
				return self::renderForm();
			}
		}

		public static function renderForm() {
			$city = 'Lviv';
			$form = '
			<div class="container-fluid">
				<div class="row">
					<div class="col-12">
						<form action="" method="post">
							<div class="form-group">
								<label>
									Вкажіть місто
									<input type="text" name="city" placeholder="Місто" value="'.$city.'">
								</label>
								<button name="submit">Перевірити погоду</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			';

			return $form;
		}

		public static function renderWeather($response) {
			$city = $response->name;
			$country_code = $response->sys->country;
			$temperature = $response->main->temp;
			$presure = $response->main->pressure;
			$weather_description = $response->weather[0]->description;

			$sunrise = new DateParser($response->sys->sunrise);
			$sunset = new DateParser($response->sys->sunset);
			$day_time = $sunset->dateDifference($sunrise);

			$sunrise = $sunrise->toString();
			$sunset = $sunset->toString();

			$result = '
			<div class="container-fluid">
				<div class="row">
					<div class="col-6">
						<h1>'.$city.'('.$country_code.')</h1>
						<h2>Температура: '.$temperature.' <sup>о</sup>С</h2>
						<h3>Погода: '.$weather_description.'</h3>
					</div>
					<div class="col-6">
						<h3>Тиск: '.$presure.' hPa</h3>
						<h3>Схід сонця о: '.$sunrise.' </h3>
						<h3>Захід сонця о: '.$sunset.'</h3>
						<h3>Тривалість дня: '.$day_time.'</h3>
					</div>
				</div>	
			</div>
			';

			return $result;
		}


		public static function renderError($response) {
			$code = $response->cod;
			$message = $response->message;
			
			$result = '
			<div class="container-fluid">
				<div class="row">
					<div class="col-12">
						<h1>Some Error</h1>
						<h2>Error Code: '.$code.'</h2>
						<h3>Error message: '.$message.'</h3>
					</div>
				</div>	
			</div>
			';

			return $result;
		}

		public static function renderResponse() {
			$city = $_POST['city'];
			$API_key = '3a74df1ca285609f805c6dfc4ae45245';
			$options = [
				CURLOPT_URL => "api.openweathermap.org/data/2.5/weather?q={$city}&appid={$API_key}&units=metric&lang=ua",
				CURLOPT_RETURNTRANSFER => true
			];
			$curl_chanel = curl_init();
			curl_setopt_array($curl_chanel, $options);
			$response = curl_exec($curl_chanel);
			curl_close($curl_chanel);

			$response = json_decode($response);

			if($response->cod == 200) {
				return App::renderWeather($response);
			} else {
				return App::renderError($response);
			}
		}
	}

	echo App::render();
?>

	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<a href=''>Form</a>
			</div>
		</div>
	</div>

	</body>
</html>