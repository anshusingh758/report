
<nav class="navbar navbar-inverse navbar-fixed-top" style="background-color: #2266AA;border: none;color: #fff;">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle pull-right" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a href="index.php" class="navbar-brand" style="color: #fff;margin: -1px auto;font-size: 20px;"><i class="fa fa-fw fa-file-text"></i> vTech Reports</a>
    </div>
    <!--Top Menu-->
    <ul class="nav navbar-right top-nav">
        <?php
            if(isset($_SESSION['user'])){
                $query="SELECT concat(first_name,' ',last_name)AS unameX FROM users where uid='$user'";
                $result=mysqli_query($misReportsConn,$query);
                if($res=mysqli_fetch_array($result)){
        ?>
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" style="cursor: pointer;"><i class="fa fa-fw fa-user"></i> <?php echo $res['unameX']; ?> <b class="fa fa-fw fa-angle-down"></b></a>
            <ul class="dropdown-menu">
                <li><a data-toggle="modal" data-target="#change_password_modal" style="cursor: pointer;"><i class="fa fa-fw fa-key"></i> Change Password</a></li>
                <hr style="border: 0.5px #ccc solid;margin: 5px 0px;">
                <li><a href="logout.php"><i class="fa fa-fw fa-power-off"></i> Logout</a></li>
            </ul>
        </li>
        <?php
                }
            }
        ?>
    </ul>
</nav>

<div class="footer">&copy; vTech Solution, Inc. All rights reserved.</div>