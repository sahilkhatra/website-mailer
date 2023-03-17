<?php
    session_start();
    if(isset($_GET['t'])){
        $servername = "localhost";
        $username = "root";
        $password = '';
        $db_name = "lab4_messageassignment";
        $conn = new mysqli($servername, $username, $password,$db_name);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        if($_GET['t'] == "register"){
            $sendvalue['status'] = false;
            $sendvalue['message'] = "";
            $password =  $_POST["password"];
            $uppercase = preg_match('@[A-Z]@', $password);
            $lowercase = preg_match('@[a-z]@', $password);
            $number    = preg_match('@[0-9]@', $password);
            $specialChars = preg_match('@[^\w]@', $password);

            if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
                $sendvalue['message'] = 'Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.';
            }
            else{
                $sql = "SELECT * FROM users WHERE username = '".$_POST["username"]."'";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) == 0) {
                    $userpassword = hash("sha512", $_POST["password"]);
                    $sql = "INSERT INTO users (name, email, username, password) VALUES ('".$_POST["fullname"]."', '".$_POST["email"]."', '".$_POST["username"]."', '".$userpassword."')";
                    if (mysqli_query($conn, $sql)) {
                        $sendvalue['message'] = "Register successfully";
                        $sendvalue['status'] = true;
                    }
                    else {
                        $sendvalue['message'] = "Register failed: ".mysqli_error($conn);
                    }
                }
                else{
                    $sendvalue['message'] = "Username already available";
                }
                mysqli_close($conn);
            }

            echo json_encode($sendvalue);
        }
        else if($_GET['t'] == "login"){
            $sendvalue['status'] = false;
            $sendvalue['message'] = "";
            $sql = "SELECT * FROM users WHERE username = '".$_POST["username"]."'";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    $userpassword = hash("sha512", $_POST["password"]);
                    if($row["password"] == $userpassword){
                        $_SESSION["login_data"] = $row;
                        $sendvalue['message'] = "Login Successfully";
                        $sendvalue['status'] = true;
                    }
                    else{
                        $sendvalue['message'] = "Password does not match";
                    }
                }
            }
            else{
                $sendvalue['message'] = "Username not found";
            }
            echo json_encode($sendvalue);
        }
        else if($_GET['t'] == "message"){
            $sendvalue['status'] = false;
            $sendvalue['message'] = ""; 
            $sendvalue['message'] = ""; 


            $sql = "INSERT INTO messages (email, datetime, message) VALUES ('".$_POST["email"]."', '".$_POST["date"]." ".$_POST["time"]."', '".$_POST["message"]."')";
            if (mysqli_query($conn, $sql)) {
                $sendvalue['message'] = "Message scheduled successfully";
                $sendvalue['status'] = true;
            }
            else {
                $sendvalue['message'] = "Submit failed: ".mysqli_error($conn);
            }

            echo json_encode($sendvalue);
        }
        else if($_GET['t'] == 'logout'){
            session_destroy();
        }
        else{
            $sendvalue['status'] = false;
            $sendvalue['message'] = "Try Again";

            echo json_encode($sendvalue);
        }

        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages Assignment</title>
    <style>
        * {
            margin:0;
            padding:0;
        }
        body{
            background: #00b0c0;
            font-family: sans-serif;
        }
        .row {
            display: flex;
            padding: 0px 100px;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .col-50 {
            width: 50%;
        }
        .form-div {
            background: white;
            padding: 50px 60px;
            border-radius: 16px;
        }
        .form-div .form-heading {
            text-align: center;
            font-size: 24px;
            padding-bottom: 20px;
        }
        .form-div .form-controller {
            display: flex;
            margin: 12px auto;
        }
        .form-div .form-controller .form-label {
            width: 150px;
            text-align: right;
            padding-right: 12px;
            margin: 5px 0;
            font-size: 15px;
            font-weight: bold;
        }
        .form-div .form-controller div {
            width: 100%;
            padding: 0px 5px;
        }
        .form-div .form-controller .form-label-checkbox {
            width: 22%;
            text-align: right;
            padding-right: 12px;
            margin: 5px 0;
            font-size: 15px;
            font-weight: bold;
        }
        .form-div .form-controller .form-field {
            width: 100%;
            padding: 10px 5px;
            font-size: 12px;
            border-radius: 6px;
            border: 1px solid #000000;
            margin-bottom: 5px;
        }
        .form-div .form-controller .submit-btn {
            background: #ff4a49;
            color: white;
            padding: 10px 25px;
            font-size: 18px;
            border-radius: 6px;
            cursor: pointer;
            border: none;
            margin: 0 auto;
            display: block;
        }
        .form-div .form-controller .error {
            color:red;
        }
    </style>
</head>
<body>
    <div class="row">
        <div class="col-50">
            <?php
                if(!isset($_SESSION['login_data'])){
            ?>
            <div id="register-div" style="display:none" >
                <div class="form-div">
                    <form action="#" id="registerForm" method="post">
                        <h4 class="form-heading">Register</h4>
                        <div class="form-controller" id="req_fullname">
                            <label for="" class="form-label">Full Name</label>
                            <div>
                                <input type="text" name="fullname" class="form-field" required>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="form-controller" id="req_email">
                            <label for="" class="form-label">Email</label>
                            <div>
                                <input type="email" name="email" class="form-field" required>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="form-controller" id="req_username">
                            <label for="" class="form-label">Username</label>
                            <div>
                                <input type="text" name="username" class="form-field" required>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="form-controller" id="req_password">
                            <label for="" class="form-label">Password</label>
                            <div>
                                <input type="password" name="password" class="form-field" required id="passwordval">
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="form-controller" style="margin: 25px auto 20px;" id="req_checkbox">
                            <label for="" class="form-label-checkbox"></label>
                            <input type="checkbox" name="terms" class="form-field-checkbox" required>
                            <span style="font-size: 13px;padding-left: 9px;">
                                I accept the <b style="color: #00b3c2;">Terms of user</b>
                                <span class="error"></span>
                            </span>
                        </div>
                        <div class="form-controller" style="display: block;">
                            <input class="submit-btn"  type="submit" name="submit_button" value="Register"><br>
                            <p style="text-align: center;margin-top: 0;font-size: 14px;">I have already registered. <a href="#"  onclick="openLogin()">Login</a></p>
                        </div>

                    </form>
                </div>
            </div>
            <div id="login-div">
                <div class="form-div">
                    <form action="#" id="loginForm" method="post">
                        <h4 class="form-heading">Login</h4>
                        <div class="form-controller">
                            <label for="" class="form-label">Username</label>
                            <input type="text" name="username" class="form-field" id="login-user">
                        </div>
                        <div class="form-controller">
                            <label for="" class="form-label">Password</label>
                            <input type="password" name="password" class="form-field" id="login-password">
                        </div>
                        <div class="form-controller" style="display: block;">
                            <button class="submit-btn" id="login-btn" name="submit_button" value="login_submit">Login</button><br>
                            <p style="text-align: center;margin-top: 0;font-size: 14px;">If you are not registered. <a href="#" onclick="openRegister()">Click Here</a></p>
                        </div>
                    </form>
                </div>
            </div>

            <?php
                }
                else{
            ?>
            <div id="message-div">
                <div class="form-div">
                    <form action="#" id="messagesForm" method="post">
                        <h4 class="form-heading">Message Scheduling</h4>
                        <div class="form-controller">
                            <label for="" class="form-label">Email</label>
                            <input type="email" name="email" class="form-field" id="mail-email" required >
                        </div>
                        <div class="form-controller">
                            <label for="" class="form-label">Date</label>
                            <input type="date" name="date" class="form-field" id="mail-date" required >
                        </div>
                        <div class="form-controller">
                            <label for="" class="form-label">Time</label>
                            <select class="form-field" name="time" id="mail-time" required >
                                <option value="00:00">00:00</option>
                                <option value="00:00">00:00</option>
                                <option value="00:00">00:00</option>
                                <option value="00:30">00:30</option>
                                <option value="01:00">01:00</option>
                                <option value="01:30">01:30</option>
                                <option value="02:00">02:00</option>
                                <option value="02:30">02:30</option>
                                <option value="03:00">03:00</option>
                                <option value="03:30">03:30</option>
                                <option value="04:00">04:00</option>
                                <option value="04:30">04:30</option>
                                <option value="05:00">05:00</option>
                                <option value="05:30">05:30</option>
                                <option value="06:00">06:00</option>
                                <option value="06:30">06:30</option>
                                <option value="07:00">07:00</option>
                                <option value="07:30">07:30</option>
                                <option value="08:00">08:00</option>
                                <option value="08:30">08:30</option>
                                <option value="09:00">09:00</option>
                                <option value="09:30">09:30</option>
                                <option value="10:00">10:00</option>
                                <option value="10:30">10:30</option>
                                <option value="11:00">11:00</option>
                                <option value="11:30">11:30</option>
                                <option value="12:00">12:00</option>
                                <option value="12:30">12:30</option>
                                <option value="13:00">13:00</option>
                                <option value="13:30">13:30</option>
                                <option value="14:00">14:00</option>
                                <option value="14:30">14:30</option>
                                <option value="15:00">15:00</option>
                                <option value="15:30">15:30</option>
                                <option value="16:00">16:00</option>
                                <option value="16:30">16:30</option>
                                <option value="17:00">17:00</option>
                                <option value="17:30">17:30</option>
                                <option value="18:00">18:00</option>
                                <option value="18:30">18:30</option>
                                <option value="19:00">19:00</option>
                                <option value="19:30">19:30</option>
                                <option value="20:00">20:00</option>
                                <option value="20:30">20:30</option>
                                <option value="21:00">21:00</option>
                                <option value="21:30">21:30</option>
                                <option value="22:00">22:00</option>
                                <option value="22:30">22:30</option>
                                <option value="23:00">23:00</option>
                                <option value="23:30">23:30</option>
                            </select>
                        </div>
                        <div class="form-controller">
                            <label for="" class="form-label">Message</label>
                            <textarea name="message" id="mail-message" rows="10" class="form-field" required ></textarea>
                        </div>
                        <div class="form-controller" style="display: block;">
                            <button class="submit-btn" id="login-btn" name="submit_button" value="login_submit">Submit</button><br>
                            <p style="text-align: center;margin-top: 0;font-size: 14px;">Logout<a href="#" onclick="logout()">Click Here</a></p>
                        </div>
                    </form>
                </div>
            </div>
            <?php
                }
            ?>
        </div>
        <div class="col-50">
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script>
        function openRegister(){
            console.log('Register');
            $('#register-div').show();
            $('#login-div').hide();
            $('#message-div').hide();
        }
        function openLogin(){
            console.log('Login');
            $('#register-div').hide();
            $('#login-div').show();
            $('#message-div').hide();
        }
        $('#registerForm').submit(function(e){
            e.preventDefault();
            $('.submit-btn').prop('disabled', true);
            $.ajax({
                url: 'lab4.php?t=register',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    alert(obj.message);
                    if(obj.status){
                        $('#registerForm')[0].reset();
                        openLogin();
                    }
                    $('.submit-btn').prop('disabled', false);
                }
            });
        });
        $('#loginForm').submit(function(e){
            e.preventDefault();
            $('.submit-btn').prop('disabled', true);
            $.ajax({
                url: 'lab4.php?t=login',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    alert(obj.message);
                    if(obj.status){
                        $('#loginForm')[0].reset();
                        location.reload();
                    }
                    $('.submit-btn').prop('disabled', false);
                }
            });
        });
        $('#messagesForm').submit(function(e){
            e.preventDefault();
            $('.submit-btn').prop('disabled', true);
            $.ajax({
                url: 'lab4.php?t=message',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    var obj = jQuery.parseJSON(data);
                    alert(obj.message);
                    if(obj.status){
                        $('#messagesForm')[0].reset();
                    }
                    $('.submit-btn').prop('disabled', false);
                }
            });
        });
        function logout(){
            $.ajax({
                url: 'lab4.php?t=logout',
                type: 'GET',
                success: function(data) {
                    location.reload();
                }
            });
        }
        $('#passwordval').keyup(function(){
            console.log('1');
            var number = /([0-9])/;
            var alphabets = /([a-zA-Z])/;
            var special_characters = /([~,!,@,#,$,%,^,&,*,-,_,+,=,?,>,<])/;
            var password = $(this).val().trim();
            if (password.length >= 8) {
                if(password.match(number) && password.match(alphabets) && password.match(special_characters)) {
                    $('#req_password .error').html('');
                    $('#req_password .error').hide();
                }
                else{
                    $('#req_password .error').html('Medium (should include alphabets, numbers and special characters.)');
                    $('#req_password .error').show();
                }
            }
            else{
                $('#req_password .error').html('Weak (should be atleast 8 characters.)');
                $('#req_password .error').show();
            }
        });



    </script>
</body>
</html>