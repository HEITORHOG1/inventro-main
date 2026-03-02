<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f4;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 520px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1a1a2e, #16213e); padding: 32px 40px; text-align: center;">
                            <h1 style="margin: 0; font-size: 22px; font-weight: 600; color: #25D366;">Inventro</h1>
                            <p style="margin: 4px 0 0; font-size: 13px; color: #a0a0a0;">Cardapio Digital</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.5;">
                                Ola <?php echo html_escape($name); ?>,
                            </p>

                            <p style="margin: 0 0 20px; font-size: 15px; color: #555555; line-height: 1.6;">
                                Voce solicitou a redefinicao de senha da sua conta. Clique no botao abaixo para criar uma nova senha:
                            </p>

                            <!-- Button -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin: 32px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="<?php echo html_escape($reset_url); ?>" style="display: inline-block; padding: 14px 40px; background-color: #25D366; color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 600; border-radius: 8px; letter-spacing: 0.3px;">Redefinir Senha</a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 12px; font-size: 14px; color: #888888; line-height: 1.5;">
                                Este link expira em 1 hora.
                            </p>

                            <p style="margin: 0 0 20px; font-size: 14px; color: #888888; line-height: 1.5;">
                                Se voce nao solicitou a redefinicao de senha, ignore este e-mail. Sua conta permanecera segura.
                            </p>

                            <!-- Fallback URL -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top: 24px; border-top: 1px solid #eeeeee; padding-top: 20px;">
                                <tr>
                                    <td>
                                        <p style="margin: 0 0 8px; font-size: 12px; color: #999999;">Se o botao nao funcionar, copie e cole o link abaixo no seu navegador:</p>
                                        <p style="margin: 0; font-size: 12px; color: #25D366; word-break: break-all;">
                                            <?php echo html_escape($reset_url); ?>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 24px 40px; text-align: center; border-top: 1px solid #eeeeee;">
                            <p style="margin: 0; font-size: 13px; color: #999999;">
                                Inventro - Cardapio Digital
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
