<?php

trait ActionStartPortal {
    public static function StartPortal($_postData) {
		OpLog::Log("Action: StartPortal");

		$_pageData = json_decode($_postData['pagedata'], true);

		$returnedCode = "";

		$_page = "Dashboard";
		$_accountid = 0;
		$_employeeid = 0;

		if (isset($_pageData['page'])) {
			$_page = $_pageData['page'];
		}

		if (isset($_pageData['accountid'])){
			$_accountid = $_pageData['accountid'];
		}

		if (isset($_pageData['employeeid'])){
			$_employeeid = $_pageData['employeeid'];
		}

		if (Actions::CheckSession()) {
			$returnedCode .= <<<HTML
				<script>
					
				</script>
			HTML;

			$getClockinUpdate = Actions::UpdatePunchDisplay([]);

			$returnedCode .= <<<HTML
			<script>
				var clockinUpdate = `$getClockinUpdate`;
				Action_UpdatePunchDisplayResponse(true, clockinUpdate);
			</script>
			<div id='topbar_container'>
				<div class='sitelogo'>
					<img src='img/logo1.png' width='400px'/>
				</div>
				<div class='topright_pane'>
					<div class='topbarbuttons'>
						<img src='img/user_green.png'/>
						<img src='img/help_green.png'/>
						<img class='open_settings_page' src='img/settings_green.png'/>
					</div>
					<div class='searchboxholder'>
						<input type='text' placeholder='Search'/><img src='img/search_gray.png'/>
					</div>
					<div class='topbarloginnote'>
						<span class='clockin_time'></span><span class='clockin_display'></span>
					</div>
				</div>
			</div>	
			<div id='leftpane_container'>
			HTML;
	
			$returnedCode .= Actions::GetMainMenuButtons();
	
			$returnedCode .= <<<HTML
			</div>
				<div id='rightpane_container'>
			HTML;
			
			$data = [];
			$data['accountid'] = $_accountid;
			$data['employeeid'] = $_employeeid;
			$data['pageid'] = $_page;

			$returnedCode .= Actions::LoadPage($data);
			$returnedCode .= "</div>";
		}
		else {
			$returnedCode .= <<<HTML
			<script>
				$('.input_login_password').keydown(function (event) {
					if (event.keyCode === 13) {
					event.preventDefault();
					var loginEmail = $('.input_login_email').val();
					var loginPassword = $('.input_login_password').val();
					StartSession(loginEmail, loginPassword);
					}
				});
				$('.input_login_email').keydown(function (event) {
					if (event.keyCode === 13) {
					event.preventDefault();
					var loginEmail = $('.input_login_email').val();
					var loginPassword = $('.input_login_password').val();
					StartSession(loginEmail, loginPassword);
					}
				});
				$('.input_login_button').click(function() {
					var loginEmail = $('.input_login_email').val();
					var loginPassword = $('.input_login_password').val();
					StartSession(loginEmail, loginPassword);
				});
			</script>
			HTML;

			$returnedCode .= <<<HTML
			<div class='login_wrapper_bg'>
				<video autoplay muted loop id="backgroudvideo">
					<source src="img/bgvideo1.mp4" type="video/mp4">
				</video>
				<div class='login_page_backer'>
					<img src='img/logo6.png' width='450px' style='margin-left:8px;'/>
					<div class='login_page_content'>
					<div class='formsection_line' style='margin-bottom:10px;'>
						<input type='text' placeholder='Email' class='input_login_email formsection_input_2'/>
					</div>
					<div class='formsection_line' style='margin-bottom:10px;'>
						<input type='password' placeholder='Password' class='input_login_password formsection_input_2'/>
					</div>
					<div class='formsection_line_centered' style='margin-bottom:20px;'>
						<div class='input_login_button button_type_2' style='padding-top:7px;padding-bottom:7px;padding-left:45px;padding-right:45px'>Log In</div>
					</div>
					<div class='formsection_line_centered' style='margin-bottom:20px;'>
						<div class='formsection_input_centered_text_button'>Trouble Logging In?</div>
					</div>
					<div class='formsection_line_centered'>
						<div class='formsection_input_centered_text'>This page is for current account holders. To set up a new account, reach out to your service manager.</div>
					</div>
				</div>
			</div>
			HTML;
		}

		return $returnedCode;
	}
}

?>