<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">
<html>
<head>

</head>
<body style='margin: 0;font-family:"Myriad Pro","Arial","Helvetica",sans-serif;font-size:16px;line-height:1.4;color:#505152;background-color:#fff;' bgcolor="#fff">
    <table id="layout" style="padding: 8px;background: linear-gradient(255deg, #4A95AA, #2D477C);" cellspacing="0" cellpadding="0" border="0" width="100%">
        <tr>
            <td style='font-family:"Myriad Pro","Arial","Helvetica",sans-serif;font-size:16px;line-height:1.4;' align="center" valign="top">
                <table id="container" cellspacing="0" cellpadding="0" border="0" width="700" style="background:#fff;border:1px solid #c7d0d4;border-radius:4px;margin-top:20px;margin-bottom:50px;overflow:hidden;">
                    <tr>
                        <td id="header" style='font-family:"Myriad Pro","Arial","Helvetica",sans-serif;font-size:16px;line-height:1.4;padding:10px 50px;background:#fff;border-bottom:1px solid #dde4e7;' height="64" align="center" valign="middle">
                            <a href="" target="_blank" style="color:#3b99d9;text-decoration:none;">
                                <p>RAPIDTASK</p>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td id="body" style='font-family:"Myriad Pro","Arial","Helvetica",sans-serif;font-size:16px;line-height:1.4;padding:20px 50px 40px 50px;' align="left" valign="top">
                            <div id="content">
                                <h1 style="margin:20px 0 10px 0;color:#505152;font-size:24px;font-weight:bold;">Convite</h1>
                                <p style='font-family:"Myriad Pro","Arial","Helvetica",sans-serif;font-size:16px;line-height:1.4;'>
                                    Olá {{$receiverName}},<br>
                                    <br>
                                    {{$nomeDonoTime}} convidou você para ser colaborador do time {{$nomeTime}} na plataforma de gerenciamento de projetos RAPIDTASK.<br>
                                    <br>
                                    Você pode <b>aceitar</b> ou <b>recusar</b> este convite.<br> 
                                    <a style="background-color: #155724;border-radius: 2px;box-sizing: border-box;color: #fff;display: block;font-family: 'Lato','ArialMT',Helvetica,sans-serif;font-size: 18px;font-weight: normal;min-height: 48px;margin: 28px auto 0;max-width: 280px;padding: 0 20px;text-align: center;border-radius: 4px;text-decoration: none;width: 100%;min-width: 232px" href="http://127.0.0.1:8000/time-membro/aceitar/{{$identificacao}}"><span style="font-family: 'Lato','Helvetica','ArialMT',sans-serif;line-height: 24px;padding: 12px 0;display: block;font-weight: 300;">Aceitar</span></a>
                                    <a style="background-color: #721c24;border-radius: 2px;box-sizing: border-box;color: #fff;display: block;font-family: 'Lato','ArialMT',Helvetica,sans-serif;font-size: 18px;font-weight: normal;min-height: 48px;margin: 28px auto 0;max-width: 280px;padding: 0 20px;text-align: center;border-radius: 4px;text-decoration: none;width: 100%;min-width: 232px" href="http://127.0.0.1:8000/time-membro/recusar/{{$identificacao}}"><span style="font-family: 'Lato','Helvetica','ArialMT',sans-serif;line-height: 24px;padding: 12px 0;display: block;font-weight: 300;">Recusar</span></a> 
                                    <br>
                                </p>
                            </div>
                            <div id="footer">
                                <p style='font-family:"Myriad Pro","Arial","Helvetica",sans-serif;font-size:16px;line-height:1.4;'>
                                    Obrigado.<br>
                                    Equipe Rapistask.                                  
                                </p>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>