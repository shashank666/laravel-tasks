<!DOCTYPE html>
<html>

<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <style>
        /* CLIENT-SPECIFIC STYLES */

        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
        }
        /* RESET STYLES */

        img {
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        table {
            border-collapse: collapse !important;
        }

        body {
            font-family: sans-serif;
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        /* iOS BLUE LINKS */

        a[x-apple-data-detectors] {
            color: #007bff;
            text-decoration: none;
            font-size: inherit;
            font-family: inherit;
            font-weight: inherit;
            line-height: inherit;
        }
        /* MOBILE STYLES */

        a {
            color: #007bff;
            text-decoration: none;
        }

        @media screen and (max-width:600px) {
            h1 {
                font-size: 32px !important;
                line-height: 32px !important;
            }
        }
        /* ANDROID CENTER FIX */

        div[style*="margin: 16px 0;"] {
            margin: 0 !important;
        }

        ul>li {
            margin-top: 8px;
        }
    </style>
</head>

<body style="background-color: #f4f4f4; margin: 0 !important; padding: 0 !important;">

    <!-- HIDDEN PREHEADER TEXT -->
    <div style="display: none; font-size: 1px; color: #fefefe; line-height: 1px;max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
        {{ $email_subject }}
    </div>

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <!-- LOGO -->
        <tr>
            <td bgcolor="#fff" align="center">

                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 98%;">
                    <tr>
                        <td bgcolor="#ffffff" align="center" valign="top" style="/*padding: 40px 10px 40px 10px;*/">
                            <a href="https://www.weopined.com" target="_blank" title="Opined - Where Every Opinion Matters !">
                                <img src="https://www.weopined.com/img/logo.png" height="70" width="210">
                            </a>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
        <!-- HERO -->
        <tr>
            <td  align="center" style="padding: 0px 10px 0px 10px;background: linear-gradient(180deg, rgba(255,255,255,1) 0%, rgba(255,152,0,0.5) 91%);">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 100%;">
                    <tr>
                        <td bgcolor="#ffffff" align="left" style="padding: 2px 10px 2px 10px; color: #000;font-size: 18px; line-height: 25px; text-align: justify;">
                            <p style="margin: 0; font-family: Open Sans, Helvetica, Arial, sans-serif;font-weight:lighter"><br/>{!! $email_content !!}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- COPY BLOCK -->
        <!-- <tr>
            <td align="center" style="padding: 0px 10px 0px 10px;background: linear-gradient(180deg, rgba(255,152,0,0.5) 0%, rgba(219,229,234,1) 91%)">

                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 100%;">
                    --> 
                    <!-- COPY -->
                   <!--  <tr>
                        <td bgcolor="#ffffff" align="left" style="padding: 0px 30px 40px 30px; border-radius: 0px 0px 4px 4px; color: #000;font-size: 18px;  line-height: 25px; text-align: justify;">
                            <p style="margin:0;font-weight:lighter">Cheers! 🍺<br/>Opined Team</p>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr> -->
        <!-- FOOTER -->
        <table data-module="footer-light-2cols0"
        data-thumb=""
        width="100%" cellspacing="0" cellpadding="0" border="0" role="presentation" class="">

        <tbody>
            <tr>
                <td class="o_bg-light o_px o_pb-lg" align="center" data-bgcolor="Bg Light"
                    style="background-color: #dbe5ea;/*padding-left: 16px;padding-right: 16px;padding-bottom: 32px;*/">
                    <!--[if mso]><table cellspacing="0" cellpadding="0" border="0" role="presentation"><tbody><tr><td width="200" align="left" valign="top" style="padding:0px 8px;"><![endif]-->
                    <div class="o_col o_col-4"
                        style="display: inline-block;vertical-align: top;width: 100%;max-width: 400px;">
                        <div style="font-size: 32px; line-height: 32px; height: 32px;">&nbsp; </div>
                        <div class="o_px-xs o_sans o_text-xs o_text-light o_left o_xs-center" data-color="Light"
                            data-size="Text XS" data-min="10" data-max="18"
                            style="font-family: Helvetica, Arial, sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;color: #82899a;text-align: left;padding-left: 8px;padding-right: 8px;">
                            <p class="o_mb-xs" style="margin-top: 0px;margin-bottom: 8px;">This is system generated email, please do not reply</p>
                            <p class="o_mb-xs" style="margin-top: 0px;margin-bottom: 8px;">© {{ \Carbon\Carbon::now()->format('Y') }}  Opined . All rights reserved.</p>
                            <p class="o_mb-xs" style="margin-top: 0px;margin-bottom: 8px;"></p>
                            <p style="margin-top: 0px;margin-bottom: 0px;">
                                <a class="o_text-light o_underline" href="http://www.weopined.com/contactus" data-color="Light"
                                    style="text-decoration: underline;outline: none;color: #82899a;">Contact Us</a>
                                <span class="o_hide-xs">&nbsp; • &nbsp;</span><br class="o_hide-lg"
                                    style="display: none;font-size: 0;max-height: 0;width: 0;line-height: 0;overflow: hidden;mso-hide: all;visibility: hidden;">
                                <a class="o_text-light o_underline" href="http://www.weopined.com/legal/terms_of_service" data-color="Light"
                                    style="text-decoration: underline;outline: none;color: #82899a;">Terms Of Service</a>
                                <span class="o_hide-xs">&nbsp; • &nbsp;</span><br class="o_hide-lg"
                                    style="display: none;font-size: 0;max-height: 0;width: 0;line-height: 0;overflow: hidden;mso-hide: all;visibility: hidden;">
                                <a class="o_text-light o_underline" href="http://www.weopined.com/legal/privacy_policy" data-color="Light"
                                    style="text-decoration: underline;outline: none;color: #82899a;">Privacy Policy</a>
                            </p>
                        </div>
                    </div>
                    <!--[if mso]></td><td width="400" align="right" valign="top" style="padding:0px 8px;"><![endif]-->
                    <div class="o_col o_col-2"
                        style="display: inline-block;vertical-align: top;width: 100%;max-width: 200px;">
                        <div style="font-size: 32px; line-height: 32px; height: 32px;">&nbsp; </div>
                        <div class="o_px-xs o_sans o_text-xs o_text-light o_right o_xs-center" data-size="Text XS"
                            data-min="10" data-max="18"
                            style="font-family: Helvetica, Arial, sans-serif;margin-top: 0px;margin-bottom: 0px;font-size: 14px;line-height: 21px;color: #82899a;/*text-align: right;*/padding-left: 8px;padding-right: 8px;">
                            <p style="margin-top: 0px;margin-bottom: 0px;">
                                <a class="o_text-light"  href="https://www.facebook.com/weopined" data-color="Light" target="_blank" title="Follow Opined On Facebook"
                                    style="text-decoration: none;outline: none;color: #82899a;"><img
                                        src="https://www.weopined.com/img/ei1.png"
                                        width="36" height="36" alt="fb"
                                        style="max-width: 36px;-ms-interpolation-mode: bicubic;vertical-align: middle;border: 0;line-height: 100%;height: auto;outline: none;text-decoration: none;"
                                        data-crop="false"></a><span> &nbsp;</span>
                                <a class="o_text-light" href="https://twitter.com/weopined" data-color="Light" target="_blank" title="Follow Opined On Twitter"
                                    style="text-decoration: none;outline: none;color: #82899a;"><img
                                        src="https://www.weopined.com/img/ei2.png"
                                        width="36" height="36" alt="tw"
                                        style="max-width: 36px;-ms-interpolation-mode: bicubic;vertical-align: middle;border: 0;line-height: 100%;height: auto;outline: none;text-decoration: none;"
                                        data-crop="false"></a><span> &nbsp;</span>
                                <a class="o_text-light" href="https://www.linkedin.com/company/opined" data-color="Light" target="_blank" title="Follow Opined On Linkedin"
                                    style="text-decoration: none;outline: none;color: #82899a;"><img
                                        src="https://www.weopined.com/img/ei4.png"
                                        width="36" height="36" alt="ln"
                                        style="max-width: 36px;-ms-interpolation-mode: bicubic;vertical-align: middle;border: 0;line-height: 100%;height: auto;outline: none;text-decoration: none;"
                                        data-crop="false"></a><span> &nbsp;</span>
                            </p>
                        </div>
                    </div>
                    <!--[if mso]></td></tr></table><![endif]-->
                    <!-- <div class="o_hide-xs" style="font-size: 64px; line-height: 64px; height: 64px;">&nbsp; </div> -->
                </td>
            </tr>

        </tbody>
    </table>
    </table>
</body>

</html>
