<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>Crystal Webmail Admin Panel :: Login</title>
        <link rel="index" href="./?_task=login" />
<link rel="shortcut icon" href="skins/crystal/../crystal/images/favicon.ico"/>
<link rel="stylesheet" type="text/css" href="../skins/crystal/common.css?s=1276276489" />

        <link rel="stylesheet" type="text/css" href="../skins/crystal/login.css?s=1275926860" />
        <style type="text/css" media="screen"> 
        html {
        }
        
        body {
        font: 12px 'Lucida Grande', Arial, sans-serif;
        margin: auto auto auto auto;
        width: 100%;
        height: 100%;
        min-height: 100%;
        position: absolute;
        top: 0px;
        left: 0px;
        background: #a8bbfb url("../skins/crystal/../crystal/images/bg.jpg") center center no-repeat;
    z-index: -1;
        }
        
        #login-container {
        }
        
            #login-container #mainwin {
                background: #c3d0f8;
                border-bottom: 2px solid #ffffff;
                border-left: 2px solid #ffffff;
                border-right: 2px solid #ffffff;
                padding: 20px 20px 20px 20px;
                width: 300px;
                margin: 0 auto;
                text-align: center;
                box-shadow: 1px 2px 6px rgba(0,0,0, 0.5);
                -moz-box-shadow: 1px 2px 6px rgba(0,0,0, 0.5);
                -webkit-box-shadow: 1px 2px 6px rgba(0,0,0, 0.5);
                -webkit-border-bottom-right-radius: 10px;
                -webkit-border-bottom-left-radius: 10px;
                -moz-border-radius-bottomright: 10px;
                -moz-border-radius-bottomleft: 10px;
                border-bottom-right-radius: 10px;
                border-bottom-left-radius: 10px;
            }
            
                        #login-container #topwin {
                background: #92a0c4;
                color: #ffffff;
                border-top: 2px solid #ffffff;
                border-left: 2px solid #ffffff;
                border-right: 2px solid #ffffff;
                padding: 15px 20px 15px 20px;
                width: 300px;
                margin: 0 auto;
                text-align: center;
                box-shadow: 1px 2px 6px rgba(0,0,0, 0.5);
                -moz-box-shadow: 1px 2px 6px rgba(0,0,0, 0.5);
                -webkit-box-shadow: 1px 2px 6px rgba(0,0,0, 0.5);
                -webkit-border-top-left-radius: 10px;
                -webkit-border-top-right-radius: 10px;
                -moz-border-radius-topleft: 10px;
                -moz-border-radius-topright: 10px;
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
            }
            
    </style>
</head>
    <body>
        <table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0" class="bg">
        <tr>
            <td width="100%" height="100%" align="center" valign="middle">
                    <form name="form" action="./" method="post">
<input type="hidden" name="_token" value="c26b271a69fbd345d51a3c574e873f7e" />
                    <div id="login-container"> 
                    <div id="topwin">
                        <b>Welcome to the Crystal Web-Mail Admin Panel</b><br><br>
                        <small>Please authenticate yourself</small>
                        </div>
                    <div id="mainwin">
                    
                    
                        <center><input type="hidden" value="login" /><input type="hidden" name="_timezone" id="rcmlogintz" value="_default_" /><input type="hidden" name="_url" id="rcmloginurl" value="" /><table summary="" border="0"><tbody><tr><td class="title"><label for="rcmloginuser">Username</label>
</td>
<td><input name="user" id="rcmloginuser" type="text" /></td>
</tr>
<tr><td class="title"><label for="rcmloginpwd">Password</label>
</td>
<td><input name="pass" id="rcmloginpwd" type="password" /></td>
</tr>
      </tr></tbody>
</table>
</center>
<br>
                                <input type="submit" value="Login" />
<br><br>
                        <div id="loginmessage"></div>

                    </form>
                </div>
            </td>
        </tr>
        </table>
        <div class="canvas"></div>
</body>
</html>