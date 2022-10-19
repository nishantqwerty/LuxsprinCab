<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Forgot password</title>
</head>
<style>
    body{
        margin:0; padding:0;
    }
    td.borders::after {
        position: absolute;
        content: "";
        height: 5px;
        width: 60px;
        background: #70737a;
        top: 7px;
        left: 0;
        right: 0;
        margin: 0 auto;
    }
    td.borders {
        position: relative;
    }
    a {
        text-decoration: none;
    }
</style>

<body>
<table style="font-family: arial;max-width: 610px; margin: 0 auto;  padding: 0;   width: 100%; text-align:center;" cellpadding="0" cellspacing="0">
    <tbody>
    <tr>
        <td style="background: url({{ asset('images/emails/bg.png') }});
    background-repeat: no-repeat;
    padding: 50px 10px; text-align:left;"><img src="{{ asset('images/emails/logo.png') }}"></td>
    </tr>

    <tr>
        <td style="width:100%;padding: 60px 0 100px;">
            <table style="max-width:400px; margin:0 auto; padding:0; width: 100%;" cellpadding="0" cellspacing="0">
                <tbody>
                <tr>
                    <td><h1 style="font-size: 35px;margin:15px 0;">Forgot Your Password?</h1></td>
                </tr>
                <tr>
                    <td style="color:#bdbbc6; font-size:20px;     padding: 10px 10px 30px;line-height: 26px;">No worries, here is your link to get a new password!</td>
                </tr>
                <tr>
                    <td style="color:#bdbbc6; font-size:18px; padding:0 0 20px 0;">
                        <a href = "{{$url}}">{{ $url }}</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table style="max-width: 530px;
                    margin: 20px auto 0;
                    padding: 15px 5px;
                    width: 100%;
                    border-top: 1px solid #a9aabe;" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="color:#bdbbc6; font-size:15px; padding:0; width:50%; text-align:left;">
                        <a href="https://brightworld.com" style="color:#bdbbc6;">www.hcab.com</a></td>
                    <td style="color:#bdbbc6; font-size:15px; padding:0; width:50%; text-align:right;">
                        Follow Us
                        <a href="https://www.facebook.com/" style="vertical-align: middle;"><img src="{{ asset('images/emails/f.png') }}"></a>
                        <a href="https://www.instagram.com/" style="vertical-align: middle;"><img src="{{ asset('images/emails/2.png') }}"></a>
                        <a href="https://twitter.com/" style="vertical-align: middle;"><img src="{{ asset('images/emails/t.png') }}"></a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    </tbody>
</table>
</body>
</html>
