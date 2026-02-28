<?php 
    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Start session configuration
    ini_set('session.use_trans_sid', false);
    ini_set('session.use_cookies', true);
    ini_set('session.use_only_cookies', true);

    // Determine HTTPS protocol
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
             (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    $protocol = $https ? 'https://' : 'http://';

    // Get the base URL dynamically
    $dirname = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/';
    $installerurl = $protocol . $_SERVER['HTTP_HOST'] . $dirname;

    // Set session cookie parameters
    session_name('ci_installer');
    session_set_cookie_params(0, $dirname, $_SERVER['HTTP_HOST'], $https, true);
    session_start();

    // Include vendor autoload
    require_once __DIR__ . '/vendor/autoload.php';

    use Php\Requirements;
    use Php\Validation;
    use Php\DbImport;
    use Php\FileWrite;

    // Initialize class instances
    $Requirements = new Requirements();
    $Validation   = new Validation();
    $DbImport     = new DbImport();
    $FileWrite    = new FileWrite(); 

    // Set absolute paths for files
    $path = [
        'sql_path'      => __DIR__ . '/sql/install.sql',
        'template_path' => __DIR__ . '/php/Database.php',
        'output_path'   => __DIR__ . '/../application/config/database.php',
        'config_path'   => __DIR__ . '/../application/config/config.php',
    ];

    $message = null; 

    // Generate CSRF token if not set
    if (empty($_SESSION['_token'])) {
        $_SESSION['_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
    $token = $_SESSION['_token']; 

    // Handle Step 3
    if (isset($_GET['complete']) && !$Validation->checkEnvFileExists()) {
        $FileWrite->createEnvFile();
        session_destroy();
    } else {
        $Validation->checkEnvFileExists();
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (!empty($_POST['email']) && !empty($_POST['password'])) {
            // Validate login data
            $lresult = $Validation->validate_login($_POST);
            if ($lresult === true) {
                // Insert login data
                if ($DbImport->insert_login($_POST)) {
                    header('Location: ' . $installerurl . 'index.php?complete=true');
                    exit;
                } else {
                    $message .= "<li>Failed! Please Try Again</li>";
                }
            } else {
                $message .= $lresult;
            }
        }

        // Validate installation input
        if ($Validation->run($_POST) === true) {
            if (!$Validation->checkFileExists($path['sql_path'])) {
                $message .= "<li>install.sql file is missing in sql/ directory!</li>";
            } else {
                // Handle database configuration
                if (!$FileWrite->databaseConfig($path, $_POST)) {
                    $message .= "<li>Database configuration file could not be written. Please chmod application/config/database.php to 777.</li>";
                } elseif (!$FileWrite->baseUrl($path['config_path'])) {
                    $message .= "<li>Config file could not be written. Please chmod application/config/config.php to 777.</li>";
                } elseif (!$DbImport->createDatabase($_POST)) {
                    $message .= "<li>The database could not be created. Please verify your settings.</li>";
                } elseif (!$DbImport->createTables($_POST)) {
                    $message .= "<li>The database tables could not be created. Please verify your settings.</li>";
                } else { 
                    header('Location: ' . $installerurl . 'index.php?step2=true');
                    exit;
                }   
            }
        } else {
            $message = $Validation->run($_POST);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head> 
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="<?php echo $installerurl;?>assets/img/favicon.png" sizes="32x32">

        <title>CI Application Installer</title>
        <link rel="stylesheet" href="<?php echo $installerurl;?>assets/css/bootstrap.min.css">
        <!-- custom css  -->
        <link rel="stylesheet" href="<?php echo $installerurl;?>assets/css/style.css"> 

        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>

    <body>
        <!-- begin of container -->
        <div class="container"> 
            <!-- begin of row -->
            <div class="row"> 
                <div class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2"> 

                <!-- begin of step 1 -->
                <?php 
                if (isset($_GET['step1']) || (!isset($_GET['step1']) && !isset($_GET['step2']) && !isset($_GET['complete']))) {  
                ?>
                <div class="row">
                    <div class="app_title"> 
                     
                    </div>
                    <div class="app_content">
                        <div class="row">                  
                         <div class="text-center">
                        <img src="<?php echo $installerurl;?>assets/img/logo.png" alt="OcTexPro" width="20%">
                        </div>
                            <div class="col-sm-12">
                                <h3 class="title text-center margin">App Installation Information</h3>

                                    <?php
                                        if (!empty($message)) {
                                            echo "<div class=\"alert alert-danger\"><ul>$message</ul></div>";
                                        }
                                    ?>

                                <form action="#" method="post" >

                                    <input type="hidden" name="_token" value="<?= (!empty($token)?$token:null) ?>"/>

                                    <div class="form-group col-sm-6">
                                        <label for="database">Database Name </label>
                                        <input type="text" name="database" class="form-control" id="database" placeholder="Database Name" value="<?= (!empty($_POST['database'])?$_POST['database']:null) ?>">
                                    </div> 
                                    <div class="form-group col-sm-6">
                                        <label for="username">Username </label>
                                        <input type="text" name="username" class="form-control" id="username" placeholder="Username" value="<?= (!empty($_POST['username'])?$_POST['username']:null) ?>">
                                    </div> 
                                    <div class="form-group col-sm-6">
                                        <label for="password">Password </label>
                                        <input type="text" name="password" class="form-control" id="password" placeholder="Password" value="<?= (!empty($_POST['password'])?$_POST['password']:null) ?>">
                                    </div>  
                                    <div class="form-group col-sm-6">
                                        <label for="hostname">Host Name </label>
                                        <input type="text" name="hostname" class="form-control" id="hostname" placeholder="Host Name"  value="<?= (!empty($_POST['hostname'])?$_POST['hostname']:"localhost") ?>">
                                    </div> 
                                       
                             
                                    <button type="submit" class="cbtn pull-right">Next</button>
                                </form>

                            </div>
                        </div>
                    </div> 
                    <div class="app_footer"> 
                        <h3>Developed by <a href="https://octexpro.com/">OcTexPro</a></h3>
                    </div>
                </div>
                <?php 
                } 
                ?>
                <!-- ends of step 1 -->


                <!-- begin of step 2 -->
                <?php 
                if (isset($_GET['step2'])) { 
                ?>
                <div class="row">
                    <div class="app_title"> 
                        
                    </div>
                    <div class="app_content">
                        <div class="row">                  
                         <div class="text-center">
                        <img src="<?php echo $installerurl;?>assets/img/logo.png" alt="Six Experts" width="20%">
                        </div>
                        <div class="col-sm-12" id="hidecontent"><h3 id="step2progress">&nbsp;</h3></div>
                            <div class="col-sm-12 hide" id="stepmain">
                                <h3 class="title text-center margin">App Login Information</h3>
										<p class="text-center">Please add your own initial Email and Password. Please change that after login</p>
                                    <?php
                                        if (!empty($message)) {
                                            echo "<div class=\"alert alert-danger\"><ul>$message</ul></div>";
                                        }
                                    ?>

                                <form action="<?php echo $installerurl;?>index.php?complete=true" method="post" name="step2">
                                <input type="hidden" name="_token" value="<?= (!empty($token)?$token:null) ?>"/>
                                    <div class="form-group col-sm-6">
                                        <label for="email">User Email </label>
                                        <input type="email" name="email" class="form-control" id="email" placeholder="Your Email" value="" required>
                                    </div> 
                                    <div class="form-group col-sm-6">
                                        <label for="password">User Password </label>
                                        <input type="password" name="password" class="form-control" id="password" placeholder="Password" value="" required>
                                    </div>   
                                   
                                    <button type="submit" class="cbtn pull-right" id="step2">Next</button>
                                </form>

                            </div>
                        </div>
                    </div> 
                    <div class="app_footer"> 
                        <h3>Developed by OcTexPro</h3>
                    </div>
                </div>
                <?php } ?>
                <!-- ends of step 3 -->



                <!-- begin of step 3 -->
                <?php if (isset($_GET['complete'])) { 
				
				?>
                <div class="row">
                    <div class="app_title"> 
                      
                    </div>
                    <div class="app_content">
                        <div class="row">
                        <div class="text-center">
                        <img src="<?php echo $installerurl;?>assets/img/logo.png" alt="Six Experts" width="20%">
                        </div>
                            <div class="col-sm-12">
                                <h3 class="title text-center margin">Installation complete</h3> 
                                
                                <div class="alert alert-success">
                                    <strong>Your application installed successfully !!!</strong>
                                </div>

                                <div class="divider"></div>

                                <h3 class="text-center" id="btn-before">&nbsp;</h3>
                                <div class="text-center hide" id="btn-complete">
                                    <a href="index.php" class="btn cbtn">Click to launch your application </a>
                                </div>

                            </div>
                        </div>
                    </div> 
                    <div class="app_footer"> 
                        <h3>Developed by <a href="https://octexpro.com/">OcTexPro</a></h3>
                    </div>
                </div>
                <?php } ?>
                <!-- ends of step3 -->


                </div>
            </div>
            <!-- ends of row -->
        </div> 
        <!-- ends of container -->


        <!-- start of javascript  -->
        <script type="text/javascript" src="<?php echo $installerurl;?>assets/js/jquery.min.js"></script>
        <script type="text/javascript">
        $(document).ready(function() {
            'use strict';
            var wait = 55000; //10 second

            var time = 55;
            setInterval(function(){
             $("#step2progress").html("You need to wait "+time+" second before you can proceed");
             time--;
            }, 1000);

            setTimeout(function() {
                $("#step2progress").addClass('hide');
                $("#hidecontent").removeClass('hide');
				$("#stepmain").removeClass('hide');
            }, wait);

        });
        </script>
        <!-- ends of javascript -->

        
    </body>
</html>