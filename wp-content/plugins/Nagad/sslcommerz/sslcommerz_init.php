<?php
/*
** Template Name: SSLCommerz
*/
session_start();
get_header();
global $wp;
$img = get_template_directory_uri() . "/SSLCommerz.png";
$store_id = get_option('store');
$store_pass = get_option('password');
$mode = get_option('testmode');
$current_url = home_url(add_query_arg(array(), $wp->request));
$_SESSION['CUS_HISTORY']['SITE_URL'] = $current_url;

if ($mode == 'on') {
	$request_url = 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php';
} else {
	$request_url = 'https://securepay.sslcommerz.com/gwprocess/v4/api.php';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST['proceed'])) {
		$post_data = array();
		$post_data['store_id'] = $store_id;
		$post_data['store_passwd'] = $store_pass;
		$post_data['currency'] = $_POST['currency'];
		$post_data['total_amount'] = str_replace(',', '', $_POST['total_amount']);
		$post_data['tran_id'] = $trxid = "sslc" . uniqid();

		$post_data['cus_name'] = $_POST['cus_name'];
		$post_data['cus_email'] = $_POST['cus_email'];
		$post_data['cus_phone'] = $_POST['cus_phone'];
		$post_data['cus_add1'] = $_POST['cus_add1'];
		$post_data['cus_country'] = $_POST['cus_country'];
		$post_data['cus_city'] = $_POST['cus_city'];
		$post_data['cus_postcode'] = $_POST['cus_postcode'];
		$post_data['product_category'] = 'Car';
		$post_data['product_name'] = $_POST['product_name'];
		$post_data['product_profile'] = $_POST['product_profile'];
		$post_data['emi_option'] = '0';
		$post_data['shipping_method'] = 'No';
		$post_data['num_of_item'] = '1';


		$post_data['success_url'] = get_site_url() . "/index.php?sslcsuccess";
		$post_data['fail_url'] = get_site_url() . "/index.php?sslcfail";
		$post_data['cancel_url'] = get_site_url() . "/index.php?sslccancel";

		$handle = curl_init();
		curl_setopt($handle, CURLOPT_URL, $request_url);
		curl_setopt($handle, CURLOPT_POST, 1);
		curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		$content = curl_exec($handle);
		$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);


		if ($code == 200 && !(curl_errno($handle))) {
			curl_close($handle);
			$sslcommerzResponse = $content;
			# PARSE THE JSON RESPONSE 
			$sslcz = json_decode($sslcommerzResponse, true);
			if (isset($sslcz['status']) && $sslcz['status'] == 'SUCCESS') {

				if (isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL'] != '') {
					if (isset($trxid)) {
						global $wpdb;
						$table_name = $wpdb->prefix . 'sslcommerz_payment';
						$field_Data = array(
							'trxid' => $trxid,
							'tran_status' => 'Pending',
							'card_type' => $_POST['card_type'],
							'total_amount' => $_POST['total_amount'],
							'product_name' => $_POST['product_name'],
							'cus_name' => $_POST['cus_name'],
							'cus_email' => $_POST['cus_email'],
							'cus_phone' => $_POST['cus_phone'],
							'cus_address' => $_POST['cus_add1'],
							'cus_country' => $_POST['cus_country'],
							'cus_city' => $_POST['cus_city'],
							'cus_postcode' => $_POST['cus_postcode'],
						);
						// $field_Data_type = array('%s', '%s', '%s', '%s', '%f', '%s', '%s','%s','%s','%s');
						$wpdb->insert($table_name, $field_Data);
					}
					$_SESSION['CUS_HISTORY']['TRANID'] = $trxid;
					$_SESSION['CUS_HISTORY']['CUS_NAME'] = $_POST['cus_name'];
					$_SESSION['CUS_HISTORY']['CUS_EMAIL'] = $_POST['cus_email'];
					$_SESSION['CUS_HISTORY']['CUS_PHONE'] = $_POST['cus_phone'];
					$_SESSION['CUS_HISTORY']['CUS_ADD'] = $_POST['cus_add'];
					$_SESSION['CUS_HISTORY']['CUS_COUNTRY'] = $_POST['cus_country'];
					$_SESSION['CUS_HISTORY']['CUS_STATE'] = $_POST['cus_city'];
					$_SESSION['CUS_HISTORY']['CUS_CURRENCY'] = $_POST['currency'];

					echo '<meta http-equiv="refresh" content="0; url=' . $sslcz['GatewayPageURL'] . '" />';
					#header("Location: " . $sslcz['GatewayPageURL']);
					exit;
				} else {
					echo "No redirect URL found!";
				}
			} else {
				var_dump($sslcz);
				exit;
				echo "Invalid Credential!";
			}
		} else {
			curl_close($handle);
			echo "FAILED TO CONNECT WITH SSLCOMMERZ API";
			exit;
		}
	}
}
?>
<link rel="stylesheet" href="//www.gdassist.com/wp-content/plugins/GDAssist Custom SSLCommerz-APIv4-Hosted/css/bootstrap.css">
<link rel="stylesheet" href="//www.gdassist.com/wp-content/plugins/GDAssist Custom SSLCommerz-APIv4-Hosted/css/select2.min.css">
<?php
if (empty($_SESSION['CUS_HISTORY']['GET_STATUS'])) {
	?>
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="form-group">
					<br><br><br><br>
					<h2>Payment Method:</h2>
					<img src="<?php echo $img; ?>"><br>
					<label>
						<h4><input type="radio" checked="checked" value="sslcommerz" name="payment-mode"><?php echo get_option('title'); ?></h4>
						<p><?php echo get_option('description'); ?></p>
					</label>
				</div>
				<hr>
				<h2>Customer Information:</h2><br>
				<form name="frm1" id="frm1" method="POST" action="<?php echo $current_url; ?>">

					<div class="form-group">
						<label style="color: #000000" for="name" class="col-sm-3">Customer Name <span style="color:red;font-size:22px;">*</span></label>
						<div class="col-sm-9">
							<input name="cus_name" type="text" class="form-control" placeholder="Enter Your Full Name" required autofocus style="border-radius: 0px;">
						</div>
					</div><br><br><br>

					<div class="form-group">
						<label style="color: #000000" for="email" class="col-sm-3">Customer Email <span style="color:red;font-size:22px;">*</span></label>
						<div class="col-sm-9">
							<input name="cus_email" type="text" class="form-control" placeholder="Enter Your Email Address" required style="border-radius: 0px;">
						</div>
					</div><br><br>
					<div class="form-group">
						<label style="color: #000000" for="phone" class="col-sm-3">Phone Number <span style="color:red;font-size:22px;">*</span></label>
						<div class="col-sm-9">
							<input name="cus_phone" type="text" class="form-control" placeholder="Enter Your Phone Number" required style="border-radius: 0px;">
						</div>
					</div>
					<br><br>
					<div class="form-group">
						<label style="color: #000000" for="product_name" class="col-sm-3 product_name">Product <span style="color:red;font-size:22px;">*</span></label>
						<div class="col-sm-9">
							<select required name="product_name" class="form-control product-list" style="border-radius: 0px;">
								<option value=""></option>
								<option value"Air Ticket">Air Ticket</option>
								<option value"Hotel">Hotel</option>
								<option value"Visa fee">Visa fee</option>
								<option value"Health Card">Health Card</option>
								<option value"Appointments">Appointments</option>
								<option value"Air Ambulance">Air Ambulance</option>
								<option value"Tele Video Consultation">Tele Video Consultation</option>
								<option value"Second Medical Opinion (SMO)">Second Medical Opinion (SMO)</option>
							</select>
						</div>
					</div><br><br>
					<div class="form-group">
						<label style="color: #000000" for="total_amount" class="col-sm-3">Currency/Amount <span style="color:red;font-size:22px;">*</span></label>
						<div class="col-sm-6">
							<input name="total_amount" id="total_amount" type="text" class="form-control" placeholder="Amount" style="border-radius: 0px;" required>
						</div>
						<div class="col-sm-3">
							<select required="" class="form-control currency" style="border-radius: 0px;" name="currency">
								<option value="BDT">BDT</option>
								<option value="EUR">EUR</option>
								<option value="GBP">GBP</option>
								<option value="AUD">AUD</option>
								<option value="USD">USD</option>
								<option value="CAD">CAD</option>
							</select>
						</div>
					</div><br><br>
					<input type="hidden" id="product_profile" name="product_profile" value="general">

					<div class="form-group">
						<label style="color: #000000" for="address" class="col-sm-3">Address <span style="color:red;font-size:22px;">*</span></label>
						<div class="col-sm-9">
							<textarea name="cus_add1" id="cus_add1" rows="2" class="form-control" placeholder="Enter Street Address" required style="border-radius: 0px;"></textarea>
						</div>
					</div><br><br><br>

					<div class="form-group">
						<label style="color: #000000" for="cus_postcode" class="col-sm-3">Postcode <span style="color:red;font-size:22px;">*</span></label>
						<div class="col-sm-9">
							<input name="cus_postcode" type="text" class="form-control" placeholder="Enter Postcode" required style="border-radius: 0px;">
						</div>
					</div><br><br><br>

					<div class="form-group">
						<label style="color: #000000" for="cus_city" class="col-sm-3">City <span style="color:red;font-size:22px;">*</span></label>
						<div class="col-sm-9">
							<input name="cus_city" type="text" class="form-control" placeholder="Enter City" required style="border-radius: 0px;">
						</div>
					</div><br><br>

					<div class="form-group">
						<label style="color: #000000" for="country" class="col-sm-3">Country <span style="color:red;font-size:22px;">*</span></label>
						<div class="col-sm-9">
							<select required name="cus_country" class="form-control cus_country" style="border-radius: 0px;">
								<option value="Afghanistan">Afghanistan</option>
								<option value="Albania">Albania</option>
								<option value="Algeria">Algeria</option>
								<option value="American Samoa">American Samoa</option>
								<option value="Andorra">Andorra</option>
								<option value="Angola">Angola</option>
								<option value="Anguilla">Anguilla</option>
								<option value="Antartica">Antarctica</option>
								<option value="Antigua and Barbuda">Antigua and Barbuda</option>
								<option value="Argentina">Argentina</option>
								<option value="Armenia">Armenia</option>
								<option value="Aruba">Aruba</option>
								<option value="Australia">Australia</option>
								<option value="Austria">Austria</option>
								<option value="Azerbaijan">Azerbaijan</option>
								<option value="Bahamas">Bahamas</option>
								<option value="Bahrain">Bahrain</option>
								<option value="Bangladesh" selected>Bangladesh</option>
								<option value="Barbados">Barbados</option>
								<option value="Belarus">Belarus</option>
								<option value="Belgium">Belgium</option>
								<option value="Belize">Belize</option>
								<option value="Benin">Benin</option>
								<option value="Bermuda">Bermuda</option>
								<option value="Bhutan">Bhutan</option>
								<option value="Bolivia">Bolivia</option>
								<option value="Bosnia and Herzegowina">Bosnia and Herzegowina</option>
								<option value="Botswana">Botswana</option>
								<option value="Bouvet Island">Bouvet Island</option>
								<option value="Brazil">Brazil</option>
								<option value="British Indian Ocean Territory">British Indian Ocean Territory</option>
								<option value="Brunei Darussalam">Brunei Darussalam</option>
								<option value="Bulgaria">Bulgaria</option>
								<option value="Burkina Faso">Burkina Faso</option>
								<option value="Burundi">Burundi</option>
								<option value="Cambodia">Cambodia</option>
								<option value="Cameroon">Cameroon</option>
								<option value="Canada">Canada</option>
								<option value="Cape Verde">Cape Verde</option>
								<option value="Cayman Islands">Cayman Islands</option>
								<option value="Central African Republic">Central African Republic</option>
								<option value="Chad">Chad</option>
								<option value="Chile">Chile</option>
								<option value="China">China</option>
								<option value="Christmas Island">Christmas Island</option>
								<option value="Cocos Islands">Cocos (Keeling) Islands</option>
								<option value="Colombia">Colombia</option>
								<option value="Comoros">Comoros</option>
								<option value="Congo">Congo</option>
								<option value="Congo">Congo, the Democratic Republic of the</option>
								<option value="Cook Islands">Cook Islands</option>
								<option value="Costa Rica">Costa Rica</option>
								<option value="Cota D'Ivoire">Cote d'Ivoire</option>
								<option value="Croatia">Croatia (Hrvatska)</option>
								<option value="Cuba">Cuba</option>
								<option value="Cyprus">Cyprus</option>
								<option value="Czech Republic">Czech Republic</option>
								<option value="Denmark">Denmark</option>
								<option value="Djibouti">Djibouti</option>
								<option value="Dominica">Dominica</option>
								<option value="Dominican Republic">Dominican Republic</option>
								<option value="East Timor">East Timor</option>
								<option value="Ecuador">Ecuador</option>
								<option value="Egypt">Egypt</option>
								<option value="El Salvador">El Salvador</option>
								<option value="Equatorial Guinea">Equatorial Guinea</option>
								<option value="Eritrea">Eritrea</option>
								<option value="Estonia">Estonia</option>
								<option value="Ethiopia">Ethiopia</option>
								<option value="Falkland Islands">Falkland Islands (Malvinas)</option>
								<option value="Faroe Islands">Faroe Islands</option>
								<option value="Fiji">Fiji</option>
								<option value="Finland">Finland</option>
								<option value="France">France</option>
								<option value="France Metropolitan">France, Metropolitan</option>
								<option value="French Guiana">French Guiana</option>
								<option value="French Polynesia">French Polynesia</option>
								<option value="French Southern Territories">French Southern Territories</option>
								<option value="Gabon">Gabon</option>
								<option value="Gambia">Gambia</option>
								<option value="Georgia">Georgia</option>
								<option value="Germany">Germany</option>
								<option value="Ghana">Ghana</option>
								<option value="Gibraltar">Gibraltar</option>
								<option value="Greece">Greece</option>
								<option value="Greenland">Greenland</option>
								<option value="Grenada">Grenada</option>
								<option value="Guadeloupe">Guadeloupe</option>
								<option value="Guam">Guam</option>
								<option value="Guatemala">Guatemala</option>
								<option value="Guinea">Guinea</option>
								<option value="Guinea-Bissau">Guinea-Bissau</option>
								<option value="Guyana">Guyana</option>
								<option value="Haiti">Haiti</option>
								<option value="Heard and McDonald Islands">Heard and Mc Donald Islands</option>
								<option value="Holy See">Holy See (Vatican City State)</option>
								<option value="Honduras">Honduras</option>
								<option value="Hong Kong">Hong Kong</option>
								<option value="Hungary">Hungary</option>
								<option value="Iceland">Iceland</option>
								<option value="India">India</option>
								<option value="Indonesia">Indonesia</option>
								<option value="Iran">Iran (Islamic Republic of)</option>
								<option value="Iraq">Iraq</option>
								<option value="Ireland">Ireland</option>
								<option value="Israel">Israel</option>
								<option value="Italy">Italy</option>
								<option value="Jamaica">Jamaica</option>
								<option value="Japan">Japan</option>
								<option value="Jordan">Jordan</option>
								<option value="Kazakhstan">Kazakhstan</option>
								<option value="Kenya">Kenya</option>
								<option value="Kiribati">Kiribati</option>
								<option value="Democratic People's Republic of Korea">Korea, Democratic People's Republic of</option>
								<option value="Korea">Korea, Republic of</option>
								<option value="Kuwait">Kuwait</option>
								<option value="Kyrgyzstan">Kyrgyzstan</option>
								<option value="Lao">Lao People's Democratic Republic</option>
								<option value="Latvia">Latvia</option>
								<option value="Lebanon">Lebanon</option>
								<option value="Lesotho">Lesotho</option>
								<option value="Liberia">Liberia</option>
								<option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option>
								<option value="Liechtenstein">Liechtenstein</option>
								<option value="Lithuania">Lithuania</option>
								<option value="Luxembourg">Luxembourg</option>
								<option value="Macau">Macau</option>
								<option value="Macedonia">Macedonia, The Former Yugoslav Republic of</option>
								<option value="Madagascar">Madagascar</option>
								<option value="Malawi">Malawi</option>
								<option value="Malaysia">Malaysia</option>
								<option value="Maldives">Maldives</option>
								<option value="Mali">Mali</option>
								<option value="Malta">Malta</option>
								<option value="Marshall Islands">Marshall Islands</option>
								<option value="Martinique">Martinique</option>
								<option value="Mauritania">Mauritania</option>
								<option value="Mauritius">Mauritius</option>
								<option value="Mayotte">Mayotte</option>
								<option value="Mexico">Mexico</option>
								<option value="Micronesia">Micronesia, Federated States of</option>
								<option value="Moldova">Moldova, Republic of</option>
								<option value="Monaco">Monaco</option>
								<option value="Mongolia">Mongolia</option>
								<option value="Montserrat">Montserrat</option>
								<option value="Morocco">Morocco</option>
								<option value="Mozambique">Mozambique</option>
								<option value="Myanmar">Myanmar</option>
								<option value="Namibia">Namibia</option>
								<option value="Nauru">Nauru</option>
								<option value="Nepal">Nepal</option>
								<option value="Netherlands">Netherlands</option>
								<option value="Netherlands Antilles">Netherlands Antilles</option>
								<option value="New Caledonia">New Caledonia</option>
								<option value="New Zealand">New Zealand</option>
								<option value="Nicaragua">Nicaragua</option>
								<option value="Niger">Niger</option>
								<option value="Nigeria">Nigeria</option>
								<option value="Niue">Niue</option>
								<option value="Norfolk Island">Norfolk Island</option>
								<option value="Northern Mariana Islands">Northern Mariana Islands</option>
								<option value="Norway">Norway</option>
								<option value="Oman">Oman</option>
								<option value="Pakistan">Pakistan</option>
								<option value="Palau">Palau</option>
								<option value="Panama">Panama</option>
								<option value="Papua New Guinea">Papua New Guinea</option>
								<option value="Paraguay">Paraguay</option>
								<option value="Peru">Peru</option>
								<option value="Philippines">Philippines</option>
								<option value="Pitcairn">Pitcairn</option>
								<option value="Poland">Poland</option>
								<option value="Portugal">Portugal</option>
								<option value="Puerto Rico">Puerto Rico</option>
								<option value="Qatar">Qatar</option>
								<option value="Reunion">Reunion</option>
								<option value="Romania">Romania</option>
								<option value="Russia">Russian Federation</option>
								<option value="Rwanda">Rwanda</option>
								<option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
								<option value="Saint LUCIA">Saint LUCIA</option>
								<option value="Saint Vincent">Saint Vincent and the Grenadines</option>
								<option value="Samoa">Samoa</option>
								<option value="San Marino">San Marino</option>
								<option value="Sao Tome and Principe">Sao Tome and Principe</option>
								<option value="Saudi Arabia">Saudi Arabia</option>
								<option value="Senegal">Senegal</option>
								<option value="Seychelles">Seychelles</option>
								<option value="Sierra">Sierra Leone</option>
								<option value="Singapore">Singapore</option>
								<option value="Slovakia">Slovakia (Slovak Republic)</option>
								<option value="Slovenia">Slovenia</option>
								<option value="Solomon Islands">Solomon Islands</option>
								<option value="Somalia">Somalia</option>
								<option value="South Africa">South Africa</option>
								<option value="South Georgia">South Georgia and the South Sandwich Islands</option>
								<option value="Span">Spain</option>
								<option value="SriLanka">Sri Lanka</option>
								<option value="St. Helena">St. Helena</option>
								<option value="St. Pierre and Miguelon">St. Pierre and Miquelon</option>
								<option value="Sudan">Sudan</option>
								<option value="Suriname">Suriname</option>
								<option value="Svalbard">Svalbard and Jan Mayen Islands</option>
								<option value="Swaziland">Swaziland</option>
								<option value="Sweden">Sweden</option>
								<option value="Switzerland">Switzerland</option>
								<option value="Syria">Syrian Arab Republic</option>
								<option value="Taiwan">Taiwan, Province of China</option>
								<option value="Tajikistan">Tajikistan</option>
								<option value="Tanzania">Tanzania, United Republic of</option>
								<option value="Thailand">Thailand</option>
								<option value="Togo">Togo</option>
								<option value="Tokelau">Tokelau</option>
								<option value="Tonga">Tonga</option>
								<option value="Trinidad and Tobago">Trinidad and Tobago</option>
								<option value="Tunisia">Tunisia</option>
								<option value="Turkey">Turkey</option>
								<option value="Turkmenistan">Turkmenistan</option>
								<option value="Turks and Caicos">Turks and Caicos Islands</option>
								<option value="Tuvalu">Tuvalu</option>
								<option value="Uganda">Uganda</option>
								<option value="Ukraine">Ukraine</option>
								<option value="United Arab Emirates">United Arab Emirates</option>
								<option value="United Kingdom">United Kingdom</option>
								<option value="United States">United States</option>
								<option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option>
								<option value="Uruguay">Uruguay</option>
								<option value="Uzbekistan">Uzbekistan</option>
								<option value="Vanuatu">Vanuatu</option>
								<option value="Venezuela">Venezuela</option>
								<option value="Vietnam">Viet Nam</option>
								<option value="Virgin Islands (British)">Virgin Islands (British)</option>
								<option value="Virgin Islands (U.S)">Virgin Islands (U.S.)</option>
								<option value="Wallis and Futana Islands">Wallis and Futuna Islands</option>
								<option value="Western Sahara">Western Sahara</option>
								<option value="Yemen">Yemen</option>
								<option value="Yugoslavia">Yugoslavia</option>
								<option value="Zambia">Zambia</option>
								<option value="Zimbabwe">Zimbabwe</option>
							</select>
						</div>
					</div><br><br>


					<div class="form-group">
						<div class="col-sm-8">
							<div class="checkbox">
								<label><input type="checkbox" value="" required>By clicking Proceed, you will agree to our <a href="">Terms & Condition</a></label>
							</div>
						</div>
					</div><br><br>

					<div class="form-group">
						<div class="col-sm-12 pull-right">
							<input name="proceed" type="submit" class="btn btn-primary btn-block" value="Proceed to Payment">
						</div>
					</div><br><br><br><br>

				</form>
			</div>
		</div>
	</div>
<?php } elseif ($_SESSION['CUS_HISTORY']['GET_STATUS'] == 'VALID' || $_SESSION['CUS_HISTORY']['GET_STATUS'] == '
VALIDATED') {
	?>
	<br>
	<div class="row">
		<div class="col-md-6 col-md-offset-3">

			<div class="text-success">
				<center>
					<h2>We have received your payment successfully</h2>
				</center>
			</div>
			<br><br>
			<div>
				<table class="table table-striped table-hover">
					<tr>
						<td class="text-right"><b>Transaction ID:</b> </td>
						<td><?php echo $_SESSION['CUS_HISTORY']['TRANID']; ?></td>
					</tr>
					<tr>
						<td class="text-right"><b>Customer Name:</b></td>
						<td><?php echo $_SESSION['CUS_HISTORY']['CUS_NAME']; ?></td>
					</tr>
					<tr>
						<td class="text-right"><b>Phone:</b></td>
						<td><?php echo $_SESSION['CUS_HISTORY']['CUS_PHONE']; ?></td>
					</tr>
					<tr>
						<td class="text-right"><b>Email:</b></td>
						<td><?php echo $_SESSION['CUS_HISTORY']['CUS_EMAIL']; ?></td>
					</tr>
					<tr>
						<td class="text-right"><b>Amount:</b></td>
						<td><?php echo $_SESSION['CUS_HISTORY']['AMOUNTS'] . " BDT"; ?></td>
					</tr>
					<tr>
						<td class="text-right"><b>Status:</b></td>
						<td><?php echo $_SESSION['CUS_HISTORY']['DATA_STATUS']; ?></td>
					</tr>
				</table>
				<br><br>
			</div>
		</div>
	</div>
<?php
} elseif ($_SESSION['CUS_HISTORY']['GET_STATUS'] == 'FAILED') {
	?>
	<br><br><br><br><br><br>
	<div class="row">
		<div class="col-md-6 col-md-offset-3">

			<div class="text-danger">
				<center>
					<h2>Sadly, your payment failed.</h2>
				</center>
			</div>
            <br><br>
			<div>
				<table class="table table-striped table-hover">
					<tr>
						<td class="text-right"><b>Transaction ID:</b> </td>
						<td><?php echo $_SESSION['CUS_HISTORY']['TRANID']; ?></td>
					</tr>
					<tr>
						<td class="text-right"><b>Customer Name:</b></td>
						<td><?php echo $_SESSION['CUS_HISTORY']['CUS_NAME']; ?></td>
					</tr>
					<tr>
						<td class="text-right"><b>Phone:</b></td>
						<td><?php echo $_SESSION['CUS_HISTORY']['CUS_PHONE']; ?></td>
					</tr>
					<tr>
						<td class="text-right"><b>Email:</b></td>
						<td><?php echo $_SESSION['CUS_HISTORY']['CUS_EMAIL']; ?></td>
					</tr>
					<tr>
						<td class="text-right"><b>Amount:</b></td>
						<td><?php echo $_SESSION['CUS_HISTORY']['AMOUNTS'] . " BDT"; ?></td>
					</tr>
					<tr>
						<td class="text-right"><b>Status:</b></td>
						<td><?php echo $_SESSION['CUS_HISTORY']['DATA_STATUS']; ?></td>
					</tr>
				</table>
				<br><br>
			</div>
		</div>
	</div>
	<br><br><br><br><br><br>
<?php
} elseif ($_SESSION['CUS_HISTORY']['GET_STATUS'] == 'CANCELLED') {
	?>
	<br><br><br><br><br><br>
	<div class="row">
		<div class="col-md-6 col-md-offset-3">
			<div class="text-danger">
				<center>
					<h2>Your payment has been canceled</h2>
				</center>
			</div>
            <br><br>
			<div>
				<table class="table table-striped table-hover">
					<tr>
						<td class="text-right"><b>Transaction ID:</b> </td>
						<td><?php echo $_SESSION['CUS_HISTORY']['TRANID']; ?></td>
					</tr>
					<tr>
						<td class="text-right"><b>Customer Name:</b></td>
						<td><?php echo $_SESSION['CUS_HISTORY']['CUS_NAME']; ?></td>
					</tr>
					<tr>
						<td class="text-right"><b>Phone:</b></td>
						<td><?php echo $_SESSION['CUS_HISTORY']['CUS_PHONE']; ?></td>
					</tr>
					<tr>
						<td class="text-right"><b>Email:</b></td>
						<td><?php echo $_SESSION['CUS_HISTORY']['CUS_EMAIL']; ?></td>
					</tr>
					<tr>
						<td class="text-right"><b>Amount:</b></td>
						<td><?php echo $_SESSION['CUS_HISTORY']['AMOUNTS'] . " BDT"; ?></td>
					</tr>
					<tr>
						<td class="text-right"><b>Status:</b></td>
						<td><?php echo $_SESSION['CUS_HISTORY']['DATA_STATUS']; ?></td>
					</tr>
				</table>
				<br><br>
			</div>
		</div>
	</div>
	<br><br><br><br><br><br>
<?php
}
?>
<script src="//www.gdassist.com/wp-content/plugins/GDAssist Custom SSLCommerz-APIv4-Hosted/js/jquery-3.4.1.min.js"></script>
<script src="//www.gdassist.com/wp-content/plugins/GDAssist Custom SSLCommerz-APIv4-Hosted/js/select2.min.js"></script>
<script>
	$(document).ready(function() {
		$('.product-list').select2({
			placeholder: "Please Select Desired Product",
			allowClear: true
		});
		$('.currency').select2({
			placeholder: "Please Select Desired Product",
		});
		$('.cus_country').select2({
			placeholder: "Please Select Desired Product",
		});
	});
</script>
<?php
unset($_SESSION['CUS_HISTORY']['GET_STATUS']);
get_footer(); ?>