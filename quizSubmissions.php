<?php
// PDO
error_reporting(0);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
session_start();
$connType = "PDO";
require("./connect.php");
include("./services/saveUserActivityPdo.php");
$useractivity = new ActivityHistory();
$useractivity->saveHistory($conn, "Quiz Feedback", "Opened Quiz Feedback");
if (!$_SESSION["loggedin"]) {
  header("Location:./index.php");
}

?>
<!doctype html>
<html lang="en">

  <head>
    <link rel="icon" 
        type="image/png" 
        href="./resources/favicon.jpeg">
    <title>Animation Demo</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-sweetalert/1.0.1/sweetalert.min.css">

    <!-- Scripts By Self -->
    <script src="./scripts/quizSubmissions.js" type="text/javascript"></script>
    <!-- <script src="./scripts/animate.js" type="text/javascript"></script> -->
    <link rel="stylesheet" href="./cssstyles/style.css" />
  </head>

  <body>
    <?php 
      require("./navigationbar.php");
    ?>
      <div class="container-fluid" >
          <div class="centerAlign">
              <h2 class="animationHeading">
                  Your Quiz Results
              </h2>
          </div>
          <!--  -->
          <div class="card ">
            <form>
              <div class="form-group row">
                <div class = "col-sm-12 col-md-12">
                  <table class="table tabresponsive">
                      <thead>
                          <tr>
                               <th>User ID</th>
                              <th>First Name</th>
                              <th>Last Name</th>
                              <th>Role</th>
                          </tr>
                      </thead>
                      <tbody id="pullQuizAnsForEachUSer">
                        <?php 
                     $sql = "SELECT id, fname ,lname ,r_name FROM users as u left JOIN pmp_user_role_mapping as purm on u.id = purm.user_id LEFT join pmp_role as pr on pr.role_id = purm.role_id where quiz_status=:qs";

                    //  $sql = "SELECT id,fname ,lname ,role FROM `users` where quiz_status=:qs";

                        $stmt1= $conn->prepare($sql);
                        $stmt1->execute(['qs'=>1]);
                        $result1=$stmt1->fetchAll(PDO::FETCH_ASSOC);

                        if($result1){
                              // echo json_encode($result1);
                            foreach($result1 as $row1){
                              // var_dump($row1) ;

                              echo '<tr>';
                              echo '<td>'.$row1["id"].'</td>';
                              echo '<td>'.$row1["fname"].'</td>';
                              echo '<td>'.$row1["lname"].'</td>';
                              echo '<td>'.$row1["r_name"].'</td>';
                              echo '<td><button id="showQuizRes" type="button" data-toggle="modal" data-target="#myModal" onclick = "showQuizResults('.$row1["id"].')" class="btn btn-success" > Show</button>
                              </td>';

                              echo '</tr>';
                               
                            }
                            } else {
                          echo "<tr><td colspan='7' style='text-align:center;font-size: xx-large;' class='redColor'>No Quiz Submissions yet!!! </td></tr>";
                            }
                        ?>

                      </tbody>
                  </table>
                </div>
              </div>
            </form>
          </div>
        
      </div>

      <!-- The Modal -->
  <div class="modal" id="myModal">
    <div class="modal-dialog">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Quiz Results</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
            <div class="card" id="ansresults"> 
                  <!-- <form>
                    <div class="form-group row">
                      <div class = "col-sm-12 col-md-12">
                        <table class="table tabresponsive">
                            <thead>
                                <tr>
                                    <th>Question Number</th>
                                    <th>User Name</th>
                                    <th>Always</th>
                                    <th>Very Often</th>
                                    <th>Sometimes</th>
                                    <th>Almost Never</th>
                                    <th>Never(pops up)</th>
                                </tr>
                            </thead>
                            <tbody id="pullquizans">
                              <?php 
                            $sql = "SELECT q.id,a.user_id,u.fname,u.lname, a.ans_value,a.timestamp FROM fb_qs as q LEFT JOIN fb_ans as a on q.id = a.q_id LEFT join users as u on a.user_id = u.id WHERE a.user_id = ?";

                              $stmt= $conn->prepare($sql);
                              // $stmt->execute([$_SESSION["userid"]]);
                              $stmt->execute([$row1["id"]]);
                              // var_dump($stmt);
                              $result=$stmt->fetchAll(PDO::FETCH_ASSOC);

                              if($result){
                                  //  echo json_encode($result);
                                  foreach($result as $row){
                                  //  var_dump($row) ;

                                    echo '<tr>';
                                    echo '<td>'.$row["id"].'</td>';
                                    echo '<td>'.$row["fname"].'</td>';

                                      if($row["ans_value"]=="Always"){
                                      echo '<td><label><input type="radio" disabled  name="optradio'.$row["id"].'" checked></label></td>';
                                      }else {
                                        echo '<td><label><input type="radio" disabled name="optradio'.$row["id"].'"></label></td>';
                                      }
                                    if($row["ans_value"]=="Very Often"){
                                      echo '<td><label><input type="radio"  disabled name="optradio'.$row["id"].'" checked></label></td>';
                                    }else {
                                      echo '<td><label><input type="radio"  disabled name="optradio'.$row["id"].'"></label></td>';
                                    }
                                    if($row["ans_value"]=="Sometimes"){
                                      echo '<td><label><input type="radio"  disabled name="optradio'.$row["id"].'" checked></label></td>';
                                    }else {
                                      echo '<td><label><input type="radio"  disabled name="optradio'.$row["id"].'"></label></td>';
                                    }
                                    if($row["ans_value"]=="Almost Never"){
                                      echo '<td><label><input type="radio"  disabled name="optradio'.$row["id"].'" checked></label></td>';
                                    }else {
                                      echo '<td><label><input type="radio"  disabled name="optradio'.$row["id"].'"> </label></td>';
                                    }
                                    if($row["ans_value"]=="Never (pops up)"){
                                      echo '<td><label><input type="radio"  disabled name="optradio'.$row["id"].'" checked></label></td>';
                                    }else {
                                      echo '<td><label><input type="radio"  disabled name="optradio'.$row["id"].'"></label></td>';
                                    }
                                    echo '</tr>';
                                  }
                                  } else {
                                echo "<tr><td colspan='7' style='text-align:center;font-size: xx-large;' class='redColor'>No Quiz Submissions yet!!! </td></tr>";
                                  }
                              ?>

                            </tbody>
                        </table>
                      </div>
                    </div>
                  </form> -->
            </div>
        </div>

        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>

      </div>
    </div>
  </div>
  </body>

</html>